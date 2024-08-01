<?php
session_start();

// $table_id = isset($_GET['table_id']) ? $_GET['table_id'] : null;

// if (!$table_id) {
//     echo "ไม่พบโต๊ะที่ระบุ";
//     exit;
// }
include 'includes/db_connect.php';

$sql = "SELECT * FROM menuitems WHERE status = 1";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เมนูอาหาร</title>
    <link rel="stylesheet" href="css/styles1.css">
    <style>
        body {
            background-color: #fff5f5;
            color: #4b4b4b;
            font-family: Arial, sans-serif;
        }
        .navbar {
            background-color: #ff4c4c;
        }
        .navbar-brand, .navbar-nav .nav-link {
            color: #ffffff;
        }

        .menu-items {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
        }

        .menu-item {
            background-color: #f4f4f4;
            border: 1px solid #ccc;
            border-radius: 10px;
            margin: 10px;
            padding: 10px;
            width: calc(100% - 40px);
            max-width: 400px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .menu-item img {
            width: 100%;
            border-radius: 10px;
        }

        .menu-item-details {
            padding: 10px 0;
        }

        .menu-item-name {
            font-size: 1.5em;
            margin: 0;
        }

        .menu-item-price {
            color: #e91e63;
        }

        .add-to-cart-form {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;
        }

        .add-to-cart-form input[type='number'] {
            width: 60px;
        }

        .add-to-cart-btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
        }

        .add-to-cart-btn:hover {
            background-color: #0056b3;
        }

        @media (min-width: 600px) {
            .menu-item {
                width: calc(50% - 40px);
            }
        }

        @media (min-width: 900px) {
            .menu-item {
                width: calc(33.3333% - 40px);
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>เมนูอาหาร</h1>
    </header>

    <main>
    <section class="menu-items">
        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<div class='menu-item'>";
            echo "<img src='" . $row['image_path'] . "' alt='" . $row['name'] . "' class='menu-item-image'>";
            echo "<div class='menu-item-details'>";
            echo "<h2 class='menu-item-name'>" . $row['name'] . "</h2>";
            echo "<p class='menu-item-price'>ราคา: " . $row['price'] . ' บาท' ."</p>";
            echo "<p class='menu-item-description'>รายละเอียด: " . $row['description'] . "</p>";

            echo "<form action='cart.php' method='GET' class='add-to-cart-form'>";
            echo "<input type='hidden' name='item' value='" . $row['name'] . "'>";
            echo "<input type='hidden' name='price' value='" . $row['price'] . "'>";
            echo "<input type='hidden' name='image' value='" . $row['image_path'] . "'>";
            echo "<label for='quantity'>จำนวน:</label>";
            echo "<input type='number' name='quantity' value='1' min='1'>";
            echo "<button type='submit' class='add-to-cart-btn'>เพิ่มในตะกร้า</button>";
            echo "</form>";

            echo "</div>";
            echo "</div>";
        }
        ?>
    </section>
</main>
</body>
</html>
