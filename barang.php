<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: auth/login.php");
    exit;
}

include 'config/koneksi.php';

/* TAMBAH BARANG */
if (isset($_POST['tambah'])) {
    $nama   = $_POST['nama_barang'];
    $harga  = $_POST['harga'];
    $stok   = $_POST['stok'];

    mysqli_query($conn, "INSERT INTO barang VALUES (
        NULL,
        '$nama',
        '$harga',
        '$stok'
    )");
}

/* HAPUS BARANG */
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM barang WHERE id_barang='$id'");
}

$data = mysqli_query($conn, "SELECT * FROM barang ORDER BY id_barang DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Barang</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f5fb;
        }

        .content {
            margin-left: 260px;
            padding: 30px;
        }

        .card {
            border-radius: 18px;
            border: none;
        }

        .btn-custom {
            border-radius: 12px;
        }

        .form-control {
            border-radius: 12px;
        }
    </style>
</head>

<body>

<?php include 'sidebar.php'; ?>

<div class="content">
    <h3>Data Barang</h3>
    <p class="text-muted">Kelola data produk</p>

    <!-- FORM TAMBAH BARANG -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="post" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="nama_barang" class="form-control" placeholder="Nama Barang" required>
                </div>
                <div class="col-md-4">
                    <input type="number" name="harga" class="form-control" placeholder="Harga" required>
                </div>
                <div class="col-md-4">
                    <input type="number" name="stok" class="form-control" placeholder="Stok" required>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" name="tambah" class="btn btn-primary btn-custom">
                        <i class="bi bi-plus-circle"></i> Tambah Barang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- TABEL BARANG -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no = 1; while ($b = mysqli_fetch_assoc($data)) { ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $b['nama_barang'] ?></td>
                        <td>Rp <?= number_format($b['harga']) ?></td>
                        <td><?= $b['stok'] ?></td>
                        <td>
                            <a href="?hapus=<?= $b['id_barang'] ?>"
                               onclick="return confirm('Hapus barang ini?')"
                               class="btn btn-danger btn-sm">
                               <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>