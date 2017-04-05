<?php
    session_start(); // Start the session
    unset($_SESSION['user_name']);
    session_unset();
    session_destroy(); // Destroy the session
    header('Location: login'); // Go to login page
    exit();
?>
