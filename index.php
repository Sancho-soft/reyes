<?php
session_start();
include 'db.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    // Check if the user is logged in via cookie
    if (isset($_COOKIE['remember_user'])) {
        $user_id = $_COOKIE['remember_user'];
        $stmt = $conn->prepare("SELECT id, email FROM registered_users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
        } else {
            // If the cookie is invalid, delete it
            setcookie('remember_user', '', time() - 3600, "/");
            header("Location: login.php");
            exit();
        }
    } else {
        header("Location: login.php");
        exit();
    }
}

// Fetch the logged-in user's name
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name FROM registered_users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$user_name = $user['name']; // Get the user's name

// Fetch all users from the database
$result = $conn->query("SELECT * FROM users");
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-image: url('images/bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .table-container {
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">User Management</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="index.php">View Users</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'create.php' ? 'active' : '' ?>" href="create.php">Create User</a>
        </li>
      </ul>

      <!-- Logout Button (Aligned to the Right) -->
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link btn btn-danger text-white px-3" href="logout.php" onclick="return confirm('Are you sure you want to logout?');">
            Logout
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<br>
<div class="container">
    <div class="table-container">
        <h2>Welcome, <?= htmlspecialchars($user_name); ?>!</h2> <!-- Display logged-in user's name -->
        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td>
                        <a href="update.php?id=<?= $row['id'] ?>" class="btn btn-warning">Edit</a> 
                        <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

<?php
// Close the database connection
$conn->close();
?>
</body>
</html>