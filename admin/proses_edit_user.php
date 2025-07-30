<?php
include '../includes/config.php';
include '../includes/session.php';

if (isset($_POST['edit_user'])) {
    $id = $_POST['id_pengguna'];
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $role = $_POST['role'];

    // Cek apakah username dan email sama (tidak diperbolehkan)
    if (strtolower($username) === strtolower($email)) {
        $_SESSION['toast'] = ['message' => 'Username dan Email tidak boleh sama.', 'type' => 'danger'];
    }
    // Cek apakah username sudah dipakai oleh pengguna lain
    elseif (mysqli_num_rows(mysqli_query($koneksi, "SELECT 1 FROM tb_pengguna WHERE username='$username' AND id_pengguna != '$id'")) > 0) {
        $_SESSION['toast'] = ['message' => 'Username sudah digunakan oleh pengguna lain.', 'type' => 'danger'];
    }
    // Cek apakah email sudah dipakai oleh pengguna lain
    elseif (mysqli_num_rows(mysqli_query($koneksi, "SELECT 1 FROM tb_pengguna WHERE email='$email' AND id_pengguna != '$id'")) > 0) {
        $_SESSION['toast'] = ['message' => 'Email sudah digunakan oleh pengguna lain.', 'type' => 'danger'];
    } else {
        // Jika valid, lakukan update
        mysqli_query($koneksi, "UPDATE tb_pengguna 
            SET nama_lengkap='$nama', username='$username', email='$email', role='$role'
            WHERE id_pengguna='$id'");
        $_SESSION['toast'] = ['message' => 'Data berhasil diperbarui!', 'type' => 'success'];
    }

    header("Location: kelola_user.php");
    exit;
}
