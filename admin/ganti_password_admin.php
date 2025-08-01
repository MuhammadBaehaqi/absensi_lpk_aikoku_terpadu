<?php
include '../includes/session.php';
include '../includes/config.php';

$id_pengguna = $_SESSION['id_pengguna'];

if (isset($_POST['submit'])) {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    $query = mysqli_query($koneksi, "SELECT password FROM tb_pengguna WHERE id_pengguna='$id_pengguna'");
    $data = mysqli_fetch_assoc($query);

    if (!password_verify($password_lama, $data['password'])) {
        $pesan = ['message' => 'Password lama salah!', 'type' => 'danger'];
    } elseif ($password_baru !== $konfirmasi_password) {
        $pesan = ['message' => 'Konfirmasi password tidak cocok!', 'type' => 'warning'];
    } else {
        $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
        mysqli_query($koneksi, "UPDATE tb_pengguna SET password='$password_hash' WHERE id_pengguna='$id_pengguna'");
        $pesan = ['message' => 'Password berhasil diperbarui!', 'type' => 'success'];
    }

    $_SESSION['toast'] = $pesan;
    header("Location: ganti_password_admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>Ganti Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
    <style>
        /* Card dalam dark mode */
        .dark-mode .card {
            background-color: #1e1e1e;
            color: #f1f1f1;
            border: 1px solid #444;
        }

        /* Header dan form di dalam card */
        .dark-mode .card h3,
        .dark-mode .card label {
            color: #f1f1f1;
        }

        .dark-mode .form-control {
            background-color: #2c2c2c;
            color: #fff;
            border: 1px solid #555;
        }

        .dark-mode .form-control::placeholder {
            color: #ccc;
        }
    </style>
</head>

<body>

    <body>
        <?php include '../includes/sidebar.php'; ?>

        <div class="content container mt-5">
            <div class="card shadow mx-auto p-4" style="max-width: 550px;">
                <h3 class="mb-3">🔐 Ganti Password</h3>

                <?php if (isset($_SESSION['toast'])): ?>
                    <div class="alert alert-<?= $_SESSION['toast']['type'] ?> alert-dismissible fade show" role="alert">
                        <?= $_SESSION['toast']['message'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['toast']); ?>
                <?php endif; ?>

                <form method="POST" class="mt-3">
                    <div class="mb-3">
                        <label>Password Lama</label>
                        <input type="password" name="password_lama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password Baru</label>
                        <input type="password" name="password_baru" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" name="konfirmasi_password" class="form-control" required>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>

    </body>

</html>