<?php
session_start();
require_once("/home/retonos/public_html/connect.php");

if(!isset($_SESSION['webID'])){
    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/index.html");
    exit;
}

$webId = $_SESSION['webID'];

$conn = DataBase::connectDB();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$query = "SELECT * FROM Users WHERE webId = :webId";
$statement = $conn->prepare($query);
$statement->execute(['webId' => $webId]);
$row = $statement->fetch(PDO::FETCH_ASSOC);
if($row['isBlacklisted'] == 1){
    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/Authenticate/blacklistedPage.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="stylesAuth.css" rel="stylesheet">
</head>

<body>
    <?php
    //echo "This is " .$webId. "'s page";
    ?>
    <nav class="mt-3">
        <ul id="left-items">
            <li><a class="btn fs-5 site-options" href="signedInHomepage.html">Rebel Reviewer</a></li>
            <li><a class="btn fs-5 site-options" href="howItWorksSignedIn.html">How does it work?</a></li>
            <li><a class="btn fs-5 site-options" href="contactSignedIn.html">Contact</a></li>
        </ul>
        <ul id="choices">
            <li><a class="btn btn-lg business-options" href="signedInRestaurants.php">Restaurants</a></li>
            <li><a class="btn btn-lg business-options" href="signedInBars.php">Bars</a></li>
            <li><a class="btn btn-lg business-options" href="signedInCoffeeshops.php">Coffeeshops</a></li>
        </ul>
        <ul id="right-items">
        <li><a class="btn btn-lg account-action" href="myFavorites.php">My Favorite Orders</a></li>
            <li><a class="btn btn-lg account-action" href="logout.php">Sign Out</a></li>
        </ul>
    </nav>
    <div>
    <table id="unapproved-reviews" class='table justify-content-center align-items-center table-bordered mt-5'>
        <tr>
    <td>Business Name</td>
    <td>Rating</td>
    <td>Date Submitted</td>
    <td>Review Text</td>
    <td>Status</td>
</tr>

        <?php
        try{
            $query = 'SELECT reviews.*, businessData.businessName
            FROM reviews
            JOIN businessData ON reviews.businessId = businessData.businessId WHERE
            reviews.webId = ?
            ORDER BY reviews.date_submitted DESC';
        $statement = $conn->prepare($query);
        $statement->bindParam(1,$webId);
        $statement->execute();
        } catch(PDOException $e){
            echo $e->getMessage();
        }
        while ($row = $statement->fetch()) {
            echo " <tr>
            <td>" . $row['businessName'] . "</td>
            <td >" . $row['rating'] . "</td>
            <td>" . $row['date_submitted'] . "</td>
            <td>" . htmlspecialchars($row['reviewText'], ENT_QUOTES) . "</td>";
           if($row['approved'] == 1){
            echo "<td id='live'>Approved!</td>";
           }
           else{
            echo "<td id='pending'>Pending</td>";
           }
            echo "
            </tr>
            ";
        }

        ?>
        </table>

    </div>
    
    </table>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>