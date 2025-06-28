<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Login</h2>
    <form action="../pages/login_action.php" method="post">
        <input type="text" name="username" placeholder="Username" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button type="submit">Login</button>
    </form>
    <?php
    if (isset($_SESSION['error'])) {
        echo "<p style='color:red'>{$_SESSION['error']}</p>";
        unset($_SESSION['error']);
    }
    ?>
</body>
</html>
