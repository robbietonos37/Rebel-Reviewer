<?php
session_start();
require_once("/home/retonos/public_html/PHP/database.php");
require_once("/home/retonos/public_html/PHP/included_functions.php");

$conn = Database::dbConnect();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

</head>

<body>


    <nav class="mt-3">
        <ul id="left-items">
            <li><a class="btn fs-5 site-options" href="index.html">Rebel Reviewer</a></li>
            <li><a class="btn fs-5 site-options" href="howItWorks.html">How does it work?</a></li>
            <li><a class="btn fs-5 site-options" href="something.html">Contact</a></li>
        </ul>
        <ul id="right-items">
        </ul>
    </nav>

    <div id="all-restaurants">
        <?php
        try {
            $query = 'SELECT * FROM businessDetails bs LEFT JOIN businessTypes bt ON businessType = Restaurant AND bt.businessId = bs.businessId';
            $stmt = $conn->query($query);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        // 

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $businessId = $row['businessId'];

            echo "<tr>
            <td>" . $row['businessName'] . "</td>
            <td>" . $row["address"] . "</td>
            <td>" . $row["description"] . "</td>
            <td>" . $row["overallRating"] . "</td>
            <td><a href='businessInfo.php'>More Info</a></td>
            <td><a href='WriteReview.php'>Write a Review</a></td>
            </tr>";

            echo "
            <div class='apt border border-secondary d-flex align-items-center flex-column justify-content-center gap-2>
            <h3>" . $row['businessName'] . "</h3>
            <span>" . $row['address'] . "</span>
            <span>" . $row['description'] . "</span>
            <label> " . $row['overallRating'] . "</label>
            <a href='business_info.php?businessId={$businessId}' class='btn btn-primary view-reviews'>View Business Info</a>
        </div>";
        }
        ?>
    </div>

</body>

</html>