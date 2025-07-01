<?php
require_once('../classes/DB.php');

$db = new DB();
$conn = $db->connect();

// Fetch customer order details with joins
$query = "
SELECT 
    c.id AS customer_id,
    c.name AS customer_name,
    c.phone,
    c.address,
    b.total_amount,
    b.created_at,
    cd.brand_name
FROM customer_details cd
JOIN customers c ON cd.customer_id = c.id
JOIN bills b ON cd.bill_id = b.bill_id
ORDER BY b.created_at DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Orders</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            border: 1px solid #aaa;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        h2 {
            margin-top: 20px;
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
    <h2>Customer Order Details</h2>

    <table>
        <thead>
            <tr>
                <th>Customer Name</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Brand Ordered</th>
                <th>Bill Amount</th>
                <th>Order Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['customer_name']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['address']) ?></td>
                        <td><?= htmlspecialchars($row['brand_name']) ?></td>
                        <td>Rs <?= number_format($row['total_amount'], 2) ?></td>
                        <td><?= $row['created_at'] ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">No orders found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

</body>
</html>
