<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" />
    <link rel="stylesheet" href="../assets/style.css">

</head>

<body>
    <?php
    if (isset($_POST["login"])) {
        $email = $_POST["email"];
        $password = $_POST["password"];
        require_once "database.php";
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if ($user) {
            if (!password_verify($password, $user["password"])) {
                echo "<div class='alert alert-danger' id='error-message' style='position: fixed; top: 20px; right: 20px; opacity: 1; transition: opacity 1s ease-in-out; border: 2px solid #f00; border-radius: 5px; padding: 10px; background-color: #ffeeee;'> Password does not match</div>";
                echo "<script>setTimeout(function() { document.getElementById('error-message').style.opacity = 0; }, 1000);</script>";
            } else {
                session_start();
                $_SESSION["user"] = $user["id"];
                header("Location: index.php");
            }
        } else {

            echo "<div class='alert alert-danger' id='error-message' style='position: fixed; top: 20px; right: 20px; opacity: 1; transition: opacity 1s ease-in-out; border: 2px solid #f00; border-radius: 5px; padding: 10px; background-color: #ffeeee;'> Email does not match</div>";
            echo "<script>  (function() { document.getElementById('error-message').style.opacity = 0; }, 1000);</script>";
        }
    }
    ?>
    <div class="background">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    <form action="login.php" method="post">
        <h3>Login</h3>
        <label for="username">Username </label>
        <input type="email" placeholder="Enter Email:" name="email" required />
        <label for="password">Password </label>
        <input type="password" placeholder="Enter Password:" name="password" required />

        <button id="submit" type="submit" name="login">Login</button>
        <div class="form">
            <p>Not registered yet <a href="register.php">Register Here</a></p>
        </div>

    </form>
</body>

</html>