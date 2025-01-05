<?php
// Database connection
include '../../Php_Code/db_connection.php';
session_start();

// Check if the manager is logged in
if (!isset($_SESSION['manager_mobile_no'])) {
    header("Location: ../../login.php");
    exit();
}

// Fetch manager details from the `managers` table
$manager_mobile_no = $_SESSION['manager_mobile_no'];
$sql = "SELECT Name as manager_name, Hospital_Name as hospital_name, Hospital_License_Number as hospital_license_number FROM managers WHERE Mobile_No = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $manager_mobile_no);
$stmt->execute();
$result = $stmt->get_result();
$manager = $result->fetch_assoc();
$stmt->close();

if (!$manager) {
    die("Manager information not found. Please log in again.");
}

// Initialize message variables
$message = "";

// Handle form submission for adding, editing, or deleting doctors
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add Doctor
    if (isset($_POST['add_doctor'])) {
        $doctor_name = $_POST['doctor_name'];
        $doctor_license = $_POST['doctor_license'];
        $doctor_type = $_POST['doctor_type'];
        $location = $_POST['location'];
        $visit_days = implode(',', $_POST['visit_days']);
        $visit_start = $_POST['visit_start'];
        $visit_end = $_POST['visit_end'];
        $visit_money = $_POST['visit_money'];

        $sql = "INSERT INTO hospitals 
                (Hospital_Name, Hospital_License, Manager_Name, Manager_Mobile_Number, Doctor_Name, Doctor_License_Number, Dr_Categories, Location, Visit_Day_Start, Visit_Time_Start, Visit_Time_End, Visit_Money, Hospital_Mobile_Number) 
                VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssssssssi",
            $manager['hospital_name'],
            $manager['hospital_license_number'],
            $manager['manager_name'],
            $manager_mobile_no,
            $doctor_name,
            $doctor_license,
            $doctor_type,
            $location,
            $visit_days,
            $visit_start,
            $visit_end,
            $visit_money,
            $manager_mobile_no
        );

        if ($stmt->execute()) {
            $message = "Doctor added successfully!";
            header("Location: managerDashboard.php");
            exit();
        } else {
            $message = "Error adding doctor: " . $conn->error;
        }

        $stmt->close();
    }

    // Edit Doctor
    if (isset($_POST['edit_doctor'])) {
        $doctor_id = $_POST['doctor_id'];
        $doctor_name = $_POST['doctor_name'];
        $doctor_license = $_POST['doctor_license'];
        $doctor_type = $_POST['doctor_type'];
        $location = $_POST['location'];
        $visit_days = implode(',', $_POST['visit_days']);
        $visit_start = $_POST['visit_start'];
        $visit_end = $_POST['visit_end'];
        $visit_money = $_POST['visit_money'];

        $sql = "UPDATE hospitals 
                SET Doctor_Name = ?, Doctor_License_Number = ?, Dr_Categories = ?, Location = ?, Visit_Day_Start = ?, Visit_Time_Start = ?, Visit_Time_End = ?, Visit_Money = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssii", $doctor_name, $doctor_license, $doctor_type, $location, $visit_days, $visit_start, $visit_end, $visit_money, $doctor_id);

        if ($stmt->execute()) {
            $message = "Doctor updated successfully!";
            header("Location: managerDashboard.php");
            exit();
        } else {
            $message = "Error updating doctor: " . $conn->error;
        }

        $stmt->close();
    }

    // Delete Doctor
    if (isset($_POST['delete_doctor'])) {
        $doctor_id = $_POST['doctor_id'];

        $sql = "DELETE FROM hospitals WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $doctor_id);

        if ($stmt->execute()) {
            $message = "Doctor deleted successfully!";
            header("Location: managerDashboard.php");
            exit();
        } else {
            $message = "Error deleting doctor: " . $conn->error;
        }

        $stmt->close();
    }
}

