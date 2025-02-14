<?php
session_start();

// Destroy session
session_unset();
session_destroy();

// Remove "remember_user" and "user_id" cookies
setcookie("remember_user", "", time() - 3600, "/");
setcookie("user_id", "", time() - 3600, "/");

// Redirect to login page
header("Location: login.php");
exit();
?>