<?php
session_start();

require_once("/home/");
if (isset($_POST["username"]) && isset($_POST["password"]) && $_POST["username"] !== "" && $_POST["password"] !== "") {
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}

$username = validate($_POST['username']);

try {
    // query database to see if user has an account
    $query1 = 'select roleID from Users where webID = ?';
    $stmt1 = $conn->prepare($query1);
    $stmt1->execute([$username]);
    $row1 = $stmt1->fetch(PDO::FETCH_ASSOC);

    $query = 'select webID, password from Login where webID = ?';
    $statement = $conn->prepare($query);
    $statement->execute([$username]);
    $row = $statement->fetch(PDO::FETCH_ASSOC);

    // if user is found in the database then give entry into rest of the web app.
    // otherwise redirect them back to the landing/login page
    if ($row["webID"] === $username && password_verify($password, $row["password"])) {
        if ($row1["roleID"] !== 4) {
            $_SESSION["user"] = $username;
            redirect("https://turing.cs.olemiss.edu/~group1/PHP/landingpage.php");
        } else {
            $_SESSION["admin"] = $username;
            redirect("https://turing.cs.olemiss.edu/~group1/PHP/admin.php");
        }
    } else {
        redirect("https://turing.cs.olemiss.edu/~group1/HTML/error.html");
        // echo "<script src='https://turing.cs.olemiss.edu/~group1/JS/index.js'></script>";

    }

    Database::dbDisconnect();
} catch (PDOException $e) {
    echo $e->getMessage();
}

// $conn = Database::dbConnect();
// $conn->setAttribute(PDO::ATR_ERRMode, PDO::ERRMODE_EXCEPTION);
