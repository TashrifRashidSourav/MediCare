<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];

    // Determine the table based on user type
    $table = ucfirst($user_type) . 's';

    // Insert the user into the respective table
    $sql = "INSERT INTO $table (Name, Mobile_No, Password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $phone_number, $password);

    if ($stmt->execute()) {
        header("Location: ../html_Code/login.html");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
