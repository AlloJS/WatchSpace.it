<?php
require_once 'connection.php';

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$brandFilter = isset($_GET['brand']) ? trim($_GET['brand']) : '';

$results = [];

try {

    // =========================
    // STATS
    // =========================
    $totalWatches = $pdo->query("SELECT COUNT(*) as c FROM watches")->fetch()['c'];

    $totalBrands = $pdo->query("
        SELECT COUNT(*) as c FROM (
            SELECT DISTINCT brand FROM watches
        )
    ")->fetch()['c'];

    // =========================
    // BRANDS
    // =========================
    $brandsStmt = $pdo->query("
        SELECT brand, COUNT(*) as total
        FROM watches
        GROUP BY brand
        ORDER BY brand ASC
    ");
    $brands = $brandsStmt->fetchAll();

    // =========================
    // ALL WATCHES (for grouping)
    // =========================
    $allStmt = $pdo->query("
        SELECT *
        FROM watches
        ORDER BY brand ASC, model ASC
    ");
    $allWatches = $allStmt->fetchAll();

    $grouped = [];
    foreach ($allWatches as $w) {
        $grouped[$w['brand']][] = $w;
    }

    // =========================
    // SEARCH / FILTER
    // =========================
    if ($search !== '' || $brandFilter !== '') {

        $sql = "SELECT * FROM watches WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (brand LIKE :search OR model LIKE :search OR reference LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if ($brandFilter !== '') {
            $sql .= " AND brand = :brand";
            $params[':brand'] = $brandFilter;
        }

        $sql .= " ORDER BY brand ASC, model ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $results = $stmt->fetchAll();
    }

} catch (PDOException $e) {
    die("Errore: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>WatchSpecs.io</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{background:#f8f9fa;}
.hero{background:linear-gradient(135deg,#0f172a,#1e293b);color:white;padding:90px 0;}
.hero h1{font-size:4rem;font-weight:800;}
.search-input{height:60px;border-radius:16px;}
.stats-card{border:none;border-radius:18px;box-shadow:0 10px 30px rgba(0,0,0,.05);}
.brand-btn{
    margin:4px;
    border-radius:999px;
    padding:6px 14px;
    border:1px solid #ddd;
    background:white;
    text-decoration:none;
    color:#111;
    display:inline-block;
}
.brand-btn:hover{background:#111;color:white;}
.watch-card{border:none;border-radius:18px;transition:.2s;}
.watch-card:hover{transform:translateY(-4px);}
.footer{background:#111827;color:white;padding:40px 0;margin-top:80px;}
</style>
</head>

<body>

<!-- HERO -->
<section class="hero">
<div class="container text-center">

<h1>⌚ WatchSpecs</h1>
<p class="lead mb-4">Watch encyclopedia database</p>

<form method="GET">
<input type="text"
       name="q"
       class="form-control form-control-lg search-input"
       value="<?= htmlspecialchars($search) ?>"
       placeholder="Search watches...">
</form>

</div>
</section>

<div class="container">

<!-- STATS -->
<div class="row g-4 mt-n5">

<div class="col-md-4">
<div class="card stats-card p-4 text-center">
<h2><?= $totalWatches ?></h2>
<small>Total Watches</small>
</div>
</div>

<div class="col-md-4">
<div class="card stats-card p-4 text-center">
<h2><?= $totalBrands ?></h2>
<small>Brands</small>
</div>
</div>

<div class="col-md-4">
<div class="card stats-card p-4 text-center">
<h2>∞</h2>
<small>Growing Daily</small>
</div>
</div>

</div>

<!-- BRANDS BUTTONS -->
<section class="mt-5">

<h4>Brands</h4>

<a class="brand-btn" href="?">All</a>

<?php foreach($brands as $b): ?>
    <a class="brand-btn" href="?brand=<?= urlencode($b['brand']) ?>">
        <?= htmlspecialchars($b['brand']) ?> (<?= $b['total'] ?>)
    </a>
<?php endforeach; ?>

</section>

<!-- SEARCH / FILTER RESULTS -->
<?php if($search !== '' || $brandFilter !== ''): ?>

<section class="mt-5">

<h3>Results (<?= count($results) ?>)</h3>

<div class="row g-4 mt-2">

<?php foreach($results as $watch): ?>

<div class="col-lg-4">

<a href="watch.php?id=<?= $watch['id'] ?>"
   class="card watch-card shadow-sm h-100 text-decoration-none text-dark">

<div class="card-body">

<span class="badge bg-dark mb-2">
<?= htmlspecialchars($watch['brand']) ?>
</span>

<h5><?= htmlspecialchars($watch['model']) ?></h5>

<p class="text-muted">
Ref: <?= htmlspecialchars($watch['reference']) ?>
</p>

</div>

</a>

</div>

<?php endforeach; ?>

</div>

</section>

<?php else: ?>

<!-- CATALOG GROUPED -->
<section class="mt-5">

<h3>Catalog</h3>

<?php foreach($grouped as $brand => $watches): ?>

<div class="mb-5">

<h4 class="mb-3"><?= htmlspecialchars($brand) ?></h4>

<div class="row g-3">

<?php foreach($watches as $watch): ?>

<div class="col-lg-3 col-md-4">

<a href="watch.php?id=<?= $watch['id'] ?>"
   class="card watch-card shadow-sm h-100 text-decoration-none text-dark">

<div class="card-body">

<span class="badge bg-dark mb-2">
<?= htmlspecialchars($watch['brand']) ?>
</span>

<h6><?= htmlspecialchars($watch['model']) ?></h6>

<small class="text-muted">
Ref: <?= htmlspecialchars($watch['reference']) ?>
</small>

</div>

</a>

</div>

<?php endforeach; ?>

</div>

</div>

<?php endforeach; ?>

</section>

<?php endif; ?>

</div>

<!-- FOOTER -->
<footer class="footer text-center">
<h5>WatchSpecs.io</h5>
<p>Watch encyclopedia database</p>
</footer>

</body>
</html>