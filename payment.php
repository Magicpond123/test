<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าชำระเงิน (Cashier)</title>
    <link rel="stylesheet" href="css/payment.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>ระบบชำระเงิน</h1>
            <div class="cashier-info">
                <p>พนักงาน: <strong>ชื่อพนักงาน</strong></p>
                <p>วันที่: <strong><?php echo date('d/m/Y H:i:s'); ?></strong></p>
            </div>
        </header>

        <!-- เลือกประเภทการสั่งซื้อ -->
        <section class="order-type">
            <h2>เลือกประเภทการสั่งซื้อ</h2>
            <div>
                <button class="btn-order-type" onclick="showBuffet()">บุฟเฟต์</button>
                <button class="btn-order-type" onclick="showTakeaway()">สั่งกลับบ้าน</button>
            </div>
        </section>

        <!-- ส่วนรายการบุฟเฟต์ -->
        <section id="buffet-section" class="order-summary" style="display: none;">
            <h2>บุฟเฟต์</h2>
            <div>
                <label for="adults">จำนวนผู้ใหญ่:</label>
                <input type="number" id="adults" name="adults" min="1" value="1" onchange="calculateBuffetTotal()">
                <label for="children">จำนวนเด็ก:</label>
                <input type="number" id="children" name="children" min="0" value="0" onchange="calculateBuffetTotal()">
            </div>
            <div>
                <p>รวมผู้ใหญ่: <span id="adult-total">0 บาท</span></p>
                <p>รวมเด็ก: <span id="child-total">0 บาท</span></p>
            </div>
            <p>ยอดรวม: <strong id="buffet-total">0 บาท</strong></p>
        </section>

        <!-- ส่วนรายการสั่งกลับบ้าน -->
        <section id="takeaway-section" class="order-summary" style="display: none;">
            <h2>สั่งกลับบ้าน</h2>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>ชื่อสินค้า</th>
                        <th>จำนวน</th>
                        <th>ราคา/หน่วย</th>
                        <th>รวม</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>หมูสามชั้น</td>
                        <td>
                            <button class="btn-quantity" onclick="updateQuantity(-1)">-</button>
                            <span>1</span>
                            <button class="btn-quantity" onclick="updateQuantity(1)">+</button>
                        </td>
                        <td>100 บาท</td>
                        <td>100 บาท</td>
                    </tr>
                </tbody>
            </table>
            <p>ยอดรวม: <strong id="takeaway-total">100 บาท</strong></p>
        </section>

        <!-- ส่วนการชำระเงิน -->
        <section class="payment-section">
            <h2>ชำระเงิน</h2>
            <div class="payment-method">
                <label>วิธีการชำระเงิน:</label>
                <select>
                    <option value="cash">เงินสด</option>
                    <option value="credit">บัตรเครดิต/เดบิต</option>
                    <option value="qr">QR Code</option>
                </select>
            </div>
            <div class="total-amount">
                <p>รวมทั้งหมด: <strong id="final-total">100 บาท</strong></p>
            </div>

            <div class="actions">
                <button class="btn-confirm">ยืนยันการชำระเงิน</button>
                <button class="btn-cancel">ยกเลิกรายการ</button>
            </div>
        </section>
    </div>

    <script>
        function showBuffet() {
            document.getElementById('buffet-section').style.display = 'block';
            document.getElementById('takeaway-section').style.display = 'none';
        }

        function showTakeaway() {
            document.getElementById('buffet-section').style.display = 'none';
            document.getElementById('takeaway-section').style.display = 'block';
        }

        function calculateBuffetTotal() {
            let adults = document.getElementById('adults').value;
            let children = document.getElementById('children').value;

            let adultTotal = adults * 149;
            let childTotal = children * 99;

            document.getElementById('adult-total').textContent = adultTotal + ' บาท';
            document.getElementById('child-total').textContent = childTotal + ' บาท';
            document.getElementById('buffet-total').textContent = (adultTotal + childTotal) + ' บาท';
        }
    </script>
</body>
</html>
