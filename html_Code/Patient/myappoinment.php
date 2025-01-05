<?php
// Start session and include database connection
session_start();
include '../../Php_Code/db_connection.php';

// Check if the patient is logged in
if (!isset($_SESSION['patient_id'])) {
    header("Location: ../../html_Code/login.php?error=not_logged_in");
    exit();
}

$patient_id = $_SESSION['patient_id'];
$message = "";

// Fetch patient appointments
$sql_appointments = "SELECT appointment_id, Doctor_Name, Hospital_Name, Appointment_Date, Appointment_Time, Serial_Number, Is_Accepted, Calling_Time, Manager_Name, Manager_Mobile_No 
                     FROM appointments 
                     WHERE Patient_Id = ?";
$stmt = $conn->prepare($sql_appointments);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle Delete Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_appointment'])) {
    $appointment_id = intval($_POST['appointment_id']);

    $sql_delete = "DELETE FROM appointments WHERE appointment_id = ? AND Patient_Id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("ii", $appointment_id, $patient_id);

    if ($stmt_delete->execute()) {
        $message = "Appointment deleted successfully!";
    } else {
        $message = "Error deleting appointment: " . $stmt_delete->error;
    }

    $stmt_delete->close();
    // Refresh appointments after delete
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1000px;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .message {
            text-align: center;
            color: green;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #f4f4f4;
        }
        .actions button {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            background-color: #dc3545;
            color: #fff;
        }
        .actions button:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>My Appointments</h2>
        <?php if (!empty($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <?php if (empty($appointments)): ?>
            <p>No appointments found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Hospital</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Serial Number</th>
                        <th>Status</th>
                        <th>Calling Time</th>
                        <th>Manager</th>
                        <th>Manager Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?= htmlspecialchars($appointment['Doctor_Name']) ?></td>
                            <td><?= htmlspecialchars($appointment['Hospital_Name']) ?></td>
                            <td><?= htmlspecialchars($appointment['Appointment_Date']) ?></td>
                            <td><?= htmlspecialchars($appointment['Appointment_Time']) ?></td>
                            <td><?= htmlspecialchars($appointment['Serial_Number'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars(ucfirst($appointment['Is_Accepted'] ?? 'Pending')) ?></td>
                            <td><?= htmlspecialchars($appointment['Calling_Time'] ?? 'Pending') ?></td>
                            <td><?= htmlspecialchars($appointment['Manager_Name']) ?></td>
                            <td><?= htmlspecialchars($appointment['Manager_Mobile_No']) ?></td>
                            <td class="actions">
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment['appointment_id'] ?? '') ?>">
                                    <button type="submit" name="delete_appointment">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
