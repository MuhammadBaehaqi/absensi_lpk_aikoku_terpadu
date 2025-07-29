<?php
include 'includes/config.php';
include 'includes/session.php';
?>
<?php
date_default_timezone_set('Asia/Jakarta');

$hour = date('H'); // ambil jam saat ini (00–23)
if ($hour < 11) {
    $salamJepang = "おはようございます"; // Pagi
} elseif ($hour < 15) {
    $salamJepang = "こんにちは"; // Siang
} else {
    $salamJepang = "こんばんは"; // Sore/Malam
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="img/logo.png">
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
                <img src="img/logo.png" alt="Logo LPK" class="logo-img">
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
    <?php
    $tanggal = date('Y-m-d');
    $id_pengguna = $_SESSION['id_pengguna'];

    $cek = mysqli_query($koneksi, "SELECT * FROM tb_absensi WHERE id_pengguna='$id_pengguna' AND tanggal='$tanggal'");
    $absen = mysqli_fetch_assoc($cek);

    $badge = '';
    if (!$absen) {
        $badge = '<span class="badge bg-secondary">Belum Absen</span>';
    } elseif ($absen['keterangan'] == 'Alpha') {
        // Cek apakah ada koreksi
        $koreksi = mysqli_query($koneksi, "SELECT * FROM tb_koreksi_absen WHERE id_pengguna='$id_pengguna' AND tanggal='$tanggal'");
        if (mysqli_num_rows($koreksi) > 0) {
            $row = mysqli_fetch_assoc($koreksi);
            if ($row['status'] == 'Menunggu') {
                $badge = '<span class="badge bg-warning text-dark">Menunggu Koreksi ⚠️</span>';
            } elseif ($row['status'] == 'Disetujui') {
                $badge = '<span class="badge bg-info text-dark">Koreksi Diterima 📝</span>';
            } else {
                $badge = '<span class="badge bg-danger">Alpha ❌</span>';
            }
        } else {
            $badge = '<span class="badge bg-danger">Alpha ❌</span>';
        }
    } elseif (!empty($absen['jam_masuk'])) {
        $jamMasuk = strtotime($absen['jam_masuk']);
        $batas = strtotime('09:00:00');
        if ($jamMasuk > $batas) {
            $badge = '<span class="badge bg-warning text-dark">Terlambat ⏰</span>';
        } else {
            $badge = '<span class="badge bg-success">Hadir ✅</span>';
        }
    }
    ?>

    <div class="mt-3 text-center">
        Status Kehadiran Hari Ini: <?= $badge ?>
    </div>

    <div class="container mt-5">
        <div class="text-center mb-4">
            <h2><?= $salamJepang; ?>, <?= $_SESSION['username']; ?> さん!</h2>
            <p class="text-muted mb-1">Semangat belajar hari ini! Silakan lakukan pencatatan kehadiran.</p>
            <p class="text-muted" id="clock" style="font-size: 1rem;"></p>
        </div>
        <div class="row g-3 justify-content-center">
            <div class="col-md-3 col-6">
                <a href="absensi/absen_masuk.php" class="btn btn-success w-100 py-2">Absen Masuk</a>
            </div>
            <div class="col-md-3 col-6">
                <a href="absensi/absen_pulang.php" class="btn btn-danger w-100 py-2">Absen Pulang</a>
            </div>
            <div class="col-md-3 col-6">
                <a href="absensi/ajukan_izin.php" class="btn btn-warning w-100 py-2">Ajukan Izin / Sakit</a>
            </div>
            <div class="col-md-3 col-6">
                <a href="absensi/riwayat_siswa.php" class="btn btn-secondary w-100 py-2">Riwayat Absensi</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateClock() {
            const now = new Date();
            const options = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
            const timeString = now.toLocaleTimeString('id-ID', options);
            document.getElementById('clock').textContent = `Sekarang pukul ${timeString}`;
        }

        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>

</html>