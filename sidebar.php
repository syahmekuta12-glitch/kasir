<?php
// sidebar.php
?>

<!-- SIDEBAR -->
<div class="sidebar">
    <h4>Sistem Kasir</h4>

    <div class="menu">
        <a href="dashboard.php">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>

        <a href="transaksi.php">
            <i class="bi bi-cart"></i>
            <span>Transaksi</span>
        </a>

        <a href="barang.php">
            <i class="bi bi-box-seam"></i>
            <span>Data Barang</span>
        </a>

        <a href="member.php">
            <i class="bi bi-people"></i>
            <span>Member</span>
        </a>

        <a href="laporan.php">
            <i class="bi bi-file-earmark-text"></i>
            <span>Laporan</span>
        </a>
    </div>

    <div class="logout">
        <a href="auth/logout.php">
            <i class="bi bi-box-arrow-left"></i>
            <span>Logout</span>
        </a>
    </div>
</div>

<!-- STYLE SIDEBAR -->
<style>
.sidebar {
    width: 260px;
    height: 100vh;
    background: linear-gradient(180deg, #3f3cbb, #2c2a8f);
    position: fixed;
    top: 0;
    left: 0;
    padding-top: 25px;
    color: white;
    z-index: 1000;
}

.sidebar h4 {
    text-align: center;
    margin-bottom: 30px;
    font-weight: bold;
    letter-spacing: 1px;
}

.menu a {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 22px;
    margin: 6px 15px;
    color: #dcdcff;
    text-decoration: none;
    border-radius: 14px;
    transition: all 0.3s ease;
    font-size: 15px;
}

.menu a i {
    font-size: 18px;
}

.menu a:hover,
.menu a.active {
    background: #5f5bff;
    color: #ffffff;
    transform: translateX(4px);
}

.logout {
    position: absolute;
    bottom: 20px;
    width: 100%;
}

.logout a {
    display: flex;
    align-items: center;
    gap: 14px;
    margin: 0 15px;
    padding: 14px 22px;
    color: #ffdede;
    text-decoration: none;
    border-radius: 14px;
    transition: all 0.3s ease;
}

.logout a:hover {
    background: #ff6b6b;
    color: #fff;
}
</style>