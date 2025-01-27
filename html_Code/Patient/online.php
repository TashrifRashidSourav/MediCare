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

// Fetch session data
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Search doctors
$doctors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_doctors'])) {
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $category = $conn->real_escape_string($_POST['category'] ?? '');

    $query = "SELECT * FROM doctors WHERE 1=1";
    if (!empty($name)) {
        $query .= " AND Name LIKE '%$name%'";
    }
    if (!empty($category)) {
        $query .= " AND Dr_Categories='$category'";
    }

    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $doctors[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Search Doctors - MediCare</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            margin: 30px auto;
            max-width: 800px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group input, .form-group select, .form-group button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #0056b3;
        }
        .doctor-card {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            background: #f9f9f9;
        }
        .doctor-card h5 {
            margin: 0;
            margin-bottom: 10px;
            color: #007bff;
        }
        .doctor-card p {
            margin: 5px 0;
        }
        .doctor-card button {
            margin-top: 10px;
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        .doctor-card button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Search Doctors</h2>
        <form method="POST">
            <div class="form-group">
                <label for="name">Doctor Name (optional):</label>
                <input type="text" id="name" name="name" placeholder="Enter doctor's name">
            </div>
            <div class="form-group">
                <label for="category">Category (optional):</label>
                <select id="category" name="category">
                    <option value="">Select a category</option>
                    <option value="Gynecologist">Gynecologist</option>
                    <option value="Cardiologist">Cardiologist</option>
                    <option value="Nephrologist">Nephrologist</option>
                    <option value="Dermatologist">Dermatologist</option>
                    <option value="Neurologist">Neurologist</option>
                    <option value="Pediatrician">Pediatrician</option>
                    <option value="Orthopedic Surgeon">Orthopedic Surgeon</option>
                    <option value="Gastroenterologist">Gastroenterologist</option>
                    <option value="Endocrinologist">Endocrinologist</option>
                    <option value="Oncologist">Oncologist</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" name="search_doctors">Search</button>
            </div>
        </form>

        <?php if (!empty($doctors)): ?>
            <h3>Search Results:</h3>
            <?php foreach ($doctors as $doctor): ?>
                <div class="doctor-card">
                    <h5><?= htmlspecialchars($doctor['Name']) ?></h5>
                    <p><strong>License No:</strong> <?= htmlspecialchars($doctor['License_No']) ?></p>
                    <p><strong>Category:</strong> <?= htmlspecialchars($doctor['Dr_Categories']) ?></p>
                    <p><strong>Expertise:</strong> <?= htmlspecialchars($doctor['Expertise'] ?? 'N/A') ?></p>
                    <button onclick="startChat(<?= $doctor['d_id'] ?>, '<?= htmlspecialchars($doctor['Name']) ?>')">Message</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                <p>No doctors found matching your criteria.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script>
        function startChat(doctorId, doctorName) {
            const url = `chat.php?doctor_id=${doctorId}&patient_id=<?= $user_id ?>`;
            window.location.href = url;
        }
    </script>
</body>
</html>
