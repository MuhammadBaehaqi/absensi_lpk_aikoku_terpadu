<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            margin: 0;
            padding: 0;
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
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
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

        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            font-weight: bold;
            border-radius: 5px;
        }

        .topbar {
            position: fixed;
            top: 0;
            left: 250px;
            right: 0;
            height: 60px;
            background-color: #343a40;
            color: white;
            z-index: 1040;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .main-content {
            margin-left: 250px;
            padding: 70px 20px 20px;
        }

        @media (max-width: 991.98px) {
            .main-content {
                margin-left: 0;
            }
        }

        body {
            overflow-x: hidden;
        }

        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            transform: translateX(0);
            transition: transform 0.3s ease-in-out;
            z-index: 1050;
            padding-top: 60px;
        }

        .sidebar a {
            padding: 12px 20px;
            display: block;
            color: #fff;
            text-decoration: none;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #495057;
        }

        .sidebar-header {
            background-color: #212529;
        }

        .sidebar.hidden {
            transform: translateX(-100%);
        }

        .topbar {
            position: fixed;
            top: 0;
            left: 250px;
            right: 0;
            height: 60px;
            background-color: #212529;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            z-index: 1000;
            transition: left 0.3s ease-in-out;
        }

        .topbar.sidebar-hidden {
            left: 0;
        }

        .content {
            margin-left: 250px;
            padding: 80px 20px 20px;
            transition: margin-left 0.3s ease-in-out;
        }

        .content.sidebar-hidden {
            margin-left: 0;
        }

        /* Dark mode */
        .dark-mode {
            background-color: #121212;
            color: white;
        }

        .dark-mode .card {
            background-color: #1f1f1f;
            color: white;
        }

        .dark-mode .sidebar {
            background-color: #1a1a1a;
        }

        .dark-mode .topbar {
            background-color: #000;
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
                <a href="../admin/dashboard_admin.php" class="nav-link text-white fw-bold">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="../admin/kelola_user.php" class="nav-link text-white">
                    <i class="bi bi-people me-2"></i>Kelola User
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="../absensi/input_manual.php" class="nav-link text-white">
                    <i class="bi bi-pencil-square me-2"></i>Input Absensi Manual
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="../admin/verifikasi_izin.php" class="nav-link text-white">
                    <i class="bi bi-check-circle me-2"></i>Verifikasi Izin/Sakit
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="../absensi/data_absensi.php" class="nav-link text-white">
                    <i class="bi bi-table me-2"></i>Lihat Data Absensi
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="../absensi/rekap_bulanan.php" class="nav-link text-white">
                    <i class="bi bi-bar-chart me-2"></i>Rekap Bulanan Siswa
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="../absensi/rekap_bulanan_semua.php" class="nav-link text-white">
                    <i class="bi bi-bar-chart-line me-2"></i>Rekap Semua Siswa Bulanan
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="../absensi/rekap_mingguan.php" class="nav-link text-white">
                    <i class="bi bi-calendar-week me-2"></i>Rekap Mingguan Siswa
                </a>
            </li>
            <li class="nav-item mb-2">
                <a href="../absensi/rekap_mingguan_semua.php" class="nav-link text-white">
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
        <strong>Dashboard Admin</strong>
        <div class="d-flex align-items-center gap-3">
            <span><i class="bi bi-person-circle"></i> <?= $_SESSION['username']; ?> (<?= $_SESSION['role']; ?>)</span>
            <button id="toggleMode" class="btn btn-outline-light btn-sm">🌙</button>
        </div>
    </div>

    <!-- JS -->
    <script>
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

        // Tutup sidebar setelah klik link (di layar kecil)
        document.querySelectorAll('#sidebar .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    sidebar.classList.remove('show');
                    overlay.style.display = 'none';
                }
            });
        });

        // Highlight link aktif
        document.querySelectorAll('#sidebar .nav-link').forEach(link => {
            if (link.href === window.location.href) {
                link.classList.add('active');
            }
        });
    </script>
    <script>
        // Tombol toggle
        const toggleModeBtn = document.getElementById('toggleMode');
        const body = document.body;

        // Aktifkan dark mode jika sebelumnya sudah disimpan di localStorage
        if (localStorage.getItem('theme') === 'dark') {
            body.classList.add('dark-mode');
        }

        toggleModeBtn?.addEventListener('click', () => {
            body.classList.toggle('dark-mode');
            // Simpan preferensi
            if (body.classList.contains('dark-mode')) {
                localStorage.setItem('theme', 'dark');
            } else {
                localStorage.setItem('theme', 'light');
            }
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>