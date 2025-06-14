<?php
session_start();

// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    include 'db.php';
} catch (Exception $e) {
    error_log("Database connection failed: " . $e->getMessage(), 3, "errors.log");
    die("Error: Unable to connect to the database. Please try again later.");
}

// Fetch chat history
try {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT message, is_user, created_at FROM chat_history WHERE user_id = ? ORDER BY created_at ASC");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->errorInfo()[2]);
    }
    $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . implode(", ", $stmt->errorInfo()));
    }
    $chat_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Chat history query failed: " . $e->getMessage(), 3, "errors.log");
    $chat_history = [];
    $error = "Failed to load chat history. Please try again.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with AI</title>
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
        .chat-container {
            width: 100%;
            max-width: 600px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            animation: fadeIn 0.5s ease-in;
        }
        .chat-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .chat-header h2 {
            color: #ffcc00;
            text-shadow: 0 0 5px rgba(255, 204, 0, 0.5);
        }
        .chat-box {
            height: 400px;
            overflow-y: auto;
            padding: 10px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .message {
            margin: 10px 0;
            padding: 10px;
            border-radius: 10px;
            max-width: 80%;
        }
        .user-message {
            background: #ffcc00;
            color: #1a1a3d;
            margin-left: auto;
        }
        .ai-message {
            background: #fff;
            color: #1a1a3d;
        }
        .error {
            color: #ff4444;
            text-align: center;
            margin-bottom: 10px;
        }
        .input-area {
            display: flex;
            gap: 10px;
        }
        input[type="text"] {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 50px;
            background: rgba(255, 255, 255, 0.8);
            color: #1a1a3d;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 50px;
            background: #ffcc00;
            color: #1a1a3d;
            cursor: pointer;
            transition: transform 0.3s;
        }
        button:hover {
            transform: scale(1.1);
        }
        #voice-btn {
            background: #ff4444;
            color: #fff;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @media (max-width: 600px) {
            .chat-container { margin: 10px; }
            .chat-box { height: 300px; }
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <h2>Chat with AI</h2>
            <a href="#" onclick="redirectTo('index.php')">Home</a>
        </div>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <div class="chat-box" id="chat-box">
            <?php foreach ($chat_history as $row): ?>
                <div class="message <?php echo $row['is_user'] ? 'user-message' : 'ai-message'; ?>">
                    <?php echo htmlspecialchars($row['message']); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="input-area">
            <input type="text" id="user-input" placeholder="Type your message...">
            <button onclick="sendMessage()">Send</button>
            <button id="voice-btn" onclick="toggleVoice()">Voice</button>
        </div>
    </div>
    <script>
        function redirectTo(page) {
            window.location.href = page;
        }

        // Mock AI responses for testing (replace with real API)
        const mockResponses = {
            "hello": "Hi there! How can I assist you today?",
            "how are you": "I'm doing great, thanks for asking! How about you?",
            "what is ai": "AI, or Artificial Intelligence, is like me—a system that mimics human intelligence to solve problems, answer questions, and sometimes even be a bit witty!",
            "default": "Hmm, that's an interesting question! Can you tell me more?"
        };

        async function getAIResponse(userMessage) {
            // Mock AI response for testing
            const lowerMessage = userMessage.toLowerCase().trim();
            return mockResponses[lowerMessage] || mockResponses["default"];

            // Uncomment below to use xAI API (requires API key)
            /*
            const apiKey = 'YOUR_XAI_API_KEY'; // Get from https://x.ai/api
            try {
                const response = await fetch('https://api.x.ai/v1/chat', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${apiKey}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        model: 'grok',
                        messages: [{ role: 'user', content: userMessage }]
                    })
                });
                const data = await response.json();
                if (data.choices && data.choices[0].message) {
                    return data.choices[0].message.content;
                }
                return "Sorry, I couldn't process that request.";
            } catch (error) {
                console.error('AI API error:', error);
                return "Error connecting to AI service.";
            }
            */

            // Alternatively, use OpenAI API (requires API key)
            /*
            const apiKey = 'YOUR_OPENAI_API_KEY'; // Get from https://platform.openai.com
            try {
                const response = await fetch('https://api.openai.com/v1/chat/completions', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${apiKey}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        model: 'gpt-3.5-turbo',
                        messages: [{ role: 'user', content: userMessage }]
                    })
                });
                const data = await response.json();
                if (data.choices && data.choices[0].message) {
                    return data.choices[0].message.content;
                }
                return "Sorry, I couldn't process that request.";
            } catch (error) {
                console.error('AI API error:', error);
                return "Error connecting to AI service.";
            }
            */
        }

        async function sendMessage() {
            const input = document.getElementById('user-input');
            const message = input.value.trim();
            if (!message) return;

            const chatBox = document.getElementById('chat-box');
            const userMessage = document.createElement('div');
            userMessage.className = 'message user-message';
            userMessage.textContent = message;
            chatBox.appendChild(userMessage);
            chatBox.scrollTop = chatBox.scrollHeight;

            // Save user message to database
            try {
                const response = await fetch('save_message.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `message=${encodeURIComponent(message)}&is_user=1`
                });
                if (!response.ok) throw new Error('Failed to save user message');
            } catch (error) {
                console.error('Error saving user message:', error);
            }

            // Get AI response
            const aiResponse = await getAIResponse(message);
            const aiMessage = document.createElement('div');
            aiMessage.className = 'message ai-message';
            aiMessage.textContent = aiResponse;
            chatBox.appendChild(aiMessage);
            chatBox.scrollTop = chatBox.scrollHeight;

            // Save AI message to database
            try {
                const response = await fetch('save_message.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `message=${encodeURIComponent(aiResponse)}&is_user=0`
                });
                if (!response.ok) throw new Error('Failed to save AI message');
            } catch (error) {
                console.error('Error saving AI message:', error);
            }

            input.value = '';
        }

        let isRecording = false;
        let recognition;
        if ('SpeechRecognition' in window || 'webkitSpeechRecognition' in window) {
            recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
            recognition.lang = 'en-US';
            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                document.getElementById('user-input').value = transcript;
                sendMessage();
            };
            recognition.onerror = (event) => {
                console.error('Speech recognition error:', event.error);
            };
        } else {
            document.getElementById('voice-btn').style.display = 'none';
            console.warn('Speech recognition not supported in this browser');
        }

        function toggleVoice() {
            if (!recognition) return;
            const voiceBtn = document.getElementById('voice-btn');
            if (!isRecording) {
                recognition.start();
                voiceBtn.textContent = 'Stop Voice';
                voiceBtn.style.background = '#44ff44';
                isRecording = true;
            } else {
                recognition.stop();
                voiceBtn.textContent = 'Voice';
                voiceBtn.style.background = '#ff4444';
                isRecording = false;
            }
        }
    </script>
</body>
</html>
