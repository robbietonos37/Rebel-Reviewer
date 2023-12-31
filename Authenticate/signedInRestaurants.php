<?php
session_start();
require_once("/home/retonos/public_html/connect.php");

if(!isset($_SESSION['webID'])){
    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/index.html");
    exit;
}

$webId = $_SESSION['webID'];


$conn = Database::connectDB();
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
    <title>Restaurants</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="stylesAuth.css" rel="stylesheet">
</head>

<body>


    <nav class="mt-3">
        <ul id="left-items">
            <li><a class="btn fs-5 site-options" href="signedInHomepage.html">Rebel Reviewer</a></li>
            <li><a class="btn fs-5 site-options" href="howItWorksSignedIn.html">How does it work?</a></li>
            <li><a class="btn fs-5 site-options" href="contactSignedIn.html">Contact</a></li>
        </ul>
        <ul id="right-items">
            <li><a class="btn btn-lg business-options" href="signedInCoffeeshops.php">Coffeeshops</a></li>
            <li><a class="btn btn-lg business-options" href="signedInBars.php">Bars</a></li>
            <li><a class="btn btn-lg account-action" href="myReviews.php">My Reviews</a></li>
            <li><a class="btn btn-lg account-action" href="myFavorites.php">My Favorite Orders</a></li>
            <li><a class="btn btn-lg account-action" href="logout.php">Sign Out</a></li>
            <?php if($webId === 'retonos'){
                echo "
                <li><a class='btn btn-lg account-action' href='admin.php'>Admin Page</a></li>
                ";
            }
            ?>
        </ul>
    </nav>

    <div id="filter-box" class='card-body'>
        <div class='row d-flex justify-content-center' id='filter-section'>
            <div class='col-md-7'>
                <form action="signedInRestaurants.php" method="POST">
                    <div class='input-group d-flex mb-3'>
                        <div id='search-box' class='d-flex flex-row'>
                            <input type='text' name='restaurantName' class='form-control' placeholder='Search Restaurants'>
                            <button type='submit' class='btn btn-lg' name='search' id='searcher'>Search</button>
                        </div>
                        <div id="myDropdown" class="dropdown-content mt-3">
                            <label>Select a Cuisine</label>
                            <select name="cuisineId">
                                <option value="None" selected>
                                    None Selected
                                </option>
                                <?php
                                // dropdown used to filter by cuisine
                                    try{
                                    $query = 'SELECT * FROM Cuisine';
                                    $statement = $conn->query($query);
                                    } catch (PDOException $e) {
                                    echo $e->getMessage();
                                    }
                                    while($row = $statement->fetch(PDO::FETCH_ASSOC)){
                                        echo "<option value=" . $row['cuisineId'] . ">"
                                        . $row['cuisineDesc'] . 
                                        "</option>";
                                    }
                            ?>
                            </select>
                            <button type='submit' class='btn site-options btn-lg m-1' name="cuisine-search">Filter</button>
                        </div>
                    </div>    
                <form>
            </div>
        </div>
    </div>

    <h3 class='text-center'>Restaurants In Oxford</h3>

    <div id="all-restaurants" class='mb-5'>
        <?php
        // queries for all restaurants
        if((!isset($_POST['search']) And !isset($_POST['cuisine-search'])) Or ($_POST['restaurantName'] === '' And !isset($_POST['cuisine-search']))){
            try {
                $query = 'SELECT * FROM businessData AS bd LEFT JOIN businessTypes AS bt ON bt.businessId = bd.businessId WHERE bt.type = "Restaurant" ORDER BY businessName';
                $stmt = $conn->query($query);
            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }
        // queries for all restaurants based on cuisine filter
        else if(isset($_POST['cuisine-search'])){
            try{
                $cuisineId = $_POST['cuisineId'];
                $query = 'SELECT bd.*
                FROM businessData AS bd
                JOIN businessTypes AS bt ON bd.businessId = bt.businessId
                JOIN businessCuisines AS bc ON bd.businessId = bc.businessId
                WHERE bt.type = "Restaurant" AND bc.cuisineId = ?';
                $stmt = $conn->prepare($query);
                $stmt->execute([$cuisineId]);
                } catch (PDOException $e) {
                    echo "Error executing the query: " . $e->getMessage();
                }
        }
        // queries for all restaurants based on search filter
        else {
                try{
                $restaurantName = $_POST['restaurantName'];
                $query = 'SELECT * FROM businessData AS bd 
                LEFT JOIN businessTypes AS bt ON bt.businessId = bd.businessId AND bt.type = "Restaurant" 
                WHERE bd.businessName LIKE ? 
                ORDER BY bd.businessName';
                $stmt = $conn->prepare($query);
                $stmt->execute(["%" . $restaurantName . "%"]);
                } catch (PDOException $e) {
                    echo "Error executing the query: " . $e->getMessage();
                }
        } 
         
        // renders data returned from one of the 3 queries above
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


            if($row['url'] !== ''){
            echo "
            <div class='mt-3 mb-3 border border-secondary d-flex align-items-center flex-column justify-content-center gap-2 business-posting'>
            <h3 class='mt-3'> <a href="  . $row['url'] . " target='_blank'>" . $row['businessName'] . "</a></h3>
            <span>" . $row['address'] . "</span>
            <span class='text-center'><a href="  . $row['url'] . " target='_blank'>Website</a></span>
            <span>Overall Rating: " . $row['overallRating'] . "</span>
            <span>Primary Cuisine: " . $cuisineRow['cuisineDesc'] . "</span>
            <a href='business_info_signedIn.php?businessId={$businessId}' class='btn view-reviews mb-3'>View Business Reviews</a>
        </div>";
            }
            else {
                echo "
                <div class='mt-3 mb-3 border border-secondary d-flex align-items-center flex-column justify-content-center gap-2 business-posting'>
                <h3 class='mt-3'>" . $row['businessName'] . "</h3>
                <span>" . $row['address'] . "</span>
                <span>Overall Rating: " . $row['overallRating'] . "</span>
                <span>Primary Cuisine: " . $cuisineRow['cuisineDesc'] . "</span>
                <a href='business_info_signedIn.php?businessId={$businessId}' class='btn view-reviews mb-3'>View Business Reviews</a>
            </div>";  
            }
        }
        }
        ?>
    </div>

</body>

</html>