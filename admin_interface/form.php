<?php
require_once 'lib/auth.php';
require_once 'lib/db.php';
checkLogin();

$config = require 'config/config.php';
$schema = require 'lib/schema.php';

$tableName = $_GET['table'] ?? '';
$id = $_GET['id'] ?? null;

if (!isset($schema[$tableName])) {
    die("Invalid table name.");
}

$tableDef = $schema[$tableName];
$pk = $tableDef['pk'];
$db = new Database($config['db']);

$data = [];
$isEdit = false;

if ($id) {
    if ($tableName === 'CUSTOMER') {
        // Customer email is special in some schemas, but here PK is ID.
    }
    $sql = "SELECT * FROM $tableName WHERE $pk = :id";
    $data = $db->fetchOne($sql, [':id' => $id]);
    if (!$data) {
        die("Record not found.");
    }
    $isEdit = true;
}

// Prepare FK options
$fkOptions = [];
foreach ($tableDef['columns'] as $colName => $colDef) {
    if ($colDef['type'] === 'fk') {
        $targetTable = $colDef['table'];
        $targetPk = $schema[$targetTable]['pk'];
        $displayCol = $colDef['display'];
        
        // Fetch all options (limit to 1000 for sanity)
        $optSql = "SELECT $targetPk, $displayCol FROM $targetTable";
        
        // Oracle fetch might return uppercase keys
        $rows = $db->query($optSql);
        $options = [];
        foreach ($rows as $r) {
            // Normalize keys to find the values
            $val = $r[$targetPk] ?? $r[strtoupper($targetPk)];
            $label = $r[$displayCol] ?? $r[strtoupper($displayCol)];
            $options[] = ['value' => $val, 'label' => $label . " (ID: $val)"];
        }
        $fkOptions[$colName] = $options;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $isEdit ? 'Edit' : 'New'; ?> <?php echo htmlspecialchars($tableDef['label']); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="sidebar">
        <h2>Concert Admin</h2>
        <?php foreach($schema as $t => $tDef): ?>
            <a href="list.php?table=<?php echo urlencode($t); ?>" class="<?php echo $t === $tableName ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($tDef['label']); ?>
            </a>
        <?php endforeach; ?>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="main-content">
        <header>
            <h1><?php echo $isEdit ? 'Edit' : 'Create'; ?> <?php echo htmlspecialchars($tableDef['label']); ?></h1>
            <a href="list.php?table=<?php echo urlencode($tableName); ?>" class="btn btn-secondary">Back to List</a>
        </header>

        <form action="action.php" method="post" style="max-width: 600px; background: #fff; padding: 20px; border: 1px solid #dee2e6;">
            <input type="hidden" name="table" value="<?php echo htmlspecialchars($tableName); ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="pk_val" value="<?php echo htmlspecialchars($id); ?>">
                <!-- If PK is purely internal/auto-increment, we don't show it or show readonly -->
                <div class="form-group">
                    <label>ID</label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($id); ?>" disabled>
                </div>
            <?php else: ?>
                <!-- On Create, show ID input if reasonable -->
                <?php 
                    $nextIdSql = "SELECT MAX($pk) + 1 as NEXT_ID FROM $tableName";
                    $res = $db->fetchOne($nextIdSql);
                    $nextId = $res['NEXT_ID'] ?? $res['next_id'] ?? 1;
                ?>
                <div class="form-group">
                    <label>ID (Leave as is for auto-suggestion)</label>
                    <input type="number" name="<?php echo $pk; ?>" class="form-control" value="<?php echo $nextId; ?>" required>
                </div>
            <?php endif; ?>

            <?php foreach ($tableDef['columns'] as $colName => $colDef): ?>
                <?php 
                    if ($colName === $pk) continue; // Handled above
                    
                    $val = $isEdit ? ($data[$colName] ?? '') : ($colDef['default'] ?? '');
                    
                    // Basic date transform for HTML5
                    if ($isEdit && $val && ($colDef['type'] === 'datetime-local' || $colDef['type'] === 'date')) {
                        if (strtotime($val)) {
                             $val = date($colDef['type'] === 'date' ? 'Y-m-d' : 'Y-m-d\TH:i', strtotime($val));
                        }
                    }
                ?>
                
                <div class="form-group">
                    <label><?php echo htmlspecialchars($colDef['label']); ?> <?php if($colDef['required']) echo '<span style="color:red">*</span>'; ?></label>
                    
                    <?php if ($colDef['type'] === 'textarea'): ?>
                        <textarea name="<?php echo $colName; ?>" class="form-control" rows="4" <?php if($colDef['required']) echo 'required'; ?>><?php echo htmlspecialchars($val); ?></textarea>
                    
                    <?php elseif ($colDef['type'] === 'select'): ?>
                        <select name="<?php echo $colName; ?>" class="form-control" <?php if($colDef['required']) echo 'required'; ?>>
                            <?php foreach($colDef['options'] as $opt): ?>
                                <option value="<?php echo htmlspecialchars($opt); ?>" <?php if($val == $opt) echo 'selected'; ?>><?php echo htmlspecialchars($opt); ?></option>
                            <?php endforeach; ?>
                        </select>

                    <?php elseif ($colDef['type'] === 'fk'): ?>
                        <select name="<?php echo $colName; ?>" class="form-control" <?php if($colDef['required']) echo 'required'; ?>>
                            <option value="">-- Select --</option>
                            <?php foreach($fkOptions[$colName] as $opt): ?>
                                <option value="<?php echo htmlspecialchars($opt['value']); ?>" <?php if($val == $opt['value']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($opt['label']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    <?php else: ?>
                        <input type="<?php echo $colDef['type']; ?>" 
                               name="<?php echo $colName; ?>" 
                               class="form-control" 
                               value="<?php echo htmlspecialchars($val); ?>"
                               <?php if(isset($colDef['step'])) echo 'step="'.$colDef['step'].'"'; ?>
                               <?php if($colDef['required']) echo 'required'; ?>>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Save <?php echo htmlspecialchars($tableDef['label']); ?></button>
            </div>
        </form>
    </div>
</body>
</html>