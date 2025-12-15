<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: auth/login.php");
    exit;
}

// ================= STATISTIK =================

// Transaksi hari ini
$q1 = mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM transaksi 
    WHERE DATE(tanggal) = CURDATE()
");
$trx_hari = mysqli_fetch_assoc($q1);

// Pendapatan hari ini
$q2 = mysqli_query($conn, "
    SELECT SUM(total - diskon) AS pendapatan 
    FROM transaksi 
    WHERE DATE(tanggal) = CURDATE()
");
$pend_hari = mysqli_fetch_assoc($q2);

// Transaksi bulan ini
$q3 = mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM transaksi 
    WHERE MONTH(tanggal) = MONTH(CURDATE()) 
      AND YEAR(tanggal) = YEAR(CURDATE())
");
$trx_bulan = mysqli_fetch_assoc($q3);

// Pendapatan bulan ini
$q4 = mysqli_query($conn, "
    SELECT SUM(total - diskon) AS pendapatan 
    FROM transaksi 
    WHERE MONTH(tanggal) = MONTH(CURDATE()) 
      AND YEAR(tanggal) = YEAR(CURDATE())
");
$pend_bulan = mysqli_fetch_assoc($q4);

// Total transaksi
$q5 = mysqli_query($conn, "SELECT COUNT(*) AS total FROM transaksi");
$total_trx = mysqli_fetch_assoc($q5);

// Total pendapatan
$q6 = mysqli_query($conn, "SELECT SUM(total - diskon) AS pendapatan FROM transaksi");
$total_pend = mysqli_fetch_assoc($q6);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | Sistem Kasir</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .content {
            margin-left: 280px;
            padding: 25px;
        }
        .card {
            border-radius: 18px;
        }
        .welcome {
            background: linear-gradient(135deg, #5f5bff, #3f3cbb);
            color: white;
        }
    </style>
</head>

<body>
<?php include 'sidebar.php'; ?>

<div class="content">

    <!-- WELCOME -->
    <div class="card welcome p-4 mb-4 shadow-sm">
        <h4>👋 Selamat Datang, <?= $_SESSION['nama']; ?></h4>
        <small>Semoga hari ini penjualan lancar 🚀</small>
    </div>

    <!-- STATISTIK -->
    <div class="row g-4">

        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <small class="text-muted">Transaksi Hari Ini</small>
                <h3><?= $trx_hari['total'] ?? 0 ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <small class="text-muted">Pendapatan Hari Ini</small>
                <h4>Rp <?= number_format($pend_hari['pendapatan'] ?? 0) ?></h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <small class="text-muted">Transaksi Bulan Ini</small>
                <h3><?= $trx_bulan['total'] ?? 0 ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <small class="text-muted">Pendapatan Bulan Ini</small>
                <h4>Rp <?= number_format($pend_bulan['pendapatan'] ?? 0) ?></h4>
            </div>
        </div>

    </div>

    <!-- TOTAL -->
    <div class="row g-4 mt-2">

        <div class="col-md-6">
            <div class="card p-4 shadow-sm">
                <small class="text-muted">Total Seluruh Transaksi</small>
                <h3><?= $total_trx['total'] ?? 0 ?></h3>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-4 shadow-sm">
                <small class="text-muted">Total Seluruh Pendapatan</small>
                <h3>Rp <?= number_format($total_pend['pendapatan'] ?? 0) ?></h3>
            </div>
        </div>

    </div>

</div>

</body>
</html>