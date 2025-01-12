<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];
    $hashed_password = password_hash($password, PASSWORD_BCRYPT); // Hash the password
    $email = $_POST['email'];
    $user_type = $_POST['user_type'];

    // Common fields for all users
    $license_no = null;
    $dr_category = null;
    $expertise = null;
    $details = null;
    $hospital_name = null;
    $hospital_license_number = null;

    // Populate fields based on user type
    if ($user_type === "doctor") {
        $license_no = $_POST['license_no'] ?? null;
        $dr_category = $_POST['dr_categories'] ?? null;
        $expertise = $_POST['expertise'] ?? null;
        $details = $_POST['details'] ?? null;
    } elseif ($user_type === "manager") {
        $hospital_name = $_POST['hospital_name'] ?? null;
        $hospital_license_number = $_POST['hospital_license_number'] ?? null;
        $expertise = "Manager at $hospital_name (License: $hospital_license_number)";
    } elseif ($user_type === "patient") {
        $expertise = "Patient";
    } else {
        echo "Invalid user type.";
        exit;
    }

    // Insert into Doctors table (common table for all user types)
    $sql = "INSERT INTO doctors (Name, License_No, Dr_Categories, Expertise, Password, Email, Details, Mobile_No, Is_Accepted)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $name, $license_no, $dr_category, $expertise, $hashed_password, $email, $details, $phone_number);

    // Execute the query
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
