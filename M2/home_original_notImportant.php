<?php
require(__DIR__ . "/partials/nav.php");
?>
<style>
    body {
        background-color: #f0f0f0;
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }
    .content-box {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
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
</style>
<div class="content-box">
    <h1>Home</h1>
    <?php
    if (is_logged_in()) {
        echo "<p class='welcome-message'>Welcome, " . get_user_email() . "</p>";
    } else {
        echo "<p class='not-logged-in-message'>You're not logged in</p>";
    }

    //shows session info
    echo "<pre class='session-info'>" . var_export($_SESSION, true) . "</pre>";
    ?>
</div>