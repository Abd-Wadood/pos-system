<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once('../classes/DB.php');
$db = new DB();
$conn = $db->connect();

// Fetch all sales sessions
$query = "SELECT id, start_time, total_sale, logout_time FROM sales_sessions ORDER BY start_time DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Sessions</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f7f9fb;
            padding: 40px 20px;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px 12px;
            text-align: center;
            border-bottom: 1px solid #e2e6ea;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:hover {
            background-color: #f1f5f9;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #007bff;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🛒 Sales Sessions</h2>

    <table>
        <thead>
            <tr>
                <th>Session ID</th>
                <th>Start Time</th>
                <th>Total Sale (PKR)</th>
                <th>Logout Time</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= $row['id']; ?></td>
                        <td><?= date('Y-m-d h:i A', strtotime($row['start_time'])); ?></td>
                        <td><strong><?= number_format($row['total_sale'], 2); ?></strong></td>
                        <td>
                            <?= $row['logout_time'] 
                                ? date('Y-m-d h:i A', strtotime($row['logout_time'])) 
                                : '<em>Dummy LogIn</em>'; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">No sales sessions found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a class="back-link" href="dashboard.php">⬅️ Back to Dashboard</a>
</div>

</body>
</html>
