<?php
session_start();

// Logout check
if (isset($_GET["logout"])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

// Already logged in check
if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    // Include database connection
        require_once __DIR__ . '/config/db.php'; 

        // 1. Fetch user by email (using MySQLi)
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // 2. Verify password
        if ($user && ($password === $user['password'] || password_verify($password, $user['password']) || md5($password) === $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Expense Tracker</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">
<div class="login-box">
<h1>Student Expense Tracker</h1>
<p class="muted">Login to manage your expenses</p>
<?php if($error): ?>
<div class="alert error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<form method="post">
<label>Email</label>
<input type="email" name="email"
 placeholder="student@example.com" required>
<label>Password</label>
<input type="password" name="password"
 placeholder="12345" required>
<button type="submit">Login</button>
</form>
<p style="margin-top: 15px;">Don't have an account? <a href="register.php">Create Account / Sign Up</a></p>

</div>
</body>
</html>