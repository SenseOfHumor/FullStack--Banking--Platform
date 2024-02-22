<?php
session_start();
//Note: this is to resolve cookie issues with port numbers
$domain = $_SERVER["HTTP_HOST"];
if (strpos($domain, ":")) {
    $domain = explode(":", $domain)[0];
}
$localWorks = true; //some people have issues with localhost for the cookie params
//if you're one of those people make this false

//this is an extra condition added to "resolve" the localhost issue for the session cookie
// if (($localWorks && $domain == "localhost") || $domain != "localhost") {
//     session_set_cookie_params([
//         "lifetime" => 60 * 60,
//         "path" => "/Project",
//         //"domain" => $_SERVER["HTTP_HOST"] || "localhost",
//         "domain" => $domain,
//         "secure" => true,
//         "httponly" => true,
//         "samesite" => "lax"
//     ]);
// }
require_once(__DIR__ . "/../lib/functions.php");



?>

<!DOCTYPE html>
<html>
<head>
<style>
nav ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
  overflow: hidden;
  background-color: #333;
}

nav li {
  float: left;
}

nav li a {
  display: block;
  color: white;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
}

nav li a:hover {
  background-color: #00C5FF;
}
</style>
</head>
<body>

<!-- <ul>
  <li><a class="active" href="#home">Home</a></li>
  <li><a href="#news">News</a></li>
  <li><a href="#contact">Contact</a></li>
  <li><a href="#about">About</a></li>
</ul> -->

</body>
</html>
<nav>
    <ul>
        <?php if (is_logged_in()) : ?>
            <li><a href="home.php">Home</a></li>
            <li><a href="profile.php">Profile</a></li>
        <?php endif; ?>
        <?php if (!is_logged_in()) : ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php endif; ?>
        <?php if (is_logged_in()) : ?>
            <li><a href="logout.php">Logout</a></li>
        <?php endif; ?>
    </ul>
</nav>


