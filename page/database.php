<?php

$hostName = "localhost";
$dbUser = "root";
$dbPassword = "";
$dbName = "web-image";
$conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);
if (!$conn) {
    die("Something went wrong;");
}

?>