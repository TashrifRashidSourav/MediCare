<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_BCRYPT); // Hash the password
    $user_type = $_POST['user_type'];

    if ($user_type === "doctor") {
        $license_no = $_POST['license_no'];
        $expertise = $_POST['expertise'];
        $email = $_POST['email'];
        $details = $_POST['details'];

        // Insert into Doctors table
        $sql = "INSERT INTO Doctors (Name, License_No, Expertise, Password, Email, Details) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $name, $license_no, $expertise, $hashed_password, $email, $details);

    } elseif ($user_type === "patient") {
        // Insert into Patients table
        $sql = "INSERT INTO Patients (Name, Mobile_No, Password) 
                VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $phone_number, $hashed_password);

    } elseif ($user_type === "manager") {
        $hospital_name = $_POST['hospital_name'];
        $hospital_license_number = $_POST['hospital_license_number'];

        // Insert into Managers table
        $sql = "INSERT INTO Managers (Name, Mobile_No, Password, Hospital_Name, Hospital_License_Number) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $phone_number, $hashed_password, $hospital_name, $hospital_license_number);

    } elseif ($user_type === "admin") {
        // Insert into Admins table
        $sql = "INSERT INTO Admins (Name, Mobile_No, Password) 
                VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $phone_number, $hashed_password);
    }

    if ($stmt->execute()) {
        echo "Registration successful!";
        header("Location: ../html_Code/login.html");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
