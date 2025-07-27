<?php
include '../includes/session.php';
include '../includes/config.php';

// Handle form submit
if (isset($_POST['submit'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $cek = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE username='$username' OR email='$email'");

    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['toast'] = ['message' => 'Username / Email sudah digunakan.', 'type' => 'danger'];
    } else {
        $insert = mysqli_query($koneksi, "INSERT INTO tb_pengguna (nama_lengkap, username, email, password, role)
                                          VALUES ('$nama', '$username', '$email', '$password', '$role')");
        $_SESSION['toast'] = ['message' => 'Pengguna berhasil ditambahkan!', 'type' => 'success'];
    }

    header("Location: kelola_user.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Kelola User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
     <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
        }

        .main-content {
            margin-left: 250px;
            padding: 80px 20px 20px;
            width: 100%;
        }

        @media (max-width: 991.98px) {
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    <link rel="icon" type="image/png" href="../img/logo.png">
</head>
    <?php include '../includes/sidebar.php'; ?>
<div class="main-content bg-light">
    <?php if (isset($_SESSION['toast'])): ?>
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999;">
            <div class="toast align-items-center text-white bg-<?= $_SESSION['toast']['type'] ?> border-0 show"
                role="alert">
                <div class="d-flex">
                    <div class="toast-body"><?= $_SESSION['toast']['message'] ?></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['toast']); endif; ?>

    <div class="container">
        <h3>Tambah Pengguna Baru</h3>
        <form method="post" class="row g-3">
            <div class="col-md-6">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label>Password</label>
                <input type="text" name="password" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label>Role</label>
                <select name="role" class="form-control" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="admin">Admin</option>
                    <option value="siswa">Siswa</option>
                </select>
            </div>
            <div class="col-12">
                <button class="btn btn-primary" name="submit">Simpan</button>
                <a href="dashboard_admin.php" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>