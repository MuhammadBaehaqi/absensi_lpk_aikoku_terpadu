<?php
include '../includes/session.php';
include '../includes/config.php';

$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');

// Ambil semua siswa
$siswa = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE role='siswa'");

// Siapkan array rekap
$rekap = [];

while ($s = mysqli_fetch_assoc($siswa)) {
    $id = $s['id_pengguna'];
    $nama = $s['nama_lengkap'];

    $query = mysqli_query($koneksi, "
        SELECT keterangan, COUNT(*) AS total 
        FROM tb_absensi 
        WHERE id_pengguna='$id' 
        AND MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun'
        GROUP BY keterangan
    ");

    $rekap[$id] = [
        'nama' => $nama,
        'Hadir' => 0,
        'Izin' => 0,
        'Sakit' => 0,
        'Alpha' => 0
    ];

    while ($r = mysqli_fetch_assoc($query)) {
        $rekap[$id][$r['keterangan']] = $r['total'];
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Rekap Semua Siswa Bulanan</title>
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
        <h3>Rekap Absensi Semua Siswa</h3>
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <label>Bulan</label>
                <select name="bulan" class="form-control" required>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= str_pad($m, 2, '0', STR_PAD_LEFT) ?>" <?= $bulan == str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>>
                            <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label>Tahun</label>
                <input type="number" name="tahun" class="form-control" value="<?= $tahun ?>" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100">Tampilkan</button>
            </div>
        </form>

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
                foreach ($rekap as $id => $r): ?>
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
        <a href="rekap_bulanan_semua_pdf.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" target="_blank"
            class="btn btn-danger mt-3">
            Cetak PDF Semua Siswa
        </a>

        <a href="../admin/dashboard_admin.php" class="btn btn-secondary">Kembali</a>
    </div>
</body>

</html>