<?php
require_once 'lib/auth.php';
require_once 'lib/db.php';
checkLogin();

$config = require 'config/config.php';
$schema = require 'lib/schema.php';

$tableName = $_REQUEST['table'] ?? '';
$action = $_REQUEST['action'] ?? 'save'; // save or delete

if (!isset($schema[$tableName])) {
    die("Invalid table.");
}

$tableDef = $schema[$tableName];
$pk = $tableDef['pk'];
$db = new Database($config['db']);

try {
    if ($action === 'delete') {
        $id = $_GET['id'] ?? null;
        if (!$id)
            die("Missing ID.");

        $sql = "DELETE FROM $tableName WHERE $pk = :id";
        $db->query($sql, [':id' => $id]);

        header("Location: list.php?table=" . urlencode($tableName));
        exit;
    } elseif ($action === 'save') {
        $pkVal = $_POST['pk_val'] ?? null; // If set, it's an UPDATE
        $params = [];

        if ($pkVal) {
            // UPDATE
            $setParts = [];
            foreach ($tableDef['columns'] as $colName => $colDef) {
                if ($colName === $pk)
                    continue; // Don't update PK
                if (isset($_POST[$colName])) {
                    $val = $_POST[$colName];
                    $bindName = ":$colName";

                    if (($colDef['type'] === 'datetime-local' || $colDef['type'] === 'date') && $val) {
                        // Oracle format logic
                        if ($colDef['type'] === 'datetime-local') {
                            // HTML5 sends "2023-01-01T12:00"
                            // We bind this string. SQL uses TO_TIMESTAMP(:val, 'YYYY-MM-DD"T"HH24:MI')
                            $setParts[] = "$colName = TO_TIMESTAMP($bindName, 'YYYY-MM-DD\"T\"HH24:MI')";
                        } else {
                            $setParts[] = "$colName = TO_DATE($bindName, 'YYYY-MM-DD')";
                        }
                    } else {
                        $setParts[] = "$colName = $bindName";
                    }
                    $params[$bindName] = $val;
                }
            }

            if (!empty($setParts)) {
                $sql = "UPDATE $tableName SET " . implode(', ', $setParts) . " WHERE $pk = :pk_id";
                $params[':pk_id'] = $pkVal;
                $db->query($sql, $params);
            }

        } else {
            // INSERT
            $cols = [];
            $vals = [];

            // Grab PK from form if provided
            if (isset($_POST[$pk]) && $_POST[$pk] !== '') {
                $cols[] = $pk;
                $vals[] = ":$pk";
                $params[":$pk"] = $_POST[$pk];
            }

            foreach ($tableDef['columns'] as $colName => $colDef) {
                if ($colName === $pk)
                    continue; // PK handled separately
                if (isset($_POST[$colName])) {
                    $val = $_POST[$colName];
                    // Skip empty optional fields to allow defaults? 
                    // Or explicit NULL? Let's explicit null if empty and not required.
                    if ($val === '' && !$colDef['required']) {
                        continue;
                    }
                    // What if required but empty? Database will complain. Stick to logic.

                    $bindName = ":$colName";

                    if (($colDef['type'] === 'datetime-local' || $colDef['type'] === 'date') && $val) {
                        $cols[] = $colName;
                        if ($colDef['type'] === 'datetime-local') {
                            $vals[] = "TO_TIMESTAMP($bindName, 'YYYY-MM-DD\"T\"HH24:MI')";
                        } else {
                            $vals[] = "TO_DATE($bindName, 'YYYY-MM-DD')";
                        }
                    } else {
                        $cols[] = $colName;
                        $vals[] = $bindName;
                    }
                    $params[$bindName] = $val;
                }
            }

            $colStr = implode(', ', $cols);
            $valStr = implode(', ', $vals);
            $sql = "INSERT INTO $tableName ($colStr) VALUES ($valStr)";

            $db->query($sql, $params);
        }

        header("Location: list.php?table=" . urlencode($tableName));
        exit;
    }

} catch (Exception $e) {
    // Basic Error Page
    echo "<h1>Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<a href='list.php?table=" . urlencode($tableName) . "'>Back</a>";
}
