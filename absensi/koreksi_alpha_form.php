<?php
include '../includes/config.php';
include '../includes/session.php';

$id_pengguna = $_SESSION['id_pengguna'];
$tanggal = date('Y-m-d');
$jam = date('H:i:s');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alasan = htmlspecialchars($_POST['alasan']);
    // Simpan koreksi, tanpa ubah absensi utama
    mysqli_query($koneksi, "INSERT INTO tb_koreksi_absen (id_pengguna, tanggal, waktu_koreksi, alasan) 
    VALUES ('$id_pengguna', '$tanggal', NOW(), '$alasan')");


    // Catat koreksi
    mysqli_query($koneksi, "INSERT INTO tb_koreksi_absen (id_pengguna, tanggal, waktu_koreksi, alasan) 
                            VALUES ('$id_pengguna', '$tanggal', NOW(), '$alasan')");

    unset($_SESSION['koreksi_alpha']);
    echo "<script>alert('Absen berhasil dan sedang dikoreksi.'); window.location.href='../dashboard_siswa.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Koreksi Alpha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h4>Koreksi Kehadiran (Terlambat Absen)</h4>
        <p>Kamu sudah dianggap <strong>Alpha</strong> karena tidak absen tepat waktu.<br>
            Jika kamu benar-benar hadir, silakan isi alasan di bawah ini.</p>
        <form method="POST">
            <div class="mb-3">
                <label>Alasan:</label>
                <textarea name="alasan" class="form-control" required rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Ajukan Koreksi dan Absen</button>
            <a href="../dashboard_siswa.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>

</html>