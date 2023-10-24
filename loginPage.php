<?php 
require_once("/home/retonos/public_html/connect.php");

$conn = DataBase::connectDB();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="RegisterLogin.css">
</head>

<body style="background-color: #508bfc;">
    <div class="m-5">
        <a class="btn btn-lg fs-5" href="index.html" id="return-to-homepage">Back to HomePage</a>
    </div>
    <?php
    if (isset($_POST['submit'])){
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $email = $_POST['email'];

        $sql = 'INSERT INTO Users(webId, firstName, lastName, email, admin) VALUES ( ?, ?, ?, ?, ?)';
        $statement = $conn->prepare($sql);
        $statement->execute()
    }
    ?>
    <section class="vh-100">
        <div class="container m-5">
            <div class="row d-flex justify-content-center">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card shadow-2-strong" style="border-radius: 1rem;">
                        <div class="card-body p-5 text-center">

                            <h3 class="mb-5">Please fill out the form to finish creating your account</h3>
                            <form method="POST" action="loginPage.php" id="registration-form">

                                <div class="form-outline mb-4">
                                    <label class="form-label" for="firstName">First Name</label>
                                    <input type="text" id="firstName" name="firstName" class="form-control form-control-lg mb-3"/>
                                </div>

                                <div class="form-outline mb-4">
                                    <label class="form-label" for="lastName">Last Name</label>
                                    <input type="text" id="lastName" name="lastName" class="form-control form-control-lg mb-3"/>
                                </div>

                                <div class="form-outline mb-4">
                                    <label class="form-label" for="typeEmail">Email</label>
                                    <input type="email" id="lastName" name="email" class="form-control form-control-lg mb-3"/>
                                </div>

                                <div class="form-outline mb-4">
                                    <label class="form-label" for="typeEmail">Confirm Email</label>
                                    <input type="email" id="typeEmail" name="confirmEmail" class="form-control form-control-lg mb-3"/>
                                </div>

                                <button class="btn btn-lg" type="submit" id="register-btn" name="submit">Create Account</button>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>