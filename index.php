<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chatbot - Home</title>
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
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .container {
            text-align: center;
            padding: 20px;
            max-width: 800px;
            animation: fadeIn 1s ease-in;
        }
        h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #ffcc00;
            text-shadow: 0 0 10px rgba(255, 204, 0, 0.5);
        }
        p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .btn {
            background: #ffcc00;
            color: #1a1a3d;
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(255, 204, 0, 0.4);
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 40px;
        }
        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
            transition: transform 0.3s;
        }
        .feature-card:hover {
            transform: scale(1.05);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 600px) {
            h1 { font-size: 2rem; }
            p { font-size: 1rem; }
            .btn { padding: 10px 20px; font-size: 1rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to AI Chatbot</h1>
        <p>Experience natural conversations with our advanced AI. Talk or type, on any device, anytime. Your personal assistant is just a click away!</p>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="chat.php" class="btn">Start Chatting</a>
        <?php else: ?>
            <a href="#" onclick="redirectTo('signup.php')" class="btn">Get Started</a>
            <a href="#" onclick="redirectTo('login.php')" class="btn">Login</a>
        <?php endif; ?>
        <div class="features">
            <div class="feature-card">
                <h3>Voice Interaction</h3>
                <p>Speak naturally and let our AI respond in real-time.</p>
            </div>
            <div class="feature-card">
                <h3>Text Chat</h3>
                <p>Type your queries for quick and accurate responses.</p>
            </div>
            <div class="feature-card">
                <h3>Multi-Platform</h3>
                <p>Access via web, phone, or mobile apps seamlessly.</p>
            </div>
        </div>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }
    </script>
</body>
</html>
