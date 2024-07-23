<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ping Pong Chat</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }
        #chat-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 80vh;
        }
        #chat-header {
            background-color: #007bff;
            color: #fff;
            padding: 15px;
            text-align: center;
            font-weight: 500;
            font-size: 1.2em;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        #chat-header i {
            font-size: 1.5em;
        }
        #chat-messages {
            flex: 1;
            padding: 20px;
            background-color: #f9f9f9;
            overflow-y: auto;
        }
        .messagebox {
            display: flex;
            margin-bottom: 10px;
        }
        .messagebox.own {
            justify-content: flex-end;
        }
        .messagebox.other {
            justify-content: flex-start;
        }
        .message {
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 20px;
            color: #fff;
            font-size: 0.9em;
            line-height: 1.4;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .message.own {
            background-color: #007bff;
        }
        .message.other {
            background-color: #6c757d;
        }
        #chat-form {
            display: flex;
            padding: 15px;
            background-color: #fff;
            border-top: 1px solid #ccc;
        }
        #message-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 20px;
            margin-right: 10px;
            font-size: 1em;
        }
        #chat-form button {
            padding: 10px 15px;
            background-color: #007bff;
            border: none;
            color: #fff;
            border-radius: 20px;
            cursor: pointer;
            font-size: 1em;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #chat-form button:hover {
            background-color: #0056b3;
        }
        /* #chat-form button i {
            margin-left: 5px;
        } */
        @media (max-width: 600px) {
            #chat-container {
                width: 90%;
                height: 90vh;
            }
            .message {
                max-width: 80%;
            }
        }
    </style>
</head>
<body>
    <div id="chat-container">
        <div id="chat-header">
            <span>Ping Pong Chat</span>
            <i class="fas fa-comments"></i>
        </div>
        <div id="chat-messages">
            <!-- Messages will be appended here dynamically -->
        </div>
        <form id="chat-form">
            <input type="text" id="message-input" placeholder="Type your message..." autocomplete="off">
            <button type="submit"><i class="fas fa-paper-plane"></i></button>
        </form>
    </div>

    <!-- Include Pusher JavaScript library -->
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>

    <!-- Include jQuery and Laravel Echo -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.js"></script>

    <script>
    $(document).ready(function() {
        // Prompt for username
        let username = prompt("Enter your name:", "Anonymous");
        if (!username) {
            username = "Anonymous";
        }

        var pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
            cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
            wsHost: window.location.hostname,
            wsPort: 6001,
            forceTLS: false,
            disableStats: true,
            enabledTransports: ['ws', 'wss']
        });

        function loadMessages() {
            var chatMessages = $('#chat-messages');
            var messages = JSON.parse(localStorage.getItem('chatMessages')) || [];
            messages.forEach(function (message) {
                var messageClass = message.username === username ? 'own' : 'other';
                chatMessages.append('<div class="messagebox ' + messageClass + '"><div class="message ' + messageClass + '"><strong>' + message.username + ':</strong> ' + message.message + '</div></div>');
            });
            chatMessages.scrollTop(chatMessages.prop("scrollHeight"));
        }

        function saveMessages(message) {
            var messages = JSON.parse(localStorage.getItem('chatMessages')) || [];
            messages.push(message);
            localStorage.setItem('chatMessages', JSON.stringify(messages));
        }

        var channel = pusher.subscribe('chat');
        channel.bind('message', function(data) {
            console.log('Received message:', data.message);
            var message = data.message;
            var sender = data.username;
            var chatMessages = $('#chat-messages');
            var messageClass = sender === username ? 'own' : 'other';
            chatMessages.append('<div class="messagebox ' + messageClass + '"><div class="message ' + messageClass + '"><strong>' + sender + ':</strong> ' + message + '</div></div>');
            chatMessages.scrollTop(chatMessages.prop("scrollHeight"));
            saveMessages(data);
        });

        loadMessages();

        $('#chat-form').on('submit', function(event) {
            event.preventDefault();
            var messageInput = $('#message-input');
            var message = messageInput.val();

            $.ajax({
                url: '/chat/send',
                method: 'POST',
                headers: {
                    'X-CSRF-Token': '{{ csrf_token() }}'
                },
                data: { message: message, username: username },
                success: function(data) {
                    messageInput.val('');
                },
                error: function(xhr, status, error) {
                    console.error('Error sending message:', error);
                }
            });
        });
    });
    </script>
</body>
</html>
