<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login Absensi LPK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Sawarabi+Mincho&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <link rel="icon" type="image/png" href="img/logo.png">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Sawarabi Mincho', serif;
            background: #000000;
            /* ← warna hitam */
            height: 100vh;
            position: relative;
            overflow: hidden;
        }


        #particles-js {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .login-card {
            backdrop-filter: blur(12px);
            background: rgba(0, 0, 0, 0.5);
            /* transparan hitam */
            border-radius: 20px;
            border: 1px solid #444;
            padding: 2rem;
            width: 100%;
            max-width: 360px;
            box-shadow: 0 12px 24px rgba(255, 255, 255, 0.05);
            /* soft putih glow */
            z-index: 1;
            margin: 20px;
        }

        .btn-primary {
            background-color: #c4002f;
            border: none;
            border-radius: 12px;
            font-weight: bold;
        }

        .btn-primary:hover {
            background-color: #a00025;
        }

        h4,
        p {
            color: #ffffff;
            /* teks putih */
            font-weight: bold;
            text-shadow: none;
        }

        small.text-white-50 {
            color: #ffc0cb !important;
            /* pink muda */
            text-shadow: none;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-radius: 12px;
            border: 1px solid #999;
        }

        .form-control::placeholder {
            color: #ccc;
        }


        .logo-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 2px solid #fff;
            margin-bottom: 10px;
        }
    </style>
</head>

<body class="min-vh-100 d-flex flex-column justify-content-center align-items-center">
    <div id="particles-js"></div>

    <?php session_start(); ?>
    <?php if (isset($_SESSION['toast'])): ?>
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999;">
            <div id="liveToast" class="toast align-items-center text-white bg-<?= $_SESSION['toast']['type'] ?> border-0"
                role="alert" data-bs-delay="3000">
                <div class="d-flex">
                    <div class="toast-body">
                        <?= $_SESSION['toast']['message'] ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['toast']); endif; ?>

    <div class="login-card text-center">
        <img src="img/logo.png" alt="Logo LPK" class="logo-img">
        <h4>🌸 Login Absensi</h4>
        <p>LPK Aikoku Terpadu</p>
        <small class="text-white-50 fst-italic d-block mt-2">
            "できるかできないかじゃない。やるかやらないかだ。" - Bukan soal bisa atau tidak, tapi mau atau tidak.
        </small>
        <form method="POST" action="auth/proses_login.php">
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">🔐 Login</button>
        </form>
    </div>

    <!-- Particle.js Config -->
    <script>
        particlesJS("particles-js", {
            particles: {
                number: { value: 40 },
                color: { value: "#ff69b4" },
                shape: {
                    type: "image",
                    image: {
                        src: "img/sakura.png", // pastikan file ada di folder img/
                        width: 30,
                        height: 30
                    }
                },
                opacity: { value: 0.9 },      // lebih terang
                size: { value: 14 },          // disesuaikan dengan gambar
                line_linked: {
                    enable: true,
                    distance: 150,
                    color: "#ff69b4",
                    opacity: 0.8,
                    width: 1.5
                },
                move: {
                    enable: true,
                    speed: 1.2
                }
            },
            interactivity: {
                detect_on: "canvas",
                events: {
                    onhover: { enable: true, mode: "repulse" },
                    onclick: { enable: true, mode: "push" }
                },
                modes: {
                    repulse: { distance: 160, duration: 0.6 },
                    push: { particles_nb: 3 }
                }
            },
            retina_detect: true
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const toastEl = document.getElementById('liveToast');
        if (toastEl) {
            const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
            toast.show();
        }
    </script>
</body>

</html>