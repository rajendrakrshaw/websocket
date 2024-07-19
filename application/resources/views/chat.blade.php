<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ping Pong</title>
    <style>
        #chat-messages {
            height: 300px;
            overflow-y: scroll;
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }
        .message {
            margin: 5px;
            padding: 10px;
            border-radius: 5px;
        }
        .messagebox.other {
            display: flex;
            width: 100%;
            justify-content: start;
            flex-direction: row;
        }
        .messagebox.own {
            display: flex;
            width: 100%;
            justify-content: end;
            flex-direction: row;
        }
        .message.own {
            text-align: right;
            background-color: #dcf8c6;
            width: auto;
        }
        .message.other {
            text-align: left;
            background-color: #0000001f;
            width: auto;
        }
    </style>
</head>
<body>
    <h1>Ping Pong</h1>
    
    <div id="chat-messages">
        <!-- Messages will be appended here dynamically -->
    </div>
    
    <form id="chat-form">
        <input type="text" id="message-input" placeholder="Type your message...">
        <button type="submit">Send</button>
    </form>
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
            forceTLS: false, // Adjust according to your setup
            disableStats: true, // Adjust according to your setup
            enabledTransports: ['ws', 'wss']  // Adjust based on your setup
        });

        var channel = pusher.subscribe('chat');
        channel.bind('message', function(data) {
            console.log('Received message:', data.message);
            var message = data.message;
            var sender = data.username;
            var chatMessages = $('#chat-messages');
            var messageClass = sender === username ? 'own' : 'other';
            // chatMessages.append('<div>' + message + '</div>');
            chatMessages.append('<div class="messagebox ' + messageClass + '"><div class="message ' + messageClass + '"><strong>' + sender + ':</strong> ' + message + '</div></div>');
            chatMessages.scrollTop(chatMessages.prop("scrollHeight"));
        });
// });



        $('#chat-form').on('submit', function(event) {
            event.preventDefault();
            var messageInput = $('#message-input');
            var message = messageInput.val();
            var message = messageInput.val();

            // Send message to server using jQuery
            $.ajax({
                url: '/chat/send',
                method: 'POST',
                headers: {
                    'X-CSRF-Token': '{{ csrf_token() }}' // Include CSRF token
                },
                data: { message: message, username: username },
                success: function(data) {
                    console.log(message,username);
                    messageInput.val(''); // Clear input field after sending
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
