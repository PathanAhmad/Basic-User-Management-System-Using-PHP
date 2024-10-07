<?php
session_start(); // Start the session

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Unset all of the session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();
}

// After logout, show the logout state page
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management System</title>
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
        .container {
            text-align: center;
            background-color: white; /* White background for buttons */
            padding: 20px;
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }
        button {
            padding: 10px 20px;
            margin: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .login-button {
            background-color: #007bff; /* Blue color */
            color: white;
        }
        .register-button {
            background-color: #28a745; /* Green color */
            color: white;
        }
        button:hover {
            opacity: 0.9; /* Slight opacity change on hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>You have successfully logged out.</h2>
        <p>Please register or log in to continue.</p>
        <a href="register.php"><button class="register-button">Register</button></a>
        <a href="login.php"><button class="login-button">Login</button></a>
    </div>
</body>
</html>
