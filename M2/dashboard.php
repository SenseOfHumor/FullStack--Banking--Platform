<?php
require(__DIR__ . "/partials/nav.php");
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;900&display=swap');

    body {
        background: url('bg1.jpg') no-repeat center center fixed; 
        margin: 0;
        width: 100vw;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Poppins', sans-serif;
    }

    .container {
        width: 350px;
        padding: 40px;
        background: #ecf0f3;
        border-radius: 20px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .brand-title {
        margin-bottom: 20px;
        font-weight: 900;
        font-size: 1.8rem;
        color: #363636;
        letter-spacing: 1px;
    }

    button {
        margin-top: 20px;
        padding: 10px;
        width: 100%;
        background-color: #1DA1F2;
        border: none;
        border-radius: 20px;
        color: #fff;
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #0d8ae5;
    }

    .footer {
        margin-top: 20px;
        font-size: 0.9rem;
        color: #666;
    }
</style>

<div class="container">
    <div class="brand-title">DASHBOARD</div>

    <button onclick="window.location.href='accounts.php'">ACCOUNTS</button>
    <button onclick="window.location.href='account_create.php'">CREATE ACCOUNT</button>
    <button onclick="window.location.href='withdraw_deposit.php'">WITHDRAW/DEPOSIT</button>
    <div class="footer">
        <p>Want to Logout? <a href="login.php">Register</a></p>
    </div>
</div>

<?php
if (isset($_GET['message'])) {
    echo "<span class='white-text'>" . $_GET['message'] . "</span>";
}

//TODO 2: add PHP Code
if (isset($_POST["email"]) && isset($_POST["password"])) {
    // PHP validation and authentication logic
}
?>
