<?php
session_start();
require_once("/home/retonos/public_html/connect.php");
$businessId = $_GET['businessId'];

$conn = DataBase::connectDB();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="posting.css" rel="stylesheet">
</head>

<body>
    <nav class="mt-3">
        <ul id="left-items">
            <li><a class="btn fs-5 site-options" href="index.html">Rebel Reviewer</a></li>
            <li><a class="btn fs-5 site-options" href="howItWorks.html">How does it work?</a></li>
            <li><a class="btn fs-5 site-options" href="something.html">Contact</a></li>
        </ul>
        <ul id="choices">
            <li><a class="btn btn-lg business-options" href="restaurants.php">Restaurants</a></li>
            <li><a class="btn btn-lg business-options" href="bars.php">Bars</a></li>
            <li><a class="btn btn-lg business-options" href="coffeeshops.php">Coffeeshops</a></li>
        </ul>
        <ul id="right-items">
            <li><a class="btn fs-5 account-action" href="https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/Authenticate/">Log In</a></li>
        </ul>
    </nav>
    <div>

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
        <div class='d-flex align-items-center flex-column justify-content-center gap-2 mb-3'>
        <h3 class='text-center mb-3'>" . $row['businessName'] . "</h3>
        <span>Address: " . $row['address'] . "</span>
        </div>
        ";
        }

        ?>

    </div>
    <h2 class='text-center'>Reviews</h2>
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
    <div id='review' class='d-flex align-items-center flex-column justify-content-center'>
    <h3 class='text-center mb-3'>Rating Value " . $row['rating'] . "</h3>
    <span>Date: " . $row['date_submitted'] . "</span>
    <a href='userReviewNotSignedIn.php?webId={$row['webId']}'>User: " . $row['webId'] . "</a>
    <p>Review: " . $row['reviewText'] . "</p>

    </div>

    ";
    }

        ?>

    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>