<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');  // Redirect to login if not an admin
    exit;
}

// Connect to the database
$conn = new mysqli('localhost', 'root', '', 'user_management');


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle user updates
if (isset($_POST['update_user'])) {
    $id = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    // Check if the password field is not empty
    if (!empty($_POST['password'])) {
        // Hash the new password
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        // Update user with new password
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, is_admin = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssisi", $username, $email, $is_admin, $password, $id);
    } else {
        // Update without changing the password
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, is_admin = ? WHERE id = ?");
        $stmt->bind_param("ssii", $username, $email, $is_admin, $id);
    }

    if ($stmt->execute()) {
        echo "User updated successfully!";
    } else {
        echo "Error updating user: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch all users from the database
$result = $conn->query("SELECT id, username, email, is_admin FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            background-color: white; /* White background for the table */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 80%;
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 90%;
            padding: 5px;
        }
        .update-button {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            background-color: #28a745; /* Green button */
            color: white;
        }
        .update-button:hover {
            opacity: 0.9;
        }
        .logout-button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            background-color: #dc3545; /* Red button for logout */
            color: white;
        }
        .logout-button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Dashboard</h2>

        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Admin</th>
                <th>New Password</th>
                <th>Actions</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <form method="POST" action="admin.php">
                    <td><?php echo $row['id']; ?></td>
                    <td>
                        <input type="text" name="username" value="<?php echo $row['username']; ?>" required>
                    </td>
                    <td>
                        <input type="email" name="email" value="<?php echo $row['email']; ?>" required>
                    </td>
                    <td>
                        <input type="checkbox" name="is_admin" <?php echo $row['is_admin'] ? 'checked' : ''; ?>>
                    </td>
                    <td>
                        <input type="password" name="password" placeholder="Enter new password">
                    </td>
                    <td>
                        <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="update_user" class="update-button">Update</button>
                    </td>
                </form>
            </tr>
            <?php endwhile; ?>
        </table>

        <a href="index.php">
            <button class="logout-button">Logout</button>
        </a>
    </div>
</body>
</html>
