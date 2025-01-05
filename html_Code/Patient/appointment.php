<?php
// Start session and include database connection
session_start();
include '../../Php_Code/db_connection.php';

// Check if the patient is logged in
if (!isset($_SESSION['patient_id'])) {
    header("Location: ../../html_Code/login.php?error=not_logged_in");
    exit();
}

// Fetch patient details from the `patients` table
$patient_id = $_SESSION['patient_id'];
$sql_patient = "SELECT Name, Mobile_No FROM patients WHERE p_id = ?";
$stmt_patient = $conn->prepare($sql_patient);
$stmt_patient->bind_param("i", $patient_id);
$stmt_patient->execute();
$result_patient = $stmt_patient->get_result();
$patient = $result_patient->fetch_assoc();
$stmt_patient->close();

if (!$patient) {
    session_destroy(); // Destroy invalid session
    header("Location: ../../html_Code/login.php?error=invalid_session");
    exit();
}

$patient_name = $patient['Name'];
$patient_mobile_no = $patient['Mobile_No'];

$search_results = [];
$message = "";

// Search functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_doctors'])) {
    $locations = isset($_POST['locations']) ? $_POST['locations'] : [];
    $categories = isset($_POST['categories']) ? $_POST['categories'] : [];

    $sql = "SELECT * FROM hospitals WHERE 1=1";
    $params = [];
    $types = '';

    // Add location filters
    if (!empty($locations)) {
        $placeholders = implode(',', array_fill(0, count($locations), '?'));
        $sql .= " AND Location IN ($placeholders)";
        $params = array_merge($params, $locations);
        $types .= str_repeat('s', count($locations));
    }

    // Add category filters
    if (!empty($categories)) {
        $placeholders = implode(',', array_fill(0, count($categories), '?'));
        $sql .= " AND Dr_Categories IN ($placeholders)";
        $params = array_merge($params, $categories);
        $types .= str_repeat('s', count($categories));
    }

    $stmt = $conn->prepare($sql);
    if ($types) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $search_results = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Appointment booking functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_appointment_submit'])) {
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $doctor_id = intval($_POST['doctor_id']);

    // Fetch doctor and hospital details
    $sql_fetch = "SELECT Doctor_Name, Doctor_License_Number, Hospital_Name, Hospital_License, Manager_Name, Manager_Mobile_Number
                  FROM hospitals
                  WHERE id = ?";
    $stmt_fetch = $conn->prepare($sql_fetch);
    $stmt_fetch->bind_param("i", $doctor_id);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();
    $doctor_info = $result->fetch_assoc();
    $stmt_fetch->close();

    if ($doctor_info) {
        // Insert appointment into `appointments` table
        $sql_insert = "INSERT INTO appointments 
                       (Patient_Id, Patient_Name, Patient_Mobile_No, Doctor_Name, Doctor_License_Number, Hospital_Name, Hospital_License, Appointment_Date, Appointment_Time, Manager_Name, Manager_Mobile_No) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param(
            "issssssssss",
            $patient_id,
            $patient_name,
            $patient_mobile_no,
            $doctor_info['Doctor_Name'],
            $doctor_info['Doctor_License_Number'],
            $doctor_info['Hospital_Name'],
            $doctor_info['Hospital_License'],
            $appointment_date,
            $appointment_time,
            $doctor_info['Manager_Name'],
            $doctor_info['Manager_Mobile_Number']
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
