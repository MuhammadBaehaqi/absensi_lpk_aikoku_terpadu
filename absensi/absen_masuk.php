<?php
include '../includes/session.php';
include '../includes/config.php';

date_default_timezone_set('Asia/Jakarta');

$id_pengguna = $_SESSION['id_pengguna'];
$tanggal = date('Y-m-d');
$jam = date('H:i:s');
$jam_sekarang = date('H:i');

$cek = mysqli_query($koneksi, "SELECT * FROM tb_absensi WHERE id_pengguna='$id_pengguna' AND tanggal='$tanggal'");
$data = mysqli_fetch_assoc($cek);

// ⏰ Kalau sebelum jam 18:00 (normal)
if ($jam_sekarang < '18:00') {
    if (!$data) {
        mysqli_query($koneksi, "INSERT INTO tb_absensi (id_pengguna, tanggal, jam_masuk) 
                                VALUES ('$id_pengguna', '$tanggal', '$jam')");
        $pesan = "Absen Masuk berhasil!";
    } elseif (empty($data['jam_masuk'])) {
        mysqli_query($koneksi, "UPDATE tb_absensi SET jam_masuk='$jam', keterangan=NULL WHERE id_absen='{$data['id_absen']}'");
        $pesan = "Absen Masuk berhasil!";
    } else {
        $pesan = "Kamu sudah absen hari ini.";
    }
} else {
    // ⏰ Setelah jam 18:00
    if ($data && $data['keterangan'] == 'Alpha' && empty($data['jam_masuk'])) {
        // Munculkan form koreksi jika belum absen masuk
        $_SESSION['koreksi_alpha'] = true;
        header("Location: koreksi_alpha_form.php"); // buat file ini
        exit;
    } else {
        $pesan = "Kamu sudah absen hari ini atau sudah lewat batas waktu.";
    }
}

echo "<script>alert('$pesan'); window.location.href='../dashboard_siswa.php';</script>";
