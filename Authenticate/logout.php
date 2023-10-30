<?php
session_start(); // Start the session

if (isset($_SESSION['webID'])) {

    session_unset();

    session_destroy();

    header("Location: index.html");
    exit(); 
} else {
    echo "You are not logged in.";
}
?>