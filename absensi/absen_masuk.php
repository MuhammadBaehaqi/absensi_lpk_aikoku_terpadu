<?php
include '../includes/session.php';
include '../includes/config.php';

$id_pengguna = $_SESSION['id_pengguna'];
$tanggal = date('Y-m-d');
$jam = date('H:i:s');

// Cek apakah sudah absen hari ini
$cek = mysqli_query($koneksi, "SELECT * FROM tb_absensi WHERE id_pengguna='$id_pengguna' AND tanggal='$tanggal'");
if (mysqli_num_rows($cek) == 0) {
    mysqli_query($koneksi, "INSERT INTO tb_absensi (id_pengguna, tanggal, jam_masuk) 
                            VALUES ('$id_pengguna', '$tanggal', '$jam')");
    echo "<script>alert('Absen Masuk berhasil!'); window.location.href='../dashboard_siswa.php';</script>";
} else {
    echo "<script>alert('Kamu sudah absen hari ini!'); window.location.href='../dashboard_siswa.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Absen Masuk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="../img/logo.png">

</head>

<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="text-center">
        <h3>Absen Masuk</h3>
        <p>Hari ini: <strong><?= date('d-m-Y') ?></strong></p>
        <button class="btn btn-success btn-lg">Klik untuk Absen Masuk</button>
    </div>
</body>

</html>