<?php
session_start();
include 'includes/db_connect.php';

date_default_timezone_set('Asia/Bangkok');

if (!isset($_SESSION['cart_buffet'])) {
    $_SESSION['cart_buffet'] = [];
}

// Remove item from buffet cart
if (isset($_POST['remove'])) {
    $itemToRemove = htmlspecialchars($_POST['remove']);
    if (isset($_SESSION['cart_buffet'][$itemToRemove])) {
        unset($_SESSION['cart_buffet'][$itemToRemove]);
    }
}

// Update item quantity in buffet cart
if (isset($_POST['update_quantity'])) {
    $item_id = htmlspecialchars($_POST['item_id']);
    $new_quantity = (int) $_POST['quantity'];
    if (isset($_SESSION['cart_buffet'][$item_id])) {
        if ($new_quantity <= 0) {
            unset($_SESSION['cart_buffet'][$item_id]);
        } else {
            $_SESSION['cart_buffet'][$item_id]['quantity'] = $new_quantity;
        }
    }
}

// Complete buffet order
if (isset($_POST['action']) && $_POST['action'] === 'complete_order') {
    $orderSuccess = true;
    $conn->begin_transaction(); // Start transaction
    try {
        $table_id = 2;
        $emp_id = 2;
        $order_date = date('Y-m-d H:i:s');
        $adults = $_SESSION['adults'];
        $children = $_SESSION['children'];
        $price_adults = $_SESSION['price_adults'];
        $price_children = $_SESSION['price_children'];

        // Insert into order_buffet table
        $stmt = $conn->prepare("INSERT INTO order_buffet (table_id, emp_id, order_date, ad, hc, price_ad, price_ch) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("iisiiii", $table_id, $emp_id, $order_date, $adults, $children, $price_adults, $price_children);
        if (!$stmt->execute()) {
            throw new Exception("Buffet order insertion failed: " . $stmt->error);
        }
        $orderbf_id = $stmt->insert_id;
        $stmt->close();
        
        foreach ($_SESSION['cart_buffet'] as $item_id => $details) {
            $status = 1; // Set status to 1 for all items
            $stmt = $conn->prepare("INSERT INTO order_bd (orderbf_id, item_id, quantity, status) VALUES (?, ?, ?, ?)");
            if ($stmt === false) {
                die('Prepare failed: ' . htmlspecialchars($conn->error));
            }
            $quantity = $details['quantity'];
            $stmt->bind_param("iiii", $orderbf_id, $item_id, $quantity, $status);
            if (!$stmt->execute()) {
                throw new Exception("Buffet order details insertion failed: " . $stmt->error);
            }
            $stmt->close();
        }

        // Clear the buffet cart and session variables
        $_SESSION['cart_buffet'] = [];
        $_SESSION['adults'] = 1;
        $_SESSION['children'] = 0;
        $_SESSION['price_adults'] = 0;
        $_SESSION['price_children'] = 0;

        $conn->commit(); // Commit transaction
        header("Location: success_page_buffet.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback(); // Rollback transaction
        echo "Error: " . $e->getMessage(); // Display the error message
    }
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตะกร้าสินค้า (บุฟเฟ่ต์)</title>
    <link rel="stylesheet" href="css/cart_buffet.css">
</head>

<body>
    <div class="page-container">
        <div class="header-container">
            <h1>ตะกร้าสินค้า (บุฟเฟ่ต์)</h1>
        </div>
        <div class="cart-container">
            <?php if (empty($_SESSION['cart_buffet'])) : ?>
                <p>ไม่มีรายการอาหารในตะกร้า</p>
            <?php else : ?>
                <?php foreach ($_SESSION['cart_buffet'] as $item_id => $details) : ?>
                    <?php
                    // Fetch item details from the database
                    $stmt = $conn->prepare("SELECT name, price, image_path FROM menuitems WHERE item_id = ?");
                    $stmt->bind_param("i", $item_id);
                    $stmt->execute();
                    $stmt->bind_result($name, $price, $image_path);
                    $stmt->fetch();
                    $stmt->close();

                    // Use image_path from database if available, otherwise use the session value
                    $image = $image_path ?: $details['image'];
                    ?>
                    <div class="cart-item">
                        <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($name); ?>">
                        <div>
                            <h3><?php echo htmlspecialchars($name); ?></h3>
                            <p>ราคา: <?php echo htmlspecialchars($price); ?> บาท</p>
                            <p>จำนวน:
                                <div class="quantity-controls">
                                    <button type="button" class="quantity-btn" onclick="updateQuantity('<?php echo htmlspecialchars($item_id); ?>', -1)">-</button>
                                    <input type="text" id="quantity_<?php echo htmlspecialchars($item_id); ?>" value="<?php echo htmlspecialchars($details['quantity']); ?>" readonly>
                                    <input type="hidden" id="hidden_quantity_<?php echo htmlspecialchars($item_id); ?>" value="<?php echo htmlspecialchars($details['quantity']); ?>">
                                    <button type="button" class="quantity-btn" onclick="updateQuantity('<?php echo htmlspecialchars($item_id); ?>', 1)">+</button>
                                </div>
                            </p>
                        </div>
                        <div style="text-align: center;">
                            <form action="cart_buffet.php" method="POST">
                                <input type="hidden" name="remove" value="<?php echo htmlspecialchars($item_id); ?>">
                                <button type="submit" class="remove-btn">ลบ</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
                <form id="orderForm" action="cart_buffet.php" method="POST">
                    <input type="hidden" name="action" value="complete_order">
                    <button type="button" class="checkout-btn" onclick="completeOrder()">สั่งอาหาร</button>
                </form>
            <?php endif; ?>
        </div>
        <a href="menu_order_buffet.php" class="checkout-btn">กลับไปที่เมนูอาหาร</a>
    </div>

    <!-- SweetAlert2 Integration -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function updateQuantity(itemId, change) {
            const quantityInput = document.getElementById('quantity_' + itemId);
            let currentQuantity = parseInt(quantityInput.value, 10);
            let newQuantity = currentQuantity + change;

            // Ensure new quantity is not below 1
            if (newQuantity < 1) {
                newQuantity = 1;
            }

            // Update the display
            quantityInput.value = newQuantity;

            // Update the hidden input field
            document.getElementById('hidden_quantity_' + itemId).value = newQuantity;

            // Send AJAX request to update quantity in the server
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "cart_buffet.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send("update_quantity=1&item_id=" + encodeURIComponent(itemId) + "&quantity=" + encodeURIComponent(newQuantity));
        }

        function completeOrder() {
            Swal.fire({
                title: 'ยืนยันการสั่งซื้อ',
                text: "คุณแน่ใจว่าต้องการสั่งรายการอาหารเรียบร้อย?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ใช่, สั่งเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('orderForm').submit();
                }
            });
        }
    </script>
</body>

</html>
