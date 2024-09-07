<?php
session_start();
include 'includes/db_connect.php';

// รับค่า order_pickup_id จาก URL
$order_pickup_id = $_GET['order_pickup_id'];

// ดึงข้อมูลออเดอร์จากฐานข้อมูล
$sql_order = "SELECT o.order_pickup_id, o.emp_id, o.order_date
              FROM order_pickup o
              WHERE o.order_pickup_id = '$order_pickup_id'";
$result_order = $conn->query($sql_order);

// ตรวจสอบว่าคำสั่ง SQL ดำเนินการสำเร็จ
if (!$result_order) {
    die("Error: " . $conn->error);
}

$order = $result_order->fetch_assoc();

// ดึงรายการอาหารจากฐานข้อมูลพร้อมชื่อและราคา
$sql_food_items = "SELECT pd.order_pickup_detail_id, pd.item_id, pd.quantity, mi.name, mi.price
                   FROM order_pickup_details pd
                   JOIN menuitems mi ON pd.item_id = mi.item_id
                   WHERE pd.order_pickup_id = '$order_pickup_id'";
$result_food_items = $conn->query($sql_food_items);

// ตรวจสอบว่าคำสั่ง SQL ดำเนินการสำเร็จ
if (!$result_food_items) {
    die("Error: " . $conn->error);
}

// คำนวณราคารวมสำหรับอาหาร
$total_food_price = 0;
while ($food_item = $result_food_items->fetch_assoc()) {
    $total_food_price += $food_item['price'] * $food_item['quantity'];
}

// รีเซ็ตการค้นหารายการอาหารเพื่อให้แสดงอีกครั้งในหน้า HTML
$result_food_items->data_seek(0);

// ประมวลผลข้อมูลเมื่อมีการส่งฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['cancel_food'])) {
        $order_pickup_detail_id = $_POST['order_pickup_detail_id'];
        $sql_cancel = "DELETE FROM order_pickup_details WHERE order_pickup_detail_id = '$order_pickup_detail_id' AND order_pickup_id = '$order_pickup_id'";
        if ($conn->query($sql_cancel) === TRUE) {
            $_SESSION['success_message'] = "ยกเลิกรายการอาหารสำเร็จ!";
            header("Location: cashier_details_pickup.php?order_pickup_id=$order_pickup_id");
            exit();
        } else {
            $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการยกเลิกรายการอาหาร: " . $conn->error;
        }
    }

    if (isset($_POST['update_food_quantity'])) {
        $order_pickup_detail_id = $_POST['order_pickup_detail_id'];
        $quantity = $_POST['quantity'];
        $sql_update_quantity = "UPDATE order_pickup_details SET quantity = '$quantity' WHERE order_pickup_detail_id = '$order_pickup_detail_id' AND order_pickup_id = '$order_pickup_id'";
        if ($conn->query($sql_update_quantity) === TRUE) {
            $_SESSION['success_message'] = "อัปเดตจำนวนอาหารสำเร็จ!";
            header("Location: cashier_details_pickup.php?order_pickup_id=$order_pickup_id");
            exit();
        } else {
            $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการอัปเดตจำนวนอาหาร: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดออเดอร์สั่งกลับบ้าน</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/cashier_details.css">

</head>

<body>
    <div class="container mt-4">
        <!-- ลิงก์ไปยังหน้าอื่น -->
        <a href="cashier.php" class="btn btn-secondary mt-4">กลับสู่หน้าแรก</a>
        <h1 class="text-center">รายละเอียดออเดอร์สั่งกลับบ้าน</h1>

        <!-- แสดงข้อความสถานะ -->
        <div class="mb-4">
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
        </div>

        <!-- ข้อมูลออเดอร์ -->
        <div class="mb-4">
            <h3>ข้อมูลออเดอร์</h3>
            <form id="orderForm">
                <input type="hidden" name="order_pickup_id" value="<?php echo htmlspecialchars($order['order_pickup_id']); ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="emp_id" class="form-label">พนักงาน ID</label>
                        <input type="text" id="emp_id" class="form-control" value="<?php echo htmlspecialchars($order['emp_id']); ?>" readonly>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="order_date" class="form-label">วันที่</label>
                        <input type="text" id="order_date" class="form-control" value="<?php echo htmlspecialchars($order['order_date']); ?>" readonly>
                    </div>
                </div>
            </form>
        </div>

        <!-- รายการอาหาร -->
        <div class="mb-4">
            <h3>รายการอาหาร</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>รหัสอาหาร</th>
                        <th>ชื่ออาหาร</th>
                        <th>จำนวน</th>
                        <th>ราคา</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($food_item = $result_food_items->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($food_item['item_id']); ?></td>
                            <td><?php echo htmlspecialchars($food_item['name']); ?></td>
                            <td><?php echo htmlspecialchars($food_item['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($food_item['price']); ?></td>
                            <td>
                                <!-- ปุ่มแก้ไขจำนวน -->
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editQuantityModal" 
                                        data-orderpickup-detail-id="<?php echo htmlspecialchars($food_item['order_pickup_detail_id']); ?>"
                                        data-current-quantity="<?php echo htmlspecialchars($food_item['quantity']); ?>">
                                    แก้ไขจำนวน
                                </button>
                                <!-- ปุ่มลบรายการอาหาร -->
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="order_pickup_detail_id" value="<?php echo htmlspecialchars($food_item['order_pickup_detail_id']); ?>">
                                    <button type="submit" name="cancel_food" class="btn btn-danger">ลบ</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- ราคารวม -->
        <div class="mb-4">
            <h4>ราคารวมอาหาร: <?php echo number_format($total_food_price, 2); ?> บาท</h4>
        </div>

        <!-- Modal for editing food quantity -->
        <div class="modal fade" id="editQuantityModal" tabindex="-1" aria-labelledby="editQuantityModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editQuantityModalLabel">แก้ไขจำนวนอาหาร</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <input type="hidden" name="order_pickup_detail_id" id="modal_order_pickup_detail_id">
                            <div class="mb-3">
                                <label for="modal_quantity" class="form-label">จำนวน</label>
                                <input type="number" name="quantity" id="modal_quantity" class="form-control">
                            </div>
                            <button type="submit" name="update_food_quantity" class="btn btn-primary">อัปเดต</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Container for the pay button -->
        <div class="pay-button-container">
            <form method="post">
                <button type="submit" name="pay" class="btn btn-success">จ่ายเงิน</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set the data for the Edit Quantity modal
        var editQuantityModal = document.getElementById('editQuantityModal');
        editQuantityModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var orderPickupDetailId = button.getAttribute('data-orderpickup-detail-id');
            var currentQuantity = button.getAttribute('data-current-quantity');
            var modalOrderPickupDetailId = editQuantityModal.querySelector('#modal_order_pickup_detail_id');
            var modalQuantity = editQuantityModal.querySelector('#modal_quantity');
            modalOrderPickupDetailId.value = orderPickupDetailId;
            modalQuantity.value = currentQuantity;
        });
    </script>
</body>

</html>
