<?php
session_start();
require_once('../classes/DB.php');

$db = new DB();
$conn = $db->connect();

$username = $_POST['username'];
$password = $_POST['password'];

// Replace this with your actual authentication logic
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND password = ?");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $username;

    // Start a sales session
    $stmt2 = $conn->prepare("INSERT INTO sales_sessions (user_id) VALUES (?)");
    $stmt2->bind_param("i", $_SESSION['user_id']);
    $stmt2->execute();
    $_SESSION['session_id'] = $conn->insert_id;

    header("Location: dashboard.php");
    exit;
} else {
    $_SESSION['error'] = "Invalid credentials!";
    header("Location: /login.php");
    exit;
}
?>
