<?php
include 'phpqrcode/qrlib.php';

$table_id = 3;
$url = "http://localhost/test/menu_order.php?table_id=3" . $table_id;
$filename = "qrcodes/table_$table_id.png";

// สร้างโฟลเดอร์ qrcodes ถ้าไม่มี
if (!file_exists('qrcodes')) {
    mkdir('qrcodes', 0755, true);
}

// สร้าง QR Code และบันทึกเป็นไฟล์ PNG
QRcode::png($url, $filename);

// แสดง QR Code
echo "<h2>QR Code สำหรับโต๊ะ $table_id</h2>";
echo "<img src='$filename' alt='QR Code for table $table_id'>";
?>
