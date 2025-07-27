<?php
include '../includes/session.php';
include '../includes/config.php';

$tgl_awal = $_GET['tgl_awal'] ?? '';
$tgl_akhir = $_GET['tgl_akhir'] ?? '';

$rekap = [];
if (!empty($tgl_awal) && !empty($tgl_akhir)) {
    // Ambil semua siswa
    $siswa = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE role='siswa'");

    while ($s = mysqli_fetch_assoc($siswa)) {
        $id = $s['id_pengguna'];
        $nama = $s['nama_lengkap'];

        $rekap[$id] = [
            'nama' => $nama,
            'Hadir' => 0,
            'Izin' => 0,
            'Sakit' => 0,
            'Alpha' => 0
        ];

        $q = mysqli_query($koneksi, "
            SELECT keterangan, COUNT(*) AS total 
            FROM tb_absensi 
            WHERE id_pengguna='$id' AND tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir'
            GROUP BY keterangan
        ");
        while ($r = mysqli_fetch_assoc($q)) {
            $rekap[$id][$r['keterangan']] = $r['total'];
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Rekap Mingguan Semua Siswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
</head>

<body>
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content container mt-5">
        <h3>Rekap Absensi Mingguan Semua Siswa</h3>

        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label>Dari Tanggal</label>
                <input type="date" name="tgl_awal" class="form-control" value="<?= $tgl_awal ?>" required>
            </div>
            <div class="col-md-4">
                <label>Sampai Tanggal</label>
                <input type="date" name="tgl_akhir" class="form-control" value="<?= $tgl_akhir ?>" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100">Tampilkan</button>
            </div>
        </form>

        <?php if (!empty($rekap)): ?>
            <h5 class="mb-3">Periode: <?= date('d-m-Y', strtotime($tgl_awal)) ?> s.d.
                <?= date('d-m-Y', strtotime($tgl_akhir)) ?>
            </h5>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Hadir</th>
                        <th>Izin</th>
                        <th>Sakit</th>
                        <th>Alpha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($rekap as $r): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $r['nama'] ?></td>
                            <td><?= $r['Hadir'] ?></td>
                            <td><?= $r['Izin'] ?></td>
                            <td><?= $r['Sakit'] ?></td>
                            <td><?= $r['Alpha'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="rekap_mingguan_semua_pdf.php?tgl_awal=<?= $tgl_awal ?>&tgl_akhir=<?= $tgl_akhir ?>" target="_blank"
                class="btn btn-danger">Cetak PDF</a>
        <?php elseif (!empty($tgl_awal)): ?>
            <div class="alert alert-warning">Tidak ada data absensi dalam minggu ini.</div>
        <?php endif; ?>

        <a href="../admin/dashboard_admin.php" class="btn btn-secondary mt-3">Kembali</a>
    </div>
</body>

</html>