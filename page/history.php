<?php
session_start();
require_once "database.php";
if (!isset($_SESSION["user"])) {
  header("Location: login.php");
  exit();
}

$user_id = $_SESSION["user"];

if (isset($_POST["submit"])) {
  $search_query = $_POST["search_query"];
  $uniqid = uniqid();
  shell_exec("python process_data.py " . escapeshellarg($search_query) . " " . escapeshellarg($uniqid));
  $image_path = 'text_image' . $uniqid . '.png';

  $sql = "INSERT INTO search_history (user_id, search_query, image_path) VALUES (?, ?, ?)";
  $stmt = mysqli_stmt_init($conn);
  if (mysqli_stmt_prepare($stmt, $sql)) {
    mysqli_stmt_bind_param($stmt, "iss", $user_id, $search_query, $image_path);
    mysqli_stmt_execute($stmt);
  }
}

if (isset($_GET["delete"])) {
  $search_id = $_GET["delete"];
  $sql_select_image = "SELECT image_path FROM search_history WHERE search_id = ? AND user_id = ?";
  $stmt_select_image = mysqli_stmt_init($conn);
  if (mysqli_stmt_prepare($stmt_select_image, $sql_select_image)) {
    mysqli_stmt_bind_param($stmt_select_image, "ii", $search_id, $user_id);
    mysqli_stmt_execute($stmt_select_image);
    $result_select_image = mysqli_stmt_get_result($stmt_select_image);
    $row_image = mysqli_fetch_assoc($result_select_image);
    $image_path = $row_image["image_path"];

    if (file_exists($image_path)) {
      unlink($image_path);
    }
  }

  $sql_delete = "DELETE FROM search_history WHERE search_id = ? AND user_id = ?";
  $stmt_delete = mysqli_stmt_init($conn);
  if (mysqli_stmt_prepare($stmt_delete, $sql_delete)) {
    mysqli_stmt_bind_param($stmt_delete, "ii", $search_id, $user_id);
    mysqli_stmt_execute($stmt_delete);
  }
}



$sql_select = "SELECT * FROM search_history WHERE user_id = ? ORDER BY timestamp DESC";
$stmt_select = mysqli_stmt_init($conn);
if (mysqli_stmt_prepare($stmt_select, $sql_select)) {
  mysqli_stmt_bind_param($stmt_select, "i", $user_id);
  mysqli_stmt_execute($stmt_select);
  $result_select = mysqli_stmt_get_result($stmt_select);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Search History</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="../assets/home.css">
  
</head>
<style>
  
</style>
<body>
  
  <div class="container-fluid">
    <div class="row">
      <div class="bg-white sidebar d-flex flex-column  fs-8rem col-md-1" >
        <div class="text-center">
          <ul class="nav nav-pills flex-column mt-5" >
            <li class="nav-item py-2">
              <a href="index.php" class="nav-link text-dark">
                <i class="bi bi-backpack4 fs-5" style="color:black;"></i> <br />
                Search Image
              </a>
            </li>
            <li class="nav-item py-3">
              <a href="history.php" class="nav-link text-dark">
                <i class="bi bi-clipboard fs-5" style="color:black;"></i><br />
                History
              </a>
            </li>
            <li class="nav-item py-3">
              <a href="logout.php" class="nav-link text-dark">
                <i class="bi bi-gear fs-5" style="color:black;"></i> <br />
                Logout
              </a>
            </li>
          </ul>
        </div>
      </div>
      <div class="container col-md-11 " style="color: #fff;
  background-image: url('../images/anh.png');
  background-size: 100% 100%;
  background-repeat: no-repeat; 
  background-position: center; color:#fff" >
        <style>
          * {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      box-sizing: border-box;
      color: #fff;
      background-attachment: fixed;
    }s

    body {
      background-color: #fff; 
      color: #ffff; 
    }

    h1 {
      text-align: center;
      margin: 100px auto 50px;
      font-weight: 600;
    }

    table {
      border: 2px solid #000; 
      border-collapse: collapse; 
      width: 100%;
    }

    th,
    td {
      border: 2px solid #000; 
      padding: 8px;
      text-align: left;
    }
        </style>
        <h1>Image Search</h1>
        <table class="table">
          <thead>
            <tr>
              <th>Search Query</th>
              <th>Timestamp</th>
              <th>Image</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = mysqli_fetch_assoc($result_select)) : ?>
              <tr>
                <td ><?php echo $row["search_query"]; ?></td>
                <td ><?php echo $row["timestamp"]; ?></td>
                <td><img src="<?php echo $row["image_path"]; ?>" alt="Search Image"></td>
                <td ><a href="history.php?delete=<?php echo $row["search_id"]; ?>" class="btn btn-danger btn-sm">Delete</a></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

      </div>
    </div>
  </div>

</html>