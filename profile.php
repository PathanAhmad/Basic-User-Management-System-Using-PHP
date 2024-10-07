<?php
session_start();  // Start session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');  // Redirect to login if not logged in
    exit;
}

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'user_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Initialize update messages
$update_message = "";
$password_update_message = "";

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $new_username = $_POST['username'];
        $new_email = $_POST['email'];

        // Update user info in the database
        $stmt = $conn->prepare("UPDATE users SET username=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $new_username, $new_email, $user_id);

        if ($stmt->execute()) {
            $update_message = "Profile updated successfully!";
            // Update session username if changed
            $_SESSION['username'] = $new_username;
            // Refresh user details
            $user['username'] = $new_username;
            $user['email'] = $new_email;
        } else {
            $update_message = "Error updating profile.";
        }
        $stmt->close();
    }

    // Handle password update
    if (isset($_POST['update_password'])) {
        $new_password = $_POST['password'];
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in the database
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("si", $hashed_password, $user_id);

        if ($stmt->execute()) {
            $password_update_message = "Password updated successfully!";
        } else {
            $password_update_message = "Error updating password.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f2f2f2; /* Light background color */
        }
        .container {
            text-align: center;
            background-color: white; /* White background for the container */
            padding: 40px;
            border-radius: 10px; /* Rounded corners */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            width: 300px; /* Fixed width for a neat look */
        }
        h2 {
            margin-bottom: 20px; /* Space between heading and content */
        }
        p {
            margin: 10px 0; /* Space between paragraphs */
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%; /* Full width for inputs */
            padding: 10px; /* Padding inside the input */
            margin: 5px 0 15px; /* Spacing between inputs */
            border: 1px solid #ccc; /* Border for the input */
            border-radius: 5px; /* Rounded corners for the input */
        }
        button {
            padding: 10px 20px;
            margin-top: 10px; /* Space above the button */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #007bff; /* Blue color */
            color: white;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
        a {
            display: inline-block;
            margin-top: 20px; /* Space above the link */
            padding: 10px 20px;
            text-decoration: none;
            background-color: #007bff; /* Blue color */
            color: white;
            border-radius: 5px; /* Rounded corners for the button */
            transition: background-color 0.3s;
        }
        a:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
        .message {
            margin: 10px 0;
            color: green; /* Color for success message */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
        <p>Your email: <?php echo htmlspecialchars($user['email']); ?></p>
        <p>Relax and enjoy your time here!</p>

        <!-- Update Profile Form -->
        <form action="profile.php" method="POST">
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required placeholder="New Username">
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required placeholder="New Email">
            <button type="submit" name="update_profile">Update Profile</button>
        </form>

        <?php if ($update_message): ?>
            <p class="message"><?php echo $update_message; ?></p>
        <?php endif; ?>

        <!-- Update Password Form -->
        <form action="profile.php" method="POST">
            <input type="password" name="password" required placeholder="New Password">
            <button type="submit" name="update_password">Update Password</button>
        </form>

        <?php if ($password_update_message): ?>
            <p class="message"><?php echo $password_update_message; ?></p>
        <?php endif; ?>

        <a href="index.php">Logout</a> <!-- Logout link -->
    </div>
</body>
</html>
