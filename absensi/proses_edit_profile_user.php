<?php
include '../includes/session.php';
include '../includes/config.php';

$id = $_POST['id_pengguna'];
$nama = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
$email = mysqli_real_escape_string($koneksi, $_POST['email']);
$no_telp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);

$query = mysqli_query($koneksi, "UPDATE tb_pengguna SET 
    nama_lengkap = '$nama',
    email = '$email',
    no_telp = '$no_telp'
    WHERE id_pengguna = '$id'
");

if ($query) {
    // Update session juga biar langsung berubah di dashboard
    $_SESSION['username'] = $nama;

    echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='../dashboard_siswa.php';</script>";
} else {
    echo "<script>alert('Gagal memperbarui profil!'); history.back();</script>";
}
