<?php
session_start();
include 'includes/db_connect.php';

if (isset($_POST['add_to_cart'])) {
    $item_id = $_POST['item_id'];
    $quantity = (int) $_POST['quantity'];

    if (!isset($_SESSION['cart_pickup'])) {
        $_SESSION['cart_pickup'] = [];
    }

    if (isset($_SESSION['cart_pickup'][$item_id])) {
        $_SESSION['cart_pickup'][$item_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart_pickup'][$item_id] = ['quantity' => $quantity];
    }

    // Calculate total items in the cart
    $cart_count = array_sum(array_column($_SESSION['cart_pickup'], 'quantity'));

    // Send back the updated cart count
    echo json_encode(['cart_count' => $cart_count]);
    exit;
}

$sql_buffet = "SELECT * FROM menuitems WHERE order_type = 2";
$result_buffet = $conn->query($sql_buffet);

if ($result_buffet === false) {
    die("Error: " . $conn->error);
}

$sql_food = "SELECT * FROM menuitems WHERE category_id = 1 AND order_type = 2";
$result_food = $conn->query($sql_food);

$sql_drink = "SELECT * FROM menuitems WHERE category_id = 2 AND order_type = 2";
$result_drink = $conn->query($sql_drink);

$sql_dessert = "SELECT * FROM menuitems WHERE category_id = 3 AND order_type = 2";
$result_dessert = $conn->query($sql_dessert);

?>


<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เมนูกลับบ้าน</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/menu_order.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function addToCart(itemId) {
            var quantity = $('#quantity-' + itemId).val();
            $.ajax({
                type: 'POST',
                url: 'menu_order_pickup.php', // คุณควรจะชี้ไปที่ไฟล์ที่จัดการการเพิ่มสินค้าในตะกร้า
                data: {
                    item_id: itemId,
                    quantity: quantity,
                    add_to_cart: true
                },
                success: function(response) {
                    response = JSON.parse(response);

                    if (response.cart_count !== undefined) {
                        updateCartIcon(response.cart_count);
                        alert('เพิ่มสินค้าลงตะกร้าเรียบร้อยแล้ว!');
                    } else {
                        alert('เกิดข้อผิดพลาดในการอัพเดตไอคอนตะกร้าสินค้า');
                    }
                },
                error: function() {
                    alert('เกิดข้อผิดพลาดในการเพิ่มสินค้าลงตะกร้า');
                }

            });
        }

        function updateCartIcon(cartCount) {
            $('.cart-icon .badge').text(cartCount); // Update cart icon badge
        }

        function changeQuantity(amount, id) {
            var quantityInput = document.getElementById('quantity-' + id);
            var currentQuantity = parseInt(quantityInput.value, 10);
            var newQuantity = currentQuantity + amount;

            if (newQuantity >= 1) {
                quantityInput.value = newQuantity;
            }
        }
    </script>
    <script>
        function openTab(tabName) {
            var i;
            var x = document.getElementsByClassName("menu-items");
            var tabs = document.getElementsByClassName("tab");

            // ซ่อนเมนูทั้งหมด
            for (i = 0; i < x.length; i++) {
                x[i].style.display = "none";
            }

            // นำ `active` class ออกจาก tab ทั้งหมด
            for (i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove("active");
            }

            // แสดงเมนูที่เลือกและเพิ่ม `active` class ไปยัง tab ที่เลือก
            document.getElementById(tabName).style.display = "flex";
            event.currentTarget.classList.add("active");
        }
    </script>
    <script>
        function clearCustomerForm() {
            document.getElementById('customer-count-form').reset();
        }

        document.getElementById('customer-count-form').onsubmit = function() {
            clearCustomerForm();
        };
    </script>
</head>

<body>
    <header class="navbar">
        <img src="img/logo.jpg" alt="Logo">
    </header>
    <div class="tab-container">
        <div class="tab active" onclick="openTab('menu_food')">สั่งเมนูอาหาร</div>
        <div class="tab" onclick="openTab('menu_drink')">สั่งเครื่องดื่ม</div>
        <div class="tab" onclick="openTab('menu_dessert')">ของหวาน</div>
    </div>

    <main>
        <section id="menu_food" class="menu-items" style="display: flex;">
            <?php
            while ($row = $result_food->fetch_assoc()) {
                echo "<div class='menu-item'>";
                echo "<img src='" . htmlspecialchars($row['image_path']) . "' alt='" . htmlspecialchars($row['name']) . "' class='menu-item-image'>";
                echo "<div class='menu-item-details'>";
                echo "<h2 class='menu-item-name'>" . htmlspecialchars($row['name']) . "</h2>";
                echo "<p class='menu-item-price'>ราคา: " . htmlspecialchars($row['price']) . " บาท</p>";
                echo "<p class='menu-item-description'>" . htmlspecialchars($row['description']) . "</p>";
                echo "<label for='quantity'>จำนวน:</label>";
                echo "<div class='quantity-controls'>";
                echo "<button type='button' class='quantity-btn' onclick='changeQuantity(-1, \"" . htmlspecialchars($row['item_id']) . "\")'>-</button>";
                echo "<input type='number' id='quantity-" . htmlspecialchars($row['item_id']) . "' name='quantity' value='1' min='1'>";
                echo "<button type='button' class='quantity-btn' onclick='changeQuantity(1, \"" . htmlspecialchars($row['item_id']) . "\")'>+</button>";
                echo "</div>";
                echo "<button type='button' name='add_to_cart' class='add-to-cart-btn' onclick='addToCart(\"" . htmlspecialchars($row['item_id']) . "\")'>เพิ่มในตะกร้า</button>";
                echo "</div>";
                echo "</div>";
            }
            ?>
        </section>


        <!-- เมนูเครื่องดื่ม -->
        <section id="menu_drink" class="menu-items">
            <?php
            while ($row = $result_drink->fetch_assoc()) {
                echo "<div class='menu-item'>";
                echo "<img src='" . htmlspecialchars($row['image_path']) . "' alt='" . htmlspecialchars($row['name']) . "' class='menu-item-image'>";
                echo "<div class='menu-item-details'>";
                echo "<h2 class='menu-item-name'>" . htmlspecialchars($row['name']) . "</h2>";
                echo "<p class='menu-item-price'>ราคา: " . htmlspecialchars($row['price']) . " บาท</p>";
                echo "<form action='cart_pickup.php' method='POST' class='add-to-cart-form'>";
                echo "<input type='hidden' name='item_id' value='" . htmlspecialchars($row['item_id']) . "'>";
                echo "<label for='quantity'>จำนวน:</label>";
                echo "<div class='quantity-controls'>";
                echo "<button type='button' class='quantity-btn' onclick='changeQuantity(-1, \"" . htmlspecialchars($row['item_id']) . "\")'>-</button>";
                echo "<input type='number' id='quantity-" . htmlspecialchars($row['item_id']) . "' name='quantity' value='1' min='1'>";
                echo "<button type='button' class='quantity-btn' onclick='changeQuantity(1, \"" . htmlspecialchars($row['item_id']) . "\")'>+</button>";
                echo "</div>";
                echo "<button type='button' name='add_to_cart' class='add-to-cart-btn' onclick='addToCart(\"" . htmlspecialchars($row['item_id']) . "\")'>เพิ่มในตะกร้า</button>";
                echo "</form>";
                echo "</div>";
                echo "</div>";
            }
            ?>
        </section>

        <!-- เมนูของหวาน -->
        <section id="menu_dessert" class="menu-items">
            <?php
            while ($row = $result_dessert->fetch_assoc()) {
                echo "<div class='menu-item'>";
                echo "<img src='" . htmlspecialchars($row['image_path']) . "' alt='" . htmlspecialchars($row['name']) . "' class='menu-item-image'>";
                echo "<div class='menu-item-details'>";
                echo "<h2 class='menu-item-name'>" . htmlspecialchars($row['name']) . "</h2>";
                echo "<p class='menu-item-price'>ราคา: " . htmlspecialchars($row['price']) . " บาท</p>";
                echo "<form action='cart_pickup.php' method='POST' class='add-to-cart-form'>";
                echo "<input type='hidden' name='item_id' value='" . htmlspecialchars($row['item_id']) . "'>";
                echo "<label for='quantity'>จำนวน:</label>";
                echo "<div class='quantity-controls'>";
                echo "<button type='button' class='quantity-btn' onclick='changeQuantity(-1, \"" . htmlspecialchars($row['item_id']) . "\")'>-</button>";
                echo "<input type='number' id='quantity-" . htmlspecialchars($row['item_id']) . "' name='quantity' value='1' min='1'>";
                echo "<button type='button' class='quantity-btn' onclick='changeQuantity(1, \"" . htmlspecialchars($row['item_id']) . "\")'>+</button>";
                echo "</div>";
                echo "<button type='button' name='add_to_cart' class='add-to-cart-btn' onclick='addToCart(\"" . htmlspecialchars($row['item_id']) . "\")'>เพิ่มในตะกร้า</button>";
                echo "</form>";
                echo "</div>";
                echo "</div>";
            }
            ?>

    </main>

    <a href="cart_pickup.php" class="cart-icon">
        <i class="fas fa-shopping-cart"></i>
        <div class="badge"><?php echo isset($_SESSION['cart_pickup']) ? array_sum(array_column($_SESSION['cart_pickup'], 'quantity')) : 0; ?></div>
    </a>

</body>

</html>