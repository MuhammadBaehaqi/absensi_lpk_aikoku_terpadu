<?php
include '../includes/session.php';
include '../includes/config.php';

$id_pengguna = $_SESSION['id_pengguna'];
$tanggal = date('Y-m-d');
$jam = date('H:i:s');

$cek = mysqli_query($koneksi, "SELECT * FROM tb_absensi WHERE id_pengguna='$id_pengguna' AND tanggal='$tanggal'");
$data = mysqli_fetch_assoc($cek);

if ($data) {
    if (empty($data['jam_masuk'])) {
        // Belum absen masuk sama sekali
        $pesan = "Kamu belum absen masuk hari ini. Tidak bisa absen pulang.";
    } elseif ($data['keterangan'] == 'Alpha') {
        // Masih Alpha, cek apakah koreksi sudah disetujui
        $cekKoreksi = mysqli_query($koneksi, "SELECT * FROM tb_koreksi_absen 
            WHERE id_pengguna='$id_pengguna' 
            AND tanggal='$tanggal' 
            ORDER BY id_koreksi DESC LIMIT 1");

        if (mysqli_num_rows($cekKoreksi) > 0) {
            $koreksi = mysqli_fetch_assoc($cekKoreksi);
            if ($koreksi['status'] == 'Menunggu') {
                $pesan = "Kamu sudah mengajukan koreksi. Tunggu verifikasi admin sebelum bisa absen pulang.";
            } elseif ($koreksi['status'] == 'Disetujui') {
                // Admin sudah setujui, boleh lanjut
                if (empty($data['jam_pulang'])) {
                    mysqli_query($koneksi, "UPDATE tb_absensi SET jam_pulang='$jam' WHERE id_absen='{$data['id_absen']}'");
                    $pesan = "Absen pulang berhasil!";
                } else {
                    $pesan = "Kamu sudah absen pulang hari ini!";
                }
            } else {
                // Koreksi ditolak
                $pesan = "Koreksi ditolak. Kamu dianggap Alpha dan tidak bisa absen pulang.";
            }
        } else {
            // Tidak ada koreksi sama sekali
            $pesan = "Kamu belum absen masuk hari ini. Tidak bisa absen pulang.";
        }
    } elseif (empty($data['jam_pulang'])) {
        // Normal: sudah masuk, belum pulang
        mysqli_query($koneksi, "UPDATE tb_absensi SET jam_pulang='$jam' WHERE id_absen='{$data['id_absen']}'");
        $pesan = "Absen pulang berhasil!";
    } else {
        $pesan = "Kamu sudah absen pulang hari ini!";
    }
} else {
    $pesan = "Kamu belum absen masuk hari ini.";
}

echo "<script>alert('$pesan'); window.location.href='../dashboard_siswa.php';</script>";
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