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

    try {
        $deleteQuery1 = 'DELETE FROM businessCuisines WHERE businessId = ?';
        $deleteStmt1 = $conn->prepare($deleteQuery1);
        $deleteStmt1->bindParam(1,$businessId);
        $deleteStmt1->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
    }

    try{
        $deleteQuery2 = 'DELETE FROM businessTypes WHERE businessId = ?';
        $deleteStmt2 = $conn->prepare($deleteQuery2);
        $deleteStmt2->bindParam(1,$businessId);
        $deleteStmt2->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
    }

    try{
        $deleteQuery3 = 'DELETE FROM reviews WHERE businessId = ?';
        $deleteStmt3 = $conn->prepare($deleteQuery3);
        $deleteStmt3->bindParam(1,$businessId);
        $deleteStmt3->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
    }

    try{
        $deleteQuery4 = 'DELETE FROM Favorites WHERE businessId = ?';
        $deleteStmt4 = $conn->prepare($deleteQuery4);
        $deleteStmt4->bindParam(1,$businessId);
        $deleteStmt4->execute();
    } catch(PDOException $e){
        echo $e->getMessage();
    }

    try{
        $deleteQuery5 = 'DELETE FROM businessData WHERE businessId = ?';
        $deleteStmt5 = $conn->prepare($deleteQuery5);
        $deleteStmt5->bindParam(1,$businessId);
        $deleteStmt5->execute();
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

    <div id="filter-box" class='card-body'>
        <div class='row d-flex justify-content-center' id='filter-section'>
            <div class='col-md-7'>
                <form action="allBusinesses.php" method="POST">
                    <div class='input-group d-flex justify-content-center mb-3'>
                        <div id='search-box' class='d-flex flex-row'>
                            <input type='text' name='restaurantName' class='form-control' placeholder='Search Business'>
                            <button type='submit' class='btn btn-lg' name='search' id='searcher'>Search</button>
                        </div>
                    </div>    
                <form>
            </div>
        </div>
    </div>

    <table id="unapproved-reviews" class='table justify-content-center align-items-center table-bordered mb-5'>
        <tr>
    <td>Business Name</td>
    <td>Address</td>
    <td>Delete</td>
    <td>Edit</td>
</tr>
    <?php
    //echo "this is session for webId: " .$webId;
    if(!isset($_POST['search'])  Or $_POST['restaurantName'] === ''){
    try {
            $query = 'SELECT * FROM businessData ORDER BY businessName';
            $stmt = $conn->query($query);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    else {
        $restaurantName = $_POST['restaurantName'];
        try {
            $query = 'SELECT * FROM businessData WHERE businessName LIKE ? ORDER BY businessName';
            $stmt = $conn->prepare($query);
            $stmt->execute(["%" . $restaurantName . "%"]);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
        

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "
            <form method='post' action='allBusinesses.php'>
            <tr>
            <td>" . $row['businessName'] . "</td>
            <td>" . $row['address'] . "</td>
            <input type='hidden' name='businessId' value=" . $row['businessId'] . ">
            <td><button name='delete' class='btn btn-md delete' type='submit'>Delete</button></td>
            <td><a href='editBusiness.php?businessId={$row['businessId']}' class='btn btn-md' id='edit'>Edit</a></td>
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
    const denyButtons = document.getElementsByClassName('delete');
    const denyButtonsArray = Array.from(denyButtons);
    denyButtonsArray.forEach((button) => button.addEventListener('click', (e) => {
        if(!confirm("Are you SURE you want to delete this business? If so it will be deleted FOREVER. PLEASE BE SURE!")){
            e.preventDefault();
        }
    }))
    

</script>