$timeout = 1800; // 30 minutes
if (isset($_SESSION['last_active']) && time() - $_SESSION['last_active'] > $timeout) {
    session_unset();
    session_destroy();
    header("Location: /public/login.php?timeout=1");
    exit;
}
$_SESSION['last_active'] = time();
