<?php
session_start();
include 'includes/db_connect.php';

// ดึงข้อมูลออเดอร์ในร้าน
$sql_orders = "SELECT o.order_buffet_id, o.table_id, o.adult, o.child, o.order_date 
               FROM order_buffet o";
$result_orders = $conn->query($sql_orders);

// ตรวจสอบการทำงานของคำสั่ง SQL
if (!$result_orders) {
    die("Error executing query: " . $conn->error);
}

// ประมวลผลข้อมูลเมื่อมีการส่งฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_customers'])) {
        // รับข้อมูลจากฟอร์ม
        $order_id = $_POST['order_id'];
        $ad = $_POST['ad'];
        $hc = $_POST['hc'];

        // อัปเดตข้อมูลในฐานข้อมูล
        $sql_update = "UPDATE order_buffet SET adult = '$ad', child = '$hc' WHERE order_buffet_id = '$order_id'";
        if ($conn->query($sql_update) === TRUE) {
            $_SESSION['success_message'] = "อัปเดตข้อมูลสำเร็จ!";
        } else {
            $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $conn->error;
        }
        header("Location: cashier.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการออเดอร์ภายในร้าน</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/cashier.css">
    <script>
        function goToOrderDetails(orderId) {
            window.location.href = 'cashier_details.php?order_id=' + orderId;
        }
    </script>
</head>

<body>
    <div class="container mt-4">
        <h1 class="text-center">จัดการออเดอร์ภายในร้าน</h1>

        <!-- แสดงข้อความสถานะ -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <div class="row mt-4">
            <?php while ($row = $result_orders->fetch_assoc()) { ?>
                <div class="col-md-3 mb-4">
                    <div class="table-card" onclick="goToOrderDetails('<?php echo $row['order_buffet_id']; ?>')">
                        <h3>โต๊ะ <?php echo $row['table_id']; ?></h3>
                        <p>จำนวนผู้ใหญ่: <?php echo $row['adult']; ?></p>
                        <p>จำนวนเด็ก: <?php echo $row['child']; ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
