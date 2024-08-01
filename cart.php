<?php
session_start();
include 'includes/db_connect.php';

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add item to cart
if (isset($_GET['item']) && isset($_GET['price']) && isset($_GET['quantity']) && isset($_GET['image'])) {
    $item = htmlspecialchars($_GET['item']);
    $price = htmlspecialchars($_GET['price']);
    $quantity = (int) $_GET['quantity'];
    $image = htmlspecialchars($_GET['image']);

    // Check if item is already in the cart
    if (isset($_SESSION['cart'][$item])) {
        $_SESSION['cart'][$item]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$item] = [
            'price' => $price,
            'quantity' => $quantity,
            'image' => $image
        ];
    }
}

// Remove item from cart
if (isset($_GET['remove'])) {
    $itemToRemove = htmlspecialchars($_GET['remove']);
    if (isset($_SESSION['cart'][$itemToRemove])) {
        unset($_SESSION['cart'][$itemToRemove]);
    }
}

// Complete order
if (isset($_POST['action']) && $_POST['action'] === 'complete_order') {
    $orderSuccess = true;
    $conn->begin_transaction(); // Start transaction
    try {
        foreach ($_SESSION['cart'] as $item => $details) {
            $stmt = $conn->prepare("INSERT INTO cart_items (item_name, price, quantity, image) VALUES (?, ?, ?, ?)");
            if ($stmt === false) {
                throw new Exception("Prepare statement failed");
            }

            $stmt->bind_param("sdis", $item, $details['price'], $details['quantity'], $details['image']);
            $stmt->execute();
            $stmt->close();
        }

        // Clear the cart
        $_SESSION['cart'] = [];
        $conn->commit(); // Commit transaction
    } catch (Exception $e) {
        $orderSuccess = false;
        $conn->rollback(); // Rollback transaction
    }

    // Return response
    echo json_encode(['success' => $orderSuccess]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตะกร้าสินค้า</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            text-align: center;
            margin: 0;
            padding: 20px;
        }
        .cart-container {
            margin: 20px auto;
            max-width: 800px;
            padding: 20px;
            background-color: #fff;
            border: 2px solid #ddd;
            border-radius: 10px;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 5px;
            margin-right: 20px;
        }
        .cart-item div {
            text-align: left;
        }
        .cart-item p {
            margin: 5px 0;
        }
        .checkout-btn, .remove-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 18px;
        }
        .remove-btn {
            background-color: #f44336;
        }
        .remove-btn:hover {
            background-color: #e53935;
        }
        .checkout-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>ตะกร้าสินค้า</h1>
    <div class="cart-container">
        <?php if (empty($_SESSION['cart'])): ?>
            <p>ไม่มีรายการอาหารในตะกร้า</p>
        <?php else: ?>
            <?php foreach ($_SESSION['cart'] as $item => $details): ?>
                <div class="cart-item">
                    <img src="<?php echo htmlspecialchars($details['image']); ?>" alt="<?php echo htmlspecialchars($item); ?>">
                    <div>
                        <h3><?php echo htmlspecialchars($item); ?></h3>
                        <p>ราคา: <?php echo htmlspecialchars($details['price']); ?> บาท</p>
                        <p>จำนวน: <?php echo htmlspecialchars($details['quantity']); ?></p>
                    </div>
                    <a href="cart.php?remove=<?php echo urlencode($item); ?>" class="remove-btn">ลบ</a>
                </div>
            <?php endforeach; ?>
            <button onclick="completeOrder()" class="checkout-btn">สั่งอาหาร</button>
        <?php endif; ?>
    </div>
    <a href="index.php" class="checkout-btn">กลับไปที่เมนูอาหาร</a>

    <!-- SweetAlert2 Integration -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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
                    fetch('cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            action: 'complete_order'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'สำเร็จ!',
                                'สั่งรายการอาหารเรียบร้อยแล้ว',
                                'success'
                            ).then(() => {
                                window.location.href = 'menu_order.php'; // เปลี่ยนไปที่หน้า index.php
                            });
                        } else {
                            Swal.fire(
                                'ผิดพลาด!',
                                'เกิดข้อผิดพลาดในการสั่งรายการอาหาร',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>
