<?php
require_once('../classes/DB.php');
require_once('../classes/Category.php');
require_once('../classes/Brand.php');
require_once('../classes/Supplier.php');
require_once('../classes/Purchase.php');

$db = new DB();
$conn = $db->connect();

$category = new Category($conn);
$brand = new Brand($conn);
$supplier = new Supplier($conn);
$purchase = new Purchase($conn);

$message = "";

// Handle Category Add
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['category_name'])) {
    if (!$category->add($_POST['category_name'])) {
        $message = "❌ Category already exists!";
    } else {
        header("Location: manage.php");
        exit();
    }
}

// Handle Brand Add
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_brand'])) {
    $name = $_POST['brand_name'];
    $price = $_POST['brand_price'];
    $quantity = $_POST['brand_quantity'];
    $expiration = $_POST['brand_expiration'];
    $category_id = $_POST['brand_category'];

    if (!$brand->add($name, $price, $expiration, $quantity, $category_id)) {
        $message = "❌ Brand not added! Quantity must be ≤ 500. Duplicate names in the same category are merged if total ≤ 500.";
    } else {
        header("Location: manage.php");
        exit();
    }
}

// Handle Supplier Add
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_supplier'])) {
    $supplier_name = $_POST['supplier_name'];
    $supplier_phone = $_POST['supplier_phone'];
    $supplier_category = $_POST['supplier_category'];

    $result = $supplier->add($supplier_name, $supplier_phone, $supplier_category);
    if ($result === "invalid_phone") {
        $message = "❌ Phone number must be exactly 11 digits.";
    } elseif ($result === "category_taken") {
        $message = "❌ This category is already assigned to another supplier.";
    } elseif (!$result) {
        $message = "❌ Failed to add supplier. Try again.";
    } else {
        header("Location: manage.php");
        exit();
    }
}

// Handle Purchase Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_purchase'])) {
    $supplier_id = $_POST['supplier_id'];
    $quantity = $_POST['quantity'];
    $price_per_unit = $_POST['price_per_unit'];
    $detail = !empty($_POST['detail_description']) ? $_POST['detail_description'] : null;

    // Insert into `purchases` table
    $stmt = $conn->prepare("INSERT INTO purchases (supplier_id, quantity, price_per_unit) VALUES (?, ?, ?)");
    $stmt->bind_param("iid", $supplier_id, $quantity, $price_per_unit);
    $stmt->execute();

    $purchase_id = $conn->insert_id;

    // Insert into `purchase_details` with description
    $stmt_detail = $conn->prepare("INSERT INTO purchase_details (purchase_id, detail_description) VALUES (?, ?)");
    $stmt_detail->bind_param("is", $purchase_id, $detail);
    $stmt_detail->execute();

    if ($purchase_id && $stmt_detail->affected_rows > 0) {
        echo "<p style='color:green;'>✅ Purchase added successfully.</p>";
    } else {
        echo "<p style='color:red;'>❌ Failed to add purchase.</p>";
    }
}






// Handle Deletes
if (isset($_GET['delete_category'])) {
    $category->delete($_GET['delete_category']);
    header("Location: manage.php");
    exit();
}
if (isset($_GET['delete_brand'])) {
    $brand->delete($_GET['delete_brand']);
    header("Location: manage.php");
    exit();
}
if (isset($_GET['delete_supplier'])) {
    $supplier->delete($_GET['delete_supplier']);
    header("Location: manage.php");
    exit();
}
if (isset($_GET['delete_purchase'])) {
    $purchase->delete($_GET['delete_purchase']);
    header("Location: manage.php");
    exit();
}


