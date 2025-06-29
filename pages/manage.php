<?php
require_once('../classes/DB.php');
require_once('../classes/Category.php');
require_once('../classes/Brand.php');

$db = new DB();
$conn = $db->connect();

$category = new Category($conn);
$brand = new Brand($conn);

// Handle Category Add/Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['category_name'])) {
    $category->add($_POST['category_name']);
    header("Location: manage.php");
    exit();
}

if (isset($_GET['delete_category'])) {
    $category->delete($_GET['delete_category']);
    header("Location: manage.php");
    exit();
}

// Handle Brand Add/Delete
// Handle Brand Add
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_brand'])) {
    $name = $_POST['brand_name'];
    $price = $_POST['brand_price'];
    $quantity = $_POST['brand_quantity'];
    $expiration = $_POST['brand_expiration'];
    

    $brand->add($name, $price, $expiration, $quantity);
    header("Location: manage.php");
    exit();
}

// Handle Brand Delete
if (isset($_GET['delete_brand'])) {
    $brand->delete($_GET['delete_brand']);
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
    <form method="post">
        <input type="text" name="category_name" required>
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
    <input type="number" name="brand_quantity" placeholder="Quantity" required>
    <input type="date" name="brand_expiration" required>
    <button type="submit" name="add_brand">Add Brand</button>
</form>

<?php
// Handle form submission
if (isset($_POST['add_brand'])) {
    $name = $_POST['brand_name'];
    $price = $_POST['brand_price'];
    $quantity = $_POST['brand_quantity'];
    $expiration = $_POST['brand_expiration'];
    $brand->add($name, $price, $quantity, $expiration);
    echo "<meta http-equiv='refresh' content='0'>"; // Refresh to show updated list
}
?>

<!-- Brands Table -->
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Expiration Date</th>
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
            
            <td><a href='manage.php?delete_brand={$row['id']}'>Delete</a></td>
        </tr>";
    }
    ?>
</table>


</body>
</html>
