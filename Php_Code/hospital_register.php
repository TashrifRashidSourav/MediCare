<?php
include 'db_connection.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_hospital'])) {
    $hospital_mobile_no = $_POST['hospital_mobile_no'];
    $location = $_POST['location'];
    $doctors = $_POST['doctors'];
    $dr_categories = $_POST['dr_categories'];
    $visit_day_start = implode(',', $_POST['visit_day_start']); // Convert array to comma-separated string
    $visit_time_start = $_POST['visit_time_start'];
    $visit_time_end = $_POST['visit_time_end'];
    $visit_money = $_POST['visit_money'];
    $is_accepted = 'pending'; // Default value
    $manager_mobile_no = $_SESSION['manager_mobile_no']; // Auto-fetch from session

    $hospital_name = "Example Hospital";  // Set programmatically
    $hospital_license = "LICENSE1234";   // Set programmatically

    // Insert into Hospitals table
    $sql = "INSERT INTO Hospitals (
                Hospital_Name, Hospital_License, Hospital_Mobile_Number, Manager_Mobile_No, Location,
                Doctors, Dr_Categories, Visit_Day_Start, Visit_Time_Start,
                Visit_Time_End, Visit_Money, Is_Accepted
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssssssssis",
        $hospital_name,
        $hospital_license,
        $hospital_mobile_no,
        $manager_mobile_no,
        $location,
        $doctors,
        $dr_categories,
        $visit_day_start,
        $visit_time_start,
        $visit_time_end,
        $visit_money,
        $is_accepted
    );

    if ($stmt->execute()) {
        echo "Hospital registered successfully with 'pending' status!";
        header("Location: ../../html_Code/Manager/dashboard.html");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
