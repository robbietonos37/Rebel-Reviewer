<?php
// returns webId from the Microsoft authentication
$webId = json_encode($_SERVER["uid"]);
$webId = str_replace("\"", "", $webId);
session_start();
// initializes session variable to that webId
$_SESSION['webID'] = $webId;
require_once("/home/retonos/public_html/connect.php");

$conn = Database::connectDB();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// queries to see if user exists
$query = "SELECT * FROM Users WHERE webId = :webId";
$statement = $conn->prepare($query);
$statement->execute(['webId' => $webId]);
$row = $statement->fetch(PDO::FETCH_ASSOC);
// checks to see if user is the admin
if($row && $row['isAdmin'] == 1){
    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/Authenticate/admin.php");
    exit;
}
// checks to see if user is blacklisted and redirects accordingly
if($row && $row['isBlacklisted'] == 1){
    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/Authenticate/blacklistedPage.php");
    exit;
}
// since user is not admin or blacklisted, checks to see if it even exists
else if($statement->rowCount() > 0){
    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/Authenticate/signedInHomepage.html");    
    exit;
}
// redirects to signup since no user has that webId
else {
    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/signUp.php");
    exit;
}

echo '<script>';
echo 'console.log(' . json_encode($_SERVER) . ' is the authenticated webid)';
echo '</script>';
print_r($_SERVER["uid"]);
print_r($webId);


?>

