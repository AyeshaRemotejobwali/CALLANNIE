<?php
session_start();
include 'db.php';

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validate input
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $email = isset($_POST['user_email']) ? trim($_POST['user_email']) : '';
        $password = isset($_POST['user_password']) ? trim($_POST['user_password']) : '';

        if (empty($username) || empty($email) || empty($password)) {
            $error = "All fields are required!";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format!";
        } else {
            // Check if email exists
            $stmt = $conn->prepare("SELECT id FROM users_credentials WHERE email = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->errorInfo()[2]);
            }
            $stmt->bindValue(1, $email, PDO::PARAM_STR);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . implode(", ", $stmt->errorInfo()));
            }
            if ($stmt->fetch()) {
                $error = "Email already exists!";
            } else {
                // Insert new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert_stmt = $conn->prepare("INSERT INTO users_credentials (username, email, password) VALUES (?, ?, ?)");
                if (!$insert_stmt) {
                    throw new Exception("Prepare failed: " . $conn->errorInfo()[2]);
                }
                $insert_stmt->bindValue(1, $username, PDO::PARAM_STR);
                $insert_stmt->bindValue(2, $email, PDO::PARAM_STR);
                $insert_stmt->bindValue(3, $hashed_password, PDO::PARAM_STR);
                if ($insert_stmt->execute()) {
                    $_SESSION['user_id'] = $conn->lastInsertId();
                    header("Location: chat.php");
                    exit();
                } else {
                    throw new Exception("Insert failed: " . implode(", ", $insert_stmt->errorInfo()));
                }
            }
            $stmt->closeCursor();
        }
    } catch (Exception $e) {
        // Log error to file (not displayed to user)
        error_log("Signup error: " . $e->getMessage(), 3, "errors.log");
        $error = "Signup failed! Please try again later.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #1a1a3d, #3b1e66);
            color: #fff;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .form-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            width: 100%;
            max-width: 400px;
            text-align: center;
            animation: fadeIn 0.5s ease-in;
        }
        h2 {
            color: #ffcc00;
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.8);
            color: #1a1a3d;
        }
        button {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 50px;
            background: #ffcc00;
            color: #1a1a3d;
            cursor: pointer;
            font-size: 1.1rem;
            transition: transform 0.3s;
        }
        button:hover {
            transform: scale(1.1);
        }
        .error {
            color: #ff4444;
            margin-bottom: 10px;
        }
        a {
            color: #ffcc00;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @media (max-width: 600px) {
            .form-container { margin: 10px; padding: 10px; }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Sign Up</h2>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
            <input type="email" name="user_email" placeholder="Email" value="<?php echo isset($_POST['user_email']) ? htmlspecialchars($_POST['user_email']) : ''; ?>" required>
            <input type="password" name="user_password" placeholder="Password" required>
            <button type="submit">Sign Up</button>
        </form>
        <p>Already have an account? <a href="#" onclick="redirectTo('login.php')">Login</a></p>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
