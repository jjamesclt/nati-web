<?php
// /public/create_api_key.php
require_once __DIR__ . '/../includes/auth.php';
include __DIR__ . '/../includes/header.php';

// Connect to your database using your configuration
$config = include(__DIR__ . '/../config/nati.php');
$mysqli = new mysqli(
    $config['database']['host'],
    $config['database']['user'],
    $config['database']['pass'],
    $config['database']['name'],
    $config['database']['port']
);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$new_api_key = '';  // This will hold the newly generated key to display to the user
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Function to generate a UUID v4
    function generate_uuid_v4() {
        $data = random_bytes(16);
        // Set version to 0100 (i.e., v4)
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    // Use the current username from session as the API key label
    $username = $_SESSION['username'];

    // Generate a new random API key (plain-text)
    $plain_api_key = bin2hex(random_bytes(32)); // 64 hex characters
    // Create a SHA-256 hash of the API key to store in the database
    $hashed_api_key = hash('sha256', $plain_api_key);

    // Check if an API key record for this username already exists
    $stmt = $mysqli->prepare("SELECT key_id FROM nati_api_key WHERE label = ?");
    if (!$stmt) {
        $errors[] = "Database error: " . $mysqli->error;
    } else {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            // Record exists – update it with the new API key hash
            $stmt->close();
            $update_stmt = $mysqli->prepare("UPDATE nati_api_key SET api_key_hash = ?, timestamp = CURRENT_TIMESTAMP WHERE label = ?");
            if (!$update_stmt) {
                $errors[] = "Database error: " . $mysqli->error;
            } else {
                $update_stmt->bind_param("ss", $hashed_api_key, $username);
                if (!$update_stmt->execute()) {
                    $errors[] = "Update failed: " . $mysqli->error;
                }
                $update_stmt->close();
            }
        } else {
            // No existing record – insert a new one
            $stmt->close();
            $new_key_id = generate_uuid_v4();
            $insert_stmt = $mysqli->prepare("INSERT INTO nati_api_key (key_id, label, api_key_hash) VALUES (?, ?, ?)");
            if (!$insert_stmt) {
                $errors[] = "Database error: " . $mysqli->error;
            } else {
                $insert_stmt->bind_param("sss", $new_key_id, $username, $hashed_api_key);
                if (!$insert_stmt->execute()) {
                    $errors[] = "Insert failed: " . $mysqli->error;
                }
                $insert_stmt->close();
            }
        }
        // Save the plain API key to display to the user
        $new_api_key = $plain_api_key;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create API Key - NATI</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .message { margin-top: 20px; padding: 10px; border: 1px solid #ccc; background: #f9f9f9; }
        .error { color: red; }
    </style>
</head>
<body>

<h1>Create New API Key</h1>

<?php if (!empty($errors)): ?>
    <div class="message error">
        <ul>
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars((string)$e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($new_api_key): ?>
    <div class="message">
        <p>Your new API key is:</p>
        <pre><?= htmlspecialchars((string)$new_api_key) ?></pre>
        <p><em>Please save this key securely. It will not be shown again.</em></p>
    </div>
<?php endif; ?>

<form method="post">
    <button type="submit">Generate New API Key</button>
</form>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>
