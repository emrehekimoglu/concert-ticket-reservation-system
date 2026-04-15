<?php
// Dashboard
require_once 'lib/auth.php';
checkLogin();
$schema = require 'lib/schema.php';
$config = require 'config/config.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Dashboard - <?php echo htmlspecialchars($config['app']['name']); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="sidebar">
        <h2>Concert Admin</h2>
        <?php foreach ($schema as $tableName => $tableDef): ?>
            <a href="list.php?table=<?php echo urlencode($tableName); ?>">
                <?php echo htmlspecialchars($tableDef['label']); ?>
            </a>
        <?php endforeach; ?>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="main-content">
        <header>
            <h1>Dashboard</h1>
        </header>

        <p>Welcome to the Admin Interface. Select a table from the sidebar to manage records.</p>

        <div class="stats-grid">
            <!-- Potential for simple stats here later -->
        </div>
    </div>
</body>

</html>