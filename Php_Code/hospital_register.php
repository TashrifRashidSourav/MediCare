<?php
include 'db_connection.php';

// Assuming the manager's mobile number is fetched based on their session or login
session_start();
$manager_mobile_no = $_SESSION['manager_mobile_no']; // Manager's Mobile Number from Session

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register_hospital'])) {
    $hospital_name = $_POST['hospital_name'];
    $hospital_license = $_POST['hospital_license'];
    $location = $_POST['location'];

    // Insert into Hospitals table
    $sql = "INSERT INTO Hospitals (Hospital_Name, Hospital_License, Manager_Mobile_No, Location) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $hospital_name, $hospital_license, $manager_mobile_no, $location);

    if ($stmt->execute()) {
        // Update the Manager's status to "accepted" in Managers table
        $update_sql = "UPDATE Managers SET Is_Accepted = 'accepted' WHERE Mobile_No = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("s", $manager_mobile_no);
        $update_stmt->execute();

        echo "Hospital registered successfully!";
        header("Location: ../html_Code/Manager/managerDashboard.html");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
