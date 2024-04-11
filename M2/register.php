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
        height: 700px;
        border-radius: 20px;
        padding: 40px;
        box-sizing: border-box;
        background: #ecf0f3;
        /* box-shadow: 14px 14px 20px #cbced1, -14px -14px 20px white; */
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

    .brand-title {
        margin-top: 10px;
        font-weight: 900;
        font-size: 1.8rem;
        color: #363636;
        letter-spacing: 1px;
    }

    .white-text {
        color: #ffffff;
    }
</style>
<div class="container">
    <div class="brand-logo"></div>
    <div class="brand-title">REGISTER</div>
    <form class="inputs" onsubmit="return validate(this)" method="POST">
        <label>EMAIL</label>
        <input type="email" name="email" placeholder="email address here" required />
        <label>USERNAME</label>
        <input type="text" name="username" placeholder="username here" required />
        <label>PASSWORD</label>
        <input type="password" name="password" placeholder="password here" required minlength="8" />
        <label>CONFIRM PASSWORD</label>
        <input type="password" name="confirm" placeholder="confirm password here" required minlength="8" />
        <button type="submit">REGISTER</button>
        <!-- <button type="button" onclick="window.location.href='login.php'">LOGIN</button> -->
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
</div>
<script>
    function validate(form) {
        //TODO 1: implement JavaScript validation
        //ensure it returns false for an error and true for success

        return true;
    }
</script>
<?php
//TODO 2: add PHP Code
if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm"])) {
    $email = se($_POST, "email", "", false);
    $username = se($_POST, "username", "", false);
    $password = se($_POST, "password", "", false);
    $confirm = se(
        $_POST,
        "confirm",
        "",
        false
    );
    // Check if the username or email already exists in the database
    $db = getDB();
    $stmt = $db->prepare("SELECT COUNT(*) FROM Users WHERE email = :email OR username = :username");
    $stmt->execute([":email" => $email, ":username" => $username]);
    $count = $stmt->fetchColumn();
    //TODO 3
        // If the count is greater than 0, it means a duplicate exists
        if ($count > 0) {
            echo "<span class='white-text'>The chosen username or email is already taken.</span>";
            $hasError = true;
        } else {
            //TODO 3
            $hasError = false;
            if (empty($email)) {
                echo "<span class='white-text'>Email must not be empty</span>";
                $hasError = true;
            }
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
    if (empty($confirm)) {
        echo "Confirm password must not be empty";
        $hasError = true;
    }
    if (strlen($password) < 8) {
        echo "Password too short";
        $hasError = true;
    }
    if (
        strlen($password) > 0 && $password !== $confirm
    ) {
        echo "<span class='white-text'>Passwords must match</span>";
        $hasError = true;
        
    }
    
    if (!$hasError) {
        echo "Welcome, $email";
        //TODO 4
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Users (email, password, username) VALUES(:email, :password, :username)");
        try {
            $stmt->execute([":email" => $email, ":password" => $hash, ":username" => $username]);
            echo "Successfully registered!";
        } catch (Exception $e) {
            echo "There was a problem registering";
            "<pre>" . var_export($e, true) . "</pre>";
        }
    }
}}
?>