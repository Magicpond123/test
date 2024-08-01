<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db_connect.php';

// Fetch orders data
$sql = "SELECT orders.order_id, orders.table_id, employees.firstname, employees.lastname, orders.order_date, orders.order_status 
        FROM orders 
        JOIN employees ON orders.emp_id = employees.emp_id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script>
        function checker(){
            var result =confirm('คุณต้องการออกจากระบบหรือไม่?');
            if(result == false){
                event.preventDefault();
            }
        }
    </script>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.php">ต้วงหมูกะทะ</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
        <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <div class="input-group">
                <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                <button class="btn btn-danger" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
            </div>
        </form>
        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user fa-fw"></i>
                    <?php if (isset($_SESSION['username'])) : ?>
                        <?php echo $_SESSION['username']; ?>
                    <?php else : ?>
                        ผู้เยี่ยมชม
                    <?php endif; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <?php if (isset($_SESSION['username'])) : ?>
                        <li><a onclick=checker() class="dropdown-item" href="logout.php">ออกจากระบบ</a></li>
                    <?php else : ?>
                        <li><a class="dropdown-item" href="login.php">เข้าสู่ระบบ</a></li>
                    <?php endif; ?>
                </ul>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">หน้าหลัก</div>
                        <a class="nav-link" href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            หน้าหลัก
                        </a>
                        <div class="sb-sidenav-menu-heading">เมนูต่างๆ</div>
                        <a class="nav-link" href="manage_menu.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            จัดการรายการอาหาร
                        </a>
                        <a class="nav-link" href="manage_employees.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                            จัดการพนักงาน
                        </a>
                        <a class="nav-link" href="category.php">
                            <div class="sb-nav-link-icon"><i class="fas fas fa-list"></i></div>
                            จัดการหมวดหมู่
                        </a>
                        <a class="nav-link" href="unit.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-balance-scale"></i></div>
                            จัดการหน่วย
                        </a>
                        <a class="nav-link" href="manage_tables.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                            จัดการโต๊ะ
                        </a>
                        <a class="nav-link" href="manage_orders.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-receipt"></i></div>
                            จัดการออเดอร์
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    <?php if (isset($_SESSION['username'])) : ?>
                        <?php echo $_SESSION['username']; ?>
                    <?php else : ?>
                        Guest
                    <?php endif; ?>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">จัดการออเดอร์</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">จัดการออเดอร์</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            ข้อมูลออเดอร์
                        </div>
                        <div class="card-body">
                        <div class="mb-4">
                        <a href="menu_order.php" class="btn btn-primary">สั่งออเดอร์</a>
                    </div>
                            <table id="datatablesSimple" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">เลขออเดอร์</th>
                                        <th style="text-align: center;">เลขโต๊ะ</th>
                                        <th style="text-align: center;">ชื่อพนักงาน</th>
                                        <th style="text-align: center;">วันที่</th>
                                        <th style="text-align: center;">สถานะ</th>
                                        <th style="text-align: center;">ปรับแต่ง</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i=1;
                                     while ($row = $result->fetch_assoc()) { ?>
                                    <tr>
                                        <td style="text-align: center;"><?php echo $i++; ?></td>
                                        <td style="text-align: center;"><?php echo $row['table_id']; ?></td>
                                        <td style="text-align: center;"><?php echo $row['firstname'] . ' ' . $row['lastname']; ?></td>
                                        <td style="text-align: center;"><?php echo $row['order_date']; ?></td>
                                        <td style="text-align: center;"><?php echo $row['order_status'] == 1 ? 'จ่ายแล้ว' : 'ยังไม่ได้ชำระ'; ?></td>
                                        <td style="text-align: center;">
                                            <a href="edit_order.php?id=<?php echo $row['order_id']; ?>" class="btn btn-warning btn-sm">แก้ไข</a>
                                            <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $row['order_id']; ?>">ลบ</button>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Your Website 2023</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>
</html>
<script>
document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function() {
        const order_id = this.getAttribute('data-id');
        Swal.fire({
            title: "คุณต้องการลบหรือไม่?",
            text: "",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `delete_order.php?id=${order_id}`;
            }
        });
    });
});
</script>
