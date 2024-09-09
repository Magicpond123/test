<?php
session_start();
include 'includes/db_connect.php';

// รับค่า bill_id จาก URL
$bill_id = $_GET['bill_id'];

// ดึงข้อมูลบิลจากฐานข้อมูลรวมถึงโปรโมชั่น
$sql_bill = "SELECT b.*, t.table_number, p.total_amount as payment_total, p.payment_time, p.promotion_id, pr.name as promotion_name, pr.discount 
             FROM bills b 
             JOIN tables t ON b.table_id = t.table_id
             JOIN payments p ON b.payment_id = p.payment_id
             LEFT JOIN promotions pr ON p.promotion_id = pr.promotion_id
             WHERE b.bill_id = ?";
$stmt_bill = $conn->prepare($sql_bill);
$stmt_bill->bind_param("i", $bill_id);
$stmt_bill->execute();
$result_bill = $stmt_bill->get_result();

if ($result_bill->num_rows == 0) {
    echo "ไม่พบข้อมูลบิล";
    exit();
}

$bill = $result_bill->fetch_assoc();

// คำนวณส่วนลด
$discount = 0;
if ($bill['promotion_id']) {
    $discount = $bill['discount'];
    $discount_amount = $bill['total_amount'] * ($discount / 100);
    $final_total = $bill['total_amount'] - $discount_amount;
} else {
    $final_total = $bill['total_amount'];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบเสร็จชำระเงิน</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .bill-header {
            text-align: center;
            margin-top: 20px;
        }
        .bill-container {
            margin: 50px auto;
            width: 60%;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        .bill-info {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="bill-container">
    <div class="bill-header">
        <h1>ร้านต้วงหมูกระทะ</h1>
        <p>ใบเสร็จชำระเงิน</p>
    </div>

    <div class="bill-info">
        <p><strong>หมายเลขโต๊ะ:</strong> <?php echo htmlspecialchars($bill['table_number']); ?></p>
        <p><strong>วันที่:</strong> <?php echo htmlspecialchars($bill['bill_date']); ?></p>
        <p><strong>ราคารวมก่อนส่วนลด:</strong> <?php echo number_format($bill['total_amount'], 2); ?> บาท</p>
        <?php if ($bill['promotion_id']): ?>
            <p><strong>โปรโมชั่นที่ใช้:</strong> <?php echo htmlspecialchars($bill['promotion_name']); ?> (<?php echo htmlspecialchars($discount); ?>%)</p>
            <p><strong>ส่วนลด:</strong> <?php echo number_format($discount_amount, 2); ?> บาท</p>
        <?php endif; ?>
        <p><strong>ราคารวมหลังส่วนลด:</strong> <?php echo number_format($final_total, 2); ?> บาท</p>
        <p><strong>วิธีการชำระเงิน:</strong> <?php echo htmlspecialchars($bill['payment_method']); ?></p>
    </div>

    <hr>

    <div class="text-center">
        <p>ขอบคุณที่ใช้บริการค่ะ</p>
    </div>
</div>

</body>
</html>
