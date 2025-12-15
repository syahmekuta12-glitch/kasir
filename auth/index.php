<?php
session_start();
include '../config/koneksi.php';

$error = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM pegawai WHERE username='$username'");
    $data  = mysqli_fetch_assoc($query);

    if ($data && password_verify($password, $data['password'])) {
        $_SESSION['login'] = true;
        $_SESSION['id_pegawai'] = $data['id_pegawai'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['role'] = $data['role'];

        header("Location: ../dashboard.php");
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | Sistem Kasir</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #3f3cbb, #2c2a8f);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 380px;
            border-radius: 20px;
            border: none;
        }

        .login-title {
            font-weight: bold;
            color: #3f3cbb;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px;
        }

        .btn-login {
            background: #5f5bff;
            border: none;
            border-radius: 12px;
            padding: 12px;
        }

        .btn-login:hover {
            background: #4b48e0;
        }

        .icon-input {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .input-group-custom input {
            padding-left: 40px;
        }
    </style>
</head>

<body>

<div class="card login-card shadow-lg">
    <div class="card-body p-4">
        <div class="text-center mb-4">
            <i class="bi bi-cart-check fs-1 text-primary"></i>
            <h4 class="login-title mt-2">Sistem Kasir</h4>
            <small class="text-muted">Silakan login untuk melanjutkan</small>
        </div>

        <?php if ($error) { ?>
            <div class="alert alert-danger text-center">
                <?= $error ?>
            </div>
        <?php } ?>

        <form method="post">
            <div class="mb-3 position-relative input-group-custom">
                <i class="bi bi-person icon-input"></i>
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>

            <div class="mb-3 position-relative input-group-custom">
                <i class="bi bi-lock icon-input"></i>
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>

            <button type="submit" name="login" class="btn btn-login w-100 text-white">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </button>
        </form>

        <div class="text-center mt-4">
            <small class="text-muted">
                © <?= date('Y'); ?> Sistem Kasir
            </small>
        </div>
    </div>
</div>

</body>
</html>
