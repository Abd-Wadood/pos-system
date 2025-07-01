<?php
require_once('../classes/DB.php');

$db = new DB();
$conn = $db->connect();

// Fetch all purchases with supplier info
$sql = "
    SELECT p.id, p.supplier_id, p.quantity, p.price_per_unit, p.purchase_date, s.name AS supplier_name
    FROM purchases p
    JOIN suppliers s ON p.supplier_id = s.id
    ORDER BY p.purchase_date DESC
";
$purchases = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>📦 Purchase Details</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .purchase-box {
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #ccc;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .purchase-box h3 {
            margin-top: 0;
            color: #007bff;
        }
        .purchase-box p {
            margin: 5px 0;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<h2>📋 Purchase Details</h2>

<?php
if ($purchases->num_rows > 0) {
    while ($row = $purchases->fetch_assoc()) {
        $purchase_id = $row['id'];

        // Get detail_description from purchase_details table
        $stmt = $conn->prepare("SELECT detail_description FROM purchase_details WHERE purchase_id = ?");
        $stmt->bind_param("i", $purchase_id);
        $stmt->execute();
        $detailResult = $stmt->get_result();
        $detailText = $detailResult->num_rows > 0 ? $detailResult->fetch_assoc()['detail_description'] : 'N/A';

        $total_cost = $row['quantity'] * $row['price_per_unit'];

        echo "<div class='purchase-box'>
                <h3>Purchase #{$row['id']} | Supplier: {$row['supplier_name']}</h3>
                <p><strong>Quantity:</strong> {$row['quantity']}</p>
                <p><strong>Price per Unit:</strong> Rs " . number_format($row['price_per_unit'], 2) . "</p>
                <p><strong>Total Cost:</strong> Rs " . number_format($total_cost, 2) . "</p>
                <p><strong>Date:</strong> {$row['purchase_date']}</p>
                <p><strong>Detail:</strong> {$detailText}</p>
              </div>";
    }
} else {
    echo "<p>No purchases found.</p>";
}
?>

<a href="dashboard.php" class="back-btn">← Back to Dashboard</a>

</body>
</html>
