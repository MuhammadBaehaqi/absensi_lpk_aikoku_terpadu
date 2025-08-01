<?php
include '../includes/session.php';
include '../includes/config.php';

$id = $_SESSION['id_pengguna'];
$password_lama = $_POST['password_lama'];
$password_baru = $_POST['password_baru'];
$konfirmasi = $_POST['konfirmasi_password'];

// Ambil password lama dari DB
$query = mysqli_query($koneksi, "SELECT password FROM tb_pengguna WHERE id_pengguna='$id'");
$data = mysqli_fetch_assoc($query);

// Cocokkan password lama
if (!password_verify($password_lama, $data['password'])) {
    echo "<script>alert('Password lama salah!'); history.back();</script>";
    exit;
}

// Cocokkan password baru dan konfirmasi
if ($password_baru != $konfirmasi) {
    echo "<script>alert('Konfirmasi password tidak cocok!'); history.back();</script>";
    exit;
}

// Hash password baru
$password_baru_hash = password_hash($password_baru, PASSWORD_DEFAULT);

// Update password
$update = mysqli_query($koneksi, "UPDATE tb_pengguna SET password='$password_baru_hash' WHERE id_pengguna='$id'");

if ($update) {
    echo "<script>alert('Password berhasil diperbarui!'); window.location.href='../dashboard_siswa.php';</script>";
} else {
    echo "<script>alert('Gagal memperbarui password!'); history.back();</script>";
}
