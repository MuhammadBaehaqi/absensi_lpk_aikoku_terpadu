<?php
$current = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

$pageTitles = [
    'dashboard_admin.php' => 'Dashboard Admin',
    'kelola_user.php' => 'Kelola User',
    'input_manual.php' => 'Input Absensi Manual',
    'verifikasi_izin.php' => 'Verifikasi Izin/Sakit',
    'data_absensi.php' => 'Lihat Data Absensi',
    'rekap_bulanan.php' => 'Rekap Bulanan Siswa',
    'rekap_bulanan_semua.php' => 'Rekap Semua Siswa Bulanan',
    'rekap_mingguan.php' => 'Rekap Mingguan Siswa',
    'rekap_mingguan_semua.php' => 'Rekap Mingguan Semua',
];

$judulHalaman = $pageTitles[$current] ?? 'Halaman Admin';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $judulHalaman ?></title>

    <!-- Aktifkan dark mode sebelum halaman render -->
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark-mode');
            document.body?.classList?.add('dark-mode');
        }
    </script>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .sidebar {
            height: 100vh;
            overflow-y: auto;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            background-color: #212529;
            transform: translateX(0);
            transition: transform 0.3s ease;
            z-index: 1030;
            padding-top: 60px;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .topbar {
                left: 0 !important;
            }

            .content {
                margin-left: 0 !important;
            }

            .overlay {
                display: block;
            }
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1025;
        }

        .sidebar-logo {
            width: 40px;
            height: 40px;
            object-fit: contain;
            margin-right: 10px;
        }

        .sidebar-brand {
            font-size: 1rem;
            font-weight: bold;
            margin-bottom: 0.25rem;
            letter-spacing: 0.5px;
        }

        .nav-link {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: normal;
        }

        /* Aktif link di sidebar pada dark mode */
        .dark-mode .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: #fff;
            font-weight: 600;
            border-radius: 5px;
        }

        .topbar {
            position: fixed;
            top: 0;
            left: 250px;
            right: 0;
            height: 60px;
            background-color: #212529;
            color: white;
            z-index: 1040;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: left 0.3s ease-in-out;
        }

        .content {
            margin-left: 250px;
            padding: 80px 20px 20px;
            transition: margin-left 0.3s ease-in-out;
        }

        /* Dark Mode Fixes */
        .dark-mode {
            background-color: #121212;
            color: white;
        }

        /* Dark mode: samakan warna topbar & sidebar */
        .dark-mode .topbar,
        .dark-mode .sidebar {
            background-color: #000 !important;
            color: #fff;
        }

        .dark-mode .bg-primary {
            background-color: #0d6efd !important;
            color: #fff !important;
        }

        .dark-mode .bg-success {
            background-color: #198754 !important;
            color: #fff !important;
        }

        .dark-mode .bg-warning {
            background-color: #ffc107 !important;
            color: #000 !important;
        }

        .dark-mode .bg-info {
            background-color: #0dcaf0 !important;
            color: #000 !important;
        }

        .dark-mode .bg-danger {
            background-color: #dc3545 !important;
            color: #fff !important;
        }

        .dark-mode .bg-secondary {
            background-color: #6c757d !important;
            color: #fff !important;
        }

        .dark-mode .bg-dark {
            background-color: #212529 !important;
            color: #fff !important;
        }

        /* Fallback background jika card kosong */
        .card {
            background-color: white;
            transition: background-color 0.2s ease-in-out;
        }

        /* ====== Sidebar: warna dasar link (menimpa .text-white Bootstrap) ====== */
        .sidebar .nav-link.text-white {
            color: #e9ecef !important;
            /* mode terang: abu muda */
        }

        .dark-mode .sidebar .nav-link.text-white {
            color: #f1f3f5 !important;
            /* mode gelap: sedikit lebih terang */
        }

        /* Hover & fokus (aksesibilitas) */
        .sidebar .nav-link:hover,
        .sidebar .nav-link:focus-visible {
            background-color: rgba(255, 255, 255, 0.08);
            color: #fff !important;
            outline: none;
        }

        /* ====== AKTIF (mode terang / default) ====== */
        /* Sidebar kamu di mode terang pakai bg #212529 (gelap), jadi aktif dibuat kontras */
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.15);
            color: #fff !important;
            font-weight: 600;
            border-radius: 6px;
            position: relative;
        }

        .sidebar .nav-link.active::before {
            content: "";
            position: absolute;
            left: 0;
            top: 8px;
            bottom: 8px;
            width: 3px;
            background: #0d6efd;
            /* indikator biru Bootstrap */
            border-radius: 3px;
        }

        /* ====== AKTIF (mode gelap) ====== */
        .dark-mode .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.18);
            color: #fff !important;
        }

        .dark-mode .sidebar .nav-link.active::before {
            background: #0d6efd;
        }

        /* Opsional: ikon sedikit lebih jelas saat aktif */
        .sidebar .nav-link i {
            opacity: .85;
            transition: opacity .2s ease;
        }

        .sidebar .nav-link.active i {
            opacity: 1;
        }
    </style>
