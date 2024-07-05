
<?php
session_start();
include 'includes/db_connect.php';

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "SELECT * FROM employees WHERE username='$username' AND password='$password'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $_SESSION['username'] = $username;
    header("Location: index.php");
} else {
    echo "Invalid username or password";
}
$conn->close();
?>
