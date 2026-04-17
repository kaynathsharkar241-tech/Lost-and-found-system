<?php
session_start();
include 'db_connect.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $pass  = $_POST['password'];

    $sql    = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        
        if ($pass === $user['password']) {
            $_SESSION['user_id']   = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];

            header("Location: dashboard.php");
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
        .login-form input {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            box-sizing: border-box;
        }
        .login-form button {
            display: block;
            width: 100%;
            padding: 11px;
            background: #1a2a6c;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
            margin-top: 5px;
        }
        .login-form button:hover { background: #152259; }
    </style>
</head>
<body>
    <div style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">
        <div class="card" style="width: 100%; max-width: 400px; text-align: center; padding: 30px;">

            <h2 style="color: #1a2a6c; margin-bottom: 20px;">FoundIT Login</h2>

            <?php if($error): ?>
                <p style="color: red; margin-bottom: 15px;"><?php echo $error; ?></p>
            <?php endif; ?>

            <form method="POST" class="login-form" style="text-align: left;">

                <label style="font-size: 14px; font-weight: bold; color: #34495e;">Email</label>
                <input type="email" name="email" placeholder="Enter your email" required>

                <label style="font-size: 14px; font-weight: bold; color: #34495e;">Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>

                <button type="submit">Login</button>
            </form>

            <p style="margin-top: 20px; font-size: 14px;">
                New here? <a href="register.php">Create an account</a>
            </p>

        </div>
    </div>
</body>
</html>