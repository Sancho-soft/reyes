<?php
include "db.php";

$id = $_GET['id'];

// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<div class='alert alert-success'>User deleted successfully!</div>";
} else {
    echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
}

$stmt->close();
header("Location: index.php");  // Redirect back to user list
exit();
?>