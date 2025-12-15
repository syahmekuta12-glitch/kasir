<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: auth/login.php");
    exit;
}

// ================= INIT KERANJANG =================
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// ================= TAMBAH KE KERANJANG =================
if (isset($_GET['add'])) {
    $id = intval($_GET['add']);
    $_SESSION['keranjang'][$id] = ($_SESSION['keranjang'][$id] ?? 0) + 1;
    header("Location: transaksi.php");
    exit;
}

// ================= HAPUS ITEM =================
if (isset($_GET['hapus'])) {
    unset($_SESSION['keranjang'][$_GET['hapus']]);
}

// ================= CEK MEMBER =================
$diskon = 0;
$member = null;

if (isset($_POST['cek_member'])) {
    $kode = trim($_POST['kode_member']);
    $q = mysqli_query($conn, "
        SELECT * FROM member 
        WHERE id_member='$kode' OR no_hp='$kode'
    ");
    $member = mysqli_fetch_assoc($q);

    if ($member) {
        $_SESSION['member'] = $member;
        $diskon = 0.05;
    } else {
        unset($_SESSION['member']);
    }
}

if (isset($_SESSION['member'])) {
    $member = $_SESSION['member'];
    $diskon = 0.05;
}

// ================= DATA BARANG =================
$barang = mysqli_query($conn, "SELECT * FROM barang WHERE stok > 0 ORDER BY nama_barang ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Transaksi Penjualan</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
.content {
    margin-left: 280px;
    padding: 25px;
}
.product-card {
    border: 1px solid #eee;
    border-radius: 18px;
    padding: 16px;
    cursor: pointer;
    transition: .2s;
    height: 100%;
}
.product-card:hover {
    box-shadow: 0 10px 25px rgba(0,0,0,.08);
    transform: translateY(-3px);
}
.product-price {
    color: #3f3cbb;
    font-weight: 600;
    font-size: 16px;
}
.cart-box {
    background: #fff;
    border-radius: 18px;
    padding: 20px;
}
.search-input {
    border-radius: 30px;
    padding: 12px 20px;
}
</style>
</head>

<body>
<?php include 'sidebar.php'; ?>

<div class="content">

<h3 class="mb-1">🛒 Transaksi Penjualan</h3>
<p class="text-muted mb-4">Klik barang untuk menambahkan ke keranjang</p>

<div class="row g-4">

<!-- ================= PRODUK ================= -->
<div class="col-md-8">

    <!-- SEARCH -->
    <input
        type="text"
        id="search"
        class="form-control search-input mb-4"
        placeholder="🔍 Cari nama / ID barang..."
        onkeyup="filterBarang()"
    >

    <div class="row g-3" id="produk-list">
    <?php while ($b = mysqli_fetch_assoc($barang)) { ?>
        <div class="col-md-4 product-item"
             data-nama="<?= strtolower($b['nama_barang']) ?>"
             data-id="<?= $b['id_barang'] ?>">

            <a href="?add=<?= $b['id_barang'] ?>" class="text-decoration-none text-dark">
                <div class="product-card">
                    <h6 class="mb-1"><?= $b['nama_barang'] ?></h6>
                    <small class="text-muted">ID: <?= $b['id_barang'] ?> | Stok: <?= $b['stok'] ?></small>
                    <div class="product-price mt-3">
                        Rp <?= number_format($b['harga']) ?>
                    </div>
                </div>
            </a>

        </div>
    <?php } ?>
    </div>
</div>

<!-- ================= KERANJANG ================= -->
<div class="col-md-4">
<div class="cart-box shadow-sm">

<h5 class="mb-3">
<i class="bi bi-cart"></i> Keranjang
</h5>

<?php
$subtotal = 0;
if ($_SESSION['keranjang']) {
foreach ($_SESSION['keranjang'] as $id => $qty) {
    $q = mysqli_query($conn, "SELECT * FROM barang WHERE id_barang='$id'");
    $b = mysqli_fetch_assoc($q);
    $total = $b['harga'] * $qty;
    $subtotal += $total;
?>
<div class="d-flex justify-content-between mb-2">
    <div>
        <?= $b['nama_barang'] ?><br>
        <small class="text-muted">x<?= $qty ?></small>
    </div>
    <div class="text-end">
        Rp <?= number_format($total) ?><br>
        <a href="?hapus=<?= $id ?>" class="text-danger small">hapus</a>
    </div>
</div>
<?php }} else { ?>
<p class="text-muted text-center">Keranjang kosong</p>
<?php } ?>

<hr>

<!-- MEMBER -->
<form method="post">
<label class="small">ID Member / No HP</label>
<div class="input-group mb-2">
    <input type="text" name="kode_member" class="form-control" placeholder="Contoh: 1 / 08123456789">
    <button name="cek_member" class="btn btn-success">Cek</button>
</div>
<?php if ($member) { ?>
<small class="text-success">
✔ <?= $member['nama_member'] ?> (Diskon 5%)
</small>
<?php } ?>
</form>

<hr>

<p>Subtotal : Rp <?= number_format($subtotal) ?></p>
<p>Diskon : Rp <?= number_format($subtotal * $diskon) ?></p>
<h5>Total : Rp <?= number_format($subtotal - ($subtotal * $diskon)) ?></h5>

<a href="pembayaran.php" class="btn btn-primary w-100 mt-2">
Proses Pembayaran
</a>

</div>
</div>

</div>
</div>

<!-- ================= JAVASCRIPT SEARCH ================= -->
<script>
function filterBarang() {
    const input = document.getElementById('search').value.toLowerCase();
    const items = document.querySelectorAll('.product-item');

    items.forEach(item => {
        const nama = item.dataset.nama;
        const id   = item.dataset.id;
        if (nama.includes(input) || id.includes(input)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>

</body>
</html>