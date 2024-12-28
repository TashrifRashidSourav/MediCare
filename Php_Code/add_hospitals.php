<?php
include 'db_connection.php';
session_start();

// Check if the manager is logged in
if (!isset($_SESSION['manager_mobile_no'])) {
    echo "You must be logged in as a manager to access this page.";
    exit;
}

$manager_mobile_no = $_SESSION['manager_mobile_no'];
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_hospital'])) {
    $hospital_name = $_POST['hospital_name'];
    $hospital_license = $_POST['hospital_license'];

    // Check if the hospital license already exists
    $check_sql = "SELECT * FROM Hospitals WHERE Hospital_License = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $hospital_license);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "This hospital license number already exists.";
    } else {
        // Insert the new hospital into the database
        $insert_sql = "INSERT INTO Hospitals (Hospital_Name, Hospital_License, Manager_Mobile_No, Is_Accepted) 
                       VALUES (?, ?, ?, 'pending')";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("sss", $hospital_name, $hospital_license, $manager_mobile_no);

        if ($stmt->execute()) {
            $message = "Hospital added successfully with 'pending' status!";
        } else {
            $message = "Error: " . $stmt->error;
        }
    }
    $stmt->close();
}

// Fetch hospitals created by this manager
$fetch_sql = "SELECT Hospital_Name, Hospital_License, Is_Accepted FROM Hospitals WHERE Manager_Mobile_No = ?";
$stmt = $conn->prepare($fetch_sql);
$stmt->bind_param("s", $manager_mobile_no);
$stmt->execute();
$created_hospitals = $stmt->get_result();
$stmt->close();

$conn->close();
?>
