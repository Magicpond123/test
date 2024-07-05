<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db_connect.php';

$order_id = $_GET['id'];

$sql = "DELETE FROM orders WHERE order_id='$order_id'";
if ($conn->query($sql) === TRUE) {
    header("Location: manage_orders.php");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
?>
