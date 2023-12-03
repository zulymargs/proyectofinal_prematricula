<?php
$titulo = "Pre-Matrícula UPRA";
session_start(); // Start the session
// Check if the user is logged in
if (isset($_SESSION['admID'])) {
    echo "<p>Welcome, Student ID: {$_SESSION['admID']} | <a href='../logout.php'>Logout</a></p>";
} else {
    echo "<p>Session not active <a href='login.php'>Login</a></p>";
}