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
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $discount = $_POST['discount'];
    $rule_type = $_POST['rule_type'];

    // เตรียมข้อมูล rule_data ตามประเภทของโปรโมชั่น
    if ($rule_type == 'quantity_discount') {
        // กรณีโปรโมชั่นมา 4 จ่าย 3
        $required_quantity = $_POST['required_quantity'] ?? 0;
        $free_quantity = $_POST['free_quantity'] ?? 0;
        $rule_data = json_encode([
            'required_quantity' => $required_quantity,
            'free_quantity' => $free_quantity
        ]);
    } elseif ($rule_type == 'birthday_discount') {
        // กรณีโปรโมชั่นวันเกิด
        $birthday_discount_percentage = $_POST['birthday_discount_percentage'] ?? 0;
        $rule_data = json_encode([
            'birthday_discount_percentage' => $birthday_discount_percentage
        ]);
    } else {
        // กรณีโปรโมชั่นแบบส่วนลดทั่วไป
        $rule_data = json_encode([]);
    }

    // Insert new promotion into database
    $sql = "INSERT INTO promotions (name, description, start_date, end_date, discount, rule_type, rule_data) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiss", $name, $description, $start_date, $end_date, $discount, $rule_type, $rule_data);

    if ($stmt->execute()) {
        header("Location: manage_promotions.php");
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>เพิ่มโปรโมชั่น</title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.php">ต้วงหมูกะทะ</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!">
            <i class="fas fa-bars"></i>
        </button>
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user fa-fw"></i>
                    <?= isset($_SESSION['username']) ? $_SESSION['username'] : 'ผู้เยี่ยมชม'; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="logout.php">ออกจากระบบ</a></li>
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
                        <a class="nav-link" href="category.php">
                            <div class="sb-nav-link-icon"><i class="fas fas fa-list"></i></div>
                            จัดการหมวดหมู่
                        </a>
                        <a class="nav-link" href="unit.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-balance-scale"></i></div>
                            จัดการหน่วย
                        </a>
                        <a class="nav-link" href="manage_tables.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                            จัดการโต๊ะ
                        </a>
                        <a class="nav-link" href="manage_orders.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-receipt"></i></div>
                            จัดการออเดอร์
                        </a>
                        <a class="nav-link" href="cashier.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-receipt"></i></div>
                            ชำระเงิน
                        </a>
                        <a class="nav-link" href="manage_promotions.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tags"></i></div>
                            จัดการโปรโมชั่น
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">เพิ่มโปรโมชั่น</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="manage_promotions.php">จัดการโปรโมชั่น</a></li>
                        <li class="breadcrumb-item active">เพิ่มโปรโมชั่น</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-body">
                            <form action="add_promotions.php" method="POST">
                                <div class="mb-3">
                                    <label for="name" class="form-label">ชื่อโปรโมชั่น</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">รายละเอียดโปรโมชั่น</label>
                                    <textarea class="form-control" id="description" name="description" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">วันที่เริ่ม</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                                </div>
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">วันที่สิ้นสุด</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                                </div>
                                <div class="mb-3">
                                    <label for="discount" class="form-label">ส่วนลด (%)</label>
                                    <input type="number" class="form-control" id="discount" name="discount" required>
                                </div>

                                <!-- ส่วนของ rule_type และ rule_data -->
                                <div class="mb-3">
                                    <label for="rule_type" class="form-label">ประเภทของโปรโมชั่น</label>
                                    <select class="form-control" id="rule_type" name="rule_type" required onchange="updateRuleDataFields()">
                                        <option value="quantity_discount">มา 4 จ่าย 3</option>
                                        <option value="birthday_discount">ส่วนลดวันเกิด</option>
                                    </select>
                                </div>

                                <!-- Dynamic input fields for rule_data -->
                                <div id="rule_data_fields">
                                    <div class="mb-3" id="quantity_discount_fields" style="display: none;">
                                        <label for="required_quantity" class="form-label">จำนวนลูกค้า:</label>
                                        <input type="number" class="form-control" id="required_quantity" name="required_quantity">
                                        <label for="free_quantity" class="form-label">จำนวนฟรี:</label>
                                        <input type="number" class="form-control" id="free_quantity" name="free_quantity">
                                    </div>
                                    <div class="mb-3" id="birthday_discount_fields" style="display: none;">
                                        <label for="required_quantity" class="form-label">จำนวนลูกค้า:</label>
                                        <input type="number" class="form-control" id="required_quantity" name="required_quantity">
                                        <label for="free_quantity" class="form-label">จำนวนฟรี:</label>
                                        <input type="number" class="form-control" id="free_quantity" name="free_quantity">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">เพิ่มโปรโมชั่น</button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Function to show/hide input fields based on rule_type selection
        function updateRuleDataFields() {
            const ruleType = document.getElementById('rule_type').value;
            document.getElementById('quantity_discount_fields').style.display = ruleType === 'quantity_discount' ? 'block' : 'none';
            document.getElementById('birthday_discount_fields').style.display = ruleType === 'birthday_discount' ? 'block' : 'none';
        }

        // Initialize fields visibility on page load
        updateRuleDataFields();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>
