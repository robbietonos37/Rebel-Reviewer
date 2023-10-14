<?php
$webId = json_encode($_SERVER["uid"]);
session_start();
require_once("/home/retonos/public_html/connect.php");

$conn = Database::connectDB();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$query = "SELECT * FROM Users WHERE webId = :webId";
$statement = $conn->prepare($query);
$statement->execute(['webId' => $webId]);
if($statement->rowCount() > 0){
    echo 'Robbie EXISTS!!' ;
}
else {
    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/index.html");
    exit;
}

echo '<script>';
echo 'console.log(' . json_encode($_SERVER) . ' is the authenticated webid)';
echo '</script>';
print_r($_SERVER["uid"]);
print_r($webId);


?>

<?php
session_start();
require_once("/home/retonos/public_html/connect.php");

$conn = Database::connectDB();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$query = "SELECT * FROM users WHERE webId = '$webId'";
$statement = $conn->execute($query);
if($statement){
    echo 'Robbie EXISTS!!';
}
else {
    echo "WE HAVE A PHONEY";
}
?>

<?php

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
