<?php
include '../includes/session.php';
include '../includes/config.php';

$id = $_SESSION['id_pengguna'];
$query = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE id_pengguna = '$id'");
$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../img/logo.png">
    <style>
        .logo-img {
            height: 40px;
            margin-right: 10px;
        }

        /* Tambahan CSS responsif untuk HP */
        @media (max-width: 576px) {
            .navbar .navbar-text {
                font-size: 0.8rem;
            }

            h2 {
                font-size: 1.2rem;
            }

            .logo-img {
                height: 30px;
                margin-right: 5px;
            }

            .btn {
                font-size: 0.8rem;
                padding: 8px 10px;
            }

            .alert,
            .card,
            .alert-info {
                margin-left: 10px;
                margin-right: 10px;
            }

            .card-title {
                font-size: 1rem;
            }

            ol {
                padding-left: 1rem;
            }
        }

        .logo-img {
            height: 40px;
        }

        @media (max-width: 576px) {
            .navbar .navbar-brand {
                font-size: 1rem;
            }

            .navbar .text-white {
                font-size: 0.85rem;
            }

            .btn.btn-outline-light {
                font-size: 0.75rem;
                padding: 4px 10px;
            }
        }

        .logo-img {
            height: 40px;
        }

        @media (max-width: 576px) {
            .navbar .navbar-brand {
                font-size: 1rem;
            }

            .navbar .text-white,
            .navbar .small {
                font-size: 0.85rem;
            }

            .btn.btn-outline-light {
                font-size: 0.75rem;
                padding: 4px 10px;
            }
        }
    </style>
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container text-white">
            <!-- Desktop View (Logo kiri, Salam kanan) -->
            <div class="d-none d-md-flex justify-content-between align-items-center w-100">
                <div class="d-flex align-items-center">
                    <img src="../img/logo.png" alt="Logo LPK" class="logo-img me-2">
                    <span class="navbar-brand mb-0 h5">LPK AIKOKU TERPADU</span>
                </div>
                <div class="text-end small">
                    <div>いらっしゃいませ, <?= $_SESSION['username']; ?> (<?= $_SESSION['role']; ?>)</div>
                    <a href="../auth/logout.php" class="btn btn-outline-light btn-sm mt-1">Logout</a>
                </div>
            </div>

            <!-- Mobile View (Semua tengah, logo lebih besar, teks tengah) -->
            <div class="d-block d-md-none w-100 text-center">
                <img src="../img/logo.png" alt="Logo LPK" class="mb-1" style="height: 45px;"> <!-- DIBESARKAN -->
                <div class="fw-bold text-white" style="font-size: 1rem;">LPK AIKOKU TERPADU</div>
                <div class="small">いらっしゃいませ, <?= $_SESSION['username']; ?> (<?= $_SESSION['role']; ?>)</div>
                <a href="../auth/logout.php" class="btn btn-outline-light btn-sm mt-1">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-2 mt-md-4">

        <div class="card mx-auto" style="max-width: 600px;">
            <div class="card-body">
                <h4 class="card-title text-center mb-4">✏️ Edit Profil Siswa</h4>
                <form method="POST" action="proses_edit_profile_user.php">
                    <input type="hidden" name="id_pengguna" value="<?= $data['id_pengguna']; ?>">

                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control"
                            value="<?= $data['nama_lengkap']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>Username (Tidak Bisa Diubah)</label>
                        <input type="text" class="form-control" value="<?= $data['username']; ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= $data['email']; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label>No HP</label>
                        <input type="text" name="no_hp" class="form-control" value="<?= $data['no_telp']; ?>" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                        <a href="../dashboard_siswa.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>