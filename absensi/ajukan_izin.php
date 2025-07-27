<?php
include '../includes/session.php';
include '../includes/config.php';

$id_pengguna = $_SESSION['id_pengguna'];
$tanggal = date('Y-m-d');

// Proses kirim form
if (isset($_POST['submit'])) {
    $tgl = $_POST['tanggal'];
    $jenis = $_POST['jenis'];
    $alasan = $_POST['alasan'];

    $cek = mysqli_query($koneksi, "SELECT * FROM tb_pengajuan_izin WHERE id_pengguna='$id_pengguna' AND tanggal='$tgl'");
    if (mysqli_num_rows($cek) > 0) {
        $pesan = "Kamu sudah mengajukan izin/sakit untuk tanggal tersebut.";
    } else {
        mysqli_query($koneksi, "INSERT INTO tb_pengajuan_izin (id_pengguna, tanggal, jenis, alasan) 
                                VALUES ('$id_pengguna', '$tgl', '$jenis', '$alasan')");
        $pesan = "Pengajuan berhasil dikirim. Menunggu verifikasi admin.";
    }
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
    <div class="container mt-5">
        <h3>Ajukan Izin / Sakit</h3>
        <?php if (isset($pesan)): ?>
            <div class="alert alert-info"><?= $pesan ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="tanggal">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" required value="<?= $tanggal ?>">
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