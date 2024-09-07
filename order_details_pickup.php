<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_GET['order_pickup_id'])) {
    die('Error: order_pickup_id is missing from the URL.');
}

$order_pickup_id = $_GET['order_pickup_id'];

// Prepare SQL statement
$sql = "SELECT d.item_id, d.quantity, m.name, m.price
        FROM order_pickup_details d
        JOIN menuitems m ON d.item_id = m.item_id
        WHERE d.order_pickup_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $order_pickup_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the result is valid
if ($result === false) {
    die('Query failed: ' . htmlspecialchars($stmt->error));
}

// Handle quantity update
if (isset($_POST['update_quantity'])) {
    $item_id = $_POST['item_id'];
    $new_quantity = $_POST['quantity'];
    $update_sql = "UPDATE order_pickup_details SET quantity = ? WHERE order_pickup_id = ? AND item_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    if ($update_stmt === false) {
        die('Update prepare failed: ' . htmlspecialchars($conn->error));
    }

    $update_stmt->bind_param("iii", $new_quantity, $order_pickup_id, $item_id);
    if ($update_stmt->execute()) {
        echo "<script>alert('จำนวนสินค้าถูกอัปเดตแล้ว');</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตจำนวนสินค้า');</script>";
    }
    $update_stmt->close();
    
    // Reload the page to see the updated quantity
    header("Location: order_details_pickup.php?order_pickup_id=" . $order_pickup_id);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/order_details.css"> <!-- ลิงก์ไปยังไฟล์ CSS -->
</head>
<body>
    <div class="container mt-5">
        <h2>รายละเอียดออเดอร์ #<?php echo $order_pickup_id; ?></h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ชื่อเมนู</th>
                    <th>จำนวน</th>
                    <th>ราคา</th>
                    <th>รวม</th>
                    <th>อัปเดตจำนวน</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                        <td><?php echo number_format($row['price'] * $row['quantity'], 2); ?></td>
                        <td>
                            <form action="order_details_pickup.php?order_pickup_id=<?php echo $order_pickup_id; ?>" method="POST">
                                <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                                <input type="number" name="quantity" value="<?php echo htmlspecialchars($row['quantity']); ?>" min="1" class="form-control">
                                <button type="submit" name="update_quantity" class="btn btn-success mt-2">อัปเดต</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <a href="manage_orders.php" class="btn btn-primary">กลับไปหน้าจัดการออเดอร์</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
