<?php
require_once 'lib/auth.php';
$config = require 'config/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    if (login($user, $pass, $config['app'])) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Login - <?php echo htmlspecialchars($config['app']['name']); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .login-container {
            width: 300px;
            margin: 100px auto;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .login-btn {
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2 style="text-align:center;">Login</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary login-btn">Sign In</button>
        </form>
    </div>
</body>

</html>