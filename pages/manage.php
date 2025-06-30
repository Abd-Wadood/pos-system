<?php
require_once('../classes/DB.php');
require_once('../classes/Category.php');
require_once('../classes/Brand.php');
require_once('../classes/Supplier.php');

$db = new DB();
$conn = $db->connect();

$category = new Category($conn);
$brand = new Brand($conn);
$supplier = new Supplier($conn);

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
<!-- Back to Dashboard Button -->
<a href="dashboard.php" style="display:inline-block; margin-bottom:20px; padding:10px 20px; background-color:#007bff; color:white; text-decoration:none; border-radius:5px;">
    ← Back to Dashboard
</a>

</body>
</html>
