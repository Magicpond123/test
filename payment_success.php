<?php
session_start();
if (!isset($_SESSION['success_message'])) {
    header("Location: manage_orders.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ชำระเงินสำเร็จ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-success">
            <?php echo $_SESSION['success_message']; ?>
        </div>
        <a href="bills.php?payment_id=<?php echo $_SESSION['payment_id']; ?>" class="btn btn-primary">ดูใบเสร็จ</a>
        <a href="manage_orders.php" class="btn btn-secondary">กลับไปหน้าจัดการออเดอร์</a>
    </div>
</body>
</html>
<?php
unset($_SESSION['success_message']);
unset($_SESSION['payment_id']);
?>