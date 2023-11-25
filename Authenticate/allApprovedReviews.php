<?php
session_start();
require_once("/home/retonos/public_html/connect.php");

if(!isset($_SESSION['webID'])){
    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/index.html");
    exit;
}

$webId = $_SESSION['webID'];

if($webId !== 'retonos'){
    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/Authenticate/signedInHomepage.html");
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
    <title>Document</title>
</head>

<body class="bg-light">
<?php
if (isset($_POST['delete'])) {
    $businessId = $_POST['businessId'];
    $reviewId = $_POST['reviewId'];

    try{
    $query = 'DELETE FROM reviews WHERE reviewId= ?';
    $statement = $conn->prepare($query);
    $statement->bindParam(1,$reviewId);
    $result = $statement->execute();
    // if($result){
    //     header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/Authenticate/signedInBars.php");
    // }
    // else {
    //     header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/index.html");
    // }
    } catch(PDOException $e){
        echo $e->getMessage();
    }
        $reviewCount = 0;
        $totalRating = 0;
        $roundedAvgRating = 0;

    try{
        $query = 'SELECT * FROM reviews WHERE approved = 1 AND businessId= ?';
        $statement = $conn->prepare($query);
        $statement->bindParam(1, $businessId, PDO::PARAM_INT);
        //$result = $statement->execute();
        if (!$statement->execute()) {
            // Handle execution failure
            // Log the error or show an appropriate message
            echo "Query execution failed.";
        } else {
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
               $reviewCount++;
               $totalRating += $row['rating'];
            }
            if ($reviewCount > 0) {
                $avgRating = $totalRating / $reviewCount;
                $roundedAvgRating = round($avgRating, 1);
                // Proceed with the update query here or do necessary actions with $roundedAvgRating
            }
        }
        } catch(PDOException $e){
            echo $e->getMessage();
            echo "Query 1 failing.";
        }
    
        try {
            $query = "UPDATE businessData SET overAllRating = ? WHERE businessId = ?";
            $statement = $conn->prepare($query);
            $statement->bindParam(1, $roundedAvgRating);
            $statement->bindParam(2, $businessId, PDO::PARAM_INT);
            $result = $statement->execute();
            
            // if ($result) {
            //     // Successful update
            //     echo "Overall rating updated successfully for business with ID: $businessId";
            // } else {
            //     // Update failed
            //     echo "Failed to update overall rating for business with ID: $businessId";
            // }
        } catch (PDOException $e) {
            echo $e->getMessage();
            echo "Query 2 failing.";
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



    <h2 class='text-center'>All Approved Reviews will be here</h2>

    <table id="unapproved-reviews" class='table justify-content-center align-items-center table-bordered'>
        <tr>
    <td>WebId</td>
    <td>Business Name</td>
    <td>Rating</td>
    <td>Review Text</td>
    <td>Date Submitted</td>
    <td>Delete</td>
</tr>
    <?php
    try {
            $query = 'SELECT reviews.*, businessData.businessName
FROM reviews
JOIN businessData ON reviews.businessId = businessData.businessId WHERE reviews.approved = 1
ORDER BY reviews.date_submitted';
            $stmt = $conn->query($query);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "
            <form method='post' action='allApprovedReviews.php'>
            <tr>
            <td>" . $row['webId'] . "</td>
            <td>" . $row['businessName'] . "</td>
            <td>" . $row['rating'] . "</td>
            <td>" . $row['reviewText'] . "</td>
            <td>" . $row['date_submitted'] . "</td>
            <input type='hidden' name='reviewId' value=" . $row['reviewId'] . ">
            <input type='hidden' name='businessId' value=" . $row['businessId'] . ">
            <td><button name='delete' class='btn btn-md deny' type='submit'>Delete</button></td>
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