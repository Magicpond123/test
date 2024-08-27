<?php
session_start();
include 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id = $_POST['item_id'];
    $quantity = intval($_POST['quantity']);

    // ตรวจสอบว่ามีตะกร้าใน session หรือไม่
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // ตรวจสอบว่าสินค้าอยู่ในตะกร้าแล้วหรือยัง ถ้ามีให้เพิ่มจำนวน
    if (isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$item_id] = ['quantity' => $quantity];
    }

    header("Location: cart_pickup.php");
    exit();
}

// โค้ดสำหรับการแสดงสินค้าที่อยู่ในตะกร้าและการดำเนินการอื่น ๆ
?>
