<?php
include '../includes/session.php';
include '../includes/config.php';

$id_pengguna = $_SESSION['id_pengguna'];
$query = mysqli_query($koneksi, "SELECT * FROM tb_absensi WHERE id_pengguna='$id_pengguna' ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Riwayat Absensi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
</head>

<body>
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
        <h3>Riwayat Absensi</h3>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Status Kehadiran</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>

                        <!-- Jam Masuk -->
                        <td>
                            <?= $row['jam_masuk'] ? $row['jam_masuk'] : '<span class="text-muted">-</span>' ?>
                        </td>

                        <!-- Jam Pulang -->
                        <td>
                            <?php if (!empty($row['jam_pulang'])): ?>
                                <?= $row['jam_pulang'] ?>
                            <?php else: ?>
                                <span class="badge bg-secondary">Belum Absen Pulang</span>
                            <?php endif; ?>
                        </td>

                        <!-- Status -->
                        <td>
                            <?php
                            $tanggal = $row['tanggal'];
                            $jam_masuk = strtotime($row['jam_masuk'] ?? '00:00:00');
                            $batas_terlambat = strtotime('10:00:00');

                            // Cek apakah ada izin yang disetujui
                            $cekIzin = mysqli_query($koneksi, "
        SELECT jenis FROM tb_pengajuan_izin 
        WHERE id_pengguna = '$id_pengguna' 
        AND tanggal = '$tanggal' 
        AND status = 'Diterima' 
        ORDER BY id_pengajuan DESC LIMIT 1
    ");

                            if (mysqli_num_rows($cekIzin) > 0) {
                                $izin = mysqli_fetch_assoc($cekIzin);
                                $jenis = htmlspecialchars($izin['jenis']);

                                // Warna badge tergantung jenis izin
                                $warna = $jenis == 'Sakit' ? 'bg-info text-dark' : 'bg-warning text-dark';
                                echo "<span class='badge $warna'>$jenis ✅</span>";
                            }

                            // Tidak ada izin disetujui, lanjut normal
                            elseif ($row['keterangan'] == 'Alpha') {
                                $cekKoreksi = mysqli_query($koneksi, "
            SELECT status FROM tb_koreksi_absen 
            WHERE id_pengguna = '$id_pengguna' 
            AND tanggal = '$tanggal' 
            ORDER BY id_koreksi DESC LIMIT 1");

                                if (mysqli_num_rows($cekKoreksi) > 0) {
                                    $kor = mysqli_fetch_assoc($cekKoreksi);
                                    if ($kor['status'] == 'Menunggu') {
                                        echo '<span class="badge bg-warning text-dark">Menunggu Koreksi ⚠️</span>';
                                    } elseif ($kor['status'] == 'Disetujui') {
                                        echo '<span class="badge bg-info text-dark">Koreksi Diterima 📝</span>';
                                    } else {
                                        echo '<span class="badge bg-danger">Alpha ❌</span>';
                                    }
                                } else {
                                    echo '<span class="badge bg-danger">Alpha ❌</span>';
                                }

                            } elseif (!empty($row['jam_masuk'])) {
                                if ($row['keterangan'] == 'Hadir (Lupa Absen)') {
                                    echo '<span class="badge bg-info text-dark">Hadir (Lupa Absen) 📝</span>';
                                } else {
                                    if ($jam_masuk > $batas_terlambat) {
                                        echo '<span class="badge bg-warning text-dark">Terlambat ⏰</span>';
                                    } else {
                                        echo '<span class="badge bg-success">Hadir ✅</span>';
                                    }
                                }
                            } else {
                                echo '<span class="badge bg-secondary">Belum Absen</span>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="../dashboard_siswa.php" class="btn btn-secondary">Kembali</a>
    </div>
</body>

</html>