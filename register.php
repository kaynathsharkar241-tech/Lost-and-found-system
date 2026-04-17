<?php
session_start();
include 'db_connect.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $pass  = $_POST['password']; // plain text to match your login

    // Check if email already exists
    $check = $conn->query("SELECT user_id FROM users WHERE email = '$email'");
    if ($check->num_rows > 0) {
        $message = "<p style='color:red;'>An account with this email already exists!</p>";
    } else {
        $sql = "INSERT INTO users (full_name, email, phone, password, role) 
                VALUES ('$name', '$email', '$phone', '$pass', 'user')";

        if ($conn->query($sql) === TRUE) {
            $message = "<p style='color:green;'>Account created successfully! <a href='login.php'>Login here</a></p>";
        } else {
            $message = "<p style='color:red;'>Error: " . $conn->error . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - FoundIT</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .register-form input {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            box-sizing: border-box;
        }
        .register-form button {
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
        .register-form button:hover { background: #152259; }
    </style>
</head>
<body>
    <div style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">
        <div class="card" style="width: 100%; max-width: 400px; text-align: center; padding: 30px;">

            <h2 style="color: #1a2a6c; margin-bottom: 20px;">Create Account</h2>

            <?php echo $message; ?>

            <form method="POST" class="register-form" style="text-align: left;">

                <label style="font-size: 14px; font-weight: bold; color: #34495e;">Full Name</label>
                <input type="text" name="full_name" placeholder="Enter your full name" required>

                <label style="font-size: 14px; font-weight: bold; color: #34495e;">Email</label>
                <input type="email" name="email" placeholder="Enter your email" required>

                <label style="font-size: 14px; font-weight: bold; color: #34495e;">Phone</label>
                <input type="text" name="phone" placeholder="e.g. 01711111111" required>

                <label style="font-size: 14px; font-weight: bold; color: #34495e;">Password</label>
                <input type="password" name="password" placeholder="Create a password" required>

                <button type="submit">Sign Up</button>
            </form>

            <p style="margin-top: 20px; font-size: 14px;">
                Already have an account? <a href="login.php">Login</a>
            </p>

        </div>
    </div>
</body>
</html>