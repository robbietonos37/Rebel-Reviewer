<?php
session_start(); 

if (isset($_SESSION['webID'])) {

    session_unset();

    session_destroy();

    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/index.html");
    exit(); 
} else {
    echo "You are not logged in.";
}
?>