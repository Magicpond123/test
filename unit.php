<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db_connect.php';


$sql = "SELECT unit_id, name FROM unit";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Units</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                    <?php if (isset($_SESSION['username'])): ?>
                        <?php echo $_SESSION['username']; ?>
                    <?php else: ?>
                        Guest
                    <?php endif; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <?php if (isset($_SESSION['username'])): ?>
                        <li><a onclick=checker() class="dropdown-item" href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a class="dropdown-item" href="login.php">Login</a></li>
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
                            <div class="sb-nav-link-icon"><i class="fas fa-list"></i></div>
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
                    <?php if (isset($_SESSION['username'])): ?>
                        <?php echo $_SESSION['username']; ?>
                    <?php else: ?>
                        Guest
                    <?php endif; ?>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Manage Units</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Manage Units</li>
                    </ol>
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-table me-1"></i>
                            Unit Data
                        </div>
                        <div class="card-body">
                            <a href="add_unit.php" class="btn btn-primary mb-3">Add New Unit</a>
                            <table id="datatablesSimple" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?php echo $row['unit_id']; ?></td>
                                        <td><?php echo $row['name']; ?></td>
                                        <td>
                                            <a href="edit_unit.php?id=<?php echo $row['unit_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $row['unit_id']; ?>">Delete</button>
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
        const unitId = this.getAttribute('data-id');
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
                window.location.href = `delete_unit.php?id=${unitId}`;
            }
        });
    });
});
</script>

