<?php
session_start();
require_once("/home/retonos/public_html/connect.php");
$webId = $_SESSION['webID'];
if($webId !== 'retonos'){
    header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/index.html");
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
if (isset($_POST['approve'])) {
    
    $reviewId = $_POST['reviewId'];

    try{
    $query = 'UPDATE reviews SET approved = 1 WHERE reviewId= ?';
    $statement = $conn->prepare($query);
    $statement->bindParam(1,$reviewId);
    $result = $statement->execute();
    if($result){
        header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/signedInRestaurants.php");
    }
    else {
        header("Location: https://turing.cs.olemiss.edu/~retonos/Rebel-Reviewer/index.html");
    }
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
        </ul>
    </nav>



    <div class='text-center'>Unapproved reviews will be here</div>

    <table id="unapproved-reviews" class='table justify-content-center align-items-center table-bordered'>
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
    echo "this is session for webId: " .$webId;
    try {
            $query = 'SELECT reviews.*, businessData.businessName
FROM reviews
JOIN businessData ON reviews.businessId = businessData.businessId WHERE reviews.approved = 0
ORDER BY reviews.date_submitted';
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
            <td>" . $row['reviewText'] . "</td>
            <td>" . $row['date_submitted'] . "</td>
            <input type='hidden' name='reviewId' value=" . $row['reviewId'] . ">
            <td><button name='approve' class='btn btn-sm approve' type='submit'>Approve</button></td>
            <td><button id='deny' class='btn btn-sm'>Deny</button></td>
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
            alert(button.id);
        }
    }))

</script>