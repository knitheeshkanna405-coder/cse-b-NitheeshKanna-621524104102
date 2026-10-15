<?php
session_start();

// 1. Database Connection
require_once __DIR__ . '/config/db.php';

// Verify database connection
if (!isset($conn) || !$conn) {
    die("Database connection error: " . mysqli_connect_error());
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username  = trim($_POST["username"] ?? "");
    $full_name = trim($_POST["full_name"] ?? "");
    $email     = trim($_POST["email"] ?? "");
    $password  = $_POST["password"] ?? "";

    if (!empty($username) && !empty($email) && !empty($password)) {
        
        // 2. Check if Email or Username already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        if ($check) {
            $check->bind_param("ss", $email, $username);
            $check->execute();
            $check_result = $check->get_result();

            if ($check_result && $check_result->num_rows > 0) {
                $error = "This username or email is already registered!";
            } else {
                // 3. Insert new user
                $stmt = $conn->prepare("INSERT INTO users (username, full_name, email, password) VALUES (?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("ssss", $username, $full_name, $email, $password);
                    if ($stmt->execute()) {
                        $success = "Registration successful! <a href='login.php' style='color: #00e5ff; font-weight: bold;'>Click here to login</a>";
                    } else {
                        $error = "Registration failed: " . $stmt->error;
                    }
                } else {
                    $error = "SQL query error: " . $conn->error;
                }
            }
        } else {
            $error = "Database query error: " . $conn->error;
        }

    } else {
        $error = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Student Expense Tracker</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">
    <div class="login-box">
        <h1>Create Account</h1>
        <p class="muted">Enter your details to register</p>

        <?php if (!empty($error)): ?>
            <div class="alert error" style="color: #ff6b6b; background: rgba(255,0,0,0.1); padding: 10px; border-radius: 5px; margin-bottom: 12px; font-size: 14px;">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert success" style="color: #51cf66; background: rgba(0,255,0,0.1); padding: 10px; border-radius: 5px; margin-bottom: 12px; font-size: 14px;">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <form method="post" action="register.php">
            <label>Full Name</label>
            <input type="text" name="full_name" placeholder="Enter your full name" required>

            <label>Username (User ID)</label>
            <input type="text" name="username" placeholder="e.g., nitheesh01" required>

            <label>Email Address</label>
            <input type="email" name="email" placeholder="example@mail.com" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Create a password" required>

            <button type="submit" style="margin-top: 15px;">Sign Up</button>
        </form>

        <p style="margin-top: 15px; font-size: 14px;">
            Already have an account? <a href="login.php" style="color: #00e5ff;">Login here</a>
        </p>
    </div>
</body>
</html>