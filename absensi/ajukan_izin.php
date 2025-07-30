<?php
include '../includes/session.php';
include '../includes/config.php';

date_default_timezone_set('Asia/Jakarta');

$id_pengguna = $_SESSION['id_pengguna'];
$tanggal_hari_ini = date('Y-m-d');
$pesan = '';
$peringatan = '';

// Jika form disubmit
if (isset($_POST['submit'])) {
    $tgl = $_POST['tanggal'];
    $jenis = $_POST['jenis'];
    $alasan = $_POST['alasan'];

    $cek = mysqli_query($koneksi, "SELECT * FROM tb_pengajuan_izin WHERE id_pengguna='$id_pengguna' AND tanggal='$tgl'");
    if (mysqli_num_rows($cek) > 0) {
        $pesan = "Kamu sudah mengajukan izin/sakit untuk tanggal tersebut.";
    } else {
        mysqli_query($koneksi, "INSERT INTO tb_pengajuan_izin (id_pengguna, tanggal, jenis, alasan, status) 
                                VALUES ('$id_pengguna', '$tgl', '$jenis', '$alasan', 'Menunggu')");
        $pesan = "Pengajuan berhasil dikirim. Menunggu verifikasi admin.";
    }
}

// Peringatan jika sudah absen hari ini
$cek_absen = mysqli_query($koneksi, "SELECT * FROM tb_absensi WHERE id_pengguna='$id_pengguna' AND tanggal='$tanggal_hari_ini'");
$absen = mysqli_fetch_assoc($cek_absen);
if ($absen && !empty($absen['jam_masuk'])) {
    $peringatan = "<div class='alert alert-warning'>
    ⚠️ <strong>Perhatian!</strong> Kamu sudah melakukan absen masuk hari ini pada pukul <strong>{$absen['jam_masuk']}</strong>.<br>
    Ajukan izin atau sakit <u>hanya jika kamu terpaksa tidak bisa melanjutkan kegiatan hari ini</u> karena alasan penting atau kondisi kesehatan.<br>
    <small class='text-muted'>Contoh: pulang lebih awal karena sakit, urusan keluarga mendadak, dan lain-lain.</small>
</div>";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Ajukan Izin / Sakit</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <div class="d-flex align-items-center">
                <img src="../img/logo.png" alt="Logo LPK" class="logo-img" style="height: 40px; margin-right: 10px;">
                <span class="navbar-brand mb-0 h5">LPK AIKOKU TERPADU</span>
            </div>
            <div class="d-flex">
                <span class="navbar-text text-white me-3">
                    Selamat datang, <?= $_SESSION['username']; ?> (<?= $_SESSION['role']; ?>)
                </span>
                <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5" style="max-width: 650px;">
        <h3>Ajukan Izin / Sakit</h3>

        <?php if (!empty($pesan)): ?>
            <div class="alert alert-info"><?= $pesan ?></div>
        <?php endif; ?>

        <?= $peringatan ?>

        <form method="POST">
            <div class="mb-3">
                <label for="tanggal">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" required value="<?= $tanggal_hari_ini ?>">
            </div>
            <div class="mb-3">
                <label for="jenis">Jenis Pengajuan</label>
                <select name="jenis" class="form-control" required>
                    <option value="Izin">Izin</option>
                    <option value="Sakit">Sakit</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="alasan">Alasan (opsional)</label>
                <textarea name="alasan" class="form-control" rows="3"></textarea>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Kirim Pengajuan</button>
            <a href="../dashboard_siswa.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>

</html>