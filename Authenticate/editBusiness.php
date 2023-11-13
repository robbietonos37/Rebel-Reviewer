<?php
session_start();
require_once("/home/retonos/public_html/connect.php");
$webId = $_SESSION['webID'];
if($webId !== 'retonos'){
    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/index.html");
}
$businessId = $_GET['businessId'];

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
    <?php
    try{
    $query = "SELECT * FROM businessData WHERE businessId = ?";
    $statement = $conn->prepare($query);
    $statement->bindParam(1,$businessId);
    $statement->execute();
    } catch(PDOException $e){
    echo $e->getMessage();
    }
    ?>

    <div class='containter-lg'>
        <div class='row justify-content-center my-5'>
            <div class='col-lg-6' id='edit-review'>
                <form action="editBusiness.php" method="POST">
                    <h3>Edit Business Information</h3>
                    <?php
                    while ($businessRow = $statement->fetch()) {
                        $address = $businessRow['address'];
                    echo "
                    <div class='form-group'>
                    <label for='name'>Business Name</label>
                    <input type='text' class='form-control' name='businessName' value=" . $businessRow['businessName'] . ">
                    </div>
                    <div class='form-group'>
                    <label for='name'>Address</label>
                    <textarea class='form-control' name='address'>". $businessRow['address'] . "</textarea>
                    </div>
                    <div class='form-group'>
                    <label for='website'>Website</label>
                    <input type='text' class='form-control' name='website' value=" . $businessRow['url'] . ">
                    </div>";
                    
                    ?>
                    <input type="hidden" name="businessId" value="<?php echo $businessId; ?>">
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
                    try {
                        $cuisineQuery = 'SELECT businessCuisines.businessId, businessCuisines.cuisineId, Cuisine.cuisineDesc
                        FROM businessCuisines
                        JOIN Cuisine ON businessCuisines.cuisineId = Cuisine.cuisineId
                        WHERE businessCuisines.businessId = ?';
                        $cuisineStatement = $conn->prepare($cuisineQuery); 
                        $cuisineStatement->bindParam(1,$businessId);
                        $cuisineStatement->execute();
                    } catch (PDOException $e) {
                        echo $e->getMessage();
                    }
                    while($cuisineRow = $cuisineStatement->fetch()){
                        if($row['cuisineId'] === $cuisineRow['cuisineId']){
                            echo "<option value=" . $cuisineRow['cuisineId'] . " selected>"
                             . $cuisineRow['cuisineDesc'] . 
                            "</option>";
                        } else{
                            echo "<option value=" . $row['cuisineId'] . ">"
                             . $row['cuisineDesc'] . 
                             "</option>";
                        }
                    }
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
                $statement3 = $conn->query($query);
                } catch (PDOException $e) {
                echo $e->getMessage();
                }
                while($row = $statement3->fetch(PDO::FETCH_ASSOC)){
                    try{
                        $query = 'SELECT * FROM businessTypes WHERE businessId = ?';
                        $statement4 = $conn->query($query);
                        $statement4->bindParam(1,$businessId);
                        $statement4->execute();
                        } catch (PDOException $e) {
                        echo $e->getMessage();
                        }
                    while($row4 = $statement4->fetch(PDO::FETCH_ASSOC)){
                        if($row4['type'] === $row['type']){
                            echo "<option value=" . $row['type'] . " selected>"
                             . $row['type'] . 
                             "</option>";
                        } else {
                            echo "<option value=" . $row['type'] . ">"
                             . $row['type'] . 
                             "</option>";
                        }
                }
            }
            }
                ?>
                </select>
            </div>
                    <button type='submit' name='confirm' class='btn btn-lg' id='complete-edit'>Complete Edit</button>
                </form>
            </div>
        </div>
    </div>



    <script src="https://code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>
</body>
</html>

