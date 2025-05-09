<?php
// /public/login.php
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        $errors[] = "Both username and password are required.";
    } else {
        $stmt = $mysqli->prepare("SELECT user_uuid, password_hash, full_name FROM nati_user WHERE username = ? AND active = TRUE AND source = 'local' LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_uuid'] = $user['user_uuid'];
                $_SESSION['username'] = $username;
                $_SESSION['full_name'] = $user['full_name'] ?? $username;
                header("Location: table_viewer.php");
                exit;
            } else {
                $errors[] = "Invalid username or password.";
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
    <title>Login - NATI</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        form { max-width: 400px; margin: auto; }
        input { display: block; width: 100%; padding: 8px; margin-bottom: 10px; }
        .error { color: red; }
    </style>
</head>
<body>

<h1>Login</h1>

<?php if ($errors): ?>
    <ul class="error">
        <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars((string)$e) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post">
    <input type="text" name="username" placeholder="Username" required value="<?= htmlspecialchars((string)($_POST['username'] ?? '')) ?>">
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>

<p>Need an account? <a href="register.php">Register here</a></p>

</body>
</html>
