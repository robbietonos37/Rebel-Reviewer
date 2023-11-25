<?php
session_start();
require_once("/home/retonos/public_html/connect.php");

if(!isset($_SESSION['webID'])){
    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/index.html");
    exit;
}

$webId = $_SESSION['webID'];

if($webId !== 'retonos'){
    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/Authenticate/signedInHomepage.html");
    exit;
}

$conn = Database::connectDB();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="stylesAuth.css" rel="stylesheet" >
    <title>Document</title>
</head>

<body class="bg-light">
<?php

//var_dump($_POST);

if (isset($_POST['deny'])) {
    
    $webIdToBlacklist = $_POST['webId'];

    try{
    $query = 'UPDATE Users SET isBlacklisted = 1 WHERE webId = ?';
    $statement = $conn->prepare($query);
    $statement->bindParam(1,$webIdToBlacklist);
    $result = $statement->execute();
    // if($result){
    //     header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/Authenticate/signedInBars.php");
    // }
    // else {
    //     header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/Authenticate/signedInRestaurants.php");
    // }
    } catch(PDOException $e){
        echo $e->getMessage();
    }

}
if (isset($_POST['deny-undo'])) {
    
    $webIdToBlacklist = $_POST['webId'];

    try{
    $query = 'UPDATE Users SET isBlacklisted = 0 WHERE webId = ?';
    $statement = $conn->prepare($query);
    $statement->bindParam(1,$webIdToBlacklist);
    $result = $statement->execute();
    // if($result){
    //     header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/Authenticate/signedInBars.php");
    // }
    // else {
    //     header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/Authenticate/signedInRestaurants.php");
    // }
    } catch(PDOException $e){
        echo $e->getMessage();
    }

}
?>
    <nav class="mt-3">
        <ul id="choices">
            <li><a class="btn btn-lg business-options" href="signedInRestaurants.php">Restaurants</a></li>
            <li><a class="btn btn-lg business-options" href="signedInBars.php">Bars</a></li>
            <li><a class="btn btn-lg business-options" href="signedInCoffeeshops.php">Coffeeshops</a></li>

            <li><a class="btn btn-lg account-action" href="admin.php">Admin Page</a></li>
            <li><a class="btn btn-lg account-action" href="allAcounts.php">All Accounts</a></li>
            <li><a class="btn btn-lg account-action" href="addBusiness.php">Add Business</a></li>
            <li><a class="btn btn-lg account-action" href="allBusinesses.php">All Businesses</a></li>
            <li><a class="btn btn-lg account-action" href="allApprovedReviews.php">All Approved Reviews</a></li>
            <li><a class="btn btn-lg account-action" href="logout.php">Sign Out</a></li>

        </ul>
    </nav>

    <h2 class='text-center'>All Accounts</h2>

    <table id="unapproved-reviews" class='table justify-content-center align-items-center table-bordered mb-5 mt-5'>
        <tr>
    <td>WebId</td>
    <td>First Name</td>
    <td>Last Name</td>
    <td>Email</td>
    <td>Blacklist</td>
    <td>Undo Blacklist</td>
</tr>
    <?php
    try {
            $query = 'SELECT * FROM Users WHERE isAdmin = 0';
            $stmt = $conn->query($query);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "
            <form method='post' action='allAcounts.php'>
            <tr>
            <td>" . $row['webId'] . "</td>
            <td>" . $row['firstName'] . "</td>
            <td>" . $row['lastName'] . "</td>
            <td>" . $row['email'] . "</td>
            <input type='hidden' name='webId' value=" . $row['webId'] . ">";
            if($row['isBlacklisted'] === 0){
                echo "<td><button name='deny' class='btn btn-md deny' type='submit'>Blacklist</button></td>
                <td></td>";
            }
            else{
                echo "<td></td><td><button name='deny-undo' class='btn btn-md deny-undo' type='submit'>Undo Blacklist</button></td>";
            }
            echo "
        </tr>
        </form>";
        }

    ?>
    </table>

    

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>

</body>

</html>

<script>
    const denyButtons = document.getElementsByClassName('deny');
    const denyButtonsArray = Array.from(denyButtons);
    denyButtonsArray.forEach((button) => button.addEventListener('click', (e) => {
        if(!confirm("Are you SURE you want to blacklist this user?")){
            e.preventDefault();
        }
    }))

    const undoBlacklistButtons = document.getElementsByClassName('deny-undo');
    const undoBlacklistButtonsArray = Array.from(undoBlacklistButtons);
    undoBlacklistButtonsArray.forEach((button) => button.addEventListener('click', (e) => {
        if(!confirm("Are you SURE you want to undo the blacklist for this user?")){
            e.preventDefault();
        }
    }))
    

</script>