<?php
echo "<h1>🚀 PHP is Working!</h1>";
echo "<p>Today is " . date("Y-m-d H:i:s") . "</p>";
echo "<p>Your server IP is: " . $_SERVER['SERVER_ADDR'] . "</p>";

echo "<h2>Server Details</h2>";
echo "<ul>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "<li>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</li>";
echo "<li>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</li>";
echo "</ul>";

// Uncomment the line below if you want to dump all PHP info
// phpinfo();
?>
