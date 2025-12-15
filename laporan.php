<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: auth/login.php");
    exit;
}

/* =============================
   FILTER PERIODE
============================= */
$periode = $_GET['periode'] ?? 'harian';

switch ($periode) {

    // ================= HARIAN (7 hari terakhir)
    case 'harian':
        $where = "DATE(tanggal) >= CURDATE() - INTERVAL 6 DAY";
        $group = "DATE(tanggal)";
        $label_sql = "DATE_FORMAT(tanggal, '%d %b')";
        break;

    // ================= MINGGUAN (minggu ini)
    case 'mingguan':
        $where = "YEARWEEK(tanggal, 1) = YEARWEEK(CURDATE(), 1)";
        $group = "DATE(tanggal)";
        $label_sql = "DATE_FORMAT(tanggal, '%d %b')";
        break;

    // ================= BULANAN (bulan ini)
    case 'bulanan':
        $where = "MONTH(tanggal) = MONTH(CURDATE()) 
                  AND YEAR(tanggal) = YEAR(CURDATE())";
        $group = "DATE(tanggal)";
        $label_sql = "DATE_FORMAT(tanggal, '%d')";
        break;

    // ================= TAHUNAN
    case 'tahunan':
        $where = "YEAR(tanggal) = YEAR(CURDATE())";
        $group = "MONTH(tanggal)";
        $label_sql = "DATE_FORMAT(tanggal, '%b')";
        break;
}

/* =============================
   STATISTIK
============================= */
$qStat = mysqli_query($conn, "
    SELECT 
        COUNT(*) AS total_transaksi,
        SUM(total_bayar) AS pendapatan
    FROM transaksi
    WHERE $where
");
$stat = mysqli_fetch_assoc($qStat);

$qBarang = mysqli_query($conn, "
    SELECT SUM(d.jumlah) AS total_barang
    FROM detail_transaksi d
    JOIN transaksi t ON d.id_transaksi = t.id_transaksi
    WHERE $where
");
$barang = mysqli_fetch_assoc($qBarang);

$rata = ($stat['total_transaksi'] > 0)
    ? $stat['pendapatan'] / $stat['total_transaksi']
    : 0;

/* =============================
   DATA GRAFIK
============================= */
$qGrafik = mysqli_query($conn, "
    SELECT 
        $label_sql AS label,
        SUM(total_bayar) AS total
    FROM transaksi
    WHERE $where
    GROUP BY $group
    ORDER BY $group
");

$labels = [];
$values = [];

while ($g = mysqli_fetch_assoc($qGrafik)) {
    $labels[] = $g['label'];
    $values[] = (int)$g['total'];
}

/* =============================
   RIWAYAT TRANSAKSI (BENAR)
============================= */
$qRiwayat = mysqli_query($conn, "
    SELECT 
        t.id_transaksi,
        DATE(t.tanggal) AS tanggal,
        TIME(t.tanggal) AS waktu,
        SUM(d.jumlah) AS jumlah_item,
        t.total_bayar
    FROM transaksi t
    JOIN detail_transaksi d ON t.id_transaksi = d.id_transaksi
    GROUP BY t.id_transaksi
    ORDER BY t.tanggal DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Penjualan</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.content { margin-left: 280px; padding: 25px; }
.card { border-radius: 18px; }
.header {
    background: linear-gradient(135deg, #5f5bff, #3f3cbb);
    color: white;
}
.stat-icon {
    width: 46px;
    height: 46px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}
</style>
</head>

<body class="bg-light">
<?php include 'sidebar.php'; ?>

<div class="content">

<div class="card header p-4 mb-4 shadow-sm">
    <h4>📊 Laporan Penjualan</h4>
    <small>Statistik dan laporan penjualan toko</small>
</div>

<!-- FILTER -->
<div class="btn-group mb-4">
    <a href="?periode=harian" class="btn btn-outline-primary <?= $periode=='harian'?'active':'' ?>">Harian</a>
    <a href="?periode=mingguan" class="btn btn-outline-primary <?= $periode=='mingguan'?'active':'' ?>">Mingguan</a>
    <a href="?periode=bulanan" class="btn btn-outline-primary <?= $periode=='bulanan'?'active':'' ?>">Bulanan</a>
    <a href="?periode=tahunan" class="btn btn-outline-primary <?= $periode=='tahunan'?'active':'' ?>">Tahunan</a>
</div>

<!-- STAT -->
<div class="row g-4 mb-4">
<?php
$statBox = [
    ['cash-stack','primary','Total Pendapatan','Rp '.number_format($stat['pendapatan'] ?? 0)],
    ['receipt','success','Total Transaksi',$stat['total_transaksi'] ?? 0],
    ['graph-up','info','Rata-rata','Rp '.number_format($rata)],
    ['box','warning','Barang Terjual',$barang['total_barang'] ?? 0]
];
foreach ($statBox as $s):
?>
<div class="col-md-3">
<div class="card p-4 shadow-sm">
<div class="stat-icon bg-<?= $s[1] ?> bg-opacity-10 text-<?= $s[1] ?> mb-3">
<i class="bi bi-<?= $s[0] ?>"></i>
</div>
<small class="text-muted"><?= $s[2] ?></small>
<h4><?= $s[3] ?></h4>
</div>
</div>
<?php endforeach; ?>
</div>

<!-- GRAFIK -->
<div class="row g-4 mb-4">
<div class="col-md-6">
<div class="card p-4 shadow-sm">
<h5>Grafik Penjualan</h5>
<canvas id="grafikPenjualan"></canvas>
</div>
</div>

<div class="col-md-6">
<div class="card p-4 shadow-sm">
<h5>Tren Transaksi</h5>
<canvas id="grafikTren"></canvas>
</div>
</div>
</div>

<!-- RIWAYAT -->
<div class="card p-4 shadow-sm">
<h5>Riwayat Transaksi</h5>
<table class="table table-hover mt-3">
<thead>
<tr>
<th>ID</th>
<th>Tanggal</th>
<th>Waktu</th>
<th>Item</th>
<th>Total</th>
</tr>
</thead>
<tbody>
<?php while ($r = mysqli_fetch_assoc($qRiwayat)): ?>
<tr>
<td><?= $r['id_transaksi'] ?></td>
<td><?= date('d M Y', strtotime($r['tanggal'])) ?></td>
<td><?= $r['waktu'] ?></td>
<td><?= $r['jumlah_item'] ?></td>
<td>Rp <?= number_format($r['total_bayar']) ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>

</div>

<script>
const labels = <?= json_encode($labels) ?>;
const values = <?= json_encode($values) ?>;

new Chart(grafikPenjualan, {
    type: 'bar',
    data: { labels, datasets: [{ data: values, backgroundColor:'#5f5bff' }] }
});

new Chart(grafikTren, {
    type: 'line',
    data: { labels, datasets: [{ data: values, borderColor:'#3f3cbb', tension:.4 }] }
});
</script>

</body>
</html>