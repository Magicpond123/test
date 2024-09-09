<?php
session_start();
include 'includes/db_connect.php';

// รับค่า order_id จาก URL
$order_id = $_GET['order_id'];

// ดึงข้อมูลออเดอร์จากฐานข้อมูลพร้อมหมายเลขโต๊ะ
$sql_order = "SELECT o.order_buffet_id, o.table_id, o.adult, o.child, o.order_date, t.table_number
              FROM order_buffet o
              JOIN tables t ON o.table_id = t.table_id
              WHERE o.order_buffet_id = ?";
$stmt_order = $conn->prepare($sql_order);
$stmt_order->bind_param("i", $order_id);  // Bind as integer
$stmt_order->execute();
$result_order = $stmt_order->get_result();
$order = $result_order->fetch_assoc();

// ดึงรายการอาหารจากฐานข้อมูลพร้อมชื่อและราคา
$sql_food_items = "SELECT bd.order_buffet_detail_id, bd.item_id, bd.quantity, bd.status, mi.name, mi.price
                   FROM order_buffet_details bd
                   JOIN menuitems mi ON bd.item_id = mi.item_id
                   WHERE bd.order_buffet_id = ?";
$stmt_food_items = $conn->prepare($sql_food_items);
$stmt_food_items->bind_param("i", $order_id);  // Bind as integer
$stmt_food_items->execute();
$result_food_items = $stmt_food_items->get_result();

// ดึงโปรโมชั่นที่ใช้ได้
$sql_promotions = "SELECT * FROM promotions WHERE start_date <= CURDATE() AND end_date >= CURDATE()";
$result_promotions = $conn->query($sql_promotions);

// คำนวณราคารวมสำหรับอาหาร
$total_food_price = 0;
$food_items = []; // สร้างอาเรย์เพื่อเก็บรายการอาหาร
while ($food_item = $result_food_items->fetch_assoc()) {
    $total_food_price += $food_item['price'] * $food_item['quantity'];
    $food_items[] = $food_item;  // เก็บรายการอาหารในอาเรย์
}

// คำนวณราคารวมสำหรับผู้ใหญ่และเด็ก
$adult_price = 149; // ราคาผู้ใหญ่
$child_price = 99;  // ราคาเด็ก
$total_people_price = ($order['adult'] * $adult_price) + ($order['child'] * $child_price);

// ราคารวมทั้งหมดก่อนส่วนลด
$total_price = $total_food_price + $total_people_price;

// ตรวจสอบว่าผู้ใช้เลือกโปรโมชั่นหรือไม่
$discount = 0;
$discount_amount = 0;
if (isset($_POST['promotion_id'])) {
    $promotion_id = $_POST['promotion_id'];

    // ดึงข้อมูลโปรโมชั่นที่เลือกมาใช้
    if ($promotion_id) {
        // ดีบัก promotion_id
        echo "<p>Promotion ID: $promotion_id</p>";

        $sql_promotion = "SELECT * FROM promotions WHERE promotion_id = ?";
        $stmt_promotion = $conn->prepare($sql_promotion);
        $stmt_promotion->bind_param("i", $promotion_id);
        $stmt_promotion->execute();
        $promotion = $stmt_promotion->get_result()->fetch_assoc();

        // ดีบัก discount ที่ได้จากฐานข้อมูล
        echo "<p>Discount from DB: " . $promotion['discount'] . "%</p>";

        // คำนวณส่วนลด
        $discount = $promotion['discount'];
        $discount_amount = $total_price * ($discount / 100);
        $total_price -= $discount_amount;

        // ดีบัก discount_amount และ total_price หลังจากคำนวณ
        echo "<p>Discount Amount: " . number_format($discount_amount, 2) . " บาท</p>";
        echo "<p>Total Price After Discount: " . number_format($total_price, 2) . " บาท</p>";
    }
}

