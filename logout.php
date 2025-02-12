<?php
session_start();

// Clear the session
session_unset();
session_destroy();

// Delete the remember_user cookie
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, "/");
}

// Redirect to login page
header("Location: login.php");
exit();
?>