<?php
session_start();
include 'includes/db_connect.php'; // เชื่อมต่อกับฐานข้อมูล

// ตรวจสอบว่ามีการส่งค่า ID มาหรือไม่
if (isset($_GET['id'])) {
    $order_id = intval($_GET['id']);

    // ลบข้อมูลออเดอร์จากฐานข้อมูล
    $stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        // ลบสำเร็จ เปลี่ยนเส้นทางกลับไปยัง manage_orders.php
        header("Location: manage_orders.php?message=Order deleted successfully");
        exit();
    } else {
        // ลบไม่สำเร็จ แสดงข้อผิดพลาด
        echo "Error deleting order: " . $stmt->error;
    }

    $stmt->close();
} else {
    // หากไม่มีการส่ง ID มา แสดงข้อผิดพลาด
    echo "Error: ID not found.";
}
?>
