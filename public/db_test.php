<?php
// Securely load configuration from one directory above the web root
$configFile = '/var/www/html/config/nati.php';

if (!file_exists($config_file)) {
    die("Error: Configuration file not found.");
}

// Parse ini file
$config = parse_ini_file($config_file, true);

// Extract database configuration
$db = $config['database'];

// Create database connection
$conn = new mysqli(
    $db['host'],
    $db['username'],
    $db['password'],
    $db['database'],
    (int)$db['port']
);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
} else {
    echo "Database Connection Successful!";
}

// Close connection
$conn->close();
?>
