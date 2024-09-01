<?php
session_start();
include 'includes/db_connect.php';

// Get and validate orderbf_id
$order_buffet_id = $_GET['order_buffet_id'] ?? '';
if (!filter_var($orderbf_id, FILTER_VALIDATE_INT)) {
    die('Invalid or missing order_buffet_id.');
}
$order_buffet_id = (int) $order_buffet_id;

// Prepare SQL statement
$sql = "SELECT d.item_id, d.quantity, m.name, m.price, d.status
        FROM order_buffet_details d
        JOIN menuitems m ON d.item_id = m.item_id
        WHERE d.order_buffet_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $order_buffet_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the result is valid
if ($result === false) {
    die('Query failed: ' . htmlspecialchars($stmt->error));
}

// Status options
$status_options = [
    1 => 'รอดำเนินการ',
    2 => 'กำลังดำเนินการ',
    3 => 'จัดส่งแล้ว',
    4 => 'ยกเลิก',
];

// Handle status update
if (isset($_POST['update_status'])) {
    if (!isset($_POST['item_id'], $_POST['status']) || !filter_var($_POST['item_id'], FILTER_VALIDATE_INT)) {
        die('Invalid input.');
    }
    $item_id = (int) $_POST['item_id'];
    $new_status = (int) $_POST['status'];

    if (!array_key_exists($new_status, $status_options)) {
        die('Invalid status value.');
    }

    $update_sql = "UPDATE order_buffet_details SET status = ? WHERE order_buffet_id = ? AND item_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    if ($update_stmt === false) {
        die('Update prepare failed: ' . htmlspecialchars($conn->error));
    }
    $update_stmt->bind_param("sii", $new_status, $order_buffet_id, $item_id);
    if ($update_stmt->execute()) {
        echo "<script>alert('สถานะออเดอร์ถูกอัปเดตแล้ว');</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตสถานะ');</script>";
    }
    $update_stmt->close();

    header("Location: order_details.php?orderbf_id=" . $order_buffet_id);
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
        <div class="card">
            <div class="card-header">
                รายละเอียดออเดอร์ #<?php echo htmlspecialchars($order_buffet_id); ?>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ชื่อเมนู</th>
                            <th>จำนวน</th>
                            <th>ราคา</th>
                            <th>รวม</th>
                            <th>สถานะ</th>
                            <th>อัปเดตสถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) {
                            $status = $row['status'];
                            $status_label = isset($status_options[$status]) ? $status_options[$status] : 'Unknown Status';
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                                <td><?php echo htmlspecialchars(number_format($row['price'], 2)); ?></td>
                                <td><?php echo number_format($row['price'] * $row['quantity'], 2); ?></td>
                                <td><?php echo htmlspecialchars($status_label); ?></td>
                                <td>
                                    <form action="order_details.php?order_buffet_id_id=<?php echo htmlspecialchars($order_buffet_id); ?>" method="POST">
                                        <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($row['item_id']); ?>">
                                        <select name="status" class="form-select">
                                            <?php foreach ($status_options as $value => $label) { ?>
                                                <option value="<?php echo htmlspecialchars($value); ?>" <?php echo $status == $value ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($label); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-primary mt-2">อัปเดต</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <a href="manage_orders.php" class="btn btn-primary back-button">กลับไปหน้าจัดการออเดอร์</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>