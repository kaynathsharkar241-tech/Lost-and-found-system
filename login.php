<?php
include 'db_connect.php';
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($pass, $user['password'])) {
            // Save info to session locker
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name']; 
            
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
</head>
<body>
    <div style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">
        <div class="card" style="width: 100%; max-width: 400px; text-align: center;">
            <h2 style="color: #1a2a6c;">FoundIT Login</h2>
            <?php if($error) echo "<p style='color:red;'>$error</p>"; ?>
            <form method="POST">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class="btn btn-primary" style="width:100%; margin-top:10px;">Login</button>
            </form>
            <p style="margin-top:20px; font-size: 14px;">New here? <a href="register.php">Create an account</a></p>
        </div>
    </div>
</body>
</html>
