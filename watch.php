<?php
require_once 'connection.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM watches WHERE id = :id");
$stmt->execute([':id' => $id]);

$watch = $stmt->fetch();

if (!$watch) {
    die("Watch not found");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($watch['brand']) ?> <?= htmlspecialchars($watch['model']) ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{background:#f8f9fa;}
.container{max-width:900px;margin-top:60px;}
.card{border:none;border-radius:18px;}
</style>
</head>

<body>

<div class="container">

<a href="index.php" class="btn btn-light mb-4">← Back</a>

<div class="card shadow-sm p-4">

<span class="badge bg-dark mb-3">
<?= htmlspecialchars($watch['brand']) ?>
</span>

<h1><?= htmlspecialchars($watch['model']) ?></h1>

<p class="text-muted">
Ref: <?= htmlspecialchars($watch['reference']) ?>
</p>

<hr>

<p><strong>Diameter:</strong> <?= $watch['case_diameter'] ?> mm</p>
<p><strong>Movement:</strong> <?= htmlspecialchars($watch['movement_type']) ?></p>
<p><strong>Calibre:</strong> <?= htmlspecialchars($watch['calibre']) ?></p>

<hr>

<p><?= htmlspecialchars($watch['description']) ?></p>

</div>

</div>

</body>
</html>