<?php
session_start();

try {

    $query1 = 'select webId from Users where webID = ?';
    $stmt1 = $conn->prepare($query1);
    $stmt1->execute([$username]);
    $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);

    if ($row["webID"] === $username) {
        if ($row1["roleID"] !== 1) {
            $_SESSION["user"] = $username;
            redirect("https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/landingpage.php");
        } else {
            $_SESSION["admin"] = $username;
            redirect("https://turing.cs.olemiss.edu/~group1/PHP/admin.php");
        }
    } else {
        redirect("https://turing.cs.olemiss.edu/~retonos/HTML/error.html");
    }

    Database::dbDisconnect();
} catch (PDOException $e) {
    echo $e->getMessage();
}
