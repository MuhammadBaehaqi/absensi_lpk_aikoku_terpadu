<?php
include __DIR__ . '/../includes/config.php';
date_default_timezone_set('Asia/Jakarta');

$tanggal = date('Y-m-d');
$jam_sekarang = date('H:i');
$hari = date('N'); // 1 = Senin, ..., 7 = Minggu

// Minggu = tidak ada absensi
if ($hari == 7) {
    echo "📅 Hari Minggu - Tidak ada auto-Alpha.\n";
    exit;
}

// Batasi waktu sampai jam 18:00
if ($jam_sekarang < '18:00') {
    echo "⏳ Masih belum lewat batas waktu (18:00). Auto-Alpha belum dijalankan.\n";
    exit;
}

// Cek apakah sudah diproses hari ini
$cekMarker = __DIR__ . "/alpha_marker_$tanggal.txt";
if (file_exists($cekMarker)) {
    echo "✅ Auto-Alpha sudah dijalankan hari ini.\n";
    exit;
}

// Proses auto-Alpha
$siswa = mysqli_query($koneksi, "SELECT id_pengguna FROM tb_pengguna WHERE role = 'siswa'");
$jumlahAlpha = 0;

while ($s = mysqli_fetch_assoc($siswa)) {
    $id = $s['id_pengguna'];
    $cek = mysqli_query($koneksi, "SELECT * FROM tb_absensi WHERE id_pengguna='$id' AND tanggal='$tanggal'");
    if (mysqli_num_rows($cek) == 0) {
        mysqli_query($koneksi, "INSERT INTO tb_absensi (id_pengguna, tanggal, keterangan) VALUES ('$id', '$tanggal', 'Alpha')");
        $jumlahAlpha++;
    }
}

file_put_contents($cekMarker, 'done');
echo "✅ Proses selesai. $jumlahAlpha siswa ditandai sebagai Alpha.\n";
