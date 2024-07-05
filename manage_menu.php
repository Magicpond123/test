<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db_connect.php';

// Fetch menu items data
$sql = "SELECT menuitems.item_id, menuitems.name, menuitems.description, menuitems.price, menuitems.image_path, category.type AS category 
        FROM menuitems 
        JOIN category ON menuitems.category_id = category.category_id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Menu</title>
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
    <h2>Manage Menu</h2>
    <a href="add_menu_item.php" class="btn btn-primary mb-3">Add New Menu Item</a>
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Category</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i=1;
             while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td><?php echo $row['price']; ?></td>
                <td><?php echo $row['category']; ?></td>
                <td>
                    <?php
                    $i++;
                     if ($row['image_path']) { ?>
                        <img src="<?php echo $row['image_path']; ?>" alt="Menu Item Image" style="width: 100px; height: auto;">
                    <?php } ?>
                </td>
                <td>
                    <a href="edit_menu_item.php?id=<?php echo $row['item_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="delete_menu_item.php?id=<?php echo $row['item_id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
