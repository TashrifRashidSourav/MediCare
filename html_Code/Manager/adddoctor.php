<?php
// Start session and include database connection
session_start();
include '../../Php_Code/db_connection.php';

// Check if the manager is logged in
if (!isset($_SESSION['manager_mobile_no'])) {
    header("Location: ../../login.php?error=not_logged_in");
    exit();
}

// Retrieve manager's mobile number from session
$manager_mobile_no = $_SESSION['manager_mobile_no'];

// Fetch manager details from the database
$sql = "SELECT Name as manager_name, Hospital_Name as hospital_name, Hospital_License_Number as hospital_license_number 
        FROM managers 
        WHERE Mobile_No = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $manager_mobile_no);
$stmt->execute();
$result = $stmt->get_result();
$manager = $result->fetch_assoc();
$stmt->close();

if (!$manager) {
    session_destroy();
    header("Location: ../../login.php?error=invalid_session");
    exit();
}

// Initialize message variable
$message = "";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add Doctor
    if (isset($_POST['add_doctor'])) {
        $doctor_name = trim($_POST['doctor_name']);
        $doctor_license = trim($_POST['doctor_license']);
        $doctor_type = trim($_POST['doctor_type']);
        $location = trim($_POST['location']);
        $visit_days = isset($_POST['visit_days']) ? implode(',', $_POST['visit_days']) : '';
        $visit_start = $_POST['visit_start'];
        $visit_end = $_POST['visit_end'];
        $visit_money = intval($_POST['visit_money']);

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
            header("Location: managerDashboard.php?success=add_doctor");
            exit();
        } else {
            $message = "Error adding doctor: " . $stmt->error;
        }

        $stmt->close();
    }

    // Edit Doctor
    if (isset($_POST['edit_doctor'])) {
        $doctor_id = intval($_POST['doctor_id']);
        $doctor_name = trim($_POST['doctor_name']);
        $doctor_license = trim($_POST['doctor_license']);
        $doctor_type = trim($_POST['doctor_type']);
        $location = trim($_POST['location']);
        $visit_days = isset($_POST['visit_days']) ? implode(',', $_POST['visit_days']) : '';
        $visit_start = $_POST['visit_start'];
        $visit_end = $_POST['visit_end'];
        $visit_money = intval($_POST['visit_money']);

        $sql = "UPDATE hospitals 
                SET Doctor_Name = ?, Doctor_License_Number = ?, Dr_Categories = ?, Location = ?, Visit_Day_Start = ?, Visit_Time_Start = ?, Visit_Time_End = ?, Visit_Money = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssii", $doctor_name, $doctor_license, $doctor_type, $location, $visit_days, $visit_start, $visit_end, $visit_money, $doctor_id);

        if ($stmt->execute()) {
            $message = "Doctor updated successfully!";
            header("Location: managerDashboard.php?success=edit_doctor");
            exit();
        } else {
            $message = "Error updating doctor: " . $stmt->error;
        }

        $stmt->close();
    }

    // Delete Doctor
    if (isset($_POST['delete_doctor'])) {
        $doctor_id = intval($_POST['doctor_id']);

        $sql = "DELETE FROM hospitals WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $doctor_id);

        if ($stmt->execute()) {
            $message = "Doctor deleted successfully!";
            header("Location: managerDashboard.php?success=delete_doctor");
            exit();
        } else {
            $message = "Error deleting doctor: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Fetch all doctors managed by this manager
$sql = "SELECT * FROM hospitals WHERE Manager_Mobile_Number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $manager_mobile_no);
$stmt->execute();
$result = $stmt->get_result();
$doctors = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>
