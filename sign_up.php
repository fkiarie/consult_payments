<?php
session_start();
include 'config.php'; // Include the database configuration file

$error = ''; // Initialize error message
$success = ''; // Initialize success message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate inputs
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        // Check if the username is already taken
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            if ($stmt->fetchColumn() > 0) {
                $error = 'Username is already taken.';
            } else {
                // Hash the password and insert the new user
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)");
                $stmt->execute(['username' => $username, 'password_hash' => $password_hash]);
                $success = 'Account created successfully. You can now <a href="login.php">log in</a>.';
            }
        } catch (PDOException $e) {
            error_log("Error: " . $e->getMessage());
            $error = 'An error occurred while creating your account. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card p-4" style="width: 400px;">
            <h2 class="card-title text-center">Sign Up</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Sign Up</button>
                <div class="text-center mt-3">
                    Already have an account? <a href="login.php">Log In</a>
                </div>
            </form>
        </div>
    </div>

    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
