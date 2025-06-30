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
        
<a href="manage.php" style="display:inline-block; margin:10px; padding:12px 20px; background-color:#007bff; color:white; text-decoration:none; border-radius:5px;">
    📦 Manage Categories & Stocks
</a>
<a href="menu.php" style="display:inline-block; margin:10px; padding:12px 20px; background-color:#28a745; color:white; text-decoration:none; border-radius:5px;">
    🍴 Manage Menu
</a>



        <!-- Add more later -->






    </ul>
    <p><a href="../pages/logout.php">Logout</a></p>
</body>
</html>
