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
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $cek = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE username='$username' OR email='$email'");
    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['toast'] = ['message' => 'Username / Email sudah digunakan.', 'type' => 'danger'];
    } else {
        mysqli_query($koneksi, "INSERT INTO tb_pengguna (nama_lengkap, username, email, password, role)
                                VALUES ('$nama', '$username', '$email', '$password', '$role')");
        $_SESSION['toast'] = ['message' => 'Pengguna berhasil ditambahkan!', 'type' => 'success'];
    }
    header("Location: kelola_user.php");
    exit;
}

// Ambil data admin dan siswa
$total_admin = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM tb_pengguna WHERE role='admin'"))[0];
$total_pages_admin = ceil($total_admin / $limit);
$data_admin = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE role='admin' ORDER BY nama_lengkap ASC LIMIT $limit OFFSET $offset_admin");

$total_siswa = mysqli_fetch_row(mysqli_query($koneksi, "SELECT COUNT(*) FROM tb_pengguna WHERE role='siswa'"))[0];
$total_pages_siswa = ceil($total_siswa / $limit);
$data_siswa = mysqli_query($koneksi, "SELECT * FROM tb_pengguna WHERE role='siswa' ORDER BY nama_lengkap ASC LIMIT $limit OFFSET $offset_siswa");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Kelola User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
        <form method="post" class="row g-3">
            <div class="col-md-6"><label>Nama Lengkap</label><input type="text" name="nama" class="form-control"
                    required></div>
            <div class="col-md-6"><label>Username</label><input type="text" name="username" class="form-control"
                    required></div>
            <div class="col-md-6"><label>Email</label><input type="email" name="email" class="form-control" required>
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
    <h3>Daftar Admin</h3>
    <form method="get" class="mb-2">
        <label>Tampilkan:</label>
        <select name="limit" onchange="this.form.submit()" class="form-select d-inline-block w-auto ms-2">
            <?php foreach ([5, 10, 15, 20] as $opt): ?>
                <option value="<?= $opt ?>" <?= ($limit == $opt ? 'selected' : '') ?>><?= $opt ?></option>
            <?php endforeach; ?>
        </select>
        <input type="hidden" name="page_siswa" value="<?= $page_siswa ?>">
    </form>
    <div class="table-responsive mb-4">
        <table class="table table-bordered table-striped">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
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
                        <td><?= htmlspecialchars($row['role']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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

    <h3>Daftar Siswa</h3>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-success">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
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
                        <td><?= htmlspecialchars($row['role']) ?></td>
                    </tr>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>