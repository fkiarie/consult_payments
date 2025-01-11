<?php session_start();
// auth.php

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    // Redirect to login page if not authenticated
    header("Location: login.php");
    exit();
}

// Check if the username is set in the session
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = 'Guest'; // Optional fallback
}
?>
