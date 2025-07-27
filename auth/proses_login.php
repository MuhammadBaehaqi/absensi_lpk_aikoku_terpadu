<?php
session_start();
include '../includes/config.php';

$username = $_POST['username'];
$password = $_POST['password'];

$query = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE username='$username'");
$data = mysqli_fetch_assoc($query);

if ($data && password_verify($password, $data['password'])) {
    $_SESSION['id_pengguna'] = $data['id_pengguna'];
    $_SESSION['username'] = $data['username'];
    $_SESSION['role'] = $data['role'];

    // ✅ Login sukses → redirect sesuai role
    $_SESSION['toast'] = [
        'message' => 'Login berhasil! Selamat datang, ' . $data['username'],
        'type' => 'success'
    ];

    if ($data['role'] == 'admin') {
        header("Location: ../admin/dashboard_admin.php");
    } else {
        header("Location: ../dashboard_siswa.php");
    }
    exit;
} else {
    // ❌ Login gagal → tampilkan toast di index.php
    $_SESSION['toast'] = [
        'message' => 'Username atau Password salah!',
        'type' => 'danger'
    ];
    header("Location: ../index.php");
    exit;
}
