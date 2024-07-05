<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db_connect.php';

$item_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $unit_id = $_POST['unit_id']; // Add this line to get unit_id from form
    $image_path = $_POST['existing_image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $image_path = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
    }

    $sql = "UPDATE menuitems 
            SET name='$name', description='$description', price='$price', category_id='$category_id', unit_id='$unit_id', image_path='$image_path' 
            WHERE item_id='$item_id'";
    if ($conn->query($sql) === TRUE) {
        header("Location: manage_menu.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    $sql = "SELECT * FROM menuitems WHERE item_id='$item_id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Menu Item</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/common.css">
</head>
<body>
<div class="container">
    <h2>Edit Menu Item</h2>
    <form action="edit_menu_item.php?id=<?php echo $item_id; ?>" method="post" enctype="multipart/form-data">
        <div class="form-group mb-3">
            <label for="name">Name:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $row['name']; ?>" required>
        </div>
        <div class="form-group mb-3">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" required><?php echo $row['description']; ?></textarea>
        </div>
        <div class="form-group mb-3">
            <label for="price">Price:</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo $row['price']; ?>" required>
        </div>
        <div class="form-group mb-3">
            <label for="category_id">Category:</label><br>
            <input type="radio" id="food" name="category_id" value="1" <?php echo ($row['category_id'] == 1) ? 'checked' : ''; ?> required>
            <label for="food">Food</label><br>
            <input type="radio" id="beverage" name="category_id" value="2" <?php echo ($row['category_id'] == 2) ? 'checked' : ''; ?> required>
            <label for="beverage">Beverage</label><br>
            <input type="radio" id="dessert" name="category_id" value="3" <?php echo ($row['category_id'] == 3) ? 'checked' : ''; ?> required>
            <label for="dessert">Dessert</label>
        </div>
        <div class="form-group mb-3">
            <label for="unit_id">Unit:</label>
            <select class="form-control" id="unit_id" name="unit_id" required>
                <?php
                $unit_sql = "SELECT * FROM unit";
                $unit_result = $conn->query($unit_sql);
                while ($unit_row = $unit_result->fetch_assoc()) {
                    $selected = ($unit_row['unit_id'] == $row['unit_id']) ? 'selected' : '';
                    echo "<option value='" . $unit_row['unit_id'] . "' $selected>" . $unit_row['name'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group mb-3">
            <label for="image">Image:</label>
            <input type="file" class="form-control" id="image" name="image">
            <input type="hidden" name="existing_image" value="<?php echo $row['image_path']; ?>">
            <?php if ($row['image_path']) { ?>
                <img src="<?php echo $row['image_path']; ?>" alt="Menu Item Image" class="mt-2" style="width: 200px;">
            <?php } ?>
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