?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Categories and Stocks</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Category Management</h2>

    <?php if (!empty($message)): ?>
        <p style="color:red; font-weight: bold;"><?= $message ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="category_name" placeholder="Category Name" required>
        <button type="submit">Add Category</button>
    </form>

    <table>
        <tr><th>ID</th><th>Name</th><th>Action</th></tr>
        <?php
        $result = $category->getAll();
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td><a href='manage.php?delete_category={$row['id']}'>Delete</a></td>
            </tr>";
        }
        ?>
    </table>

    <h2>Stock Management</h2>

    <!-- Brand Add Form -->
    <form method="post">
        <input type="text" name="brand_name" placeholder="Name" required>
        <input type="number" step="0.01" name="brand_price" placeholder="Price" required>
        <input type="number" name="brand_quantity" placeholder="Quantity (max 500)" required>
        <input type="date" name="brand_expiration" required>

        <!-- Category Dropdown -->
        <select name="brand_category" required>
            <option value="">Select Category</option>
            <?php
            $categories = $category->getAll();
            while ($row = $categories->fetch_assoc()) {
                echo "<option value='{$row['id']}'>{$row['name']}</option>";
            }
            ?>
        </select>

        <button type="submit" name="add_brand">Add Brand</button>
    </form>

    <!-- Brands Table -->
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Expiration Date</th>
            <th>Category</th>
            <th>Action</th>
        </tr>
        <?php
        $result = $brand->getAll();
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['price']}</td>
                <td>{$row['quantity']}</td>
                <td>{$row['expiration_date']}</td>
                <td>{$row['category_name']}</td>
                <td><a href='manage.php?delete_brand={$row['id']}'>Delete</a></td>
            </tr>";
        }
        ?>
    </table>

        <h2>Supplier Management</h2>
<form method="post">
    <input type="text" name="supplier_name" placeholder="Supplier Name" required>
    <input type="text" name="supplier_phone" placeholder="Phone (11 digits)" required
       pattern="\d{11}" title="Phone must be exactly 11 digits">


    <!-- Category Dropdown -->
    <select name="supplier_category" required>
        <option value="">Select Category</option>
        <?php
        $categories = $category->getAll();
        while ($row = $categories->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['name']}</option>";
        }
        ?>
    </select>

    <button type="submit" name="add_supplier">Add Supplier</button>
</form>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Phone</th>
        <th>Category</th>
        <th>Action</th>
    </tr>
    <?php
    $result = $supplier->getAll();
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['name']}</td>
            <td>{$row['phone']}</td>
            <td>{$row['category_name']}</td>
            <td><a href='manage.php?delete_supplier={$row['id']}'>Delete</a></td>
        </tr>";
    }
    ?>
</table>
<br>

<!-- ✅ Purchase Add Form -->
<form method="post" style="margin-bottom: 20px;">
    <label for="supplier_id">Supplier:</label>
    <select name="supplier_id" required>
        <option value="">Select Supplier</option>
        <?php
        $suppliers = $conn->query("SELECT id, name FROM suppliers");
        while ($row = $suppliers->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['name']}</option>";
        }
        ?>
    </select>

    <br><br>

    <label for="quantity">Quantity:</label>
    <input type="number" name="quantity" required min="1" placeholder="Enter Quantity">

    <label for="price_per_unit">Price per Unit:</label>
    <input type="number" name="price_per_unit" required step="0.01" min="0" placeholder="Enter Price">
     <br><br>
    <label for="detail_description">Detail Description:</label>
    <input type="text" name="detail_description" placeholder="Optional notes like 'July stock + unit of measure'" style="width: 50%;">

    <br><br>

    <button type="submit" name="add_purchase">➕ Add Purchase</button>
</form>

<!-- Purchases Table -->
<h3>📋 All Purchases</h3>
<table border="1" cellpadding="8" cellspacing="0">
    <thead style="background-color: #17a2b8; color: white;">
        <tr>
            <th>ID</th>
            <th>Supplier</th>
            <th>Quantity</th>
            <th>Price per Unit</th>
            <th>Total Cost</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $result = $conn->query("
            SELECT p.id, s.name AS supplier_name, p.quantity, p.price_per_unit, p.purchase_date
            FROM purchases p
            JOIN suppliers s ON p.supplier_id = s.id
            ORDER BY p.purchase_date DESC
        ");

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $total_cost = $row['quantity'] * $row['price_per_unit'];
                echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['supplier_name']}</td>
                    <td>{$row['quantity']}</td>
                    <td>Rs. " . number_format($row['price_per_unit'], 2) . "</td>
                    <td>Rs. " . number_format($total_cost, 2) . "</td>
                    <td>{$row['purchase_date']}</td>
                    <td><a href='manage.php?delete_purchase={$row['id']}' onclick=\"return confirm('Are you sure?')\">🗑 Delete</a></td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No purchases found.</td></tr>";
        }
        ?>
    </tbody>
</table>

<br>
<!-- Back to Dashboard Button -->
<a href="dashboard.php" style="display:inline-block; margin-bottom:20px; padding:10px 20px; background-color:#007bff; color:white; text-decoration:none; border-radius:5px;">
    ← Back to Dashboard
</a>

</body>
</html>
