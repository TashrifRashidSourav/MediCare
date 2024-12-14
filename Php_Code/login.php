<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $login_input = $_POST['login_input']; // Phone number
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];

    // Determine the table based on the user type
    $table = ucfirst($user_type) . 's'; // E.g., Patients, Doctors, Managers

    // Define query based on user type
    if ($user_type == 'doctor') {
        // Doctors don't use phone numbers; match using name instead
        $sql = "SELECT * FROM $table WHERE Name = ? AND Password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $login_input, $password);
    } else {
        // For Patients and Managers, match using mobile number
        $sql = "SELECT * FROM $table WHERE Mobile_No = ? AND Password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $login_input, $password);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Check if login credentials are correct
    if ($result->num_rows > 0) {
        // Redirect based on user type
        if ($user_type == 'doctor') {
            header("Location: ../html_Code/Doctor/doctorDashboard.html");
            exit;
        } elseif ($user_type == 'patient') {
            header("Location: ../html_Code/Patient/patientDashboard.html");
            exit;
        } elseif ($user_type == 'manager') {
            header("Location: ../html_Code/Manager/managerDashboard.html");
            exit;
        }
    } else {
        echo "Invalid login credentials. Please try again.";
    }
}
?>
