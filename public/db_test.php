<?php
// Secure config path outside web root
$configFile = __DIR__ . '/../config/nati.php';

// Check if the config file exists
if (!file_exists($configFile)) {
    die("Error: Configuration file not found.");
}

$config = require $configFile;

if (!is_array($config)) {
    die("Error: Failed to load configuration array from config file.");
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$mysqli = new mysqli(
    $config['host'],
    $config['username'],
    $config['password'],
    $config['database'],
    $config['port']
);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "Database connection successful!";
?>
