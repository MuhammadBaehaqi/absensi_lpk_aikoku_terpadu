<?php
include __DIR__ . '/../includes/config.php';
date_default_timezone_set('Asia/Jakarta');

$tanggal = date('Y-m-d');
$jam_sekarang = date('H:i');
$hari = date('N'); // 1 = Senin, ..., 7 = Minggu

// File log dan marker
$markerFile = __DIR__ . "/alpha_marker_$tanggal.txt";
$logFile = __DIR__ . "/alpha_log_$tanggal.txt";

// Fungsi log
function logAlpha($pesan)
{
    global $logFile;
    $waktu = date('H:i:s');
    file_put_contents($logFile, "[$waktu] $pesan\n", FILE_APPEND);
}

// 1. Lewat hari Minggu?
if ($hari == 7) {
    logAlpha("Hari Minggu — tidak ada auto-alpha.");
    exit;
}

// 2. Belum lewat jam 18:00?
if ($jam_sekarang < '18:00') {
    logAlpha("Masih jam $jam_sekarang — tunggu sampai 18:00.");
    exit;
}

// 3. Sudah diproses hari ini?
if (file_exists($markerFile)) {
    logAlpha("Sudah diproses hari ini.");
    exit;
}

// 4. Proses auto-alpha
$jumlahBaru = 0;
$jumlahUpdate = 0;

$siswa = mysqli_query($koneksi, "SELECT id_pengguna FROM tb_pengguna WHERE role = 'siswa'");
while ($s = mysqli_fetch_assoc($siswa)) {
    $id = $s['id_pengguna'];

    // Cek apakah sudah ada data absensi
    $cek = mysqli_query($koneksi, "SELECT * FROM tb_absensi WHERE id_pengguna='$id' AND tanggal='$tanggal'");

    if (mysqli_num_rows($cek) == 0) {
        // Belum ada → insert Alpha
        mysqli_query($koneksi, "INSERT INTO tb_absensi (id_pengguna, tanggal, keterangan) VALUES ('$id', '$tanggal', 'Alpha')");
        $jumlahBaru++;
        logAlpha("✅ Insert Alpha untuk ID $id (baru)");
    } else {
        // Ada, tapi kosong?
        $absen = mysqli_fetch_assoc($cek);
        if (empty($absen['jam_masuk']) && empty($absen['jam_pulang']) && empty($absen['keterangan'])) {
            mysqli_query($koneksi, "UPDATE tb_absensi SET keterangan='Alpha' WHERE id_absensi='{$absen['id_absensi']}'");
            $jumlahUpdate++;
            logAlpha("🔄 Update jadi Alpha untuk ID $id (data kosong)");
        }
    }
}

// Tandai sudah dijalankan
file_put_contents($markerFile, 'done');
logAlpha("🎉 Auto-Alpha selesai. Tambah baru: $jumlahBaru, update: $jumlahUpdate.");
echo "✅ Proses selesai. Tambah baru: $jumlahBaru, update: $jumlahUpdate.\n";
