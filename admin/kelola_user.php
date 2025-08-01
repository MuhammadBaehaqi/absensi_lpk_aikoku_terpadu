<?php
include '../includes/session.php';
include '../includes/config.php';

// Jumlah data per halaman dari select dropdown
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 5;

// Halaman admin & siswa
$page_admin = isset($_GET['page_admin']) ? (int) $_GET['page_admin'] : 1;
$page_siswa = isset($_GET['page_siswa']) ? (int) $_GET['page_siswa'] : 1;

$offset_admin = ($page_admin - 1) * $limit;
$offset_siswa = ($page_siswa - 1) * $limit;

// Proses tambah user
if (isset($_POST['submit'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $no_telp = mysqli_real_escape_string($koneksi, $_POST['no_telp']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Validasi bahwa username ≠ email
    if (strtolower($username) === strtolower($email)) {
        $_SESSION['toast'] = ['message' => 'Username dan Email tidak boleh sama.', 'type' => 'danger'];
    }
    // Cek username sudah dipakai
    elseif (mysqli_num_rows(mysqli_query($koneksi, "SELECT 1 FROM tb_pengguna WHERE username='$username'")) > 0) {
        $_SESSION['toast'] = ['message' => 'Username sudah digunakan.', 'type' => 'danger'];
    }
    // Cek email sudah dipakai
    elseif (mysqli_num_rows(mysqli_query($koneksi, "SELECT 1 FROM tb_pengguna WHERE email='$email'")) > 0) {
        $_SESSION['toast'] = ['message' => 'Email sudah digunakan.', 'type' => 'danger'];
    } else {
        // Nama boleh sama, langsung simpan
        mysqli_query($koneksi, "INSERT INTO tb_pengguna 
                (nama_lengkap, username, email, no_telp, password, role, tanggal_dibuat)
                VALUES 
                ('$nama', '$username', '$email', '$no_telp', '$password', '$role', NOW())");

        $_SESSION['toast'] = ['message' => 'Pengguna berhasil ditambahkan!', 'type' => 'success'];
    }

    header("Location: kelola_user.php");
    exit;
}

// Ambil data admin dan siswa
$total_admin = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM tb_pengguna WHERE role='admin'"))[0];
$total_pages_admin = ceil($total_admin / $limit);
$data_admin = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE role='admin' ORDER BY id_pengguna ASC
 LIMIT $limit OFFSET $offset_admin");

$total_siswa = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM tb_pengguna WHERE role='siswa'"))[0];
$total_pages_siswa = ceil($total_siswa / $limit);
$data_siswa = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE role='siswa' ORDER BY id_pengguna ASC
 LIMIT $limit OFFSET $offset_siswa");
?>
<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>Kelola User</title>
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

        /* DARK MODE STYLING */
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

        /* Perbaiki tampilan panah select di dark mode */
        .dark-mode select.form-select {
            background-color: #333;
            color: white;
            border: 1px solid #555;
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='white' viewBox='0 0 16 16'%3E%3Cpath d='M1.5 5.5l6 6 6-6'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1rem;
        }

        .dark-mode input::placeholder,
        .dark-mode .form-control::placeholder {
            color: #ccc;
        }

        .dark-mode label {
            color: #ddd;
        }

        .dark-mode .table {
            background-color: #1f1f1f;
            color: #f1f1f1;
        }

        .dark-mode .table th {
            background-color: #2c2f33;
            color: #ffffff;
        }

        .dark-mode .table td {
            background-color: #1f1f1f;
            color: #f1f1f1;
            border-color: #444;
        }

        .dark-mode .table-bordered th,
        .dark-mode .table-bordered td {
            border: 1px solid #444 !important;
        }

        .dark-mode .table tbody tr:hover {
            background-color: #2a2a2a;
        }

    
        /* Warna latar dan teks card dalam mode gelap */
        .dark-mode .card {
            background-color: #1f1f1f;
            color: #f1f1f1;
        }

        /* Warna header card */
        .dark-mode .card-header {
            background-color: #2c2f33 !important;
            color: #fff !important;
        }

        /* Warna isi body card */
        .dark-mode .card-body {
            background-color: #1f1f1f;
            color: #f1f1f1;
            border-color: #444;
        }

        /* Tambahan hover dan border */
        .dark-mode .card,
        .dark-mode .card-body,
        .dark-mode .card-header {
            border-color: #444;
        }
        .table-responsive {
            overflow-x: scroll !important;
            scrollbar-width: auto; /* Firefox */
        }

        .table-responsive::-webkit-scrollbar {
            height: 8px;
            background-color: #ccc; /* warna track */
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background-color: #888; /* warna thumb */
            border-radius: 4px;
        }

        /* DARK MODE: Modal Styling */
.dark-mode .modal-content {
    background-color: #2a2a2a;
    color: #fff;
    border: 1px solid #444;
}

.dark-mode .modal-header {
    background-color: #1f1f1f;
    border-bottom: 1px solid #444;
    color: #fff;
}

.dark-mode .modal-body {
    background-color: #2a2a2a;
    color: #fff;
}

.dark-mode .modal-footer {
    background-color: #1f1f1f;
    border-top: 1px solid #444;
}

.dark-mode .btn-close {
    filter: invert(1);
}

    </style>
    <link rel="icon" type="image/png" href="../img/logo.png">
</head>

<?php include '../includes/sidebar.php'; ?>
<div class="main-content">
    <?php if (isset($_SESSION['toast'])): ?>
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999;">
            <div class="toast align-items-center text-white bg-<?= $_SESSION['toast']['type'] ?> border-0 show"
                role="alert">
                <div class="d-flex">
                    <div class="toast-body"><?= $_SESSION['toast']['message'] ?></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['toast']); endif; ?>

    <div class="container">
    <h3>Tambah Pengguna Baru</h3>

    <div class="alert alert-info" role="alert">
        <strong>Catatan:</strong>
        <ul class="mb-0 ps-3">
            <li>Username dan email <strong>tidak boleh sama</strong>.</li>
            <li>Pastikan username dan email <strong>belum digunakan oleh pengguna lain</strong>.</li>
            <li><strong>Username digunakan untuk login</strong>, harap mudah diingat.</li>
            <li>Email harus aktif dan valid untuk keperluan verifikasi atau komunikasi.</li>
            <li>Role menentukan akses pengguna: <em>admin</em> atau <em>siswa</em>.</li>
        </ul>
    </div>

        <form method="post" class="row g-3">
            <div class="col-md-6"><label>Nama Lengkap</label><input type="text" name="nama" class="form-control"
                    required></div>
            <div class="col-md-6"><label>Username</label><input type="text" name="username" class="form-control"
                    required></div>
            <div class="col-md-6"><label>Email</label><input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label>No Telepon</label>
                <input type="text" name="no_telp" class="form-control" required>
            </div>
            <div class="col-md-6"><label>Password</label><input type="text" name="password" class="form-control"
                    required></div>
            <div class="col-md-6">
                <label>Role</label>
                <select name="role" class="form-control" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="admin">Admin</option>
                    <option value="siswa">Siswa</option>
                </select>
            </div>
            <div class="col-12">
                <button class="btn btn-primary" name="submit">Simpan</button>
                <a href="dashboard_admin.php" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>

    <hr class="my-5">

<div class="card border-primary shadow mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Daftar Admin</h5>
    </div>
    <div class="card-body">

        <form method="get" class="mb-3">
            <label class="fw-semibold">Tampilkan:</label>
            <select name="limit" onchange="this.form.submit()" class="form-select d-inline-block w-auto ms-2">
                <?php foreach ([5, 10, 15, 20] as $opt): ?>
                    <option value="<?= $opt ?>" <?= ($limit == $opt ? 'selected' : '') ?>><?= $opt ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="page_siswa" value="<?= $page_siswa ?>">
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>No Telp</th>
                        <th>Role</th>
                        <th>Tanggal Buat Akun</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = $offset_admin + 1;
                    while ($row = mysqli_fetch_assoc($data_admin)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['no_telp']) ?></td>
                            <td><?= htmlspecialchars($row['role']) ?></td>
                            <td><?= date('d-m-Y H:i', strtotime($row['tanggal_dibuat'])) ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#editModal<?= $row['id_pengguna'] ?>">Edit</button>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal<?= $row['id_pengguna'] ?>">Hapus</button>
                            </td>
                        </tr>
                    <!-- Modal Edit -->
                    <div class="modal fade" id="editModal<?= $row['id_pengguna'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="post" action="proses_edit_user.php">
                                <input type="hidden" name="id_pengguna" value="<?= $row['id_pengguna'] ?>">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Pengguna</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-2">
                                            <label>Nama Lengkap</label>
                                            <input type="text" name="nama" class="form-control" value="<?= $row['nama_lengkap'] ?>"
                                                required>
                                        </div>
                                        <div class="mb-2">
                                            <label>Username</label>
                                            <input type="text" name="username" class="form-control" value="<?= $row['username'] ?>"
                                                required>
                                        </div>
                                        <div class="mb-2">
                                            <label>Email</label>
                                            <input type="email" name="email" class="form-control" value="<?= $row['email'] ?>" required>
                                        </div>
                                        <div class="mb-2">
                                            <label>No Telepon</label>
                                            <input type="text" name="no_telp" class="form-control" value="<?= $row['no_telp'] ?>" required>
                                        </div>
                                        <div class="mb-2">
                                            <label>Role</label>
                                            <select name="role" class="form-select" required>
                                                <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                                <option value="siswa" <?= $row['role'] == 'siswa' ? 'selected' : '' ?>>Siswa</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-success" type="submit" name="edit_user">Simpan Perubahan</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                
                    <!-- Modal Hapus -->
                    <div class="modal fade" id="deleteModal<?= $row['id_pengguna'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="post" action="proses_hapus_user.php">
                                <input type="hidden" name="id_pengguna" value="<?= $row['id_pengguna'] ?>">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Hapus Pengguna</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        Yakin ingin menghapus <strong><?= $row['nama_lengkap'] ?></strong>?
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-danger" type="submit" name="hapus_user">Hapus</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
                </tbody>
            </table>
             <!-- Pagination -->
        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_pages_admin; $i++): ?>
                        <li class="page-item <?= ($i == $page_admin) ? 'active' : '' ?>">
                            <a class="page-link"
                                href="?page_admin=<?= $i ?>&page_siswa=<?= $page_siswa ?>&limit=<?= $limit ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>       
    </div>
</div>

<div class="card border-success shadow mb-4">
    <div class="card-header bg-success text-white">
        <h5 class="mb-0">Daftar Siswa</h5>
    </div>
    <div class="card-body">
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-success">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>No Telp</th>
                    <th>Role</th>
                    <th>Tanggal Buat Akun</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = $offset_siswa + 1;
                while ($row = mysqli_fetch_assoc($data_siswa)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['no_telp']) ?></td>
                        <td><?= htmlspecialchars($row['role']) ?></td>
                        <td><?= date('d-m-Y H:i', strtotime($row['tanggal_dibuat'])) ?></td>
                        <td>
                            <!-- Tombol Edit -->
                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                data-bs-target="#editModal<?= $row['id_pengguna'] ?>">Edit</button>
                
                            <!-- Tombol Hapus -->
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                data-bs-target="#deleteModal<?= $row['id_pengguna'] ?>">Hapus</button>
                        </td>
                    </tr>
                
                    <!-- Modal Edit -->
                    <div class="modal fade" id="editModal<?= $row['id_pengguna'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="post" action="proses_edit_user.php">
                                <input type="hidden" name="id_pengguna" value="<?= $row['id_pengguna'] ?>">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Pengguna</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-2">
                                            <label>Nama Lengkap</label>
                                            <input type="text" name="nama" class="form-control" value="<?= $row['nama_lengkap'] ?>"
                                                required>
                                        </div>
                                        <div class="mb-2">
                                            <label>Username</label>
                                            <input type="text" name="username" class="form-control" value="<?= $row['username'] ?>"
                                                required>
                                        </div>
                                        <div class="mb-2">
                                            <label>Email</label>
                                            <input type="email" name="email" class="form-control" value="<?= $row['email'] ?>" required>
                                        </div>
                                        <div class="mb-2">
                                            <label>No Telepon</label>
                                            <input type="text" name="no_telp" class="form-control" value="<?= $row['no_telp'] ?>" required>
                                        </div>
                                        <div class="mb-2">
                                            <label>Role</label>
                                            <select name="role" class="form-select" required>
                                                <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                                <option value="siswa" <?= $row['role'] == 'siswa' ? 'selected' : '' ?>>Siswa</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-success" type="submit" name="edit_user">Simpan Perubahan</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                
                    <!-- Modal Hapus -->
                    <div class="modal fade" id="deleteModal<?= $row['id_pengguna'] ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="post" action="proses_hapus_user.php">
                                <input type="hidden" name="id_pengguna" value="<?= $row['id_pengguna'] ?>">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">Hapus Pengguna</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        Yakin ingin menghapus <strong><?= $row['nama_lengkap'] ?></strong>?
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-danger" type="submit" name="hapus_user">Hapus</button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>
        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_pages_siswa; $i++): ?>
                    <li class="page-item <?= ($i == $page_siswa) ? 'active' : '' ?>">
                        <a class="page-link"
                            href="?page_admin=<?= $page_admin ?>&page_siswa=<?= $i ?>&limit=<?= $limit ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
    </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>