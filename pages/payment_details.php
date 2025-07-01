<?php
require_once('../classes/DB.php');

$db = new DB();
$conn = $db->connect();

// Fetch all payment details with joins
$sql = "
    SELECT 
        pd.id AS payment_detail_id,
        c.name AS customer_name,
        c.phone,
        c.address,
        b.total_amount,
        b.created_at AS bill_time,
        pm.payment_type,
        pm.payment_time
    FROM payment_details pd
    JOIN customers c ON pd.customer_id = c.id
    JOIN bills b ON pd.bill_id = b.bill_id
    JOIN payment_methods pm ON pd.payment_id = pm.id
    ORDER BY pd.id DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Payment Details</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        h2 {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #17a2b8;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .back-btn {
            display: inline-block;
            margin: 10px 0;
            padding: 12px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <h2>💳 Payment Details</h2>


    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Customer Name</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Bill Amount</th>
                <th>Bill Time</th>
                <th>Payment Type</th>
                <th>Payment Time</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): 
                $i = 1;
                while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['address']) ?></td>
                    <td>Rs <?= number_format($row['total_amount'], 2) ?></td>
                    <td><?= $row['bill_time'] ?></td>
                    <td><?= $row['payment_type'] ?></td>
                    <td><?= $row['payment_time'] ?></td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="8">No payment records found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

</body>
</html>
