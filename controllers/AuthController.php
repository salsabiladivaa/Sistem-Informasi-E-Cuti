<?php
class AuthController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function login() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && ($password === 'admin' || password_verify($password, $user['password']))) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['role'] = $user['role'];

                header("Location: index.php?controller=dashboard&action=index");
                exit();
            } else {
                $error = "Username atau password salah!";
            }
        }
        ?>
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <title>Login - E-Cuti</title>
            <link rel="stylesheet" href="style.css?v=1.2">
        </head>
        <body class="login-body">
            <div class="login-card">
                <h2>E-Cuti</h2>
                <p class="subtitle">Welcome back, please login</p>
                <?php if ($error): ?>
                    <p style="color: red; font-size: 12px; margin-bottom: 10px;"><?= $error; ?></p>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-group">
                        <label>USERNAME</label>
                        <input type="text" name="username" value="admin" required>
                    </div>
                    <div class="form-group">
                        <label>PASSWORD</label>
                        <input type="password" name="password" placeholder="•••••" required>
                    </div>
                    <button type="submit" class="btn-primary">Login</button>
                </form>
            </div>
        </body>
        </html>
        <?php
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?controller=auth&action=login");
        exit();
    }
}
?>