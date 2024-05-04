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

<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="bg-white sidebar d-flex flex-column  fs-8rem col-md-1">
        <div class="text-center">
          <ul class="nav nav-pills flex-column mt-5">
            <li class="nav-item py-2">
              <a href="index.php" class="nav-link text-dark">
                <i class="bi bi-backpack4 fs-5"></i> <br />
                Search Image
              </a>
            </li>
            <li class="nav-item py-3">
              <a href="history.php" class="nav-link text-dark">
                <i class="bi bi-clipboard fs-5"></i><br />
                History
              </a>
            </li>
            <li class="nav-item py-3">
              <a href="logout.php" class="nav-link text-dark">
                <i class="bi bi-gear fs-5"></i> <br />
                Logout
              </a>
            </li>
          </ul>
        </div>
      </div>
      <!-- Main Content Container -->
      <div class="container col-md-11  ">
        
        <h1>Image Search</h1>
        
        <form action="index.php" method="post" class='aaa'>
            <input type="text" class="form-control" id="search-box" name="search_query">
          <button type="submit" class="btn btn-primary" name="submit">Search</button>
        </form>
        <hr>  

        <!-- Display search result image and text if available -->
        <div class="d-flex justify-content-center align-items-center vh-100">
        <?php if (isset($image_path)) : ?>
            <?php if (isset($search_query)) : ?>
                <div class="mt-3">
                    <h2>Search Result for "<?php echo htmlspecialchars($search_query); ?>":</h2>
                    <img src="<?php echo $image_path; ?>" alt="Search Result Image" class="img-fluid">
                </div>
            <?php else : ?>
                <div class="mt-3">
                    <h2>Search Result:</h2>
                    <img src="<?php echo $image_path; ?>" alt="Search Result Image" class="img-fluid">
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

      </div>
    </div>
  </div>

</html>