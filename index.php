<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/layout.css">
</head>
<body>
<header>
    Restaurant Management
    <div class="login-status">
        Welcome, <?php echo $_SESSION['username']; ?> | <a href="logout.php" class="text-white">Logout</a>
    </div>
</header>
<div class="container">
    <div class="sidebar">
        <a href="manage_employees.php">Manage Employees</a>
        <a href="manage_menu.php">Manage Menu</a>
        <!-- Add more links for other functionalities -->
    </div>
    <div class="content">
        <h2>Welcome, <?php echo $_SESSION['username']; ?></h2>
        <div class="card">
            <h3>Manage Employees</h3>
            <p>Click the button below to manage employees.</p>
            <a href="manage_employees.php" class="btn">Manage Employees</a>
        </div>
        <div class="card">
            <h3>Manage Menu</h3>
            <p>Click the button below to manage menu items.</p>
            <a href="manage_menu.php" class="btn">Manage Menu</a>
        </div>
    </div>
</div>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
