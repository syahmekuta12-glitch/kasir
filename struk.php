<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config/koneksi.php';

if (!isset($_GET['id'])) {
    die("ID transaksi tidak ada");
}

$id = intval($_GET['id']);

/* ======================
   AMBIL TRANSAKSI
====================== */
$qTrx = mysqli_query($conn, "
    SELECT t.*, p.nama AS nama_kasir, m.nama_member
    FROM transaksi t
    JOIN pegawai p ON t.id_pegawai = p.id_pegawai
    LEFT JOIN member m ON t.id_member = m.id_member
    WHERE t.id_transaksi = $id
");

if (!$qTrx) {
    die("Query transaksi error: " . mysqli_error($conn));
}

$trx = mysqli_fetch_assoc($qTrx);

/* ======================
   AMBIL DETAIL BARANG
====================== */
$qDetail = mysqli_query($conn, "
    SELECT d.*, b.nama_barang
    FROM detail_transaksi d
    JOIN barang b ON d.id_barang = b.id_barang
    WHERE d.id_transaksi = $id
");

if (!$qDetail) {
    die("Query detail error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Transaksi</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', sans-serif;
        }
        .struk-wrapper {
            max-width: 700px;
            margin: 40px auto;
        }
        .header {
            background: linear-gradient(135deg, #5f5bff, #3f3cbb);
            color: white;
            border-radius: 16px 16px 0 0;
            padding: 20px;
        }
        .content {
            background: #fff;
            padding: 25px;
            border-radius: 0 0 16px 16px;
        }
        .table th {
            background: #f5f5f5;
        }
        .total-box {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 15px;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                background: white;
            }
        }
        table th,
table td {
    text-align: center;
}
    </style>
</head>

<body>

<div class="struk-wrapper shadow">

    <!-- HEADER -->
    <div class="header">
        <h4 class="mb-1">🧾 SISTEM KASIR</h4>
        <small>Struk Transaksi</small>
    </div>

    <!-- CONTENT -->
    <div class="content">

        <!-- INFO -->
        <div class="row mb-3">
            <div class="col-md-6">
                <small class="text-muted">Tanggal</small><br>
                <strong><?= $trx['tanggal'] ?></strong>
            </div>
            <div class="col-md-6 text-md-end">
                <small class="text-muted">Kasir</small><br>
                <strong><?= $trx['nama_kasir'] ?></strong>
            </div>
        </div>

        <div class="mb-3">
            <small class="text-muted">Member</small><br>
            <strong><?= $trx['nama_member'] ?? '-' ?></strong>
        </div>

        <!-- TABEL BARANG -->
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Barang</th>
                        <th width="100">Jumlah</th>
                        <th width="150" class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $total_qty = 0;
                while ($d = mysqli_fetch_assoc($qDetail)) {
                    $total_qty += $d['jumlah'];
                ?>
                    <tr>
                        <td><?= $d['nama_barang'] ?></td>
                        <td><?= $d['jumlah'] ?></td>
                        <td class="text-end">Rp <?= number_format($d['subtotal']) ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- TOTAL -->
        <div class="total-box mt-3">
            <div class="d-flex justify-content-between">
                <span>Total Qty</span>
                <strong><?= $total_qty ?></strong>
            </div>
            <div class="d-flex justify-content-between">
                <span>Subtotal</span>
                <strong>Rp <?= number_format($trx['total']) ?></strong>
            </div>
            <div class="d-flex justify-content-between">
                <span>Diskon</span>
                <strong>Rp <?= number_format($trx['diskon']) ?></strong>
            </div>
            <hr>
            <div class="d-flex justify-content-between fs-5">
                <strong>Total Bayar</strong>
                <strong class="text-success">Rp <?= number_format($trx['total_bayar']) ?></strong>
            </div>
        </div>

        <!-- BUTTON -->
        <div class="d-flex justify-content-between mt-4 no-print">
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Cetak Struk
            </button>
        </div>

    </div>
</div>



</body>
</html>