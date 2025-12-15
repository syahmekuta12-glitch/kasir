<?php
session_start();
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header('Location: auth/login.php');
    exit;
}

/* ================== TAMBAH MEMBER ================== */
if (isset($_POST['simpan'])) {
    $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
    $hp     = mysqli_real_escape_string($conn, $_POST['hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    mysqli_query($conn, "
        INSERT INTO member (nama_member, no_hp, alamat)
        VALUES ('$nama', '$hp', '$alamat')
    ");

    header('Location: member.php');
    exit;
}

/* ================== HAPUS MEMBER ================== */
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM member WHERE id_member='$id'");
    header('Location: member.php');
    exit;
}

/* ================== SEARCH MEMBER ================== */
$keyword = $_GET['search'] ?? '';

$qMember = mysqli_query($conn, "
    SELECT * FROM member
    WHERE nama_member LIKE '%$keyword%'
       OR no_hp LIKE '%$keyword%'
       OR alamat LIKE '%$keyword%'
    ORDER BY id_member ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Data Member | Sistem Kasir</title>

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
.header {
    background: linear-gradient(135deg, #5f5bff, #3f3cbb);
    color: white;
}
</style>
</head>

<body class="bg-light">

<?php include 'sidebar.php'; ?>

<div class="content">

<!-- HEADER -->
<div class="card header p-4 mb-4 shadow-sm">
    <h4 class="mb-0">👥 Data Member</h4>
    <small>Kelola data member pelanggan</small>
</div>

<div class="row g-4">

<!-- FORM TAMBAH MEMBER -->
<div class="col-md-4">
    <div class="card shadow-sm p-3">
        <h5 class="mb-3">Tambah Member</h5>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Nama Member</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">No HP</label>
                <input type="text" name="hp" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3" required></textarea>
            </div>
            <button name="simpan" class="btn btn-primary w-100">
                <i class="bi bi-save"></i> Simpan
            </button>
        </form>
    </div>
</div>

<!-- TABEL MEMBER -->
<div class="col-md-8">
    <div class="card shadow-sm p-3">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Daftar Member</h5>

            <!-- SEARCH BAR -->
            <form method="get" class="d-flex">
                <input type="text"
                       name="search"
                       value="<?= htmlspecialchars($keyword) ?>"
                       class="form-control form-control-sm me-2"
                       placeholder="Cari nama / no hp / alamat">
                <button class="btn btn-sm btn-primary">
                    <i class="bi bi-search"></i>
                </button>
            </form>
        </div>

        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr class="text-center">
                    <th>ID</th>
                    <th>Nama Member</th>
                    <th>No HP</th>
                    <th>Alamat</th>
                    <th width="90">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($qMember) == 0): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Data tidak ditemukan
                        </td>
                    </tr>
                <?php endif; ?>

                <?php while ($m = mysqli_fetch_assoc($qMember)) : ?>
                <tr>
                    <td class="text-center"><?= $m['id_member'] ?></td>
                    <td><?= $m['nama_member'] ?></td>
                    <td><?= $m['no_hp'] ?></td>
                    <td><?= $m['alamat'] ?></td>
                    <td class="text-center">
                        <a href="?hapus=<?= $m['id_member'] ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Hapus member ini?')">
                           <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>
</div>

</div>
</div>

</body>
</html>