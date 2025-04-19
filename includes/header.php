<!-- /includes/header.php -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html><head><title>NATI</title></head><body>
<?php if (isset($_SESSION['username'])): ?>
    <div style="text-align: right;">Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?> |
        <a href="/logout.php">Logout</a>
    </div>
<?php endif; ?>
<hr>
