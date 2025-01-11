<?php
session_start();
include 'config.php';

$error = '';
// CSRF protection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('CSRF token validation failed');
    }
}
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Rate limiting
$attempts = $_SESSION['login_attempts'] ?? 0;
$lastAttempt = $_SESSION['last_attempt'] ?? 0;

$isLocked = $attempts > 5 && (time() - $lastAttempt) < 300;
if ($isLocked) {
    $error = 'Too many login attempts. Please try again in 5 minutes.';
    $locked = true;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($locked)) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $_SESSION['login_attempts'] = $attempts + 1;
    $_SESSION['last_attempt'] = time();

    try {
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['logged_in'] = true;
            $_SESSION['login_attempts'] = 0;
            // Set secure cookie flags
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', 1);
            ini_set('session.cookie_samesite', 'Strict');

            // Redirect to the dashboard
            echo "<script>window.location.href='index.php';</script>";
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        $error = 'An unexpected error occurred. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card p-5">
            <h1 class="card-title text-center mb-4">Welcome Back!</h1>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100" <?= isset($locked) ? 'disabled' : '' ?>>Login</button>
            </form>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>

</html>