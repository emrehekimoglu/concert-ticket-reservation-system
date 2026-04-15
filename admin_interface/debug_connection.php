<?php
// Debug Script for ConcertSys
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Debugger</h1>";

try {
    if (!file_exists('config/config.php')) {
        throw new Exception("Config file not found!");
    }
    $config = require 'config/config.php';
    echo "Loaded config.<br>";

    // Manual Connection
    $dbConfig = $config['db'];
    $tns = "(DESCRIPTION =
            (ADDRESS = (PROTOCOL = TCP)(HOST = {$dbConfig['host']})(PORT = {$dbConfig['port']}))
            (CONNECT_DATA =
                (SERVICE_NAME = {$dbConfig['service_name']})
            )
        )";

    echo "Connecting to: $tns <br>";
    echo "User: {$dbConfig['username']} <br>";

    $conn = oci_connect($dbConfig['username'], $dbConfig['password'], $tns, $dbConfig['charset']);

    if (!$conn) {
        $e = oci_error();
        throw new Exception("Connection failed: " . $e['message']);
    }
    echo "<strong>Connection Successful!</strong><br><hr>";

    // Test Select
    $sql = "SELECT * FROM VENUE";
    echo "Executing: $sql <br>";

    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);

    $rows = [];
    $count = oci_fetch_all($stmt, $rows, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);

    echo "Found $count rows.<br>";
    if ($count > 0) {
        echo "<pre>";
        print_r($rows);
        echo "</pre>";
    } else {
        echo "Table seems empty.<br>";

        // Try Insert
        echo "Attempting test insert...<br>";
        $randId = rand(1000, 9999);
        $insert = "INSERT INTO VENUE (VenueID, Name, Address, Capacity) VALUES ($randId, 'Debug Venue $randId', 'Debug Lane', 100)";
        $stmt2 = oci_parse($conn, $insert);
        $r = oci_execute($stmt2, OCI_COMMIT_ON_SUCCESS);

        if ($r) {
            echo "Insert successful (ID: $randId). Checking again...<br>";
            oci_execute($stmt); // Re-run select
            $rows2 = [];
            $count2 = oci_fetch_all($stmt, $rows2, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);
            echo "Found $count2 rows now.<br>";
            print_r($rows2);
        } else {
            $e = oci_error($stmt2);
            echo "Insert failed: " . $e['message'];
        }
    }

} catch (Exception $e) {
    echo "<div style='color:red'><strong>Error:</strong> " . $e->getMessage() . "</div>";
}
