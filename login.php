<?php
session_start();  // Start the session

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'user_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = "";  // Initialize error message

// Handle form submission
if (isset($_POST['login'])) {
    $username_email = $_POST['username_email'];
    $password = $_POST['password'];

    // Check if user exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? OR email=?");
    $stmt->bind_param("ss", $username_email, $username_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];  // Store admin status in session

            // Redirect based on admin status
            if ($user['is_admin'] == 1) {
                // If admin, redirect to admin dashboard
                header("Location: admin.php");
            } else {
                // If regular user, redirect to profile page
                header("Location: profile.php");
            }
            exit;
        } else {
            $error_message = "Invalid password.";  // Set error message
        }
    } else {
        $error_message = "User not found.";  // Set error message
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f2f2f2; /* Light background color */
        }
        .login-container {
            text-align: center;
            background-color: white; /* White background for the box */
            padding: 20px;
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            max-width: 400px; /* Limit the width of the login box */
            width: 100%; /* Allow the box to be responsive */
        }
        button {
            padding: 10px 20px;
            margin: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            background-color: #007bff; /* Blue button */
            color: white;
        }
        button:hover {
            opacity: 0.9; /* Slight opacity change on hover */
        }
        .register-link {
            display: block;
            margin-top: 10px;
            color: #007bff; /* Blue color for the link */
            text-decoration: none; /* Remove underline */
        }
        .register-link:hover {
            text-decoration: underline; /* Underline on hover */
        }
        .error-message {
            color: red; /* Text color for the error message */
            text-align: center; /* Center the error message */
            margin-top: 10px; /* Space above the error message */
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <label for="username_email">Username or Email:<br></label>
            <input type="text" name="username_email" required><br><br>

            <label for="password">Password:<br></label>
            <input type="password" name="password" required><br><br>

            <button type="submit" name="login">Login</button>
        </form>
        <?php if ($error_message): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <a class="register-link" href="register.php">Don't have an account? Register here!</a>
    </div>
</body>
</html>
