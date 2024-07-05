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
    $image_path = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        $image_path = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
    }

    $sql = "INSERT INTO menuitems (name, description, price, category_id, image_path) 
            VALUES ('$name', '$description', '$price', '$category_id', '$image_path')";
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.php">ต้วงหมูกะทะ</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user fa-fw"></i>
                    <?php if (isset($_SESSION['username'])): ?>
                        <?php echo $_SESSION['username']; ?>
                    <?php else: ?>
                        Guest
                    <?php endif; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <?php if (isset($_SESSION['username'])): ?>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a class="dropdown-item" href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">หน้าหลัก</div>
                        <a class="nav-link" href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            หน้าหลัก
                        </a>
                        <div class="sb-sidenav-menu-heading">เมนูต่างๆ</div>
                        <a class="nav-link" href="manage_menu.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            จัดการรายการอาหาร
                        </a>
                        <a class="nav-link" href="manage_employees.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                            จัดการพนักงาน
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    <?php if (isset($_SESSION['username'])): ?>
                        <?php echo $_SESSION['username']; ?>
                    <?php else: ?>
                        Guest
                    <?php endif; ?>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Add Menu Item</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Add Menu Item</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-plus-circle me-1"></i>
                            Add New Menu Item
                        </div>
                        <div class="card-body">
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
                                    <label for="food">Food</label><br>
                                    <input type="radio" id="beverage" name="category_id" value="2" required>
                                    <label for="beverage">Beverage</label><br>
                                    <input type="radio" id="dessert" name="category_id" value="3" required>
                                    <label for="dessert">Dessert</label>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="image">Image:</label>
                                    <input type="file" class="form-control" id="image" name="image">
                                </div>
                                <button type="submit" class="btn btn-primary">Add Menu Item</button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Your Website 2023</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>
</html>
