<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $login_input = $_POST['login_input']; // Phone number or name
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];

    // Determine the table based on the user type
    $table = ucfirst($user_type) . 's'; // E.g., Patients, Doctors, Managers

    // Query setup based on user type
    if ($user_type === 'doctor') {
        // Doctors login using License_No and Password
        $sql = "SELECT * FROM $table WHERE License_No = ? AND Password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $login_input, $password);
    } else {
        // Patients and Managers login using Mobile_No and Password
        $sql = "SELECT * FROM $table WHERE Mobile_No = ? AND Password = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $login_input, $password);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Check if login credentials are correct
    if ($result->num_rows > 0) {
        // Redirect based on user type
        if ($user_type === 'doctor') {
            header("Location: ../html_Code/Doctor/doctorDashboard.html");
            exit;
        } elseif ($user_type === 'patient') {
            header("Location: ../html_Code/Patient/patientDashboard.html");
            exit;
        } elseif ($user_type === 'manager') {
            header("Location: ../html_Code/Manager/managerDashboard.html");
            exit;
        }
    } else {
        echo "Invalid login credentials. Please try again.";
    }

    $stmt->close();
}
$conn->close();
?>
