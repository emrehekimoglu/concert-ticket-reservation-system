<?php
require_once 'lib/auth.php';
require_once 'lib/db.php';
checkLogin();

$config = require 'config/config.php';
$schema = require 'lib/schema.php';

$tableName = $_GET['table'] ?? '';
if (!isset($schema[$tableName])) {
    die("Invalid table name.");
}

$tableDef = $schema[$tableName];
$pk = $tableDef['pk'];
$db = new Database($config['db']);

// Helper to look up FK labels safely (if we had a smart ORM, but we'll do raw value for now or basic left join later)
// For simplicity in V1, we just select * and show raw FK IDs. 
// A robust version would perform JOINs based on the schema definition.
// Let's attempt a basic JOIN construction if feasible, otherwise show IDs.

$selectCols = [];
$joins = [];

foreach ($tableDef['columns'] as $colName => $colDef) {
    if ($colDef['type'] === 'fk') {
        // e.g. TICKET.BOOKINGID -> BOOKING.BOOKINGID
        // We want to select BOOKING.BOOKINGID (or label) as TICKET_BOOKINGID_LABEL
        // For simplicity: Let's just show the ID in the list. JOINs get complex with potential naming collisions.
        // We will just select t.*
    }
}

// Basic Search
$search = $_GET['search'] ?? '';
$whereClause = "";
$params = [];
if ($search) {
    $whereParts = [];
    foreach ($tableDef['columns'] as $colName => $colDef) {
        // Only search text fields
        if (in_array($colDef['type'], ['text', 'email', 'textarea'])) {
            $whereParts[] = "LOWER($colName) LIKE :search";
        }
    }
    if ($whereParts) {
        $whereClause = "WHERE " . implode(' OR ', $whereParts);
        $params[':search'] = '%' . strtolower($search) . '%';
    }
}

// Order by PK desc default
$sql = "SELECT * FROM $tableName $whereClause ORDER BY $pk DESC";
// Note: Oracle 11g doesn't support OFFSET/FETCH nicely without rownum tricks.
// Oracle 12c+ does. Assuming 12c+ or ignoring pagination for MVP to ensure compatibility with most simple OCI setups.
// Let's implement basic limit if possible, or just fetch all (MVP usually fine for admin panels unless huge data).
// We'll fetch all up to 100 for safety.

$startSql = "SELECT * FROM (SELECT a.*, ROWNUM rnum FROM ($sql) a WHERE ROWNUM <= 100)";
if ($whereClause) {
    // If we have where clause, binding is needed.
    $rows = $db->query($startSql, $params);
} else {
    $rows = $db->query($startSql);
}

?>
<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($tableDef['label']); ?> - <?php echo htmlspecialchars($config['app']['name']); ?>
    </title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="sidebar">
        <h2>Concert Admin</h2>
        <?php foreach ($schema as $t => $tDef): ?>
            <a href="list.php?table=<?php echo urlencode($t); ?>" class="<?php echo $t === $tableName ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($tDef['label']); ?>
            </a>
        <?php endforeach; ?>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="main-content">
        <header>
            <h1><?php echo htmlspecialchars($tableDef['label']); ?></h1>
            <a href="form.php?table=<?php echo urlencode($tableName); ?>" class="btn btn-primary">Add New</a>
        </header>

        <div class="search-bar" style="margin-bottom: 20px;">
            <form method="get">
                <input type="hidden" name="table" value="<?php echo htmlspecialchars($tableName); ?>">
                <input type="text" name="search" class="form-control" style="width: 300px; display:inline-block;"
                    placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn btn-secondary">Search</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <?php foreach ($tableDef['columns'] as $colName => $colDef): ?>
                        <th><?php echo htmlspecialchars($colDef['label']); ?></th>
                    <?php endforeach; ?>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="<?php echo count($tableDef['columns']) + 1; ?>" style="text-align:center;">No records
                            found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <?php foreach ($tableDef['columns'] as $colName => $colDef): ?>
                                <td>
                                    <?php
                                    $val = $row[$colName] ?? '';
                                    // Simple formatting
                                    if ($colDef['type'] === 'date' || $colDef['type'] === 'datetime-local') {
                                        // Oracle dates come as string usually, depending on NLS. 
                                        // Just print for now.
                                    }
                                    echo htmlspecialchars($val);
                                    ?>
                                </td>
                            <?php endforeach; ?>
                            <td>
                                <a href="form.php?table=<?php echo urlencode($tableName); ?>&id=<?php echo urlencode($row[$pk]); ?>"
                                    class="btn btn-sm btn-secondary">Edit</a>
                                <a href="action.php?action=delete&table=<?php echo urlencode($tableName); ?>&id=<?php echo urlencode($row[$pk]); ?>"
                                    class="btn btn-sm btn-danger btn-delete">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="assets/js/app.js"></script>
</body>

</html>