<?php
// Start session and include database connection
session_start();
include '../../Php_Code/db_connection.php';

// Check if the manager is logged in
if (!isset($_SESSION['manager_mobile_no'])) {
    header("Location: ../../html_Code/login.php?error=not_logged_in");
    exit();
}

$manager_mobile_no = $_SESSION['manager_mobile_no'];
$message = "";

// Fetch all appointments for the manager's hospital
$sql_appointments = "SELECT appointment_id, Patient_Name, Patient_Mobile_No, Doctor_Name, Hospital_Name, Appointment_Date, Appointment_Time, Serial_Number, Is_Accepted, Calling_Time 
                     FROM appointments 
                     WHERE Manager_Mobile_No = ?";
$stmt = $conn->prepare($sql_appointments);
$stmt->bind_param("s", $manager_mobile_no);
$stmt->execute();
$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle Edit Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_appointment_submit'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $serial_number = intval($_POST['serial_number']);
    $calling_time = $_POST['calling_time'];
    $is_accepted = $_POST['is_accepted'];

    $sql_update = "UPDATE appointments 
                   SET Serial_Number = ?, Calling_Time = ?, Is_Accepted = ? 
                   WHERE appointment_id = ? AND Manager_Mobile_No = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("issis", $serial_number, $calling_time, $is_accepted, $appointment_id, $manager_mobile_no);

    if ($stmt_update->execute()) {
        $message = "Appointment updated successfully!";
    } else {
        $message = "Error updating appointment: " . $stmt_update->error;
    }

    $stmt_update->close();
    // Refresh appointments after update
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
    <title>Manage Appointments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1100px;
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
        .edit-form {
            display: flex;
            gap: 10px;
        }
        .edit-form input, .edit-form select {
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
            width: 100%;
        }
        .edit-form button {
            padding: 5px 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .edit-form button:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Appointments</h2>
        <?php if (!empty($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <?php if (empty($appointments)): ?>
            <p>No appointments found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>Patient Contact</th>
                        <th>Doctor</th>
                        <th>Hospital</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Serial Number</th>
                        <th>Status</th>
                        <th>Calling Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td><?= htmlspecialchars($appointment['Patient_Name']) ?></td>
                            <td><?= htmlspecialchars($appointment['Patient_Mobile_No']) ?></td>
                            <td><?= htmlspecialchars($appointment['Doctor_Name']) ?></td>
                            <td><?= htmlspecialchars($appointment['Hospital_Name']) ?></td>
                            <td><?= htmlspecialchars($appointment['Appointment_Date']) ?></td>
                            <td><?= htmlspecialchars($appointment['Appointment_Time']) ?></td>
                            <td><?= htmlspecialchars($appointment['Serial_Number'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars(ucfirst($appointment['Is_Accepted'] ?? 'Pending')) ?></td>
                            <td><?= htmlspecialchars($appointment['Calling_Time'] ?? 'Pending') ?></td>
                            <td>
                                <form method="POST" class="edit-form">
                                    <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment['appointment_id']) ?>">
                                    <input type="number" name="serial_number" value="<?= htmlspecialchars($appointment['Serial_Number']) ?>" placeholder="Serial" required>
                                    <input type="time" name="calling_time" value="<?= htmlspecialchars($appointment['Calling_Time']) ?>" required>
                                    <select name="is_accepted" required>
                                        <option value="pending" <?= $appointment['Is_Accepted'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="accepted" <?= $appointment['Is_Accepted'] === 'accepted' ? 'selected' : '' ?>>Accepted</option>
                                    </select>
                                    <button type="submit" name="edit_appointment_submit">Update</button>
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
