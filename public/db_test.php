<?php
// Full error reporting for troubleshooting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load configuration from outside web root
$configFile = __DIR__ . '/../confg/nati.php';

if (!file_exists($configFile)) {
    die("Error: Configuration file not found at $configFile");
}

$config = require $configFile;

if (!is_array($config)) {
    die("Error: Configuration file did not return an array.");
}

// Connect to MySQL
$mysqli = new mysqli(
    $config['host'],
    $config['username'],
    $config['password'],
    $config['database'],
    $config['port']
);

if ($mysqli->connect_error) {
    die("MySQL connection failed: " . $mysqli->connect_error);
}

echo "✅ Database connection successful!";
?>
