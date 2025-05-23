<?php
// includes/auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$timeout = 1800; // 30 minutes
if (isset($_SESSION['last_active']) && time() - $_SESSION['last_active'] > $timeout) {
    session_unset();
    session_destroy();
    header("Location: /login.php?timeout=1");
    exit;
}
$_SESSION['last_active'] = time();

if (!isset($_SESSION['user_uuid'])) {
    header("Location: /login.php");
    exit;
}
