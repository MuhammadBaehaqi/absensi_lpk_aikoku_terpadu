<?php
include '../../includes/config.php';

// ADMIN
$username_admin = "admin";
$check_admin = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE username='$username_admin'");
if (mysqli_num_rows($check_admin) == 0) {
    $nama_admin = "Admin LPK";
    $password_admin = password_hash("admin123", PASSWORD_DEFAULT);
    $role_admin = "admin";

    mysqli_query($koneksi, "INSERT INTO tb_pengguna (nama_lengkap, username, password, role, no_telp)
VALUES ('$nama_admin', '$username_admin', '$password_admin', '$role_admin', '08123456789')");
    echo "✅ Admin berhasil ditambahkan<br>";
} else {
    echo "⚠️ Username admin sudah ada<br>";
}

// SISWA
$username_siswa = "siswa1";
$check_siswa = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE username='$username_siswa'");
if (mysqli_num_rows($check_siswa) == 0) {
    $nama_siswa = "Siswa LPK";
    $password_siswa = password_hash("siswa123", PASSWORD_DEFAULT);
    $role_siswa = "siswa";

    mysqli_query($koneksi, "INSERT INTO tb_pengguna (nama_lengkap, username, password, role, no_telp) 
    VALUES ('$nama_siswa', '$username_siswa', '$password_siswa', '$role_siswa', '08123456788')");
    echo "✅ Siswa berhasil ditambahkan<br>";
} else {
    echo "⚠️ Username siswa1 sudah ada<br>";
}
