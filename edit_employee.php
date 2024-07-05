<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db_connect.php';

$emp_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $mail = $_POST['mail'];
    $location = $_POST['location'];
    $role = $_POST['role'];

    $sql = "UPDATE employees 
            SET username='$username', password='$password', firstname='$firstname', lastname='$lastname', mail='$mail', location='$location', role='$role' 
            WHERE emp_id='$emp_id'";
    if ($conn->query($sql) === TRUE) {
        header("Location: manage_employees.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    $sql = "SELECT * FROM employees WHERE emp_id='$emp_id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Employee</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/common.css">
</head>
<body>
<div class="container">
    <h2>Edit Employee</h2>
    <form action="edit_employee.php?id=<?php echo $emp_id; ?>" method="post">
        <div class="form-group mb-3">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo $row['username']; ?>" required>
        </div>
        <div class="form-group mb-3">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" value="<?php echo $row['password']; ?>" required>
        </div>
        <div class="form-group mb-3">
            <label for="firstname">First Name:</label>
            <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $row['firstname']; ?>" required>
        </div>
        <div class="form-group mb-3">
            <label for="lastname">Last Name:</label>
            <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $row['lastname']; ?>" required>
        </div>
        <div class="form-group mb-3">
            <label for="mail">Email:</label>
            <input type="email" class="form-control" id="mail" name="mail" value="<?php echo $row['mail']; ?>" required>
        </div>
        <div class="form-group mb-3">
            <label for="location">Location:</label>
            <input type="text" class="form-control" id="location" name="location" value="<?php echo $row['location']; ?>" required>
        </div>
        <div class="form-group mb-3">
            <label for="role">Role:</label><br>
            <input type="radio" id="Owner" name="role" value="0" <?php echo ($row['role'] == 0) ? 'checked' : ''; ?> required>
            <label for="Owner">Owner</label><br>
            <input type="radio" id="Cashier" name="role" value="1" <?php echo ($row['role'] == 1) ? 'checked' : ''; ?> required>
            <label for="Cashier">Cashier</label><br>
            <input type="radio" id="Receptionist" name="role" value="2" <?php echo ($row['role'] == 2) ? 'checked' : ''; ?> required>
            <label for="Receptionist">Receptionist</label><br>
            <input type="radio" id="Kitchen" name="role" value="3" <?php echo ($row['role'] == 3) ? 'checked' : ''; ?> required>
            <label for="Kitchen">Kitchen</label>
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
