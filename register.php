<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure way to save passwords

    $sql = "INSERT INTO users (full_name, email, password) VALUES ('$name', '$email', '$pass')";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>Account created! <a href='login.php'>Login here</a></p>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<?php
include 'db_connect.php';

$message = ""; // To store success/error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Only runs when the button is clicked
    $name = $_POST['full_name'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (full_name, email, password) VALUES ('$name', '$email', '$pass')";

    if ($conn->query($sql) === TRUE) {
        $message = "<p style='color:green;'>Account created! <a href='login.php'>Login here</a></p>";
    } else {
        $message = "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - FoundIT</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">
        <div class="card" style="width: 100%; max-width: 400px; text-align: center;">
            <h2 style="color: #1a2a6c;">Create Account</h2>
            <?php echo $message; ?>
            <form method="POST">
                <input type="text" name="full_name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class="btn btn-primary" style="width:100%; margin-top:10px;">Sign Up</button>
            </form>
            <p style="margin-top:20px; font-size: 14px;">Already have an account? <a href="login.php">Login</a></p>
        </div>
    </div>
