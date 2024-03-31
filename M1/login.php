<?php
require(__DIR__ . "/partials/nav.php");

?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;900&display=swap');

    input {
    caret-color: red;
    }

    body {
        background: url('bg1.jpg') no-repeat center center fixed; 
        margin: 0;
        width: 100vw;
        height: 100vh;
       
        display: flex;
        align-items: center;
        text-align: center;
        justify-content: center;
        place-items: center;
        overflow: hidden;
        font-family: poppins;
    }
    .container {
        position: relative;
        width: 350px;
        height: 500px;
        border-radius: 20px;
        padding: 40px;
        box-sizing: border-box;
        background: #ecf0f3;
        /* box-shadow: 14px 14px 20px #cbced1, -14px -14px 20px white; */
    }

    .brand-title {
        margin-top: 10px;
        font-weight: 900;
        font-size: 1.8rem;
        color: #363636;
        letter-spacing: 1px;
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

    button {
        color: white;
        margin-top: 20px;
        background: #1DA1F2;
        height: 40px;
        border-radius: 20px;
        cursor: pointer;
        font-weight: 900;
        box-shadow: 6px 6px 6px #cbced1, -6px -6px 6px white;
        transition: 0.5s;
    }

        button:hover {
        box-shadow: none;
    }

    .white-text {
        color: #ffffff;
    }
</style>
<div class="container">
    <div class="brand-logo"></div>
    <div class="brand-title">LOGIN</div>
    <form class="inputs" onsubmit="return validate(this)" method="POST">
        <label>EMAIL</label>
        <input type="email" name="email" placeholder="email address here" required />
        <label>PASSWORD</label>
        <input type="password" name="password" placeholder="password here" required minlength="6" />
        <button type="submit">LOGIN</button>
        <!-- <button type="button" onclick="window.location.href='register.php'">REGISTER</button> -->
    </form>
    <p>Don't have an account? <a href="register.php">Register</a></p>
</div>
<script>
    function validate(form) {
        //TODO 1: implement JavaScript validation
        //ensure it returns false for an error and true for success

        return true;
    }
</script>
<?php

if (isset($_GET['message'])) {
    echo "<span class='white-text'>" . $_GET['message'] . "</span>";
}
//TODO 2: add PHP Code
if (isset($_POST["email"]) && isset($_POST["password"])) {
    $email = se($_POST, "email", "", false);
    $password = se($_POST, "password", "", false);

    //TODO 3
    $hasError = false;
    if (empty($email)) {
        echo "Email must not be empty";
        $hasError = true;
    }
    //sanitize
    $email = sanitize_email($email);
    //validate
    if (!is_valid_email($email)) {
        echo "Invalid email address";
        $hasError = true;
    }
    if (empty($password)) {
        echo "password must not be empty";
        $hasError = true;
    }
    if (strlen($password) < 8) {
        echo "Password too short";
        $hasError = true;
    }
    if (!$hasError) {
        //TODO 4
        $db = getDB();
        $stmt = $db->prepare("SELECT id, email, password from Users where email = :email");
        try {
            $r = $stmt->execute([":email" => $email]);
            if ($r) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    $hash = $user["password"];
                    unset($user["password"]);
                    if (password_verify($password, $hash)) {
                        echo "Weclome $email";
                        $_SESSION["user"] = $user;
                        try {
                            //lookup potential roles
                            $stmt = $db->prepare("SELECT Roles.name FROM Roles 
                        JOIN UserRoles on Roles.id = UserRoles.role_id 
                        where UserRoles.user_id = :user_id and Roles.is_active = 1 and UserRoles.is_active = 1");
                            $stmt->execute([":user_id" => $user["id"]]);
                            $roles = $stmt->fetchAll(PDO::FETCH_ASSOC); //fetch all since we'll want multiple
                        } catch (Exception $e) {
                            error_log(var_export($e, true));
                        }
                        //save roles or empty array
                        if (isset($roles)) {
                            $_SESSION["user"]["roles"] = $roles; //at least 1 role
                        } else {
                            $_SESSION["user"]["roles"] = []; //no roles
                        }
                        die(header("Location: home.php"));
                    } else {
                        echo "<span class='white-text'>invalid password</span>";
                    }
                } else {
                    echo "<span class='white-text'>mail not found</span>";
                }
            }
        } catch (Exception $e) {
            echo "<pre>" . var_export($e, true) . "</pre>";
        }
    }

    
}


?>