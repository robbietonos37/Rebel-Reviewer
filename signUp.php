<?php
if (isset($_POST['create'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="signUp.css">
</head>

<body class="bg-light">
    <nav class="mt-3">
        <ul id="left-items">
            <li><a class="btn fs-5 site-options" href="howItWorks.html">How does it work?</a></li>
            <li><a class="btn fs-5 site-options" href="something.html">Contact</a></li>
        </ul>
        <ul id="right-items">
            <li>Already have an account? <a class="btn fs-5 account-action" href="loginPage.php">Log In</a></li>
        </ul>
    </nav>

    <div class="container mb-5">
        <div id="outerFormDiv" class="row align-items-center justify-content-center">
            <div class="col-sm-8 col-lg-14 bg-white rounded">
                <form method="POST" action="https://turing.cs.olemiss.edu/~retonos/PHP/signUp.php" id="register-form">
                    <div class="form-group">
                        <div id="registration">
                            <div class="form-group mt-5 mb-3 w-50">
                                <label for="first-name">First Name:</label>
                                <input class="user-info form-control" type="text" name="first-name" id="first-name" required />
                            </div>
                            <div class="form-group mb-3 w-50">
                                <label for="last-name">Last Name:</label>
                                <input class="user-info form-control" type="text" name="last-name" id="last-name" required />
                            </div>
                            <div class="form-group mb-3 w-50">
                                <label for="emailAddress">Email Address:</label>
                                <input class="user-info form-control" type="email" name="emailAddress" id="emailAddress" required />
                            </div>
                            <div class="form-group mb-2 w-50">
                                <label for="confirmEmail">Confirm Email Address:</label>
                                <input class="user-info form-control" type="email" name="confirmEmail" id="confirmEmail" required />
                            </div>
                            <span><button name="create" class="btn btn-lg mb-5" id="register-button" type="submit">Register</button></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <script src="https://turing.cs.olemiss.edu/~group1/JS/register.js"></script>
</body>

</html>