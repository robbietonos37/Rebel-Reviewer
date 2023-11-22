<?php
session_start();
require_once("/home/retonos/public_html/connect.php");

if(!isset($_SESSION['webID'])){
    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/index.html");
    exit;
}

$webId = $_SESSION['webID'];
if($webId !== 'retonos'){
    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/index.html");
    exit;
}

$conn = Database::connectDB();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
<?php
if(isset($_POST['add'])){
    $businessName = $_POST['business-name'];
    $url = $_POST['website'];
    $address = $_POST['address'] . ' Oxford, MS, 38655';
    $cuisineId = $_POST['cuisine'];
    $typeList = $_POST['typeList'];
//     echo $typeList;

// foreach($typeList as $singleType){
//     echo"<h3>" . $singleType . "</h3>";
// }
    try{
    $query = "INSERT INTO businessData (businessName, address, url, overallRating) VALUES (?,?,?,?)";
    $insertStmt = $conn->prepare($query);
    $result = $insertStmt->execute([$businessName, $address, $url, 0]);
    // if($result){
    //     header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/Authenticate/addBusiness.php");
    // }
    } catch(PDOException $e){
        echo $e->getMessage();
    }
    try{
        $query = "SELECT * FROM businessData WHERE businessName = ?";
    $statement = $conn->prepare($query);
    $statement->bindParam(1,$businessName);
    $statement->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
    }
    while ($row = $statement->fetch()) {
        $businessId = $row['businessId'];
        try{
            $query2 = 'INSERT INTO businessCuisines (businessId, cuisineId) VALUES (?, ?)';
            $insertStatement = $conn->prepare($query2);
            $result2 = $insertStatement->execute([$businessId,$cuisineId]);
        } catch(PDOException $e){
            echo $e->getMessage();
        }
    
    foreach($typeList as $singleType){
    try{
        $query3 = 'INSERT INTO businessTypes (businessId, type) VALUES (?, ?)';
        $insertStatement3 = $conn->prepare($query3);
        $result3 = $insertStatement3->execute([$businessId,$singleType]);
    } catch(PDOException $e){
        echo $e->getMessage();
    }
    }
    }
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

    <h2 class='text-center'>Enter Business Information</h2>

    <div class="container p-3 d-lg-flex justify-content-center flex-column" id="add-biz-form">
    <form method="POST" action="addBusiness.php">
        <div class="form-outline mb-4">
            <label class="form-label" for="form4Example1">Business Name</label>
            <input type="text" name="business-name" class="form-control" />
        </div>

        <div class="form-outline mb-4">
            <label class="form-label" for="form4Example2">Website</label>
            <input type="text" id="form4Example2" class="form-control" name="website"/>
        </div>

        <div class="form-outline mb-4">
            <label class="form-label" for="form4Example3">Address (Only street address. DO NOT include city, state, and zip).</label>
            <textarea class="form-control" id="form4Example3" rows="3" name="address"></textarea>
        </div>
        <div id="dropdowns">
            <div class="dropdown form-outline mb-4">
                <label class="form-label">Choose Primary Cuisine</label>
                <select class="select" name="cuisine">
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
            </div>
            <div class="dropdown form-outline mb-4">
            <label class="form-label" for="cuisine">Choose Business Types</label>
                <select class="form-select" multiple data-mdb-placeholder="Example placeholder" name="typeList[]">
                <?php
                try{
                $query = 'SELECT DISTINCT type FROM businessTypes';
                $statement = $conn->query($query);
                } catch (PDOException $e) {
                echo $e->getMessage();
                }
                while($row = $statement->fetch(PDO::FETCH_ASSOC)){
                echo "<option value=" . $row['type'] . ">"
                 . $row['type'] . 
                 "</option>";
                }
                ?>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-lg mb-4 add-business" name="add">Add Business</button>
    </form>
    </div>
    
    


<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>