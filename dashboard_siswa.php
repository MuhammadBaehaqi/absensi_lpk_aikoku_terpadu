<?php
include 'includes/config.php';
include 'includes/session.php';
date_default_timezone_set('Asia/Jakarta');

$hour = date('H');
if ($hour < 11) {
    $salamJepang = "おはようございます";
} elseif ($hour < 15) {
    $salamJepang = "こんにちは";
} else {
    $salamJepang = "こんばんは";
}

$tanggal = date('Y-m-d');
$id_pengguna = $_SESSION['id_pengguna'];
$cek = mysqli_query($koneksi, "SELECT * FROM tb_absensi WHERE id_pengguna='$id_pengguna' AND tanggal='$tanggal'");
$absen = mysqli_fetch_assoc($cek);

// Hitung status kehadiran// Cek status izin dulu
$izin = mysqli_query($koneksi, "
    SELECT jenis FROM tb_pengajuan_izin 
    WHERE id_pengguna='$id_pengguna' 
    AND tanggal='$tanggal' 
    AND status='Diterima' 
    ORDER BY id_pengajuan DESC LIMIT 1
");

if (mysqli_num_rows($izin) > 0) {
    $izinData = mysqli_fetch_assoc($izin);
    $jenis = $izinData['jenis'];
    $warna = $jenis == 'Sakit' ? 'bg-info text-dark' : 'bg-warning text-dark';
    $badge = "<span class='badge $warna'>$jenis ✅</span>";
} elseif (!$absen) {
    $badge = '<span class="badge bg-secondary">Belum Absen</span>';
} elseif ($absen['keterangan'] == 'Alpha') {
    // Cek apakah ada izin yang ditolak
    $izinDitolak = mysqli_query($koneksi, "
        SELECT jenis FROM tb_pengajuan_izin 
        WHERE id_pengguna='$id_pengguna' 
        AND tanggal='$tanggal' 
        AND status='Ditolak' 
        ORDER BY id_pengajuan DESC LIMIT 1
    ");

    if (mysqli_num_rows($izinDitolak) > 0) {
        $izinTolak = mysqli_fetch_assoc($izinDitolak);
        $jenisTolak = $izinTolak['jenis'];
        $badge = "<span class='badge bg-danger'>Alpha ❌ ($jenisTolak Ditolak)</span>";
    }

    // Kalau tidak ada izin yang ditolak, cek koreksi
    else {
        $koreksi = mysqli_query($koneksi, "SELECT * FROM tb_koreksi_absen WHERE id_pengguna='$id_pengguna' AND tanggal='$tanggal'");
        if (mysqli_num_rows($koreksi) > 0) {
            $row = mysqli_fetch_assoc($koreksi);
            if ($row['status'] == 'Menunggu') {
                $badge = '<span class="badge bg-warning text-dark">Menunggu Koreksi ⚠️</span>';
            } elseif ($row['status'] == 'Disetujui') {
                $badge = '<span class="badge bg-info text-dark">Koreksi Diterima 📝</span>';
            } elseif ($row['status'] == 'Ditolak') {
                $badge = '<span class="badge bg-danger">Alpha ❌ (Ditolak Ajukan Koreksi)</span>';
            }

        } else {
            $badge = '<span class="badge bg-danger">Alpha ❌</span>';
        }
    }
} elseif (!empty($absen['jam_masuk'])) {
    $jamMasuk = strtotime($absen['jam_masuk']);
    $batas = strtotime('10:00:00');
    if ($jamMasuk > $batas) {
        $badge = '<span class="badge bg-warning text-dark">Terlambat ⏰</span>';
    } else {
        $badge = '<span class="badge bg-success">Hadir ✅</span>';
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>Dashboard Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="img/logo.png">
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

        .btn-outline-purple {
            border-color: #6f42c1;
            color: #6f42c1;
        }

        .btn-outline-purple:hover {
            background-color: #6f42c1;
            color: #fff;
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container text-white">
            <!-- Desktop View (Logo kiri, Salam kanan) -->
            <div class="d-none d-md-flex justify-content-between align-items-center w-100">
                <div class="d-flex align-items-center">
                    <img src="img/logo.png" alt="Logo LPK" class="logo-img me-2">
                    <span class="navbar-brand mb-0 h5">LPK AIKOKU TERPADU</span>
                </div>
                <div class="text-end small">
                    <div>いらっしゃいませ, <?= $_SESSION['username']; ?> (<?= $_SESSION['role']; ?>)</div>
                    <a href="auth/logout.php" class="btn btn-outline-light btn-sm mt-1">Logout</a>
                </div>
            </div>

            <!-- Mobile View (Semua tengah, logo lebih besar, teks tengah) -->
            <div class="d-block d-md-none w-100 text-center">
                <img src="img/logo.png" alt="Logo LPK" class="mb-1" style="height: 45px;"> <!-- DIBESARKAN -->
                <div class="fw-bold text-white" style="font-size: 1rem;">LPK AIKOKU TERPADU</div>
                <div class="small">いらっしゃいませ, <?= $_SESSION['username']; ?> (<?= $_SESSION['role']; ?>)</div>
                <a href="auth/logout.php" class="btn btn-outline-light btn-sm mt-1">Logout</a>
            </div>
        </div>
    </nav>

    <div class="mt-3 text-center">
        Status Kehadiran Hari Ini: <?= $badge ?>
        <?php
        $peringatanPulang = '';
        if (
            $absen &&
            !empty($absen['jam_masuk']) &&
            empty($absen['jam_pulang']) &&
            !isset($jenis) // artinya tidak sedang izin/sakit
        ) {
            $peringatanPulang = '<div class="alert alert-warning mt-3 mx-auto text-center" style="max-width: 600px;">
        ⚠️ <strong>Kamu belum absen pulang hari ini.</strong> Pastikan absen sebelum pukul <strong>18:00</strong>!
    </div>';
        }
        ?>
        <div class="mt-2 text-muted small">
            🕘 <strong>Hadir</strong>: Absen sebelum pukul 10:01<br>
            ⏰ <strong>Terlambat</strong>: Absen antara 10:01 – 17:59<br>
            ❌ <strong>Alpha</strong>: Tidak absen sampai pukul 18:00<br>
            📌 <strong>Catatan:</strong> Jika sudah absen masuk namun lupa absen pulang, kamu tetap dianggap hadir atau
            terlambat sesuai waktu absen masuk.
        </div>

        <?= $peringatanPulang ?>

    </div>

    <div class="alert alert-info mt-3 mx-auto text-start" style="max-width: 600px;">
        <strong>ℹ️ Aturan Kehadiran (Otomatis oleh Sistem):</strong><br>
        🕘 <strong>Hadir</strong>: Absen <u>sebelum pukul 10:01</u><br>
        ⏰ <strong>Terlambat</strong>: Absen <u>antara 10:01 – 17:59</u><br>
        ❌ <strong>Alpha</strong>: Tidak absen <u>hingga pukul 18:00</u> (ditandai otomatis oleh sistem)
    </div>

    <div class="container mt-5">
        <div class="text-center mb-4">
            <h2><?= $salamJepang; ?>, <?= $_SESSION['username']; ?> さん!</h2>
            <p class="text-muted mb-1">Semangat belajar hari ini! Silakan lakukan pencatatan kehadiran.</p>
            <p class="text-muted" id="clock" style="font-size: 1rem;"></p>
        </div>

        <div class="row row-cols-2 row-cols-md-4 g-2 g-md-3 justify-content-center">
            <div class="col-md-3 col-6">
                <a href="absensi/absen_masuk.php" class="btn btn-success w-100 py-2" data-bs-toggle="tooltip"
                    title="Klik untuk mencatat kehadiran masuk">Absen Masuk</a>
            </div>
            <div class="col-md-3 col-6">
                <a href="absensi/absen_pulang.php" class="btn btn-danger w-100 py-2" data-bs-toggle="tooltip"
                    title="Klik saat pulang">Absen Pulang</a>
            </div>
            <div class="col-md-3 col-6">
                <a href="absensi/ajukan_izin.php" class="btn btn-warning w-100 py-2" data-bs-toggle="tooltip"
                    title="Ajukan izin atau sakit">Ajukan Izin / Sakit</a>
            </div>
            <div class="col-md-3 col-6">
                <a href="absensi/riwayat_siswa.php" class="btn btn-secondary w-100 py-2" data-bs-toggle="tooltip"
                    title="Lihat riwayat absensi Anda">Riwayat Absensi</a>
            </div>
            <div class="col-md-3 col-6">
                <a href="absensi/edit_profile_user.php" class="btn btn-outline-primary w-100 py-2"
                    data-bs-toggle="tooltip" title="Ubah data pribadi Anda">Edit Profil</a>
            </div>
            <div class="col-md-3 col-6">
                <a href="absensi/ganti_password_user.php" class="btn btn-outline-purple w-100 py-2"
                    data-bs-toggle="tooltip" title="Ubah Password Baru">Ganti Password</a>
            </div>
        </div>

        <p class="text-muted mt-3 text-center small">
            📌 Catatan: Pastikan melakukan absen sebelum pukul <strong>18:00</strong>.
            Data kehadiran akan <strong>diproses otomatis</strong> oleh sistem.
        </p>

        <div class="card mt-4 mx-auto" style="max-width: 700px;">
            <div class="card-body">
                <h5 class="card-title">📋 Alur Absensi Siswa</h5>
                <ol class="mb-2">
                    <li>Lakukan <strong>Absen Masuk</strong> melalui tombol yang tersedia.</li>
                    <li>Sistem akan menentukan status secara otomatis berdasarkan waktu absen.</li>
                    <li>Jika lupa absen, ajukan koreksi sebelum pukul 23:59 hari yang sama.</li>
                    <li>Jika tidak hadir karena alasan tertentu, ajukan izin/sakit.</li>
                </ol>
                <p class="mb-0 text-danger">
                    ⚠️ <strong>Dilarang keras melakukan kecurangan</strong> seperti menitip absen, menyalahgunakan
                    sistem, atau memanipulasi data.
                    Setiap pelanggaran akan dikenakan sanksi.
                </p>
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

        // Aktifkan tooltip Bootstrap
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltipTriggerList].forEach(el => new bootstrap.Tooltip(el));
    </script>
</body>

</html>