</head>

<body>

    <!-- Overlay -->
    <div id="sidebarOverlay" class="overlay"></div>

    <!-- Sidebar -->
    <div id="sidebar" class="sidebar text-white p-3">
        <div class="d-flex align-items-center mb-3">
            <img src="../img/logo.png" alt="Logo" class="sidebar-logo rounded-circle">
            <div class="sidebar-brand">LPK AIKOKU TERPADU</div>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item mb-2">
                <a href="../admin/dashboard_admin.php"
                    class="nav-link text-white <?= $current === 'dashboard_admin.php' ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="../admin/kelola_user.php"
                    class="nav-link text-white <?= $current === 'kelola_user.php' ? 'active' : '' ?>">
                    <i class="bi bi-people me-2"></i>Kelola User
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="../absensi/input_manual.php"
                    class="nav-link text-white <?= $current === 'input_manual.php' ? 'active' : '' ?>">
                    <i class="bi bi-pencil-square me-2"></i>Input Absensi Manual
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="../admin/verifikasi_koreksi.php"
                    class="nav-link text-white <?= $current === 'verifikasi_koreksi.php' ? 'active' : '' ?>">
                    <i class="bi bi-shield-check me-2"></i>Verifikasi Koreksi Absensi
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="../admin/verifikasi_izin.php"
                    class="nav-link text-white <?= $current === 'verifikasi_izin.php' ? 'active' : '' ?>">
                    <i class="bi bi-check-circle me-2"></i>Verifikasi Izin/Sakit
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="../absensi/data_absensi.php"
                    class="nav-link text-white <?= $current === 'data_absensi.php' ? 'active' : '' ?>">
                    <i class="bi bi-table me-2"></i>Lihat Data Absensi
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="../absensi/rekap_bulanan.php"
                    class="nav-link text-white <?= $current === 'rekap_bulanan.php' ? 'active' : '' ?>">
                    <i class="bi bi-bar-chart me-2"></i>Rekap Bulanan Siswa
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="../absensi/rekap_bulanan_semua.php"
                    class="nav-link text-white <?= $current === 'rekap_bulanan_semua.php' ? 'active' : '' ?>">
                    <i class="bi bi-bar-chart-line me-2"></i>Rekap Semua Siswa<br><span class="ms-4">Bulanan</span>
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="../absensi/rekap_mingguan.php"
                    class="nav-link text-white <?= $current === 'rekap_mingguan.php' ? 'active' : '' ?>">
                    <i class="bi bi-calendar-week me-2"></i>Rekap Mingguan Siswa
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="../absensi/rekap_mingguan_semua.php"
                    class="nav-link text-white <?= $current === 'rekap_mingguan_semua.php' ? 'active' : '' ?>">
                    <i class="bi bi-calendar-range me-2"></i>Rekap Mingguan Semua
                </a>
            </li>
            <li class="nav-item mt-3 border-top pt-3">
                <a href="../auth/logout.php" class="nav-link text-danger">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- Topbar -->
    <div class="topbar">
        <button id="openSidebar" class="btn btn-outline-light btn-sm d-lg-none"><i class="bi bi-list"></i></button>
        <strong><?= $judulHalaman ?></strong>
        <div class="d-flex align-items-center gap-3">
            <span><i class="bi bi-person-circle"></i> <?= $_SESSION['username']; ?> (<?= $_SESSION['role']; ?>)</span>
            <button id="toggleMode" class="btn btn-outline-light btn-sm">🌙</button>
        </div>
    </div>

    <!-- Sidebar Script -->
    <script>
        // Sidebar dan dark mode toggle
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const openBtn = document.getElementById('openSidebar');

        openBtn?.addEventListener('click', () => {
            sidebar.classList.add('show');
            overlay.style.display = 'block';
        });

        overlay?.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.style.display = 'none';
        });

        document.querySelectorAll('#sidebar .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    sidebar.classList.remove('show');
                    overlay.style.display = 'none';
                }
            });
        });

        // Dark mode toggle
        const toggleModeBtn = document.getElementById('toggleMode');
        toggleModeBtn?.addEventListener('click', () => {
            document.documentElement.classList.toggle('dark-mode');
            document.body?.classList?.toggle('dark-mode');
            const mode = document.documentElement.classList.contains('dark-mode') ? 'dark' : 'light';
            localStorage.setItem('theme', mode);
        });

        // Fallback: aktifkan ulang dark mode kalau belum ke-set di body
        document.addEventListener('DOMContentLoaded', () => {
            const theme = localStorage.getItem('theme');
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-mode');
                document.body?.classList?.add('dark-mode');
            }
        });
    </script>


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>