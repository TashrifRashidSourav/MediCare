
<?php include 'navbar.php'; ?>
<link rel="stylesheet" href="navbar.css">

<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "MediCare";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the user is logged in and is a doctor
if (!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit;
}

$doctor_id = $_SESSION['doctor_id'];

// Handle chat actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['sendMessage'])) {
        $sender_id = $doctor_id;
        $receiver_id = $_POST['receiver_id'];
        $message = $conn->real_escape_string($_POST['message']);
        $conversation_id = "doctor_{$sender_id}_doctor_{$receiver_id}";

        $sql = "INSERT INTO doctor_chat (conversation_id, sender_id, receiver_id, message) 
                VALUES ('$conversation_id', '$sender_id', '$receiver_id', '$message')";

        if ($conn->query($sql) === TRUE) {
            echo "Message sent successfully";
        } else {
            echo "Error: " . $conn->error;
        }
        exit;
    } elseif (isset($_POST['getMessages'])) {
        $contact_id = $_POST['contact_id'];
        $conversation_id = "doctor_{$doctor_id}_doctor_{$contact_id}";

        $sql = "SELECT doctor_chat.*, d1.Name AS sender_name 
                FROM doctor_chat
                JOIN doctors d1 ON doctor_chat.sender_id = d1.d_id
                WHERE (sender_id='$doctor_id' AND receiver_id='$contact_id') 
                   OR (sender_id='$contact_id' AND receiver_id='$doctor_id')
                ORDER BY timestamp ASC";
        $result = $conn->query($sql);

        $messages = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['sender_name'] = ($row['sender_id'] == $doctor_id) ? 'You' : $row['sender_name'];
                $messages[] = $row;
            }
        }

        echo json_encode($messages);
        exit;
    } elseif (isset($_POST['searchDoctors'])) {
        $name = $conn->real_escape_string($_POST['name']);
        $sql = "SELECT d_id, Name FROM doctors WHERE Name LIKE '%$name%' AND d_id != '$doctor_id'";
        $result = $conn->query($sql);

        $doctors = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $doctors[] = $row;
            }
        }

        echo json_encode($doctors);
        exit;
    } elseif (isset($_POST['getRecentContacts'])) {
        $sql = "SELECT DISTINCT 
                    CASE WHEN sender_id = '$doctor_id' THEN receiver_id ELSE sender_id END AS contact_id,
                    d.Name AS contact_name
                FROM doctor_chat
                JOIN doctors d ON CASE WHEN sender_id = '$doctor_id' THEN receiver_id ELSE sender_id END = d.d_id
                WHERE sender_id = '$doctor_id' OR receiver_id = '$doctor_id'
                ORDER BY (SELECT MAX(timestamp) 
                          FROM doctor_chat 
                          WHERE (sender_id = '$doctor_id' AND receiver_id = contact_id) 
                             OR (sender_id = contact_id AND receiver_id = '$doctor_id')) DESC";
        $result = $conn->query($sql);

        $contacts = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $contacts[] = $row;
            }
        }

        echo json_encode($contacts);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Doctor-to-Doctor Chat</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e5ddd5;
            margin: 0;
            padding: 0;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            display: flex;
            width: 80%;
            height: 80%;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            margin: 40px auto;
        }
        .sidebar {
            width: 30%;
            background-color: #fff;
            border-right: 1px solid #ccc;
            display: flex;
            flex-direction: column;
        }
        .search-bar {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        .search-bar input {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .contacts {
            flex-grow: 1;
            overflow-y: scroll;
        }
        .contact {
            padding: 15px;
            border-bottom: 1px solid #ccc;
            cursor: pointer;
        }
        .contact:hover {
            background-color: #f1f1f1;
        }
        .chat-box {
            width: 70%;
            background-color: #fcfcfc85;
            display: flex;
            flex-direction: column;
        }
        .chat-header {
            padding: 15px;
            background-color: #088b8b;
            color: #fff;
            font-size: 18px;
            font-weight: bold;
            border-bottom: 1px solid #ccc;
        }
        .messages {
            flex-grow: 1;
            padding: 10px;
            overflow-y: scroll;
        }
        .message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            max-width: 70%;
        }
        .message.sender {
            background-color: lightgreen;
            align-self: flex-end;
        }
        .message.receiver {
            background-color: #6a7fbc;
            align-self: flex-start;
        }
        .input-box {
            display: flex;
            padding: 10px;
            border-top: 1px solid #ccc;
        }
        .input-box input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .input-box button {
            padding: 10px;
            background-color: #34b7f1;
            color: #fff;
            border: none;
            border-radius: 5px;
            margin-left: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="Search doctors" oninput="searchDoctors()">
            </div>
            <div class="contacts" id="contactsList"></div>
        </div>
        <div class="chat-box">
            <div class="chat-header" id="chatHeader">Select a doctor</div>
            <div class="messages" id="messagesList"></div>
            <div class="input-box">
                <input type="text" id="messageInput" placeholder="Type a message...">
                <button onclick="sendMessage()">Send</button>
            </div>
        </div>
    </div>

    <script>
        let currentContactId = null;

        function searchDoctors() {
            const name = document.getElementById('searchInput').value;
            const formData = new FormData();
            formData.append('name', name);
            formData.append('searchDoctors', true);

            fetch('doctor_chat.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(doctors => {
                const contactsList = document.getElementById('contactsList');
                contactsList.innerHTML = '';
                doctors.forEach(doctor => {
                    const contactDiv = document.createElement('div');
                    contactDiv.classList.add('contact');
                    contactDiv.innerHTML = doctor.Name;
                    contactDiv.onclick = () => openChat(doctor.d_id);
                    contactsList.appendChild(contactDiv);
                });
            });
        }

        function openChat(receiverId) {
            currentContactId = receiverId;
            const chatHeader = document.getElementById('chatHeader');
            chatHeader.textContent = "Chatting with Doctor " + receiverId;
            loadMessages();
        }

        function loadMessages() {
            if (currentContactId === null) return;

            const formData = new FormData();
            formData.append('contact_id', currentContactId);
            formData.append('getMessages', true);

            fetch('doctor_chat.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(messages => {
                const messagesList = document.getElementById('messagesList');
                messagesList.innerHTML = '';
                messages.forEach(message => {
                    const messageDiv = document.createElement('div');
                    messageDiv.classList.add('message', message.sender_id == '<?php echo $doctor_id; ?>' ? 'sender' : 'receiver');
                    messageDiv.innerHTML = `<span>${message.sender_name}: </span>${message.message}`;
                    messagesList.appendChild(messageDiv);
                });
                messagesList.scrollTop = messagesList.scrollHeight;
            });
        }

        function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            if (message === '' || currentContactId === null) return;

            const formData = new FormData();
            formData.append('receiver_id', currentContactId);
            formData.append('message', message);
            formData.append('sendMessage', true);

            fetch('doctor_chat.php', {
                method: 'POST',
                body: formData
            })
            .then(() => {
                loadMessages();
                messageInput.value = '';
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const formData = new FormData();
            formData.append('getRecentContacts', true);
            fetch('doctor_chat.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(contacts => {
                const contactsList = document.getElementById('contactsList');
                contacts.forEach(contact => {
                    const contactDiv = document.createElement('div');
                    contactDiv.classList.add('contact');
                    contactDiv.innerHTML = `${contact.contact_name}`;
                    contactDiv.onclick = () => openChat(contact.contact_id);
                    contactsList.appendChild(contactDiv);
                });
            });
        });
    </script>
</body>
</html>
