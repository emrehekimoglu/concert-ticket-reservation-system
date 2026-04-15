<?php

class Database
{
    private $conn;
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this->connect();
    }

    private function connect()
    {
        if (!function_exists('oci_connect') || !function_exists('oci_error') || !function_exists('oci_parse')) {
            // Fallback for environments without OCI8 (just to show UI logic works)
            // In production, this should die.
            $this->conn = null;
            return;
        }

        $tns = "(DESCRIPTION =
            (ADDRESS = (PROTOCOL = TCP)(HOST = {$this->config['host']})(PORT = {$this->config['port']}))
            (CONNECT_DATA =
                (SERVICE_NAME = {$this->config['service_name']})
            )
        )";

        $this->conn = @oci_connect(
            $this->config['username'],
            $this->config['password'],
            $tns,
            $this->config['charset']
        );

        if (!$this->conn) {
            $e = oci_error();
            throw new Exception("Connection failed: " . $e['message']);
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function query($sql, $params = [])
    {
        if (!$this->conn) {
            // Mock response if no connection (dev mode)
            return [];
        }

        $stmt = oci_parse($this->conn, $sql);
        if (!$stmt) {
            $e = oci_error($this->conn);
            throw new Exception("Statement parse failed: " . $e['message']);
        }

        foreach ($params as $key => $val) {
            // Bind variables. OCI8 binds by reference, so we need a wrapper
            // But doing it simply:
            oci_bind_by_name($stmt, $key, $params[$key]);
        }

        $r = oci_execute($stmt, OCI_COMMIT_ON_SUCCESS);
        if (!$r) {
            $e = oci_error($stmt);
            throw new Exception("Execution failed: " . $e['message']);
        }

        // Determine query type to decide return
        $upperSql = strtoupper(trim($sql));
        if (strpos($upperSql, 'SELECT') === 0) {
            $results = [];
            oci_fetch_all($stmt, $results, 0, -1, OCI_FETCHSTATEMENT_BY_ROW);
            oci_free_statement($stmt);
            return $results;
        }

        oci_free_statement($stmt);
        return true;
    }

    public function fetchOne($sql, $params = [])
    {
        $rows = $this->query($sql, $params);
        return $rows ? $rows[0] : null;
    }
}
