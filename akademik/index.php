<?php
// index.php (Login Page)
session_start();
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirectDashboard();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role_input = $_POST['role'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username/NIM dan password tidak boleh kosong.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND hakakses = ? LIMIT 1");
        $stmt->bind_param("ss", $username, $role_input);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['nama']      = $user['nama'];
            $_SESSION['hakakses']  = $user['hakakses'];
            $_SESSION['nim']       = $user['nim'] ?? null;
            redirectDashboard();
        } else {
            $error = 'Username/NIM, password, atau role tidak sesuai.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistem Akademik</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2563EB;
            --primary-dark: #1D4ED8;
            --font-main: 'Plus Jakarta Sans', sans-serif;
            --font-mono: 'DM Mono', monospace;
        }
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: var(--font-main);
            min-height: 100vh;
            display: flex;
            background: #F1F5F9;
        }

        /* Left panel */
        .login-panel-left {
            width: 45%;
            background: linear-gradient(135deg, #0F172A 0%, #1E3A8A 60%, #2563EB 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px 50px;
            position: relative;
            overflow: hidden;
        }
        .login-panel-left::before {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: rgba(255,255,255,.04);
            top: -100px; right: -100px;
        }
        .login-panel-left::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(255,255,255,.04);
            bottom: -80px; left: -80px;
        }

        .login-logo {
            width: 72px; height: 72px;
            background: rgba(255,255,255,.15);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            font-size: 36px; color: #fff;
            margin-bottom: 28px;
            backdrop-filter: blur(10px);
        }
        .login-headline {
            color: #fff;
            font-size: 32px; font-weight: 800;
            text-align: center; line-height: 1.2;
            margin-bottom: 16px;
        }
        .login-sub {
            color: rgba(255,255,255,.65);
            font-size: 15px;
            text-align: center;
            line-height: 1.6;
        }

        /* Decorative dots */
        .dots-grid {
            position: absolute;
            bottom: 40px; right: 30px;
            display: grid;
            grid-template-columns: repeat(5, 8px);
            gap: 8px;
            opacity: .2;
        }
        .dots-grid span {
            width: 4px; height: 4px;
            background: #fff;
            border-radius: 50%;
            display: block;
        }

        /* Right panel */
        .login-panel-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 60px;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
        }
        .login-box h2 {
            font-size: 26px; font-weight: 800;
            margin-bottom: 6px;
        }
        .login-box .subtitle {
            font-size: 14px; color: #64748B;
            margin-bottom: 32px;
        }

        /* Role tabs */
        .role-tabs {
            display: flex;
            background: #E2E8F0;
            border-radius: 10px;
            padding: 4px;
            margin-bottom: 24px;
            gap: 4px;
        }
        .role-tab {
            flex: 1;
            border: none;
            background: transparent;
            border-radius: 8px;
            padding: 9px 8px;
            font-family: var(--font-main);
            font-size: 13px;
            font-weight: 600;
            color: #64748B;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all .2s;
        }
        .role-tab.active {
            background: #fff;
            color: var(--primary);
            box-shadow: 0 1px 4px rgba(0,0,0,.12);
        }

        /* Form */
        .form-group { margin-bottom: 18px; }
        .form-label {
            font-size: 13px; font-weight: 600;
            color: #374151; display: block;
            margin-bottom: 6px;
        }
        .input-wrap {
            position: relative;
        }
        .input-wrap .icon {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            font-size: 17px; color: #9CA3AF;
        }
        .form-control {
            border: 1.5px solid #E2E8F0;
            border-radius: 10px;
            padding: 11px 14px 11px 42px;
            font-family: var(--font-main);
            font-size: 14px;
            width: 100%;
            transition: border .2s, box-shadow .2s;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37,99,235,.12);
        }
        .toggle-pw {
            position: absolute;
            right: 14px; top: 50%;
            transform: translateY(-50%);
            cursor: pointer; color: #9CA3AF;
            font-size: 17px;
            background: none; border: none; padding: 0;
        }

        .btn-login {
            width: 100%;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-family: var(--font-main);
            font-size: 15px; font-weight: 700;
            cursor: pointer;
            transition: background .2s, transform .1s;
            margin-top: 4px;
        }
        .btn-login:hover { background: var(--primary-dark); }
        .btn-login:active { transform: scale(.98); }

        .alert-error {
            background: #FEE2E2;
            border: 1px solid #FCA5A5;
            color: #991B1B;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex; align-items: center; gap: 8px;
        }

        .login-hint {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            padding: 14px 16px;
            margin-top: 24px;
            font-size: 12px; color: #64748B;
        }
        .login-hint strong { color: #0F172A; }
        .login-hint code {
            font-family: var(--font-mono);
            background: #E2E8F0;
            padding: 1px 5px;
            border-radius: 4px;
            font-size: 11px;
        }

        @media (max-width: 768px) {
            .login-panel-left { display: none; }
            .login-panel-right { padding: 30px 24px; }
        }
    </style>
</head>
<body>

<div class="login-panel-left">
    <div class="login-logo"><i class="bi bi-mortarboard-fill"></i></div>
    <h1 class="login-headline">Sistem Informasi Akademik</h1>
    <p class="login-sub">Platform pengelolaan nilai dan data akademik mahasiswa yang terintegrasi.</p>
    <div class="dots-grid">
        <?php for ($i = 0; $i < 25; $i++): ?><span></span><?php endfor; ?>
    </div>
</div>

<div class="login-panel-right">
    <div class="login-box">
        <h2>Selamat Datang 👋</h2>
        <p class="subtitle">Pilih role dan masukkan kredensial Anda</p>

        <?php if ($error): ?>
        <div class="alert-error">
            <i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <!-- Role selector -->
            <div class="role-tabs" id="roleTabs">
                <button type="button" class="role-tab active" data-role="admin">
                    <i class="bi bi-shield-fill"></i> Admin
                </button>
                <button type="button" class="role-tab" data-role="dosen">
                    <i class="bi bi-person-badge-fill"></i> Dosen
                </button>
                <button type="button" class="role-tab" data-role="mahasiswa">
                    <i class="bi bi-person-fill"></i> Mahasiswa
                </button>
            </div>
            <input type="hidden" name="role" id="roleInput" value="admin">

            <div class="form-group">
                <label class="form-label" id="usernameLabel">Username</label>
                <div class="input-wrap">
                    <i class="bi bi-person icon"></i>
                    <input type="text" name="username" id="usernameField"
                        class="form-control"
                        placeholder="Masukkan username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        required autocomplete="username">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="input-wrap">
                    <i class="bi bi-lock icon"></i>
                    <input type="password" name="password" id="passwordField"
                        class="form-control"
                        placeholder="Masukkan password"
                        required autocomplete="current-password">
                    <button type="button" class="toggle-pw" id="togglePw">
                        <i class="bi bi-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
            </button>
        </form>

        <div class="login-hint">
            <strong>Demo akun (password: <code>password</code>)</strong><br><br>
            🛡️ Admin &nbsp;&nbsp;&nbsp;: <code>admin</code><br>
            👨‍🏫 Dosen &nbsp;&nbsp;: <code>dosen01</code><br>
            🎓 Mahasiswa: <code>22001001</code> atau <code>22001002</code>
        </div>
    </div>
</div>

<script>
    // Role tab switcher
    const tabs = document.querySelectorAll('.role-tab');
    const roleInput = document.getElementById('roleInput');
    const usernameLabel = document.getElementById('usernameLabel');
    const usernamePlaceholder = document.getElementById('usernameField');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            const role = tab.dataset.role;
            roleInput.value = role;
            if (role === 'mahasiswa') {
                usernameLabel.textContent = 'NIM Mahasiswa';
                usernamePlaceholder.placeholder = 'Masukkan NIM';
            } else {
                usernameLabel.textContent = 'Username';
                usernamePlaceholder.placeholder = 'Masukkan username';
            }
        });
    });

    // Toggle password visibility
    document.getElementById('togglePw').addEventListener('click', function () {
        const pw = document.getElementById('passwordField');
        const icon = document.getElementById('eyeIcon');
        if (pw.type === 'password') {
            pw.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            pw.type = 'password';
            icon.className = 'bi bi-eye';
        }
    });
</script>
</body>
</html>
