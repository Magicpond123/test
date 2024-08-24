<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_GET['order_id'])) {
    echo "Error: Order ID not found.";
    exit();
}

$order_id = intval($_GET['order_id']);

// Prepare the SQL statement to fetch order details
$stmt = $conn->prepare("
    SELECT od.quantity, mi.name, mi.price, mi.image_path
    FROM orderdetails od
    JOIN menuitems mi ON od.item_id = mi.item_id
    WHERE od.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<h1>รายละเอียดการสั่งอาหาร</h1>";
    while ($row = $result->fetch_assoc()) {
        echo "<div class='order-item'>";
        echo "<h2>ชื่ออาหาร: " . htmlspecialchars($row['name']) . "</h2>";
        echo "<p>ราคา: " . htmlspecialchars($row['price']) . " บาท</p>";
        echo "<p>จำนวน: " . htmlspecialchars($row['quantity']) . "</p>";
        echo "<img src='" . htmlspecialchars($row['image_path']) . "' alt='" . htmlspecialchars($row['name']) . "' style='width: 200px; height: auto;'><br><br>";
        echo "</div>";
    }
} else {
    echo "ไม่มีข้อมูลการสั่งอาหารสำหรับ Order ID นี้";
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดการสั่งอาหาร</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 20px;
        }

        h1 {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            border-radius: 10px;
            border: 3px solid #388E3C;
            display: inline-block;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .order-item {
            background-color: #fff;
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        img {
            max-width: 100%;
            border-radius: 10px;
        }
    </style>
</head>
<body>

</body>
</html>
