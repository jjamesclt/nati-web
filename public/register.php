<?php
session_start();
$config = include(__DIR__ . '/../config/nati.php');

$mysqli = new mysqli(
    $config['database']['host'],
    $config['database']['user'],
    $config['database']['pass'],
    $config['database']['name'],
    $config['database']['port']
);

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$username || !$email || !$password || !$confirm) {
        $errors[] = "All fields are required.";
    } elseif ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $uuid = bin2hex(random_bytes(16));
        $source = 'local';

        $stmt = $mysqli->prepare("INSERT INTO nati_user (user_uuid, username, full_name, email, password_hash, source) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssssss", $uuid, $username, $full_name, $email, $password_hash, $source);
            if ($stmt->execute()) {
                $success = true;
            } else {
                $errors[] = "Registration failed: " . $mysqli->error;
            }
        } else {
            $errors[] = "Database error: " . $mysqli->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - NATI</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        form { max-width: 400px; margin: auto; }
        input { display: block; width: 100%; padding: 8px; margin-bottom: 10px; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>

<h1>Register</h1>

<?php if ($success): ?>
    <p class="success">Registration successful! <a href="login.php">Log in</a></p>
<?php else: ?>
    <?php if ($errors): ?>
        <ul class="error">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars((string)$e) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="username" placeholder="Username" required value="<?= htmlspecialchars((string)($_POST['username'] ?? '')) ?>">
        <input type="text" name="full_name" placeholder="Full Name" value="<?= htmlspecialchars((string)($_POST['full_name'] ?? '')) ?>">
        <input type="email" name="email" placeholder="Email" required value="<?= htmlspecialchars((string)($_POST['email'] ?? '')) ?>">
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit">Register</button>
    </form>
<?php endif; ?>

</body>
</html>
