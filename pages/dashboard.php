<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>POS Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['username']; ?></h2>
    <ul>
        
        <li><a href="manage.php">Manage Categories</a></li>





        <!-- Add more later -->






    </ul>
    <p><a href="../pages/logout.php">Logout</a></p>
</body>
</html>
