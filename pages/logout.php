<?php
session_start();
require_once('../classes/DB.php');

$db = new DB();
$conn = $db->connect();

// Update total sale and logout time in session record
if (isset($_SESSION['session_id']) && isset($_SESSION['total_sale'])) {
    $stmt = $conn->prepare("UPDATE sales_sessions SET total_sale = ?, logout_time = NOW() WHERE id = ?");
    $stmt->bind_param("di", $_SESSION['total_sale'], $_SESSION['session_id']);
    $stmt->execute();
}

// Destroy session
session_destroy();

// ✅ Redirect to the correct login page
header("Location: http://localhost/pos-system/pages/login.php");
exit;
