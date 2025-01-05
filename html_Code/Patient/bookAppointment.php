<?php
// Database connection
include '../../Php_Code/db_connection.php';
session_start();

// Check if a doctor is selected
if (!isset($_POST['doctor_id'])) {
    header("Location: patientDashboard.php");
    exit();
}

// Retrieve doctor information from the form
$doctor_id = $_POST['doctor_id'];
$visit_days = explode(',', $_POST['visit_days']);
$visit_time_start = $_POST['visit_time_start'];
$visit_time_end = $_POST['visit_time_end'];

// Handle appointment submission
$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_appointment_submit'])) {
    $patient_name = $_POST['patient_name'];
    $patient_mobile_no = $_POST['patient_mobile_no'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];

    // Query to fetch the doctor and hospital details based on the selected doctor
    $sql_fetch = "SELECT Doctor_Name, Doctor_License_Number, Hospital_Name, Hospital_License 
                  FROM hospitals WHERE id = ?";
    $stmt_fetch = $conn->prepare($sql_fetch);
    $stmt_fetch->bind_param("i", $doctor_id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    $doctor_info = $result->fetch_assoc();
    $stmt_fetch->close();

    if ($doctor_info) {
        // Insert the appointment into the database
        $sql_insert = "INSERT INTO appointments 
                       (Patient_Name, Patient_Mobile_No, Doctor_Name, Doctor_License_Number, Hospital_Name, Hospital_License, Appointment_Date, Appointment_Time, Is_Accepted) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param(
            "ssssssss",
            $patient_name,
            $patient_mobile_no,
            $doctor_info['Doctor_Name'],
            $doctor_info['Doctor_License_Number'],
            $doctor_info['Hospital_Name'],
            $doctor_info['Hospital_License'],
            $appointment_date,
            $appointment_time
        );

        if ($stmt_insert->execute()) {
            $message = "Appointment booked successfully! Waiting for manager approval.";
        } else {
            $message = "Error booking appointment: " . $stmt_insert->error;
        }

        $stmt_insert->close();
    } else {
        $message = "Doctor or hospital information not found.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
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
            margin-top: 20px;
        }
        form input, form select, form button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        form button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        form button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            color: green;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Book Appointment</h2>

        <!-- Display Success or Error Messages -->
        <?php if (!empty($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="POST">
            <h3>Patient Information</h3>
            <label for="patient_name">Your Name:</label>
            <input type="text" id="patient_name" name="patient_name" placeholder="Enter your full name" required>

            <label for="patient_mobile_no">Your Mobile Number:</label>
            <input type="text" id="patient_mobile_no" name="patient_mobile_no" placeholder="Enter your mobile number" required>

            <h3>Appointment Details</h3>
            <label for="appointment_date">Select Appointment Date:</label>
            <select id="appointment_date" name="appointment_date" required>
                <?php foreach ($visit_days as $day): ?>
                    <option value="<?= date('Y-m-d', strtotime("next $day")) ?>"><?= date('l, F j, Y', strtotime("next $day")) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="appointment_time">Select Appointment Time:</label>
            <select id="appointment_time" name="appointment_time" required>
                <?php
                $start_time = strtotime($visit_time_start);
                $end_time = strtotime($visit_time_end);
                while ($start_time < $end_time): ?>
                    <option value="<?= date('H:i:s', $start_time) ?>"><?= date('h:i A', $start_time) ?></option>
                    <?php $start_time = strtotime('+30 minutes', $start_time); ?>
                <?php endwhile; ?>
            </select>

            <button type="submit" name="book_appointment_submit">Book Appointment</button>
        </form>
    </div>
</body>
</html>
