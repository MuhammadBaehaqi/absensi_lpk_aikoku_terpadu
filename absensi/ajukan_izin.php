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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>Ajukan Izin / Sakit</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
    <style>
        .logo-img {
            height: 40px;
            margin-right: 10px;
        }

        /* Tambahan CSS responsif untuk HP */
        @media (max-width: 576px) {
            .navbar .navbar-text {
                font-size: 0.8rem;
            }

            h2 {
                font-size: 1.2rem;
            }

            .logo-img {
                height: 30px;
                margin-right: 5px;
            }

            .btn {
                font-size: 0.8rem;
                padding: 8px 10px;
            }

            .alert,
            .card,
            .alert-info {
                margin-left: 10px;
                margin-right: 10px;
            }

            .card-title {
                font-size: 1rem;
            }

            ol {
                padding-left: 1rem;
            }
        }

        .logo-img {
            height: 40px;
        }

        @media (max-width: 576px) {
            .navbar .navbar-brand {
                font-size: 1rem;
            }

            .navbar .text-white {
                font-size: 0.85rem;
            }

            .btn.btn-outline-light {
                font-size: 0.75rem;
                padding: 4px 10px;
            }
        }

        .logo-img {
            height: 40px;
        }

        @media (max-width: 576px) {
            .navbar .navbar-brand {
                font-size: 1rem;
            }

            .navbar .text-white,
            .navbar .small {
                font-size: 0.85rem;
            }

            .btn.btn-outline-light {
                font-size: 0.75rem;
                padding: 4px 10px;
            }
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container text-white">
            <!-- Desktop View (Logo kiri, Salam kanan) -->
            <div class="d-none d-md-flex justify-content-between align-items-center w-100">
                <div class="d-flex align-items-center">
                    <img src="../img/logo.png" alt="Logo LPK" class="logo-img me-2">
                    <span class="navbar-brand mb-0 h5">LPK AIKOKU TERPADU</span>
                </div>
                <div class="text-end small">
                    <div>いらっしゃいませ, <?= $_SESSION['username']; ?> (<?= $_SESSION['role']; ?>)</div>
                    <a href="../auth/logout.php" class="btn btn-outline-light btn-sm mt-1">Logout</a>
                </div>
            </div>

            <!-- Mobile View (Semua tengah, logo lebih besar, teks tengah) -->
            <div class="d-block d-md-none w-100 text-center">
                <img src="../img/logo.png" alt="Logo LPK" class="mb-1" style="height: 45px;"> <!-- DIBESARKAN -->
                <div class="fw-bold text-white" style="font-size: 1rem;">LPK AIKOKU TERPADU</div>
                <div class="small">いらっしゃいませ, <?= $_SESSION['username']; ?> (<?= $_SESSION['role']; ?>)</div>
                <a href="../auth/logout.php" class="btn btn-outline-light btn-sm mt-1">Logout</a>
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