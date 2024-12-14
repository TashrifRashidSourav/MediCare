<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'];
    $password = $_POST['password'];
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
        $stmt->bind_param("ssssss", $name, $license_no, $expertise, $password, $email, $details);

    } elseif ($user_type === "patient") {
        $phone_number = $_POST['phone_number'];

        // Insert into Patients table
        $sql = "INSERT INTO Patients (Name, Mobile_No, Password) 
                VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $phone_number, $password);

    } elseif ($user_type === "manager") {
        $phone_number = $_POST['phone_number'];

        // Insert into Managers table
        $sql = "INSERT INTO Managers (Name, Mobile_No, Password, Is_Accepted) 
                VALUES (?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $phone_number, $password);
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
