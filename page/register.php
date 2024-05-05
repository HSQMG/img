<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registration Form</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" />
    <link rel="icon" type="image/x-icon" href="favicon.ico" />
    <link rel="stylesheet" href="../assets/style.css" />

</head>

<body>

    <?php
    if (isset($_POST["submit"])) {
        $fullName = $_POST["fullname"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $passwordRepeat = $_POST["repeat_password"];

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $errors = array();

        if (empty($fullName) or empty($email) or empty($password) or empty($passwordRepeat)) {
            array_push($errors, "All fields are required");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($errors, "Email is not valid");
        }
        if (strlen($password) < 8) {
            array_push($errors, "Password must be at least 8 charactes long");
        }
        if ($password !== $passwordRepeat) {
            array_push($errors, "Password does not match");
        }
        require_once "database.php";
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        $rowCount = mysqli_num_rows($result);
        if ($rowCount > 0) {
            array_push($errors, "Email already exists!");
        }
        if (count($errors) > 0) {
            echo "<div class='error-message-container'>";
            foreach ($errors as $index => $error) {
                echo "<div class='alert alert-danger' id='error-message-$index'>$error</div>";
                echo "<script>setTimeout(function() { document.getElementById('error-message-$index').style.opacity = 0; }, 1000);</script>";
            }
            echo "</div>";
        } else {

            $sql = "INSERT INTO users (full_name, email, password) VALUES ( ?, ?, ? )";
            $stmt = mysqli_stmt_init($conn);
            $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
            if ($prepareStmt) {
                mysqli_stmt_bind_param($stmt, "sss", $fullName, $email, $passwordHash);
                mysqli_stmt_execute($stmt);
                echo "<div class='error-message-container'>";
                echo "<div class='alert alert-danger' id='registered-successfully'>You are registered successfully.</div>";
                echo "<script>setTimeout(function() { document.getElementById('registered-successfully').style.opacity = 0; }, 1000);</script>";
                echo "</div>";
            } else {
                die("Something went wrong");
            }
        }
    }
    ?>
    <div class="background">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <form action="register.php" method="post">
        <h3>Creat Account</h3>
        <label for="username">Username </label>
        <input type="text" placeholder="Full name:" name="fullname" required />
        <label for="email">Email </label>
        <input type="email" placeholder="Email or Phone" name="email" required />
        <label for="password">Password </label>
        <input type="password" placeholder="Password" name="password" required />
        <label for="password">Repeat Password </label>
        <input type="password" placeholder="Repeat Password:" name="repeat_password" required />
        <button type="submit" name="submit">Creat Account</button>

        <div class="form">
            <p>Don't have an account?<a href="login.php">Login Here</a></p>
        </div>
    </form>
    

</body>

</html>