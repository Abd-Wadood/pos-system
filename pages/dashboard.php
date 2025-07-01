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
<div style="margin-top: 20px; clear: both;">
    <a href="customer_details.php" style="display:inline-block; margin:10px; padding:12px 20px; background-color:#17a2b8; color:white; text-decoration:none; border-radius:5px;">
        👤 Customer Details
    </a>
</div>
<a href="payment_details.php" style="display:inline-block; margin:10px; padding:12px 20px; background-color:#ffc107; color:white; text-decoration:none; border-radius:5px;">
    💳 Payment Details
</a>
<a href="purchase_details.php" style="display:inline-block; margin:10px; padding:12px 20px; background-color:#28a745; color:white; text-decoration:none; border-radius:5px;">
    📋 View Purchase Details
</a>



        <!-- Add more later -->






    </ul>
    <p><a href="../pages/logout.php">Logout</a></p>
</body>
</html>
