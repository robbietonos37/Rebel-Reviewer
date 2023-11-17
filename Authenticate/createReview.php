<?php
session_start();
require_once("/home/retonos/public_html/connect.php");
$businessId = $_GET['businessId'];

if(!isset($_SESSION['webID'])){
    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/index.html");
}

$webId = $_SESSION['webID'];
$todayDate = date("Y-m-d");
$realBusinessId = $businessId;

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
<?php 
try{
$reviewCheck = 'SELECT * FROM reviews WHERE webId = ? AND businessId = ?';
$checkStatement = $conn->prepare($reviewCheck);
$checkStatement->bindParam(1,$webId);
$checkStatement->bindParam(2,$businessId);
$checkStatement->execute();
} catch(PDOException $e){
    echo $e->getMessage();
}

if($checkStatement->rowCount() != 0){
    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/Authenticate/business_info_signedIn.php?businessId={$businessId}");
}
?>


<body>
<?php
$something = $realBusinessId;
if(isset($_POST['review'])){
    date_default_timezone_set('America/Chicago');
    $ratingNumber = $_POST['numeric-rating'];
    $reviewText = $_POST['review-text'];
    $businessId = $_POST['businessId'];
    $date = date("Y-m-d");

    try{
    $query = "INSERT INTO reviews (webId, businessId, rating, reviewText, date_submitted, approved) VALUES (?,?,?,?,?,?)";
    $insertStmt = $conn->prepare($query);
    $result = $insertStmt->execute([$webId, $businessId, $ratingNumber, $reviewText, $date,0]);
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

    <div class='d-flex justify-content-center align-items-center d-column'>
        <form method="POST" action="createReview.php">
            <div id='review-box'class='d-flex justify-content-center align-items-center flex-column gap-3 p-5'>
                <label>Rating 0.0-5.0</label>
                <input type="range" min="0" max="5" step="0.1" name="numeric-rating" oninput='rangeValue.innerText = this.value'>
                <p id="rangeValue">2.5</p>
                <!-- <input type="number" min="0" max="5" step="0.1" name="numeric-rating" onkeydown="return false"> -->
                <label>Please tell us about your experience (Limit 250 Characters)</label>
                <textarea type="textarea" name="review-text" cols="51" rows="7" class='p-1' maxlength="250"></textarea>
                <input type="hidden" name="businessId" value="<?php echo $businessId; ?>">
                <input class="btn btn-md site-options"name="review" type="submit">

            </div>
         </form> 

    </div>

</body>

</html>