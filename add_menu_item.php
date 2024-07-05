<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $unit_id = $_POST['unit_id']; // Add this line to get unit_id from form
    $image_path = '';

    // Create uploads directory if it doesn't exist
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $image_path = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
            echo "The file " . htmlspecialchars(basename($_FILES["image"]["name"])) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    $sql = "INSERT INTO menuitems (name, description, price, category_id, unit_id, image_path) 
            VALUES ('$name', '$description', '$price', '$category_id', '$unit_id', '$image_path')";
    if ($conn->query($sql) === TRUE) {
        header("Location: manage_menu.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Menu Item</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/common.css">
</head>
<body>
<div class="container">
    <h2>Add New Menu Item</h2>
    <form action="add_menu_item.php" method="post" enctype="multipart/form-data">
        <div class="form-group mb-3">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group mb-3">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
        </div>
        <div class="form-group mb-3">
            <label for="price">Price:</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
        </div>
        <div class="form-group mb-3">
            <label for="category_id">Category:</label><br>
            <input type="radio" id="food" name="category_id" value="1" required>
            <label for="food">Food</label>
            <input type="radio" id="beverage" name="category_id" value="2" required>
            <label for="beverage">Beverage</label>
            <input type="radio" id="dessert" name="category_id" value="3" required>
            <label for="dessert">Dessert</label>
        </div>
        <div class="form-group mb-3">
            <label for="unit_id">Unit:</label><br>
            <input type="radio" id="กิโลกรัม" name="unit_id" value="1" required>
            <label for="กิโลกรัม">กิโลกรัม</label>
            <input type="radio" id="กรัม" name="unit_id" value="2" required>
            <label for="กรัม">กรัม</label><br>
            <input type="radio" id="ชิ้น" name="unit_id" value="3" required>
            <label for="ชิ้น">ชิ้น</label>
            <input type="radio" id="แพ็ค" name="unit_id" value="4" required>
            <label for="แพ็ค">แพ็ค</label>
        </div>
            </select>
        </div>
        <div class="form-group mb-3">
            <label for="image">Image:</label>
            <input type="file" class="form-control" id="image" name="image">
        </div>
        <button type="submit" class="btn btn-primary">Add Menu Item</button>
    </form>
</div>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
