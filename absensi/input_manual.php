<?php
include '../includes/session.php';
include '../includes/config.php';

// Ambil semua siswa untuk dropdown
$siswa = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE role='siswa'");

if (isset($_POST['submit'])) {
    $id_pengguna = $_POST['id_pengguna'];
    $tanggal = $_POST['tanggal'];
    $jam_masuk = $_POST['jam_masuk'] ?: null;
    $jam_pulang = $_POST['jam_pulang'] ?: null;
    $keterangan = $_POST['keterangan'];

    // Cek apakah data sudah ada
    $cek = mysqli_query($koneksi, "SELECT * FROM tb_absensi WHERE id_pengguna='$id_pengguna' AND tanggal='$tanggal'");
    if (mysqli_num_rows($cek) > 0) {
        // Update
        mysqli_query($koneksi, "UPDATE tb_absensi SET jam_masuk='$jam_masuk', jam_pulang='$jam_pulang', keterangan='$keterangan' WHERE id_pengguna='$id_pengguna' AND tanggal='$tanggal'");
        $pesan = "Data absensi berhasil diperbarui.";
    } else {
        // Insert
        mysqli_query($koneksi, "INSERT INTO tb_absensi (id_pengguna, tanggal, jam_masuk, jam_pulang, keterangan)
            VALUES ('$id_pengguna', '$tanggal', '$jam_masuk', '$jam_pulang', '$keterangan')");
        $pesan = "Data absensi berhasil ditambahkan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Input Manual Absensi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/png" href="../img/logo.png">
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
        }

        .main-content {
            margin-left: 250px;
            padding: 80px 20px 20px;
            width: 100%;
        }

        @media (max-width: 991.98px) {
            .main-content {
                margin-left: 0;
            }
        }
        * Tambahan untuk dark mode pada kelola_user.php */
        .dark-mode .main-content {
            background-color: #1f1f1f;
            color: white;
        }

        .dark-mode input,
        .dark-mode select,
        .dark-mode .form-control {
            background-color: #333;
            color: white;
            border: 1px solid #555;
        }

        .dark-mode input::placeholder {
            color: #ccc;
        }

        .dark-mode label {
            color: #ddd;
        }
    </style>
    </style>
</head>

<body>
    <?php include '../includes/sidebar.php'; ?>
    <div class="main-content container mt-5">
        <h3>Input Absensi Manual</h3>
        <?php if (isset($pesan)): ?>
            <div class="alert alert-success"><?= $pesan ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label>Nama Siswa</label>
                <select name="id_pengguna" class="form-control" required>
                    <option value="">-- Pilih Siswa --</option>
                    <?php while ($row = mysqli_fetch_assoc($siswa)): ?>
                        <option value="<?= $row['id_pengguna'] ?>"><?= $row['nama_lengkap'] ?> (<?= $row['username'] ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Tanggal</label>
                <input type="date" name="tanggal" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Jam Masuk (opsional)</label>
                <input type="time" name="jam_masuk" class="form-control">
            </div>
            <div class="mb-3">
                <label>Jam Pulang (opsional)</label>
                <input type="time" name="jam_pulang" class="form-control">
            </div>
            <div class="mb-3">
                <label>Keterangan</label>
                <select name="keterangan" class="form-control" required>
                    <option value="Hadir">Hadir</option>
                    <option value="Izin">Izin</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Alpha">Alpha</option>
                </select>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
            <a href="../admin/dashboard_admin.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>

</html>