<?php
session_start();
$servername = "localhost";
$username = "root"; // Database username
$password = ""; // Database password
$database = "MediCare"; // Database name

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    die("You are not logged in.");
}

// Retrieve session details
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Check if doctor_id and patient_id are provided
if (!isset($_GET['doctor_id']) || !isset($_GET['patient_id'])) {
    die("Invalid request. No conversation specified.");
}

$doctor_id = $_GET['doctor_id'];
$patient_id = $_GET['patient_id'];

// Generate a unique conversation ID
function getConversationId($doctor_id, $patient_id) {
    return "doctor_{$doctor_id}_patient_{$patient_id}";
}

$conversation_id = getConversationId($doctor_id, $patient_id);

// Handle sending messages
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
    $message = $conn->real_escape_string($_POST['message']);
    $receiver_role = ($user_role === 'doctor') ? 'patient' : 'doctor';
    $receiver_id = ($user_role === 'doctor') ? $patient_id : $doctor_id;

    // Validate receiver_id
    if ($receiver_role === 'doctor') {
        $receiver_exists = $conn->query("SELECT 1 FROM doctors WHERE d_id = '$receiver_id'")->num_rows > 0;
    } elseif ($receiver_role === 'patient') {
        $receiver_exists = $conn->query("SELECT 1 FROM patients WHERE p_id = '$receiver_id'")->num_rows > 0;
    } else {
        $receiver_exists = false;
    }

    if (!$receiver_exists) {
        die("Invalid receiver ID.");
    }

    // Insert the message into the chat table
    $sql = "INSERT INTO chat (conversation_id, sender_role, sender_id, receiver_role, receiver_id, message) 
            VALUES ('$conversation_id', '$user_role', '$user_id', '$receiver_role', '$receiver_id', '$message')";
    if (!$conn->query($sql)) {
        die("Error: " . $conn->error);
    }
}

// Fetch messages for the conversation
$sql = "SELECT * FROM chat 
        WHERE conversation_id = '$conversation_id'
        ORDER BY timestamp ASC";

$result = $conn->query($sql);
$messages = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Chat</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { margin: 20px auto; max-width: 600px; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); }
        .messages { height: 400px; overflow-y: auto; margin-bottom: 20px; border: 1px solid #ddd; padding: 10px; border-radius: 5px; background: #f9f9f9; }
        .message { margin-bottom: 10px; padding: 10px; border-radius: 5px; }
        .message.sent { background: #d4edda; text-align: right; }
        .message.received { background: #f8d7da; }
        .form-group { display: flex; }
        .form-group input { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px 0 0 5px; }
        .form-group button { padding: 10px; background: #007bff; color: #fff; border: none; border-radius: 0 5px 5px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h3>Chat</h3>
        <div class="messages">
            <?php foreach ($messages as $msg): ?>
                <div class="message <?= $msg['sender_id'] == $user_id ? 'sent' : 'received' ?>">
                    <p><?= htmlspecialchars($msg['message']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <form method="POST" class="form-group">
            <input type="text" name="message" placeholder="Type your message..." required>
            <button type="submit">Send</button>
        </form>
    </div>
</body>
</html>