// Retrieve all doctors added by this manager
$sql = "SELECT * FROM hospitals WHERE Manager_Mobile_Number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $manager_mobile_no);
$stmt->execute();
$result = $stmt->get_result();
$doctors = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            margin-bottom: 20px;
        }
        form input, form select, form button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        form button {
            background-color: #5cb85c;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        form button:hover {
            background-color: #4cae4c;
        }
        .doctor-list {
            margin-top: 20px;
        }
        .doctor-item {
            border: 1px solid #ddd;
            background: #f9f9f9;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 4px;
        }
        .doctor-item button {
            background-color: #d9534f;
            color: #fff;
            border: none;
            padding: 5px 10px;
            margin-top: 5px;
            border-radius: 4px;
            cursor: pointer;
        }
        .doctor-item button:hover {
            background-color: #c9302c;
        }
        .checkbox-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .checkbox-container label {
            display: flex;
            align-items: center;
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #f9f9f9;
            cursor: pointer;
        }
        .checkbox-container input {
            margin-right: 5px;
        }
        .message {
            text-align: center;
            color: green;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Doctors</h2>

        <?php if (!empty($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="POST" id="doctor_form">
            <input type="hidden" name="doctor_id" id="doctor_id" value="">
            <label for="doctor_name">Doctor Name:</label>
            <input type="text" id="doctor_name" name="doctor_name" placeholder="Enter Doctor Name" required>

            <label for="doctor_license">Doctor License Number:</label>
            <input type="text" id="doctor_license" name="doctor_license" placeholder="Enter Doctor License Number" required>

            <label for="doctor_type">Doctor Type:</label>
            <select id="doctor_type" name="doctor_type" required>
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

            <label for="location">Location:</label>
            <select id="location" name="location" required>
                <option value="Dhaka">Dhaka</option>
                <option value="Rajshahi">Rajshahi</option>
                <option value="Chittagong">Chittagong</option>
                <option value="Other">Other</option>
            </select>

            <label>Visit Days:</label>
            <div class="checkbox-container">
                <label><input type="checkbox" name="visit_days[]" value="Monday"> Monday</label>
                <label><input type="checkbox" name="visit_days[]" value="Tuesday"> Tuesday</label>
                <label><input type="checkbox" name="visit_days[]" value="Wednesday"> Wednesday</label>
                <label><input type="checkbox" name="visit_days[]" value="Thursday"> Thursday</label>
                <label><input type="checkbox" name="visit_days[]" value="Friday"> Friday</label>
                <label><input type="checkbox" name="visit_days[]" value="Saturday"> Saturday</label>
                <label><input type="checkbox" name="visit_days[]" value="Sunday"> Sunday</label>
            </div>

            <label for="visit_start">Visit Start Time:</label>
            <input type="time" id="visit_start" name="visit_start" required>

            <label for="visit_end">Visit End Time:</label>
            <input type="time" id="visit_end" name="visit_end" required>

            <label for="visit_money">Visit Money:</label>
            <input type="number" id="visit_money" name="visit_money" placeholder="Enter Visit Money" required>

            <button type="submit" name="add_doctor" id="add_doctor_button">Add Doctor</button>
        </form>

        <div class="doctor-list">
            <h3>Doctors in Your Hospital</h3>
            <?php foreach ($doctors as $doctor): ?>
                <div class="doctor-item">
                    <p><strong>Doctor Name:</strong> <?= htmlspecialchars($doctor['Doctor_Name']) ?></p>
                    <p><strong>License Number:</strong> <?= htmlspecialchars($doctor['Doctor_License_Number']) ?></p>
                    <p><strong>Type:</strong> <?= htmlspecialchars($doctor['Dr_Categories']) ?></p>
                    <p><strong>Location:</strong> <?= htmlspecialchars($doctor['Location']) ?></p>
                    <p><strong>Visit Days:</strong> <?= htmlspecialchars($doctor['Visit_Day_Start']) ?></p>
                    <p><strong>Visit Time:</strong> <?= htmlspecialchars($doctor['Visit_Time_Start']) ?> to <?= htmlspecialchars($doctor['Visit_Time_End']) ?></p>
                    <p><strong>Visit Money:</strong> <?= htmlspecialchars($doctor['Visit_Money']) ?></p>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="doctor_id" value="<?= $doctor['id'] ?>">
                        <button type="button" onclick="editDoctor(<?= htmlspecialchars(json_encode($doctor)) ?>)">Edit</button>
                        <button type="submit" name="delete_doctor">Delete</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        const doctorForm = document.getElementById('doctor_form');
        const addDoctorButton = document.getElementById('add_doctor_button');
        const doctorIdInput = document.getElementById('doctor_id');

        function editDoctor(doctor) {
            doctorIdInput.value = doctor.id;
            document.getElementById('doctor_name').value = doctor.Doctor_Name;
            document.getElementById('doctor_license').value = doctor.Doctor_License_Number;
            document.getElementById('doctor_type').value = doctor.Dr_Categories;
            document.getElementById('location').value = doctor.Location;
            const visitDays = doctor.Visit_Day_Start.split(',');
            document.querySelectorAll("input[name='visit_days[]']").forEach(input => {
                input.checked = visitDays.includes(input.value);
            });
            document.getElementById('visit_start').value = doctor.Visit_Time_Start;
            document.getElementById('visit_end').value = doctor.Visit_Time_End;
            document.getElementById('visit_money').value = doctor.Visit_Money;

            addDoctorButton.name = 'edit_doctor';
            addDoctorButton.innerText = 'Save Changes';
        }

        doctorForm.addEventListener('reset', () => {
            doctorIdInput.value = '';
            addDoctorButton.name = 'add_doctor';
            addDoctorButton.innerText = 'Add Doctor';
        });
    </script>
</body>
</html>
