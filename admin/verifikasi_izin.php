<?php
include '../includes/session.php';
include '../includes/config.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Terima / Tolak
if (isset($_GET['id']) && isset($_GET['aksi'])) {
    $id = $_GET['id'];
    $aksi = $_GET['aksi'];

    if ($aksi == 'terima') {
        // Ambil data pengajuan
        $pengajuan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM tb_pengajuan_izin WHERE id_pengajuan='$id'"));
        $id_pengguna = $pengajuan['id_pengguna'];
        $tanggal = $pengajuan['tanggal'];
        $keterangan = $pengajuan['jenis'];

        // Cek apakah sudah ada absensi
        $cek = mysqli_query($koneksi, "SELECT * FROM tb_absensi WHERE id_pengguna='$id_pengguna' AND tanggal='$tanggal'");
        if (mysqli_num_rows($cek) == 0) {
            mysqli_query($koneksi, "INSERT INTO tb_absensi (id_pengguna, tanggal, keterangan) 
                                    VALUES ('$id_pengguna', '$tanggal', '$keterangan')");
        } else {
            mysqli_query($koneksi, "UPDATE tb_absensi SET keterangan='$keterangan' 
                                    WHERE id_pengguna='$id_pengguna' AND tanggal='$tanggal'");
        }

        mysqli_query($koneksi, "UPDATE tb_pengajuan_izin SET status='Diterima' WHERE id_pengajuan='$id'");
    } elseif ($aksi == 'tolak') {
        mysqli_query($koneksi, "UPDATE tb_pengajuan_izin SET status='Ditolak' WHERE id_pengajuan='$id'");
    }

    header("Location: verifikasi_izin.php");
    exit;
}

// Ambil semua pengajuan
$data = mysqli_query($koneksi, "
    SELECT z.*, u.nama_lengkap 
    FROM tb_pengajuan_izin z 
    JOIN tb_pengguna u ON z.id_pengguna = u.id_pengguna
    ORDER BY z.tanggal DESC
");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Verifikasi Pengajuan Izin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
        }

        .main-content {
            margin-left: 250px;
            padding: 80px 20px 20px;
            width: 100%;
        }

        @media (max-width: 991.98px) {
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content container mt-5">
        <h3>Verifikasi Pengajuan Izin/Sakit</h3>
        <table class="table table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th>Nama</th>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Alasan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td><?= $row['nama_lengkap'] ?></td>
                        <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
                        <td><?= $row['jenis'] ?></td>
                        <td><?= $row['alasan'] ?></td>
                        <td><?= $row['status'] ?></td>
                        <td>
                            <?php if ($row['status'] == 'Menunggu'): ?>
                                <a href="?id=<?= $row['id_pengajuan'] ?>&aksi=terima" class="btn btn-success btn-sm">Terima</a>
                                <a href="?id=<?= $row['id_pengajuan'] ?>&aksi=tolak" class="btn btn-danger btn-sm">Tolak</a>
                            <?php else: ?>
                                <em>-</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="dashboard_admin.php" class="btn btn-secondary">Kembali</a>
    </div>
</body>

</html>