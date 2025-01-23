<?php include 'navbar.php'; ?>
<link rel="stylesheet" href="navbar.css">
<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../../Php_Code/db_connection.php';


$doctor_license = $_SESSION['doctor_license']; 


if (empty($doctor_license)) {
    echo "Please log in as a doctor.";
    exit;
}


$today_date = date('Y-m-d');

$query_doctor = "SELECT * FROM doctors WHERE License_No = ?";
$stmt_doctor = $conn->prepare($query_doctor);
$stmt_doctor->bind_param("s", $doctor_license);
$stmt_doctor->execute();
$result_doctor = $stmt_doctor->get_result();


if ($result_doctor->num_rows == 0) {
    echo "Doctor not found.";
    exit;
}

$doctor = $result_doctor->fetch_assoc(); 


$query_hospital = "SELECT * FROM hospitals WHERE Doctor_License_Number = ?";
$stmt_hospital = $conn->prepare($query_hospital);
$stmt_hospital->bind_param("s", $doctor_license);
$stmt_hospital->execute();
$result_hospital = $stmt_hospital->get_result();


if ($result_hospital->num_rows == 0) {
    echo "Doctor's license not found in the hospital records.";
    exit;
}

$hospital = $result_hospital->fetch_assoc(); 

$query_appointments = "SELECT a.Appointment_Id, a.Patient_Name, a.Patient_Mobile_No, a.Appointment_Date, a.Appointment_Time, a.Is_Accepted
          FROM appointments a
          WHERE a.Doctor_License_Number = ? AND a.Appointment_Date = ?";


$stmt_appointments = $conn->prepare($query_appointments);
$stmt_appointments->bind_param("ss", $doctor_license, $today_date);
$stmt_appointments->execute();


$result_appointments = $stmt_appointments->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Appointments</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Today's Appointments for Dr. <?php echo htmlspecialchars($doctor['Name']); ?> (<?php echo htmlspecialchars($doctor['License_No']); ?>)</h2>

    <h3>Hospital: <?php echo htmlspecialchars($hospital['Hospital_Name']); ?> (<?php echo htmlspecialchars($hospital['Hospital_License']); ?>)</h3>


    <?php if ($result_appointments->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Patient Mobile</th>
                    <th>Appointment Date</th>
                    <th>Appointment Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result_appointments->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['Patient_Name']); ?></td>
                        <td><?php echo htmlspecialchars($row['Patient_Mobile_No']); ?></td>
                        <td><?php echo htmlspecialchars($row['Appointment_Date']); ?></td>
                        <td><?php echo htmlspecialchars($row['Appointment_Time']); ?></td>
                        <td><?php echo htmlspecialchars($row['Is_Accepted']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No appointments for today.</p>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

</body>
</html>

<?php
// Close the connection
$conn->close();
?>
