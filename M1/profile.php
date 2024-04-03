<?php
require(__DIR__ . "/partials/nav.php");
?>
<style>

@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;900&display=swap');
    body {
        background: url('bg1.jpg') no-repeat center center fixed; 
        background-color: #f0f0f0;
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .content-box {
        
        position: relative;
        width: 450px;
        height: 700px;
        border-radius: 20px;
        padding: 40px;
        box-sizing: border-box;
        background: #ecf0f3;
        /* box-shadow: 14px 14px 20px #cbced1, -14px -14px 20px white; */
    
    }
    h1 {
        color: #333;
    }
    .welcome-message {
        color: green;
    }
    .not-logged-in-message {
        color: red;
    }
    .session-info {
        font-size: 0.8em;
        color: #666;
    }
    input[type="submit"] {
        border-radius: 20px; /* Adjust the value to change the roundness */
        padding: 10px 20px; /* Adjust padding as needed */
        background-color: #007bff; /* Button background color */
        color: #fff; /* Button text color */
        border: none; /* Remove default border */
        cursor: pointer;

        /* Resetting box-shadow and other properties */
        box-shadow: none;
        text-align: center;
        display: inline-block;
        margin-top: 20px;
    }

    input[type="submit"]:hover {
        background-color: #0056b3; /* Change button background color on hover */
    }

    .inputs {
        text-align: left;
        margin-top: 30px;
    }

    label, input, button {
        display: block;
        width: 100%;
        padding: 0;
        border: none;
        outline: none;
        box-sizing: border-box;
    }

    label {
        margin-bottom: 4px;
    }

    label:nth-of-type(2) {
        margin-top: 12px;
    }

    input::placeholder {
        color: gray;
    }
    input::placeholder {
        color: gray;
    }

    input {
        background: #ecf0f3;
        padding: 10px;
        padding-left: 20px;
        height: 50px;
        font-size: 14px;
        border-radius: 50px;
        box-shadow: inset 6px 6px 6px #cbced1, inset -6px -6px 6px white;
    }

    .brand-title {
        margin-top: 10px;
        font-weight: 900;
        font-size: 1.8rem;
        color: #363636;
        letter-spacing: 1px;
    }

    .flash {
    color: #ffffff;
    }

</style>
<div class="content-box">
<div class="brand-title">WELCOME TO YOUR PROFILE</div>
    
    <?php
    if (is_logged_in()) {
        //echo "Welcome, " . get_user_email();
        echo $_SESSION['user']['email'];
        echo "<br>";
        echo $_SESSION['user']['username'];
    } else {
        echo "You're not logged in";
    }
    ?>

    <?php
    if (isset($_POST["save"])) {
        $email = se($_POST, "email", null, false);
        $username = se($_POST, "username", null, false);


        // Check if the username or email already exists in the database
        $db = getDB();
        $stmt = $db->prepare("SELECT COUNT(*) FROM Users WHERE email = :email AND username = :username");
        $stmt->execute([":email" => $email, ":username" => $username]);
        $count = $stmt->fetchColumn();

        // If the count is greater than 0, it means a duplicate exists
        if ($count > 0) {
        flash("The chosen username or email is already taken.", "warning");
        } else {
        // If no duplicates, proceed with the update logic...
        // ... existing update logic here ...
        

        $params = [":email" => $email, ":username" => $username, ":id" => get_user_id()];
        // $db = getDB();
        $stmt = $db->prepare("UPDATE Users set email = :email, username = :username where id = :id");
        try {
            $stmt->execute($params);
            flash("Profile saved", "success");
        } catch (Exception $e) {
            if ($e->errorInfo[1] === 1062) {
                //https://www.php.net/manual/en/function.preg-match.php
                preg_match("/Users.(\w+)/", $e->errorInfo[2], $matches);
                if (isset($matches[1])) {
                    flash("The chosen " . $matches[1] . " is not available.", "warning");
                } else {
                    //TODO come up with a nice error message
                    echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
                }
            } else {
                //TODO come up with a nice error message
                echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
            }
        }}
        //select fresh data from table
        $stmt = $db->prepare("SELECT id, email, username from Users where id = :id LIMIT 1");
        try {
            $stmt->execute([":id" => get_user_id()]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                //$_SESSION["user"] = $user;
                $_SESSION["user"]["email"] = $user["email"];
                $_SESSION["user"]["username"] = $user["username"];
            } else {
                flash("User doesn't exist", "danger");
            }
        } catch (Exception $e) {
            flash("An unexpected error occurred, please try again", "danger");
            //echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
        }


        //check/update password
        $current_password = se($_POST, "currentPassword", null, false);
        $new_password = se($_POST, "newPassword", null, false);
        $confirm_password = se($_POST, "confirmPassword", null, false);
        if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
            if ($new_password === $confirm_password) {
                //TODO validate current
                $stmt = $db->prepare("SELECT password from Users where id = :id");
                try {
                    $stmt->execute([":id" => get_user_id()]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (isset($result["password"])) {
                        if (password_verify($current_password, $result["password"])) {
                            $query = "UPDATE Users set password = :password where id = :id";
                            $stmt = $db->prepare($query);
                            $stmt->execute([
                                ":id" => get_user_id(),
                                ":password" => password_hash($new_password, PASSWORD_BCRYPT)
                            ]);

                            flash("Password reset", "success");
                        } else {
                            flash("Current password is invalid", "warning");
                        }
                    }
                } catch (Exception $e) {
                    echo "<pre>" . var_export($e->errorInfo, true) . "</pre>";
                }
            } else {
                flash("New passwords don't match", "warning");
            }
        }
    }
    ?>

    <?php
    $email = get_user_email();
    $username = get_username();
    ?>
    <form method="POST" onsubmit="return validate(this);">
    
        <div class="mb-3">
            <br>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php se($email); ?>" />
            
        </div>
        <div class="mb-3">
            <label for="username">Username </label>
            
            <input type="username" name="username" id="username" value="<?php se($username); ?>" />
        </div>
        <!-- DO NOT PRELOAD PASSWORD -->
        <br>
        <div><h3>Password Reset</div>
        <div class="mb-3">
            <label for="cp">Current Password</label>
            <input type="password" name="currentPassword" id="cp" />
        </div>
        <div class="mb-3">
            <label for="np">New Password</label>
            <input type="password" name="newPassword" id="np" />
        </div>
        <div class="mb-3">
            <label for="conp">Confirm Password</label>
            <input type="password" name="confirmPassword" id="conp" />
        </div>
        <input type="submit" value="Update Profile" name="save" />
    </form>

    <script>
        function validate(form) {
            let pw = form.newPassword.value;
            let con = form.confirmPassword.value;
            let isValid = true;
            //TODO add other client side validation....

            //example of using flash via javascript
            //find the flash container, create a new element, appendChild
            if (pw !== con) {
                //find the container
                let flash = document.getElementById("flash");
                //create a div (or whatever wrapper we want)
                let outerDiv = document.createElement("div");
                outerDiv.className = "row justify-content-center";
                let innerDiv = document.createElement("div");

                //apply the CSS (these are bootstrap classes which we'll learn later)
                innerDiv.className = "alert alert-warning";
                //set the content
                innerDiv.innerText = "Password and Confirm password must match";

                outerDiv.appendChild(innerDiv);
                //add the element to the DOM (if we don't it merely exists in memory)
                flash.appendChild(outerDiv);
                isValid = false;
            }
            return isValid;
        }
    </script>
    <?php
    require_once(__DIR__ . "/partials/flash.php");
    ?>
</div>
