<?php
session_start();
require_once("/home/retonos/public_html/connect.php");
$businessId = $_GET['businessId'];
$webId = $_SESSION['webID'];
if(!isset($webId)){
    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/index.html");
}

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
    //echo "This is " .$webId;
    ?>
    <nav class="mt-3">
        <ul id="left-items">
        <li><a class="btn fs-5 site-options" href="signedInHomepage.html">Rebel Reviewer</a></li>
            <li><a class="btn fs-5 site-options" href="howItWorksSignedIn.html">How does it work?</a></li>
            <li><a class="btn fs-5 site-options" href="contactSignedIn.html">Contact</a></li>
        </ul>

        
        <ul id="right-items">
        <li><a class="btn btn-lg business-options" href="signedInCoffeeshops.php">Coffeeshops</a></li>
        <li><a class="btn btn-lg business-options" href="signedInBars.php">Bars</a></li>
        <li><a class="btn btn-lg business-options" href="signedInRestaurants.php">Restaurants</a></li>
        <li><a class="btn btn-lg account-action" href="myReviews.php">My Favorite Orders</a></li>
        <li><a class="btn btn-lg account-action" href="myReviews.php">My Reviews</a></li>
        <li><a class="btn btn-lg account-action" href="logout.php">Sign Out</a></li>
        </ul>
    </nav>
    <div class='d-flex align-items-center flex-column justify-content-center'>

        <?php
        try{
            $query = "SELECT * FROM businessData WHERE businessId = ?";
        $statement = $conn->prepare($query);
        $statement->bindParam(1,$businessId);
        $statement->execute();
        } catch(PDOException $e){
            echo $e->getMessage();
        }
        while ($row = $statement->fetch()) {
            echo "
        <div class='d-flex align-items-center flex-column justify-content-center gap-2 mb-1'>
        <h3 class='text-center mb-3'>" . $row['businessName'] . "</h3>
        <span>Address: " . $row['address'] . "</span>

        <a href='createFavorite.php?businessId={$businessId}' class='btn btn-md' id='to-createFavorite'>Create Favorite Order</a>
        </div>

        ";
        }

        ?>
    </div>

    <div class='d-flex justify-content-center'>
    <?php
    //echo "<span>this is " .$webId. "and"  .$businessId "</span>";
    try{
        $query = "SELECT * FROM reviews WHERE webId = ? AND businessId = ?";
        $statement = $conn->prepare($query);
        $statement->bindParam(1,$webId);
        $statement->bindParam(2,$businessId);
        $statement->execute();
        } catch(PDOException $e){
            echo $e->getMessage();
        }
        if($statement->rowCount() == 0){
            echo "<a href='createReview.php?businessId={$businessId}' id='leave-review' class='btn btn-md mt-3 mb-3'>Review Me</a>";
        }

        ?>

    </div>

    <h2 class='text-center mb-3'>Reviews</h2>
    <div id="reviews" class='d-flex align-items-center justify-content-center'>
        <?php
    try{
        $query = 'SELECT * FROM reviews WHERE businessId = ? AND approved = 1 ORDER BY date_submitted DESC';
        $statement = $conn->prepare($query);
        $statement->bindParam(1,$businessId);
        $statement->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
    }
    while ($row = $statement->fetch()) {
        echo "
    <div id='review-box' class='d-flex align-items-center flex-column justify-content-center'>
    <h3 class='text-center mb-3 mt-1'>Rating Value " . $row['rating'] . "</h3>
    <span>Date: " . $row['date_submitted'] . "</span>
    <a href='userReviews.php?webId={$row['webId']}'>User: " . $row['webId'] . "</a>
    <p class='text-center p-3'>" . $row['reviewText'] . "</p>

    </div>

    ";
    }

        ?>

    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>