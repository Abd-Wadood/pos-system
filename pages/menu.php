<?php
session_start();
require_once('../classes/DB.php');
require_once('../classes/Brand.php');

$db = new DB();
$conn = $db->connect();
$brand = new Brand($conn);

$message = "";
$totalBill = 0;

// Handle Order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['items'])) {
    $items = $_POST['items'];
    $customer_name = $_POST['customer_name'];
    $customer_phone = $_POST['customer_phone'];
    $customer_address = $_POST['customer_address'];
    $payment_type = $_POST['payment_type'];

    // Insert customer
    $stmt = $conn->prepare("INSERT INTO customers (name, phone, address) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $customer_name, $customer_phone, $customer_address);
    $stmt->execute();
    $customer_id = $conn->insert_id;

    

    foreach ($items as $id => $qty) {
        $qty = (int)$qty;
        if ($qty > 0) {
            $stmt = $conn->prepare("SELECT price, quantity, name FROM brands WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if ($result && $result['quantity'] >= $qty) {
                $subtotal = $result['price'] * $qty;
                $totalBill += $subtotal;

                // Update quantity in stock
                $update = $conn->prepare("UPDATE brands SET quantity = quantity - ? WHERE id = ?");
                $update->bind_param("ii", $qty, $id);
                $update->execute();
            }
        }
    }

    // Insert into bills
    $stmt = $conn->prepare("INSERT INTO bills (total_amount) VALUES (?)");
    $stmt->bind_param("d", $totalBill);
    $stmt->execute();
    $bill_id = $conn->insert_id;

    // Insert into customer_details (1 row per customer + bill)
    $stmt = $conn->prepare("INSERT INTO customer_details (customer_id, bill_id, brand_name) VALUES (?, ?, 'Multiple')");
    $stmt->bind_param("ii", $customer_id, $bill_id);
    $stmt->execute();

    // Insert into payment_methods
    $stmt = $conn->prepare("INSERT INTO payment_methods (payment_type) VALUES (?)");
    $stmt->bind_param("s", $payment_type);
    $stmt->execute();
    $payment_id = $conn->insert_id;

    // Insert into payment_details
    $stmt = $conn->prepare("INSERT INTO payment_details (payment_id, customer_id, bill_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $payment_id, $customer_id, $bill_id);
    $stmt->execute();


    // Track session sale
    if (!isset($_SESSION['session_id'])) {
        die("Session not started properly. Please log in.");
    }
    $_SESSION['total_sale'] = ($_SESSION['total_sale'] ?? 0) + $totalBill;

    $message = "✅ Order placed! Total: Rs " . $totalBill;
}

$items = $brand->getAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>POS Menu</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .menu-container { display: flex; flex-wrap: wrap; gap: 20px; }
        .menu-item {
            border: 2px solid #ccc; border-radius: 10px;
            padding: 15px; width: 200px; text-align: center;
            cursor: pointer;
        }
        .menu-item.selected {
            border-color: green; background-color: #eaffea;
        }
        .summary-box, .form-group { margin-top: 30px; }
        .order-btn, .refresh-btn {
            margin-top: 15px; margin-right: 10px;
            padding: 10px 20px; border-radius: 5px;
            border: none; color: white; cursor: pointer;
        }
        .order-btn { background-color: green; }
        .refresh-btn { background-color: #555; }
        .dashboard-btn {
            background-color: #007bff; text-decoration: none;
            padding: 10px 20px; color: white; border-radius: 5px;
            float: right; margin-top: 40px;
        }
        .message {
            background: #e7ffe7; color: green;
            padding: 10px 15px; border-left: 5px solid green;
            border-radius: 5px; margin-bottom: 20px; font-weight: bold;
        }
        .selected-items-list {
            border: 1px solid #ccc; padding: 15px;
            border-radius: 10px; background-color: #f9f9f9;
        }
        input, select {
            padding: 8px; margin: 5px 0;
            width: 100%; max-width: 300px;
        }
    </style>
</head>
<body>

<h2>Menu</h2>

<?php if (!empty($message)): ?>
    <p class="message" id="orderMessage"><?= $message ?></p>
<?php endif; ?>

<form method="post" id="orderForm">
    <div class="form-group">
        <h3>Customer Info:</h3>
        <input type="text" name="customer_name" placeholder="Customer Name" required>
        <input type="text" name="customer_phone" placeholder="Phone Number" required>
        <input type="text" name="customer_address" placeholder="Address" required>

        <select name="payment_type" required>
            <option value="">-- Select Payment Method --</option>
            <option value="Cash">Cash</option>
            <option value="Card">Card</option>
            <option value="Bank Transfer">Bank Transfer</option>
        </select>
    </div>

    <div class="menu-container">
        <?php while ($row = $items->fetch_assoc()): ?>
            <?php if ($row['quantity'] > 0): ?>
                <div class="menu-item" onclick="selectItem(this)" data-id="<?= $row['id'] ?>" data-price="<?= $row['price'] ?>" data-name="<?= $row['name'] ?>" data-available="<?= $row['quantity'] ?>">
                    <h3><?= $row['name'] ?></h3>
                    <p>Price: Rs <?= $row['price'] ?></p>
                    <p>Available: <?= $row['quantity'] ?></p>
                    <p class="qty-display">Qty: <span>0</span></p>
                    <input type="hidden" name="items[<?= $row['id'] ?>]" value="0">
                </div>
            <?php endif; ?>
        <?php endwhile; ?>
    </div>

    <div class="summary-box">
        <h3>Selected Items:</h3>
        <div class="selected-items-list" id="selectedItems">None</div>
        <br>
        <div class="total-bill">Total: Rs <span id="totalAmount">0</span></div>
        <button type="submit" class="order-btn">Confirm Order</button>
        <button type="button" class="refresh-btn" onclick="clearMessageAndReload()">New Order</button>
    </div>
</form>

<a href="dashboard.php" class="dashboard-btn">← Back to Dashboard</a>

<script>
function selectItem(card) {
    const qtySpan = card.querySelector('.qty-display span');
    const hiddenInput = card.querySelector('input[type="hidden"]');
    const available = parseInt(card.dataset.available);
    let qty = parseInt(qtySpan.textContent);

    if (qty < available) {
        qty++;
        qtySpan.textContent = qty;
        hiddenInput.value = qty;
        card.classList.add("selected");
        updateTotalAndList();
    }
}

function updateTotalAndList() {
    let total = 0;
    let summary = "";
    const items = document.querySelectorAll('.menu-item');

    items.forEach(item => {
        const qty = parseInt(item.querySelector('input[type="hidden"]').value);
        if (qty > 0) {
            const name = item.dataset.name;
            const price = parseFloat(item.dataset.price);
            total += price * qty;
            summary += `<p>${name} × ${qty} = Rs ${price * qty}</p>`;
        }
    });

    document.getElementById('totalAmount').textContent = total.toFixed(2);
    document.getElementById('selectedItems').innerHTML = summary || "None";
}

function clearMessageAndReload() {
    document.getElementById('orderMessage')?.remove();
    document.querySelectorAll('.qty-display span').forEach(el => el.textContent = '0');
    document.querySelectorAll('.menu-item input').forEach(el => el.value = '0');
    document.querySelectorAll('.menu-item').forEach(el => el.classList.remove('selected'));
    document.getElementById('selectedItems').innerHTML = "None";
    document.getElementById('totalAmount').textContent = "0";
}
</script>

</body>
</html>
