<?php
include '../includes/config.php';
include '../includes/session.php';

// ====== Ambil Data Statistik Absensi Hari Ini ======
$tanggal = date('Y-m-d');

// Total siswa
$query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM tb_pengguna WHERE role='siswa'");
$data = mysqli_fetch_assoc($query);
$totalSiswa = $data['total'] ?? 0;

// Hadir
$query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM tb_absensi WHERE tanggal = '$tanggal' AND keterangan = 'Hadir'");
$hadir = mysqli_fetch_assoc($query)['total'] ?? 0;

// Izin
$query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM tb_absensi WHERE tanggal = '$tanggal' AND keterangan = 'Izin'");
$izin = mysqli_fetch_assoc($query)['total'] ?? 0;

// Sakit
$query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM tb_absensi WHERE tanggal = '$tanggal' AND keterangan = 'Sakit'");
$sakit = mysqli_fetch_assoc($query)['total'] ?? 0;

// Alpha
$query = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM tb_absensi WHERE tanggal = '$tanggal' AND keterangan = 'Alpha'");
$alpha = mysqli_fetch_assoc($query)['total'] ?? 0;

// Belum Absen = Total siswa - yang sudah absen
$belumAbsen = $totalSiswa - ($hadir + $izin + $sakit + $alpha);

// ====== Dummy Data Grafik Bulanan (sementara) ======
$grafik = [
    'Hadir' => 20,
    'Izin' => 2,
    'Sakit' => 1,
    'Alpha' => 3
];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../img/logo.png">

</head>

<body>
    <?php include '../includes/sidebar.php'; ?>

    <div class="content" id="mainContent">
        <h4>📊 Statistik Absensi Hari Ini (<?= date('d-m-Y') ?>)</h4>
        <div class="row g-3 mb-4 mt-2">
            <?php
            $cards = [
                ['Total Siswa', $totalSiswa, 'primary'],
                ['Hadir', $hadir, 'success'],
                ['Izin', $izin, 'warning'],
                ['Sakit', $sakit, 'info'],
                ['Alpha', $alpha, 'danger'],
                ['Belum Absen', $belumAbsen, 'secondary']
            ];
            foreach ($cards as [$label, $value, $color]) {
                echo "
                <div class='col-md-2'>
                    <div class='card text-white bg-$color'>
                        <div class='card-body text-center'>
                            <h6 class='card-title mb-1'>$label</h6>
                            <h4 class='fw-bold'>$value</h4>
                        </div>
                    </div>
                </div>";
            }
            ?>
        </div>

        <div class="card p-4">
            <h5 class="mb-3">📈 Grafik Rekap Bulan <?= date('F') ?></h5>
            <canvas id="grafikBulanan" height="100"></canvas>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        
        // Grafik
        const ctx = document.getElementById('grafikBulanan').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
                datasets: [{
                    label: 'Jumlah Hari',
                    data: [<?= $grafik['Hadir'] ?? 0 ?>, <?= $grafik['Izin'] ?? 0 ?>, <?= $grafik['Sakit'] ?? 0 ?>, <?= $grafik['Alpha'] ?? 0 ?>],
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(13, 202, 240, 0.7)',
                        'rgba(220, 53, 69, 0.7)'
                    ],
                    borderColor: [
                        'rgba(25, 135, 84, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(13, 202, 240, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    </script>
</body>

</html>
