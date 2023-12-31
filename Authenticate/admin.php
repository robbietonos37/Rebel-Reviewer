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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="stylesAuth.css" rel="stylesheet" >
    <title>Admin</title>
</head>

<body class="bg-light">
<?php
if (isset($_POST['approve'])) {
    // updates approved status of review 
    $reviewId = $_POST['reviewId'];
    $businessId = $_POST['businessId'];

    try{
    $query = 'UPDATE reviews SET approved = 1 WHERE reviewId= ?';
    $statement = $conn->prepare($query);
    $statement->bindParam(1,$reviewId);
    $result = $statement->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
    }

    // recalculates the avg rating for the business of the review
    try{
    $query = 'SELECT * FROM reviews WHERE approved = 1 AND businessId= ?';
    $statement = $conn->prepare($query);
    $statement->bindParam(1, $businessId, PDO::PARAM_INT);
    $result = $statement->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
        echo "Query 1 failing.";
    }
    $reviewCount = 0;
    $totalRating = 0;
    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
           $reviewCount++;
           $totalRating += $row['rating'];
        }
    

    $avgRating = $totalRating / $reviewCount;
    $roundedAvgRating = round($avgRating, 1);

    // updates the business's avg rating with the calculated value
    try {
        $query = "UPDATE businessData SET overAllRating = ? WHERE businessId = ?";
        $statement = $conn->prepare($query);
        $statement->bindParam(1, $roundedAvgRating);
        $statement->bindParam(2, $businessId, PDO::PARAM_INT);
        $result = $statement->execute();
        
    } catch (PDOException $e) {
        echo $e->getMessage();
        echo "Query 2 failing.";
    }
}
if (isset($_POST['deny'])) {
    // deletes the review
    
    $reviewId = $_POST['reviewId'];

    try{
    $query = 'DELETE FROM reviews WHERE reviewId= ?';
    $statement = $conn->prepare($query);
    $statement->bindParam(1,$reviewId);
    $result = $statement->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
    }

}
?>
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



    <h2 class='text-center'>Unapproved Reviews</h2>

    <table id="unapproved-reviews" class='table justify-content-center align-items-center table-bordered mt-5'>
        <tr>
    <td>WebId</td>
    <td>Business Name</td>
    <td>Rating</td>
    <td>Review Text</td>
    <td>Date Submitted</td>
    <td>Approve</td>
    <td>Deny</td>
</tr>
    <?php
    // queries for all of the reviews that have not yet been approved
    try {
            $query = 'SELECT reviews.*, businessData.businessName
FROM reviews
JOIN businessData ON reviews.businessId = businessData.businessId WHERE reviews.approved = 0
ORDER BY reviews.reviewId';
            $stmt = $conn->query($query);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "
            <form method='post' action='admin.php'>
            <tr>
            <td>" . $row['webId'] . "</td>
            <td>" . $row['businessName'] . "</td>
            <td>" . $row['rating'] . "</td>
            <td>" . htmlspecialchars($row['reviewText'], ENT_QUOTES) . "</td>
            <td>" . $row['date_submitted'] . "</td>
            <input type='hidden' name='reviewId' value=" . $row['reviewId'] . ">
            <input type='hidden' name='businessId' value=" . $row['businessId'] . ">
            <td><button name='approve' class='btn btn-md approve' type='submit'>Approve</button></td>
            <td><button name='deny' class='btn btn-md deny' type='submit'>Deny</button></td>
            </form>
        </tr>";
        }

    ?>
    </table>

    

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"
        integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>

</body>

</html>

<script>
    // these make the admin confirm before committing to an action
    const approveButtons = document.getElementsByClassName('approve');
    const approveButtonsArray = Array.from(approveButtons);
    approveButtonsArray.forEach((button) => button.addEventListener('click', (e) => {
        if(!confirm("Are you SURE you want to approve this review? If so it will be live for everyone to see and will affect the business's rating")){
            e.preventDefault();
        }
    }))

    const denyButtons = document.getElementsByClassName('deny');
    const denyButtonsArray = Array.from(denyButtons);
    denyButtonsArray.forEach((button) => button.addEventListener('click', (e) => {
        if(!confirm("Are you SURE you want to deny this review? If so it will be deleted FOREVER. PLEASE BE SURE!")){
            e.preventDefault();
        }
    }))
    

</script>