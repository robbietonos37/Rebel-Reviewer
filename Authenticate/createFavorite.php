<?php
session_start();
require_once("/home/retonos/public_html/connect.php");
$businessId = $_GET['businessId'];

if(!isset($_SESSION['webID'])){
    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/index.html");
    exit;
}

$webId = $_SESSION['webID'];
$realBusinessId = $businessId;

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
    <title>Create Favorite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="stylesAuth.css" rel="stylesheet">
</head>


<body>
<?php
// add data from form into the favorites table for signed in user
$something = $realBusinessId;
if(isset($_POST['favorite'])){
    $reviewText = $_POST['order-text'];
    $businessId = $_POST['businessId'];

    try{
    $query = "INSERT INTO Favorites (businessId, favoriteOrder, webId) VALUES (?,?,?)";
    $insertStmt = $conn->prepare($query);
    $result = $insertStmt->execute([$businessId, $reviewText, $webId]);
    if($result){
        header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/Authenticate/business_info_signedIn.php?businessId=$businessId");
    }
    } catch(PDOException $e){
        echo $e->getMessage();
    }
    

}

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
            <li><a class="btn btn-lg account-action" href="logout.php">Sign Out</a></li>
        </ul>
    </nav>

    <div>
        <?php 
        // renders name of business that user is adding favorite order for
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
        </div>

        ";
        }
        ?>
    </div>

    <div class='d-flex justify-content-center align-items-center d-column'>
        <form method="POST" action="createFavorite.php">
            <div id='review-box'class='d-flex justify-content-center align-items-center flex-column gap-3 p-5'>
                <label>Please Enter your Favorite Order</label>
                <textarea type="textarea" name="order-text" cols="51" rows="7" class='p-1' maxlength="100"></textarea>
                <input type="hidden" name="businessId" value="<?php echo $businessId; ?>">
                <input class="btn btn-md site-options"name="favorite" type="submit">

            </div>
         </form> 

    </div>

</body>

</html>