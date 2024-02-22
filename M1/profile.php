<?php
require(__DIR__ . "/partials/nav.php");
?>

<h1>PROFILE MOTHERFUCKERS🚀</h1>
<?php
if (is_logged_in()) {
    //echo "Welcome, " . get_user_email();
    echo $_SESSION ['user']['email']; 
} else {
    echo "You're not logged in";
}
?>

