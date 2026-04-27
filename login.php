<?php
session_start();
include 'db_connect.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input to prevent SQL injection
    $email = $conn->real_escape_string($_POST['email']);
    $pass  = $_POST['password'];

    // Select the user from the database
    $sql    = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Check password (matching plain text as per your current database setup)
        if ($pass === $user['password']) {
            // Store user details in the session
            $_SESSION['user_id']   = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role']; // This will be 'admin' or 'user'

            // --- ROLE-BASED REDIRECT ---
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "No user found with that email!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - FoundIT</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .login-container {
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh;
        }
        .login-form input {
            display: block;
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            box-sizing: border-box;
        }
        .login-form button {
            display: block;
            width: 100%;
            padding: 12px;
            background: #1a2a6c;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }
        .login-form button:hover { 
            background: #b21f1f; 
        }
        .error-msg {
            color: #e74c3c;
            background: #fadbd8;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="card" style="width: 100%; max-width: 400px; padding: 40px;">

            <h2 style="color: #1a2a6c; text-align: center; margin-bottom: 25px;">FoundIT Login</h2>

            <?php if($error): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="login-form">

                <label style="font-size: 14px; font-weight: bold; color: #34495e;">Email Address</label>
                <input type="email" name="email" placeholder="e.g. rahim@gmail.com" required>

                <label style="font-size: 14px; font-weight: bold; color: #34495e;">Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>

                <button type="submit">Login to Account</button>
            </form>

            <p style="margin-top: 25px; text-align: center; font-size: 14px;">
                New to FoundIT? <a href="register.php" style="color: #1a2a6c; font-weight: bold;">Create an account</a>
            </p>

        </div>
    </div>
</body>
</html>