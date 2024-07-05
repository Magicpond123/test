<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db_connect.php';

// Fetch employees data
$sql = "SELECT * FROM employees";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Employees</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/table.css">
</head>
<body>
<header>
    Restaurant Management
    <div class="login-status">
        Welcome, <?php echo $_SESSION['username']; ?> | <a href="logout.php" class="text-white">Logout</a>
    </div>
</header>
<div class="container">
    <h2>Manage Employees</h2>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Location</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['emp_id']; ?></td>
                <td><?php echo $row['firstname']; ?></td>
                <td><?php echo $row['lastname']; ?></td>
                <td><?php echo $row['mail']; ?></td>
                <td><?php echo $row['location']; ?></td>
                <td><?php if($row['role']==1){echo 'Owner';}
                elseif($row['role']==2){
                    echo 'Cashier';
                }elseif($row['role']==3){
                    echo 'Receptionist';
                }else{echo 'Kitchen';}
                ?>
                </td>
                <td>
                    <a href="edit_employee.php?id=<?php echo $row['emp_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="delete_employee.php?id=<?php echo $row['emp_id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
