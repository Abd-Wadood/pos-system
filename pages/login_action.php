<?php
session_start();
require_once('../classes/DB.php');
require_once('../classes/User.php');

$db = new DB();
$conn = $db->connect();
$user = new User($conn);

$username = $_POST['username'];
$password = $_POST['password'];

$result = $user->login($username, $password);

if ($result->num_rows > 0) {
    $_SESSION['username'] = $username;
    header("Location: dashboard.php");
} else {
    $_SESSION['error'] = "Invalid credentials";
    header("Location: login.php");
}
?>
