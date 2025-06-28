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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['brand_name'])) {
    $brand->add($_POST['brand_name']);
    header("Location: manage.php");
    exit();
}

if (isset($_GET['delete_brand'])) {
    $brand->delete($_GET['delete_brand']);
    header("Location: manage.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Categories and Brands</title>
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

    <h2>Brand Management</h2>
    <form method="post">
        <input type="text" name="brand_name" required>
        <button type="submit">Add Brand</button>
    </form>

    <table>
        <tr><th>ID</th><th>Name</th><th>Action</th></tr>
        <?php
        $result = $brand->getAll();
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td><a href='manage.php?delete_brand={$row['id']}'>Delete</a></td>
            </tr>";
        }
        ?>
    </table>
</body>
</html>
