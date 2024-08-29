<?php
session_start();
include 'includes/db_connect.php';

$orderbf_id = $_GET['orderbf_id'];

$sql = "SELECT d.item_id, d.quantity, m.name, m.price, d.status
        FROM order_bd d
        JOIN menuitems m ON d.item_id = m.item_id
        WHERE d.orderbf_id = ?";
$stmt = $conn->prepare($sql);

// Check if the preparation was successful
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $orderbf_id);
$stmt->execute();
$result = $stmt->get_result();

$status_options = [
    1 => 'รอดำเนินการ',
    2 => 'กำลังดำเนินการ',
    3 => 'จัดส่งแล้ว',
    4 => 'ยกเลิก',
];

if (isset($_POST['update_status'])) {
    $item_id = $_POST['item_id'];
    $new_status = $_POST['status'];
    $update_sql = "UPDATE order_bd SET status = ? WHERE orderbf_id = ? AND item_id = ?";
    $update_stmt = $conn->prepare($update_sql);

    // Check if the preparation of the update statement was successful
    if ($update_stmt === false) {
        die('Update prepare failed: ' . htmlspecialchars($conn->error));
    }

    $update_stmt->bind_param("sii", $new_status, $orderbf_id, $item_id);
    if ($update_stmt->execute()) {
        echo "<script>alert('สถานะออเดอร์ถูกอัปเดตแล้ว');</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตสถานะ');</script>";
    }
    $update_stmt->close();
    
    // Reload the page to see the updated status
    header("Location: order_details.php?orderbf_id=" . $orderbf_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>รายละเอียดออเดอร์ #<?php echo $orderbf_id; ?></h2>
        <table class="table table-bordered">
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
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['price']); ?></td>
                        <td><?php echo number_format($row['price'] * $row['quantity'], 2); ?></td>
                        <td><?php echo htmlspecialchars($status_options[$row['status']]); ?></td>
                        <td>
                            <form action="order_details.php?orderbf_id=<?php echo $orderbf_id; ?>" method="POST">
                                <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                                <select name="status" class="form-select">
                                    <?php foreach ($status_options as $value => $label) { ?>
                                        <option value="<?php echo $value; ?>" <?php echo $row['status'] == $value ? 'selected' : ''; ?>>
                                            <?php echo $label; ?>
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
        <a href="manage_orders.php" class="btn btn-primary">กลับไปหน้าจัดการออเดอร์</a>
    </div>
</body>

</html>
