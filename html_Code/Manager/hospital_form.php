<?php
session_start();
include '../../Php_Code/db_connection.php'; // Database connection

if (!isset($_SESSION['manager_mobile_no'])) {
    die("Error: User not logged in.");
}

$manager_mobile_no = $_SESSION['manager_mobile_no'];
$message = "";

// Handle form submission for hospital registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_hospital'])) {
    // Retrieve form data
    $doctors = $_POST['doctors'];
    $dr_categories = $_POST['dr_categories'];
    $visit_days = implode(", ", $_POST['visit_day_start']);
    $visit_time_start = $_POST['visit_time_start'];
    $visit_time_end = $_POST['visit_time_end'];
    $visit_money = $_POST['visit_money'];

    // Get hospital info from hospital_server table based on manager's mobile number
    $stmt = $conn->prepare("SELECT * FROM hospital_server WHERE Manager_Mobile_No = ?");
    $stmt->bind_param("s", $manager_mobile_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch data from hospital_server
        $hospital_info = $result->fetch_assoc();

        // Extract hospital details from hospital_server
        $hospital_name = $hospital_info['Hospital_Name'];
        $hospital_license = $hospital_info['Hospital_License'];
        $hospital_mobile_no = $hospital_info['Hospital_Mobile'];
        $hospital_location = $hospital_info['Location'];

        // Check if hospital details already exist in the hospitals table
        $stmt_check = $conn->prepare("SELECT * FROM hospitals WHERE Manager_Mobile_No = ?");
        $stmt_check->bind_param("s", $manager_mobile_no);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows === 0) {
            // Insert data into the hospitals table
            $stmt_insert = $conn->prepare("INSERT INTO hospitals (Hospital_Name, Hospital_License, Manager_Mobile_No, Location, Doctors, Dr_Categories, Visit_Day_Start, Visit_Time_Start, Visit_Time_End, Visit_Money, Hospital_Mobile_Number, Is_Accepted)
                                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt_insert->bind_param("sssssssssss", $hospital_name, $hospital_license, $manager_mobile_no, $hospital_location, $doctors, $dr_categories, $visit_days, $visit_time_start, $visit_time_end, $visit_money, $hospital_mobile_no);

            try {
                $stmt_insert->execute();
                $message = "Hospital registered successfully!";
            } catch (mysqli_sql_exception $e) {
                $message = "Error: " . $e->getMessage();
            }
        } else {
            $message = "Hospital details already exist for this manager.";
        }
    } else {
        $message = "Manager not found in the hospital_server table.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css_Code/style.css">
    <title>Hospital Registration</title>
</head>
<body>
    <div class="container">
        <form action="" method="POST" class="form">
            <h2>Register Hospital</h2>

            <!-- Display Message -->
            <?php if ($message) echo "<p style='color:green;'>$message</p>"; ?>

            <!-- Doctors Input -->
            <label for="doctors">Doctors:</label>
            <input type="text" id="doctors" name="doctors" placeholder="Enter doctor names (comma-separated)" required>

            <!-- Doctor Categories Dropdown -->
            <label for="dr_categories">Doctor Categories:</label>
            <select id="dr_categories" name="dr_categories" required>
                <option value="Gynecologist">Gynecologist (Women's health doctor)</option>
                <option value="Cardiologist">Cardiologist (Heart doctor)</option>
                <option value="Nephrologist">Nephrologist (Kidney doctor)</option>
                <option value="Dermatologist">Dermatologist (Skin doctor)</option>
                <option value="Neurologist">Neurologist (Brain and nerves doctor)</option>
                <option value="Pediatrician">Pediatrician (Children's doctor)</option>
                <option value="Orthopedic Surgeon">Orthopedic Surgeon (Bones and joints doctor)</option>
                <option value="Gastroenterologist">Gastroenterologist (Stomach and digestion doctor)</option>
                <option value="Endocrinologist">Endocrinologist (Hormones doctor)</option>
                <option value="Oncologist">Oncologist (Cancer doctor)</option>
            </select>

            <!-- Visit Days Checkboxes -->
            <label>Visit Days:</label>
            <div class="checkbox-group">
                <label><input type="checkbox" name="visit_day_start[]" value="Monday"> Monday</label>
                <label><input type="checkbox" name="visit_day_start[]" value="Tuesday"> Tuesday</label>
                <label><input type="checkbox" name="visit_day_start[]" value="Wednesday"> Wednesday</label>
                <label><input type="checkbox" name="visit_day_start[]" value="Thursday"> Thursday</label>
                <label><input type="checkbox" name="visit_day_start[]" value="Friday"> Friday</label>
                <label><input type="checkbox" name="visit_day_start[]" value="Saturday"> Saturday</label>
                <label><input type="checkbox" name="visit_day_start[]" value="Sunday"> Sunday</label>
            </div>

            <!-- Visit Start Time -->
            <label for="visit_time_start">Visit Start Time:</label>
            <input type="time" id="visit_time_start" name="visit_time_start" required>

            <!-- Visit End Time -->
            <label for="visit_time_end">Visit End Time:</label>
            <input type="time" id="visit_time_end" name="visit_time_end" required>

            <!-- Visit Money -->
            <label for="visit_money">Visit Money:</label>
            <input type="number" id="visit_money" name="visit_money" placeholder="Enter consultation fee" required>

            <button type="submit" name="register_hospital">Register</button>
        </form>
    </div>
</body>
</html>
