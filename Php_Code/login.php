<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $login_input = $_POST['login_input'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];
    $table = ucfirst($user_type) . 's'; // Determine the table name based on user type

    if ($user_type === 'doctor') {
        // Doctors login using License_No
        $sql = "SELECT * FROM $table WHERE License_No = ?";
    } elseif ($user_type === 'patient') {
        // Patients login using Mobile_No
        $sql = "SELECT * FROM $table WHERE Mobile_No = ?";
    } else {
        // Managers and Admins login using Mobile_No
        $sql = "SELECT * FROM $table WHERE Mobile_No = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $login_input);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a user exists
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password using password_verify
        if (password_verify($password, $user['Password'])) {
            // Store user details in session
            $_SESSION['user_type'] = $user_type;

            if ($user_type === 'doctor') {
                $_SESSION['doctor_id'] = $user['d_id']; // Store doctor ID in session
                $_SESSION['doctor_license'] = $user['License_No'];
                $_SESSION['doctor_name'] = $user['Name'];
                $_SESSION['doctor_category'] = $user['Dr_Categories'];
                header("Location: ../html_Code/Doctor/doctorDashboard.php");
                exit;
            } elseif ($user_type === 'patient') {
                $_SESSION['patient_id'] = $user['p_id'];
                $_SESSION['patient_mobile_no'] = $user['Mobile_No'];
                $_SESSION['patient_name'] = $user['Name'];
                header("Location: ../html_Code/Patient/patientDashboard.php");
                exit;
            } elseif ($user_type === 'manager') {
                $_SESSION['manager_mobile_no'] = $user['Mobile_No'];
                $_SESSION['manager_name'] = $user['Name'];
                header("Location: ../html_Code/Manager/managerDashboard.php");
                exit;
            } elseif ($user_type === 'admin') {
                $_SESSION['admin_mobile_no'] = $user['Mobile_No'];
                $_SESSION['admin_name'] = $user['Name'];
                header("Location: ../html_Code/Admin/adminDashboard.html");
                exit;
            }
        } else {
            // Incorrect password
            echo "Invalid password. Please try again.";
        }
    } else {
        // User not found
        echo "Invalid login credentials. Please try again.";
    }

    $stmt->close();
}
$conn->close();
?>
