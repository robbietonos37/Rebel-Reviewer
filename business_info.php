<?php
$businessId = $_GET['businessId'];

$conn = DataBase::dbConnect();


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <nav class="mt-3">
        <ul id="left-items">
            <li><a class="btn fs-5 site-options" href="howItWorks.html">How does it work?</a></li>
            <li><a class="btn fs-5 site-options" href="something.html">Contact</a></li>
        </ul>
        <ul id="right-items">
            <li><a class="btn fs-5 account-action" href="loginPage.php">Log In</a></li>
        </ul>
    </nav>
    <div>

        <?php
        $query = 'SELECT * FROM businessData WHERE businessId = {$businessId}';
        $statement = $conn->execute($query);
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            echo "
        <div>
        <h3 class='text-center mb-3'>" . $row['businessName'] . "</h3>

        </div>

        ";
        }

        ?>

    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>