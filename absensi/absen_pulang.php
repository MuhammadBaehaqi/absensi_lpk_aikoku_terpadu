<?php
include '../includes/session.php';
include '../includes/config.php';

$id_pengguna = $_SESSION['id_pengguna'];
$tanggal = date('Y-m-d');
$jam = date('H:i:s');

// Cek apakah sudah absen masuk hari ini
$cek = mysqli_query($koneksi, "SELECT * FROM tb_absensi WHERE id_pengguna='$id_pengguna' AND tanggal='$tanggal'");
$data = mysqli_fetch_assoc($cek);

if ($data) {
    if ($data['jam_pulang'] == null) {
        // Update jam pulang
        mysqli_query($koneksi, "UPDATE tb_absensi 
                                SET jam_pulang = '$jam' 
                                WHERE id_absen = '{$data['id_absen']}'");
        $pesan = "Absen pulang berhasil!";
    } else {
        $pesan = "Kamu sudah absen pulang hari ini!";
    }
} else {
    $pesan = "Kamu belum absen masuk hari ini!";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Absen Pulang</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
     <style>
        .logo-img {
            height: 40px;
            margin-right: 10px;
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <div class="d-flex align-items-center">
                <img src="../img/logo.png" alt="Logo LPK" class="logo-img">
                <span class="navbar-brand mb-0 h5">LPK AIKOKU TERPADU</span>
            </div>
            <div class="d-flex">
                <span class="navbar-text text-white me-3">
                    Selamat datang, <?= $_SESSION['username']; ?> (<?= $_SESSION['role']; ?>)
                </span>
                <a href="auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>
    <div class="d-flex justify-content-center align-items-center vh-100">
    <div class="text-center">
        <h3><?= $pesan ?></h3>
            <p>Tanggal: <strong><?= date('d-m-Y') ?></strong></p>
            <a href="../dashboard_siswa.php" class="btn btn-primary mt-3">Kembali ke Dashboard</a>
        </div>
    </div>

</body>

</html>