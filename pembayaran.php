<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: auth/login.php");
    exit;
}

if (empty($_SESSION['keranjang'])) {
    die("Keranjang kosong");
}

$id_member = $_SESSION['member']['id_member'] ?? null;
$id_pegawai = $_SESSION['id_pegawai'];

/* HITUNG TOTAL */
$total = 0;
$items = [];
foreach ($_SESSION['keranjang'] as $id_barang => $qty) {
    $q = mysqli_query($conn, "SELECT nama_barang, harga FROM barang WHERE id_barang='$id_barang'");
    $b = mysqli_fetch_assoc($q);
    $subtotal = $b['harga'] * $qty;
    $total += $subtotal;
    $items[] = [
        'nama' => $b['nama_barang'],
        'qty' => $qty,
        'subtotal' => $subtotal
    ];
}

$diskon = $id_member ? $total * 0.05 : 0;
$total_bayar = $total - $diskon;

/* PROSES BAYAR */
if (isset($_POST['bayar'])) {

    mysqli_query($conn, "
        INSERT INTO transaksi
        (tanggal, id_pegawai, id_member, total, diskon, total_bayar)
        VALUES
        (NOW(), '$id_pegawai', " . ($id_member ? "'$id_member'" : "NULL") . ",
         '$total', '$diskon', '$total_bayar')
    ");

    $id_transaksi = mysqli_insert_id($conn);

    foreach ($_SESSION['keranjang'] as $id_barang => $qty) {
        $q = mysqli_query($conn, "SELECT harga FROM barang WHERE id_barang='$id_barang'");
        $b = mysqli_fetch_assoc($q);
        $subtotal = $b['harga'] * $qty;

        mysqli_query($conn, "
            INSERT INTO detail_transaksi
            (id_transaksi, id_barang, jumlah, subtotal)
            VALUES
            ('$id_transaksi', '$id_barang', '$qty', '$subtotal')
        ");
    }

    unset($_SESSION['keranjang']);
    unset($_SESSION['member']);

    header("Location: struk.php?id=$id_transaksi");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pembayaran</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f0f2f5;
}
.header {
    background: linear-gradient(135deg, #5f5bff, #3f3cbb);
    color: white;
    padding: 20px;
}
.card {
    border-radius: 16px;
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header text-center">
    <h4>💳 Pembayaran</h4>
    <small>Konfirmasi transaksi sebelum cetak struk</small>
</div>

<!-- CONTENT -->
<div class="container my-4" style="max-width: 720px;">

    <div class="card shadow-sm">
        <div class="card-body">

            <p>
                <strong>Kasir:</strong> <?= $_SESSION['nama'] ?><br>
                <strong>Member:</strong> <?= $_SESSION['member']['nama_member'] ?? '-' ?>
            </p>

            <hr>

            <h6>🛒 Ringkasan Belanja</h6>

            <ul class="list-group mb-3">
                <?php foreach ($items as $i): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <?= $i['nama'] ?> (<?= $i['qty'] ?>)
                    <span>Rp <?= number_format($i['subtotal']) ?></span>
                </li>
                <?php endforeach; ?>

                <li class="list-group-item d-flex justify-content-between">
                    Subtotal
                    <span>Rp <?= number_format($total) ?></span>
                </li>

                <li class="list-group-item d-flex justify-content-between text-success">
                    Diskon
                    <span>- Rp <?= number_format($diskon) ?></span>
                </li>

                <li class="list-group-item d-flex justify-content-between fw-bold fs-5">
                    Total Bayar
                    <span>Rp <?= number_format($total_bayar) ?></span>
                </li>
            </ul>

            <form method="post">
                <button type="submit" name="bayar" class="btn btn-success w-100 py-2">
                    ✔ Bayar & Cetak Struk
                </button>
            </form>

        </div>
    </div>

</div>

</body>
</html>