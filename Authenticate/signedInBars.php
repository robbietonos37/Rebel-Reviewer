<?php
session_start();
require_once("/home/retonos/public_html/connect.php");

$webId = $_SESSION['webID'];
if(!isset($webId)){
    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/index.html");
}
$conn = Database::connectDB();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
    echo "This is " .$webId;
    ?>


    <nav class="mt-3">
        <ul id="left-items">
            <li><a class="btn fs-5 site-options" href="signedInHomepage.html">Rebel Reviewer</a></li>
            <li><a class="btn fs-5 site-options" href="howItWorksSignedIn.html">How does it work?</a></li>
            <li><a class="btn fs-5 site-options" href="contactSignedIn.html">Contact</a></li>
        </ul>
        <ul id="right-items">
            <li><a class="btn btn-lg business-options" href="signedInRestaurants.php">Restaurants</a></li>
            <li><a class="btn btn-lg business-options" href="signedInCoffeeshops.php">Coffeeshops</a></li>
            <li><a class="btn btn-lg account-action" href="logout.php">Sign Out</a></li>
            <?php if($webId === 'retonos'){
                echo "
                <li><a class='btn btn-lg account-action' href='admin.php'>Admin Page</a></li>
                ";
            }
            ?>
        </ul>

    </nav>

    <h3 class='text-center'>Bars In Oxford</h3>


    <div id="all-restaurants">
        <?php
        try {
            $query = 'SELECT * FROM businessData AS bd LEFT JOIN businessTypes AS bt ON bt.businessId = bd.businessId WHERE bt.type = "Bar" ORDER BY businessName';
            $stmt = $conn->query($query);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $businessId = $row['businessId'];
            try {
                $cuisineQuery = 'SELECT cs.cuisineDesc
                FROM Cuisine AS cs
                LEFT JOIN businessCuisines AS bc ON cs.cuisineId = bc.cuisineId
                WHERE bc.businessId = ?';
                $cuisineStatement = $conn->prepare($cuisineQuery); 
                $cuisineStatement->bindParam(1,$businessId);
                $cuisineStatement->execute();
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
            while($cuisineRow = $cuisineStatement->fetch()){

            if($row['url']  !== ''){
            echo "
            <div class='mb-3 mt-3 apt border border-secondary d-flex align-items-center flex-column justify-content-center gap-2 business-posting'>
            <h3 class='mt-3'> <a href="  . $row['url'] . " target='_blank'>" . $row['businessName'] . "</a></h3>
            <span>Address: " . $row['address'] . "</span>
            <span class='text-center'><a href="  . $row['url'] . " target='_blank'>Website</a></span>
            <span> " . $row['overallRating'] . "</span>
            <span>Primary Cuisine: " . $cuisineRow['cuisineDesc'] . "</span>
            <a href='business_info_signedIn.php?businessId={$businessId}' class='btn btn-primary view-reviews mb-3'>View Business Reviews</a>
        </div>";
            } else {
                echo "
                <div class='mb-3 mt-3 apt border border-secondary d-flex align-items-center flex-column justify-content-center gap-2 business-posting'>
                <h3>" . $row['businessName'] . "</h3>
                <span>Address: " . $row['address'] . "</span>
                <span>Overall Rating: " . $row['overallRating'] . "</span>
                <span>Primary Cuisine: " . $cuisineRow['cuisineDesc'] . "</span>
                <a href='business_info_signedIn.php?businessId={$businessId}' class='btn btn-primary view-reviews mb-3'>View Business Reviews</a>
            </div>"; 
            }
        }
        }
        ?>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>