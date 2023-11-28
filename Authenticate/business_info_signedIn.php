<?php
session_start();
require_once("/home/retonos/public_html/connect.php");

if(!isset($_SESSION['webID'])){
    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/index.html");
    exit;
}

$businessId = $_GET['businessId'];

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
        <li><a class="btn btn-lg account-action" href="myReviews.php">My Reviews</a></li>
        <li><a class="btn btn-lg account-action" href="myFavorites.php">My Favorite Orders</a></li>
        <li><a class="btn btn-lg account-action" href="logout.php">Sign Out</a></li>
        </ul>
    </nav>
    <div class='d-flex align-items-center flex-column justify-content-center'>

        <?php
        if(isset($_POST['rating-search'])){
            $businessId = $_POST['businessId'];
        try{
            $query = "SELECT * FROM businessData WHERE businessId = ?";
        $statement = $conn->prepare($query);
        $statement->bindParam(1,$businessId);
        $statement->execute();
        } catch(PDOException $e){
            echo $e->getMessage();
        }
        try{
            $query2 = "SELECT * FROM businessTypes WHERE businessId = ?";
        $statement2 = $conn->prepare($query2);
        $statement2->bindParam(1,$businessId);
        $statement2->execute();
        } catch(PDOException $e){
            echo $e->getMessage();
        }
        }
        else {
            try{
                $query = "SELECT * FROM businessData WHERE businessId = ?";
            $statement = $conn->prepare($query);
            $statement->bindParam(1,$businessId);
            $statement->execute();
            } catch(PDOException $e){
                echo $e->getMessage();
            }
            try{
                $query2 = "SELECT * FROM businessTypes WHERE businessId = ?";
            $statement2 = $conn->prepare($query2);
            $statement2->bindParam(1,$businessId);
            $statement2->execute();
            } catch(PDOException $e){
                echo $e->getMessage();
            }    
        }
        while ($row = $statement->fetch()) {
            echo "
        <div class='d-flex align-items-center flex-column justify-content-center gap-2 mb-3'>
        <h3 class='text-center mb-3'>" . $row['businessName'] . "</h3>
        <h4 class='text-center mb-3'>Overall Rating: " . $row['overallRating'] . "</h4>
        <span>Address: " . $row['address'] . "</span>
        </div>
        ";
        }
        echo "<div class='d-flex flex-row justify-content-center align-items-center type-list mb-3'>
        <span>Types:  </span>";
        while($row2 = $statement2->fetch(PDO::FETCH_ASSOC)){
            echo"<span class='pr-3'>  " . $row2['type'] . "</span>";
        }
        echo "</div>
        <a href='createFavorite.php?businessId={$businessId}' class='btn btn-md' id='to-createFavorite'>Create Favorite Order</a>";

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
            echo "<a href='createReview.php?businessId={$businessId}' id='leave-review' class='btn btn-lg mt-3 mb-3'>Review Me</a>";
        }

        ?>

    </div>

    <h2 class='text-center mb-3'>Reviews</h2>
    <div class='d-flex justify-content-center mb-5'>
        <form action="business_info_signedIn.php?businessId=<?php echo $businessId; ?>" method="POST">
            <label>Filter By Rating</label>
                <select name="rating-order">
                    <option value="None" selected>
                        None Selected
                    </option>
                    <option value="highest-first">Highest to Lowest</option>
                    <option value="lowest-first">Lowest to Highest</option>
                        </select>
                    <input type="hidden" name="businessId" value="<?php echo $businessId; ?>">
            <button type='submit' class='btn site-options btn-lg m-1' name="rating-search">Filter</button>
        </form>
    </div>
    <div id="reviews" class='d-flex align-items-center flex-column justify-content-center'>
        <?php
        if(isset($_POST['rating-search']) && $_POST['rating-order'] !== "None"){
            $businessId = $_POST['businessId'];
            if($_POST['rating-order'] === 'highest-first'){
                
                try{
                    $query = 'SELECT * FROM reviews WHERE businessId = ? AND approved = 1 ORDER BY rating DESC';
                    $statement = $conn->prepare($query);
                    $statement->bindParam(1,$businessId);
                    $statement->execute();
                } catch(PDOException $e){
                    echo $e->getMessage();
                }
            }
            else if($_POST['rating-order'] === 'lowest-first'){
                try{
                    $query = 'SELECT * FROM reviews WHERE businessId = ? AND approved = 1 ORDER BY rating ASC';
                    $statement = $conn->prepare($query);
                    $statement->bindParam(1,$businessId);
                    $statement->execute();
                } catch(PDOException $e){
                    echo $e->getMessage();
                }
            }
        }
        else{
        try{
            $query = 'SELECT * FROM reviews WHERE businessId = ? AND approved = 1 ORDER BY date_submitted DESC';
            $statement = $conn->prepare($query);
            $statement->bindParam(1,$businessId);
            $statement->execute();
        } catch(PDOException $e){
            echo $e->getMessage();
        }
        }
    while ($row = $statement->fetch()) {
        echo "
        <div class='review-single d-flex flex-column justify-content-center align-items-center'>
        <h3 class='text-center mb-3'>Rating Value " . $row['rating'] . "</h3>
        <span>Date: " . $row['date_submitted'] . "</span>
        <a href='userReviews.php?webId={$row['webId']}'>User: " . $row['webId'] . "</a>
        <p class='text-center'>Review: " . $row['reviewText'] . "</p>
    
        </div>

    ";
    }

        ?>

    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>