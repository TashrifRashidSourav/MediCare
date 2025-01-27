

<?php include 'navbar.php'; ?>

<link rel="stylesheet" href="navbar.css">
<?php
session_start();
include '../../Php_Code/db_connection.php';

// Ensure the user is logged in and has a valid doctor ID
if (!isset($_SESSION['doctor_id'])) {
    die("Access denied. Please log in as a doctor."); // Redirect to login page if not logged in
}

// Fetch logged-in doctor's information
$doctor_id = $_SESSION['doctor_id'];
$doctor_query = "SELECT * FROM doctors WHERE d_id = ?";
$doctor_stmt = $conn->prepare($doctor_query);
$doctor_stmt->bind_param("i", $doctor_id);
$doctor_stmt->execute();
$doctor = $doctor_stmt->get_result()->fetch_assoc();

if (!$doctor) {
    die("Doctor information not found. Please contact support.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Search for patient by mobile number
    if (isset($_POST['search_patient'])) {
        $mobile_no = $_POST['patient_mobile'];
        $query = "SELECT * FROM patients WHERE Mobile_No = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $mobile_no);
        $stmt->execute();
        $patient_result = $stmt->get_result()->fetch_assoc();

        // Fetch patient history
        $history_query = "SELECT * FROM patients_history WHERE Patient_Mobile_No = ?";
        $history_stmt = $conn->prepare($history_query);
        $history_stmt->bind_param("s", $mobile_no);
        $history_stmt->execute();
        $history_result = $history_stmt->get_result();
    }

    // Add new history entry
    if (isset($_POST['add_history'])) {
        $mobile_no = $_POST['patient_mobile'];
        $weight = $_POST['weight'];
        $age = $_POST['age'];
        $blood_group = $_POST['blood_group'];
        $symptoms = $_POST['symptoms'];
        $diagnosis = $_POST['diagnosis'];
        $medical_test = $_POST['medical_test'];

        // Loop through medicines
        $medicines = $_POST['medicine'];
        $times = $_POST['times'];
        $dosages = $_POST['dosage'];
        $durations = $_POST['duration'];
        $next_visits = $_POST['next_visit'];

        foreach ($medicines as $index => $medicine) {
            $insert_query = "INSERT INTO patients_history (Patient_Mobile_No, Weight, Age, Blood_Group, Symptoms, Diagnosis, Medical_Test, Medicine, Times, Dosage, Duration, Next_Visit, Doctor_Name, Doctor_Category)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param(
                "sdisssssssssss",
                $mobile_no,
                $weight,
                $age,
                $blood_group,
                $symptoms,
                $diagnosis,
                $medical_test,
                $medicine,
                $times[$index],
                $dosages[$index],
                $durations[$index],
                $next_visits[$index],
                $doctor['Name'],
                $doctor['Dr_Categories']
            );

            if (!$stmt->execute()) {
                echo "<div class='error-message'>Error: " . $stmt->error . "</div>";
            }
        }
        echo "<div class='success-message'>History added successfully!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2, h3 {
            text-align: center;
            color: #444;
        }

        form {
            margin-bottom: 20px;
        }

        form input, form select, form textarea, form button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .medicine-group {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 4px;
            background: #f9f9f9;
        }

        .medicine-label {
            font-weight: bold;
            margin-bottom: 10px;
            display: block;
        }

        .checkbox-group {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .checkbox-group label {
            display: flex;
            align-items: center;
            background: #f4f4f4;
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            cursor: pointer;
        }

        .checkbox-group input {
            margin-right: 5px;
        }

        .add-more {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            display: inline-block;
            margin-top: 10px;
        }

        .add-more:hover {
            background-color: #0056b3;
        }

        form button {
            background-color: #5cb85c;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        form button:hover {
            background-color: #4cae4c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #f9f9f9;
        }

        .success-message {
            background-color: #dff0d8;
            color: #3c763d;
            padding: 10px;
            border: 1px solid #d6e9c6;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .error-message {
            background-color: #f2dede;
            color: #a94442;
            padding: 10px;
            border: 1px solid #ebccd1;
            margin-bottom: 20px;
            border-radius: 4px;
        }
    </style>
    <script>
        function addMedicineField() {
            const container = document.getElementById('medicine-container');
            const medicineGroup = document.createElement('div');
            medicineGroup.className = 'medicine-group';

            medicineGroup.innerHTML = `
                <label class="medicine-label">Medicine Details:</label>
                <input type="text" name="medicine[]" placeholder="Medicine name" required>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="times[]" value="Morning"> Morning</label>
                    <label><input type="checkbox" name="times[]" value="Noon"> Noon</label>
                    <label><input type="checkbox" name="times[]" value="Night"> Night</label>
                    <label><input type="checkbox" name="times[]" value="After Meal"> After Meal</label>
                    <label><input type="checkbox" name="times[]" value="Before Meal"> Before Meal</label>
                </div>
                <input type="text" name="dosage[]" placeholder="Dosage (e.g., 2 tablets)" required>
                <input type="text" name="duration[]" placeholder="Duration (e.g., 5 days)" required>
             
            `;

            container.appendChild(medicineGroup);
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Doctor Dashboard</h2>
        <h3>Welcome, Dr. <?= htmlspecialchars($doctor['Name']) ?> (<?= htmlspecialchars($doctor['Dr_Categories']) ?>)</h3>

        <!-- Search Patient -->
        <form method="POST">
            <label for="patient_mobile">Search Patient by Mobile Number:</label>
            <input type="text" name="patient_mobile" id="patient_mobile" placeholder="Enter patient's mobile number" required>
            <button type="submit" name="search_patient">Search</button>
        </form>

        <!-- Patient Information and History -->
        <?php if (isset($patient_result) && $patient_result): ?>
            <h3>Patient Information:</h3>
            <p><strong>Name:</strong> <?= htmlspecialchars($patient_result['Name']) ?></p>
            <p><strong>Mobile Number:</strong> <?= htmlspecialchars($patient_result['Mobile_No']) ?></p>

            <h3>Patient History:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Age</th>
                        <th>Blood Group</th>
                        <th>Weight</th>
                        <th>Symptoms</th>
                        <th>Diagnosis</th>
                        <th>Medical Test</th>
                        <th>Medicine</th>
                        <th>Times</th>
                        <th>Dosage</th>
                        <th>Duration</th>
                        <th>Next Visit</th>
                        <th>Doctor</th>
                        <th>Category</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $history_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['Form_Fill_Date']) ?></td>
                            <td><?= htmlspecialchars($row['Age']) ?></td>
                            <td><?= htmlspecialchars($row['Blood_Group']) ?></td>
                            <td><?= htmlspecialchars($row['Weight']) ?></td>
                            <td><?= htmlspecialchars($row['Symptoms']) ?></td>
                            <td><?= htmlspecialchars($row['Diagnosis']) ?></td>
                            <td><?= htmlspecialchars($row['Medical_Test']) ?></td>
                            <td><?= htmlspecialchars($row['Medicine']) ?></td>
                            <td><?= htmlspecialchars($row['Times']) ?></td>
                            <td><?= htmlspecialchars($row['Dosage']) ?></td>
                            <td><?= htmlspecialchars($row['Duration']) ?></td>
                            <td><?= htmlspecialchars($row['Next_Visit']) ?></td>
                            <td><?= htmlspecialchars($row['Doctor_Name']) ?></td>
                            <td><?= htmlspecialchars($row['Doctor_Category']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Add New History Entry -->
            <h3>Add New History Entry:</h3>
            <form method="POST">
                <input type="hidden" name="patient_mobile" value="<?= htmlspecialchars($patient_result['Mobile_No']) ?>">
                <label for="weight">Weight:</label>
                <input type="number" name="weight" id="weight" placeholder="Enter weight (kg)" required>

                <label for="age">Age:</label>
                <input type="number" name="age" id="age" placeholder="Enter age" required>

                <label for="blood_group">Blood Group:</label>
                <select name="blood_group" id="blood_group" required>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                </select>

                <label for="symptoms">Symptoms:</label>
                <textarea name="symptoms" id="symptoms" placeholder="Enter symptoms" required></textarea>

                <label for="diagnosis">Diagnosis:</label>
                <textarea name="diagnosis" id="diagnosis" placeholder="Enter diagnosis" required></textarea>

                <label for="medical_test">Medical Test:</label>
                <textarea name="medical_test" id="medical_test" placeholder="Enter medical tests"></textarea>

                <div id="medicine-container">
                    <div class="medicine-group">
                        <label class="medicine-label">Medicine Details:</label>
                        <input type="text" name="medicine[]" placeholder="Medicine name" required>
                        <div class="checkbox-group">
                            <label><input type="checkbox" name="times[]" value="Morning"> Morning</label>
                            <label><input type="checkbox" name="times[]" value="Noon"> Noon</label>
                            <label><input type="checkbox" name="times[]" value="Night"> Night</label>
                            <label><input type="checkbox" name="times[]" value="After Meal"> After Meal</label>
                            <label><input type="checkbox" name="times[]" value="Before Meal"> Before Meal</label>
                        </div>
                        <input type="text" name="dosage[]" placeholder="Dosage (e.g., 2 tablets)" required>
                        <input type="text" name="duration[]" placeholder="Duration (e.g., 5 days)" required>
                        <input type="text" name="next_visit[]" placeholder="Next Visit (e.g., 7 days)" required>
                    </div>
                </div>

                <button type="button" class="add-more" onclick="addMedicineField()">+ Add Medicine</button>
                <button type="submit" name="add_history">Submit</button>
            </form>
        <?php elseif (isset($patient_result)): ?>
            <p>No patient found with this mobile number.</p>
        <?php endif; ?>
    </div>
</body>
</html>
