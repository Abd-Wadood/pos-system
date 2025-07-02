<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>POS Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7fa;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header h2 {
            font-size: 32px;
            color: #333;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
        }

        .card {
            background-color: #fff;
            padding: 30px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            text-align: center;
            cursor: pointer;
            text-decoration: none;
            color: #333;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 18px rgba(0,0,0,0.12);
        }

        .card span.icon {
            font-size: 36px;
            display: block;
            margin-bottom: 15px;
        }

        .card.blue { background-color: #007bff; color: white; }
        .card.green { background-color: #28a745; color: white; }
        .card.teal { background-color: #17a2b8; color: white; }
        .card.yellow { background-color: #ffc107; color: black; }
        .card.orange { background-color: #fd7e14; color: white; }

        .logout {
            display: block;
            margin: 30px auto 0;
            text-align: center;
            font-weight: 600;
            color: #dc3545;
            text-decoration: none;
        }

        .logout:hover {
            text-decoration: underline;
        }

        @media (max-width: 500px) {
            .header h2 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</h2>
    </div>

    <div class="dashboard-grid">
        <a href="manage.php" class="card blue">
            <span class="icon">📦</span>
            Manage Categories & Stocks
        </a>

        <a href="menu.php" class="card green">
            <span class="icon">🍴</span>
            Manage Menu
        </a>

        <a href="customer_details.php" class="card teal">
            <span class="icon">👤</span>
            Customer Details
        </a>

        <a href="payment_details.php" class="card yellow">
            <span class="icon">💳</span>
            Payment Details
        </a>

        <a href="purchase_details.php" class="card orange">
            <span class="icon">📋</span>
            View Purchase Details
        </a>

         <!-- 🔥 New Sales Card -->
        <a href="sales.php" class="card purple">
            <span class="icon">🛒</span>
            Sales Overview
        </a>
        
    </div>

    <a href="../pages/logout.php" class="logout">🚪 Logout</a>
</div>

</body>
</html>
