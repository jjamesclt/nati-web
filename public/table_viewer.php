<?php
// /public/table_viewer.php
require_once __DIR__ . '/../includes/auth.php';
include __DIR__ . '/../includes/header.php';
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

$tables = [];
$result = $mysqli->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

$selected_table = $_GET['table'] ?? null;
$rows = [];
$columns = [];

if ($selected_table) {
    $stmt = $mysqli->prepare("SELECT * FROM `$selected_table` LIMIT 100");
    if ($stmt && $stmt->execute()) {
        $result = $stmt->get_result();
        $columns = array_keys($result->fetch_assoc() ?? []);
        $result->data_seek(0); // reset pointer
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    } else {
        $error = $mysqli->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NATI Table Viewer</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        select { padding: 5px; }
    </style>
</head>
<body>

<h1>NATI Table Viewer</h1>

<form method="get">
    <label for="table">Select Table:</label>
    <select name="table" id="table" onchange="this.form.submit()">
        <option value="">-- Choose a table --</option>
        <?php foreach ($tables as $table): ?>
            <option value="<?= htmlspecialchars((string)$table) ?>" <?= $selected_table === $table ? 'selected' : '' ?>>
                <?= htmlspecialchars((string)$table) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php if ($selected_table): ?>
    <h2>Table: <?= htmlspecialchars((string)$selected_table) ?></h2>
    <?php if (!empty($rows)): ?>
        <table>
            <thead>
            <tr>
                <?php foreach ($columns as $col): ?>
                    <th><?= htmlspecialchars((string)$col) ?></th>
                <?php endforeach; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <?php foreach ($columns as $col): ?>
                        <td><?= htmlspecialchars((string)($row[$col] ?? '')) ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif (isset($error)): ?>
        <p style="color: red;">Error: <?= htmlspecialchars((string)$error) ?></p>
    <?php else: ?>
        <p>No rows found in this table.</p>
    <?php endif; ?>
<?php endif; ?>

</body>
</html>
<?php include __DIR__ . '/../includes/footer.php'; ?>