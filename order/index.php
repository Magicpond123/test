<?php
session_start();
include '../includes/db_connect.php';

// Fetch menu items data
$sql = "SELECT * FROM menuitems WHERE status = 1";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Food</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
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
        .navbar-brand:hover, .navbar-nav .nav-link:hover {
            color: #ffe6e6;
        }
        .container {
            margin-top: 30px;
        }
        .menu-items .card {
            border: none;
            margin-bottom: 20px;
        }
        .menu-items .card img {
            border-radius: 5px;
        }
        .menu-items .card-body {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
        }
        .menu-items .card-title {
            color: #ff4c4c;
        }
        .menu-items .btn-danger {
            background-color: #ff4c4c;
            border: none;
        }
        .menu-items .btn-danger:hover {
            background-color: #e63939;
        }
        .btn-dark {
            background-color: #4b4b4b;
            border: none;
        }
        .btn-dark:hover {
            background-color: #333;
        }
        footer {
            background-color: #ff4c4c;
            color: #ffffff;
            padding: 10px 0;
            text-align: center;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">ร้านอาหารของเรา</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#">หน้าหลัก</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">เมนู</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">เกี่ยวกับเรา</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">ติดต่อเรา</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container">
    <h1 class="my-4 text-center">เมนูอาหาร</h1>
    <div class="row menu-items">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="col-md-4">
                <div class="card h-100">
                    <img src="<?php echo $row['image_path']; ?>" class="card-img-top" alt="<?php echo $row['name']; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['name']; ?></h5>
                        <p class="card-text">ราคา: <?php echo number_format($row['price'], 2); ?> บาท</p>
                        <p class="card-text"><?php echo $row['description']; ?></p>
                        <form action="cart.php" method="post">
                            <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                            <div class="mb-3">
                                <label for="quantity" class="form-label">จำนวน:</label>
                                <input type="number" name="quantity" class="form-control" value="1" min="1">
                            </div>
                            <button type="submit" class="btn btn-danger">เพิ่มในตะกร้า</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="text-center mt-4">
        <a href="view_cart.php" class="btn btn-dark">ดูตะกร้าสินค้า</a>
    </div>
</div>
<footer>
    <div class="container">
        <p>© 2023 ร้านอาหารของเรา. All rights reserved.</p>
    </div>
</footer>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
