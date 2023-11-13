<?php
session_start(); // Start the session

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: index.php");
exit(); // Ensure that no further code is executed after the redirect
?>
