<?php
include '../includes/config.php';
include '../includes/session.php';

if (isset($_POST['hapus_user'])) {
    $id = $_POST['id_pengguna'];
    $query = mysqli_query($koneksi, "DELETE FROM tb_pengguna WHERE id_pengguna='$id'");
    $_SESSION['toast'] = ['message' => 'Pengguna berhasil dihapus.', 'type' => 'success'];
}
header("Location: kelola_user.php");
exit;
