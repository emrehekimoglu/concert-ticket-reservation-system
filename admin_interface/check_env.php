<?php
// Environment Checker
echo "<h1>PHP Environment Check</h1>";

echo "<h3>1. Loaded Configuration</h3>";
$ini = php_ini_loaded_file();
if ($ini) {
    echo "Using php.ini: <strong>" . $ini . "</strong><br>";
} else {
    echo "<strong style='color:red'>No php.ini loaded!</strong><br>";
    echo "Please find <code>php.ini-development</code> in your PHP folder and rename it to <code>php.ini</code>.<br>";
}

echo "<h3>2. OCI8 Extension Status</h3>";
if (function_exists('oci_connect')) {
    echo "<strong style='color:green'>OCI8 is ENABLED!</strong> <br>";
    echo "You can now go back to the <a href='index.php'>Dashboard</a>.";
} else {
    echo "<strong style='color:red'>OCI8 is DISABLED.</strong><br>";
    echo "Please follow these steps:<br>";
    echo "<ol>";
    echo "<li>Open <strong>$ini</strong> in Notepad.</li>";
    echo "<li>Search for <code>extension_dir</code>.</li>";
    echo "<li>Make sure <code>extension_dir = \"ext\"</code> is UNCOMMENTED (remove the semicolon ;).</li>";
    echo "<li>Search for <code>extension=oci8</code> (or oci8_12c / oci8_19).</li>";
    echo "<li>Remove the semicolon (;) from the start of that line.</li>";
    echo "<li>Save the file and <strong>restart the server</strong> (close the black window and run start_server.bat again).</li>";
    echo "</ol>";
}

// Scan ext dir
$extDir = ini_get('extension_dir');
if ($extDir === 'ext' || $extDir === './ext') {
    // changing directory context might be tricky in web server, lets try relative
    $dirsToCheck = ['ext', 'C:\php\ext', dirname($ini) . '\ext'];
} else {
    $dirsToCheck = [$extDir];
}

$foundDll = false;
foreach ($dirsToCheck as $d) {
    if (is_dir($d)) {
        echo "Scanning directory: <strong>$d</strong><br>";
        $files = scandir($d);
        foreach ($files as $f) {
            if (strpos($f, 'oci8') !== false) {
                echo "Found OCI DLL: <strong>$f</strong>. <br>";
                $extName = pathinfo($f, PATHINFO_FILENAME); // e.g. php_oci8_12c
                $line = "extension=" . str_replace('php_', '', $extName); // e.g. extension=oci8_12c
                echo "Please add this line to your php.ini: <br>";
                echo "<code>$line</code><br><br>";
                $foundDll = true;
            }
        }
    }
}

if (!$foundDll) {
    echo "<strong style='color:red'>Could not find any oci8 DLLs in extension directory.</strong><br>";
    echo "Please download the PECL package for OCI8 or check if your PHP installation includes it.<br>";
}

echo "<h3>3. Connection Test</h3>";
if (function_exists('oci_connect')) {
    // Only try if extension exists
    require 'debug_connection.php';
} else {
    echo "<em>Fix OCI8 first to test connection.</em>";
}
