<?php
session_start();
require_once("/home/retonos/public_html/connect.php");

$conn = Database::connectDB();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurants</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="style.css" rel="stylesheet">
</head>

<body>


    <nav class="mt-3">
        <ul id="left-items">
            <li><a class="btn fs-5 site-options" href="index.html">Rebel Reviewer</a></li>
            <li><a class="btn fs-5 site-options" href="howItWorks.html">How does it work?</a></li>
            <li><a class="btn fs-5 site-options" href="contact.html">Contact</a></li>
        </ul>
        <ul id="right-items">
            <li><a class="btn btn-lg business-options" href="coffeeshops.php">Coffeeshops</a></li>
            <li><a class="btn btn-lg business-options" href="bars.php">Bars</a></li>
        </ul>
        <ul id="right-items">
            <li><a class="btn fs-5 account-action"
                    href="https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/Authenticate/">Log In</a></li>
        </ul>
    </nav>

    <div id="filter-box" class='card-body'>
        <div class='row d-flex justify-content-center' id='filter-section'>
            <div class='col-md-7'>
                <form action="restaurants.php" method="POST">
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

    <div id="all-restaurants">

        <?php
        if((!isset($_POST['search']) And !isset($_POST['cuisine-search'])) Or ($_POST['restaurantName'] === '' And !isset($_POST['cuisine-search']))){
            try {
            $query = 'SELECT * FROM businessData AS bd LEFT JOIN businessTypes AS bt ON bt.businessId = bd.businessId WHERE bt.type = "Restaurant" ORDER BY businessName';
            $stmt = $conn->query($query);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        }
        else if(isset($_POST['cuisine-search'])){
            try{
                $cuisineId = $_POST['cuisineId'];
                $query = 'SELECT bd.*
                FROM businessData AS bd
                JOIN businessTypes AS bt ON bd.businessId = bt.businessId
                JOIN businessCuisines AS bc ON bd.businessId = bc.businessId
                WHERE bt.type = "Restaurant" AND bc.cuisineId = ?';
                $stmt = $conn->prepare($query);
                //$stmt->bindParam(1,  "%'" . $restaurantName . "'%");
                $stmt->execute([$cuisineId]);
                //$result = $stmt->execute();
                } catch (PDOException $e) {
                    echo "Error executing the query: " . $e->getMessage();
                }
        }
        else {
            try{
            $restaurantName = $_POST['restaurantName'];
            $query = 'SELECT * FROM businessData AS bd 
            LEFT JOIN businessTypes AS bt ON bt.businessId = bd.businessId AND bt.type = "Restaurant" 
            WHERE bd.businessName LIKE ? 
            ORDER BY bd.businessName';
            $stmt = $conn->prepare($query);
            //$stmt->bindParam(1,  "%'" . $restaurantName . "'%");
            $stmt->execute(["%" . $restaurantName . "%"]);
            //$result = $stmt->execute();
            } catch (PDOException $e) {
                echo "Error executing the query: " . $e->getMessage();
            }
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

            if($row['url'] !== ''){
            echo "
            <div class='mt-3 mb-3 border border-secondary d-flex align-items-center flex-column justify-content-center gap-2 business-posting'>
            <h3 class='mt-3'> <a href="  . $row['url'] . " target='_blank'>" . $row['businessName'] . "</a></h3>
            <span>" . $row['address'] . "</span>
            <span class='text-center'><a href="  . $row['url'] . " target='_blank'>Website</a></span>
            <span>Overall Rating: " . $row['overallRating'] . "</span>
            <span>Primary Cuisine: " . $cuisineRow['cuisineDesc'] . "</span>
            <a href='business_info.php?businessId={$businessId}' class='btn view-reviews mb-3'>View Business Reviews</a>
        </div>";
            }
            else {
                echo "
                <div class='mt-3 mb-3 border border-secondary d-flex align-items-center flex-column justify-content-center gap-2 business-posting'>
                <h3 class='mt-3'>" . $row['businessName'] . "</h3>
                <span>" . $row['address'] . "</span>
                <span>Overall Rating: " . $row['overallRating'] . "</span>
                <span>Primary Cuisine: " . $cuisineRow['cuisineDesc'] . "</span>
                <a href='business_info.php?businessId={$businessId}' class='btn view-reviews mb-3'>View Business Reviews</a>
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