// ประมวลผลข้อมูลเมื่อมีการกดปุ่มชำระเงิน
if (isset($_POST['payment'])) {
    $payment_method = $_POST['payment_method'];

    // บันทึกการชำระเงินในตาราง payments
    $conn->begin_transaction();

    try {
        // บันทึกข้อมูลการชำระเงินในตาราง payments
        $sql_payment = "INSERT INTO payments (order_id, order_type, payment_time, total_amount, payment_status, promotion_id) 
                        VALUES (?, ?, NOW(), ?, ?, ?)";
        $stmt_payment = $conn->prepare($sql_payment);
        $order_type = 1; // ประเภทออเดอร์บุฟเฟต์
        $payment_status = 1; // 1 หมายถึง ชำระเงินแล้ว
        $stmt_payment->bind_param("iidii", $order_id, $order_type, $total_price, $payment_status, $promotion_id);
        $stmt_payment->execute();
        $payment_id = $stmt_payment->insert_id;  // เก็บค่า payment_id เพื่อใช้บันทึกบิล

        // บันทึกข้อมูลบิลในตาราง bills
        $sql_bill = "INSERT INTO bills (payment_id, order_id, total_amount, payment_method, promotion_id, table_id) 
                     VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_bill = $conn->prepare($sql_bill);
        $stmt_bill->bind_param("iidisi", $payment_id, $order_id, $total_price, $payment_method, $promotion_id, $order['table_id']);
        $stmt_bill->execute();
        $bill_id = $stmt_bill->insert_id;

        // อัปเดตสถานะโต๊ะเป็นพร้อมใช้งาน (table_status = 1)
        $sql_update_table = "UPDATE tables SET table_status = 1 WHERE table_id = ?";
        $stmt_update_table = $conn->prepare($sql_update_table);
        $stmt_update_table->bind_param("i", $order['table_id']);
        $stmt_update_table->execute();

        // เสร็จสิ้นการทำธุรกรรม
        $conn->commit();

        $_SESSION['success_message'] = "ชำระเงินเรียบร้อยแล้ว!";
        header("Location: bills.php?bill_id=$bill_id");  // ไปที่หน้า bills.php พร้อมส่ง bill_id
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการชำระเงิน: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดออเดอร์</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/cashier_details.css">
</head>

<body>
    <div class="container mt-4">
        <!-- แสดงข้อความสถานะ -->
        <div class="mb-4">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['error_message'];
                    unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- ข้อมูลออเดอร์ -->
        <div class="mb-4">
            <h3>ข้อมูลออเดอร์</h3>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="table_id" class="form-label">หมายเลขโต๊ะ</label>
                    <input type="text" id="table_id" class="form-control" value="<?php echo htmlspecialchars($order['table_number']); ?>" readonly>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="ad" class="form-label">จำนวนผู้ใหญ่</label>
                    <input type="number" id="ad" class="form-control" value="<?php echo htmlspecialchars($order['adult']); ?>" readonly>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="hc" class="form-label">จำนวนเด็ก</label>
                    <input type="number" id="hc" class="form-control" value="<?php echo htmlspecialchars($order['child']); ?>" readonly>
                </div>
            </div>
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
                        <th>ราคา/หน่วย</th>
                        <th>รวม</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($food_items as $food_item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($food_item['item_id']); ?></td>
                            <td><?php echo htmlspecialchars($food_item['name']); ?></td>
                            <td><?php echo htmlspecialchars($food_item['quantity']); ?></td>
                            <td><?php echo number_format($food_item['price'], 2); ?> บาท</td>
                            <td><?php echo number_format($food_item['price'] * $food_item['quantity'], 2); ?> บาท</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- ราคารวม -->
        <div class="total-price-section mb-4">
            <h4 class="total-price-title">ราคารวมทั้งหมด</h4>
            <p class="total-food-price">ราคารวมอาหาร: <?php echo number_format($total_food_price, 2); ?> บาท</p>
            <p class="total-people-price">ราคารวมสำหรับผู้ใหญ่และเด็ก: <?php echo number_format($total_people_price, 2); ?> บาท</p>
            <p class="total-price-value">รวมทั้งสิ้น: <?php echo number_format($total_price, 2); ?> บาท</p>
            <?php if ($discount_amount > 0): ?>
                <p class="total-discount">ส่วนลด: <?php echo number_format($discount_amount, 2); ?> บาท</p>
                <p class="total-price-after-discount">ราคารวมหลังส่วนลด: <?php echo number_format($total_price, 2); ?> บาท</p>
            <?php endif; ?>
        </div>

        <!-- เลือกโปรโมชั่น -->
        <div class="mb-4">
            <h3>เลือกโปรโมชั่น:</h3>
            <form method="post" action="">
                <select name="promotion_id" class="form-select mb-3" onchange="this.form.submit()">
                    <option value="">ไม่ใช้โปรโมชั่น</option>
                    <?php while ($promotion = $result_promotions->fetch_assoc()) { ?>
                        <option value="<?php echo $promotion['promotion_id']; ?>" <?php echo isset($_POST['promotion_id']) && $_POST['promotion_id'] == $promotion['promotion_id'] ? 'selected' : ''; ?>>
                            <?php echo $promotion['name'] . " (ส่วนลด " . $promotion['discount'] . "%)"; ?>
                        </option>
                    <?php } ?>
                </select>

                <!-- แสดงราคาหลังจากเลือกโปรโมชั่น -->
                <?php if (isset($discount)): ?>
                    <div class="alert alert-info">
                        โปรโมชั่น: ลด <?php echo $discount; ?>% <br>
                        ราคารวมหลังส่วนลด: <?php echo number_format($total_price, 2); ?> บาท
                    </div>
                <?php endif; ?>

                <!-- เลือกวิธีชำระเงิน -->
                <h3>เลือกวิธีการชำระเงิน:</h3>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="cash" value="cash" required onclick="toggleQRCode(false)">
                    <label class="form-check-label" for="cash">เงินสด</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_method" id="qr" value="qr" onclick="toggleQRCode(true)">
                    <label class="form-check-label" for="qr">QR Code พร้อมเพย์</label>
                </div>
                <button type="submit" name="payment" class="btn btn-success mt-4">ยืนยันการชำระเงิน</button>
            </form>
        </div>

        <!-- แสดง QR Code เมื่อเลือกชำระด้วยพร้อมเพย์ -->
        <div class="qr-code-section mt-4" id="qr-code-section" style="display: none;">
            <h3>สแกน QR Code เพื่อชำระเงิน:</h3>
            <img src="qrcodes/pp.jfif" alt="QR Code" class="img-fluid">
        </div>
    </div>

    <script>
        // ฟังก์ชันสำหรับแสดงหรือซ่อน QR Code
        function toggleQRCode(show) {
            var qrSection = document.getElementById('qr-code-section');
            if (show) {
                qrSection.style.display = 'block'; // แสดง QR Code
            } else {
                qrSection.style.display = 'none'; // ซ่อน QR Code
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>