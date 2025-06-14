<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['user_email'];
    $password = $_POST['user_password'];

    $stmt = $conn->prepare("SELECT id, password FROM users_credentials WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            header("Location: chat.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Email not found!";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
            padding: 20px;
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
        <h2>Login</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="email" name="user_email" placeholder="Email" required>
            <input type="password" name="user_password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="#" onclick="redirectTo('signup.php')">Sign Up</a></p>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
