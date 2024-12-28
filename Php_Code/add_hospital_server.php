<?php
session_start();
include 'db_connection.php'; // Include your database connection script

// Check if manager is logged in
if (!isset($_SESSION['manager_mobile_no'])) {
    die("Error: User not logged in.");
}

$manager_mobile_no = $_SESSION['manager_mobile_no'];
$message = "";

// Handle form submission to add hospitals
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['hospital_name']) && isset($_POST['hospital_license'])) {
        $hospital_names = $_POST['hospital_name'];
        $hospital_licenses = $_POST['hospital_license'];

        // Prepare SQL query to insert hospital records
        $stmt = $conn->prepare("INSERT INTO Hospitals (Hospital_Name, Hospital_License, Hospital_Mobile_Number, Is_Accepted) 
                                VALUES (?, ?, ?, 'pending')");

        try {
            for ($i = 0; $i < count($hospital_names); $i++) {
                $hospital_name = $hospital_names[$i];
                $hospital_license = $hospital_licenses[$i];

                // Insert into database
                $stmt->bind_param("sss", $hospital_name, $hospital_license, $manager_mobile_no);
                $stmt->execute();
            }
            $message = "Hospitals added successfully!";
        } catch (mysqli_sql_exception $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        $message = "Error: Missing hospital data.";
    }
}

// Fetch hospitals created by the current manager
$stmt = $conn->prepare("SELECT Hospital_ID, Hospital_Name, Hospital_License, Is_Accepted 
                        FROM Hospitals WHERE Hospital_Mobile_Number = ?");
$stmt->bind_param("s", $manager_mobile_no);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add and View Hospitals</title>
    <link rel="stylesheet" href="../../css_Code/add_hospital_server.css">
</head>
<body>
    <div class="container">
        <h1>Add Hospital</h1>
        <!-- Display message -->
        <?php if (!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>

        <!-- Hospital Form -->
        <div id="hospital_forms">
            <form method="POST" id="submit_form">
                <div class="hospital-form">
                    <label for="hospital_name">Hospital Name:</label>
                    <input type="text" name="hospital_name[]" required>
                    
                    <label for="hospital_license">Hospital License Number:</label>
                    <input type="text" name="hospital_license[]" required>
                </div>
                <!-- Add New Hospital Button -->
                <button type="button" id="add_new_hospital">Add New Hospital</button>
                <!-- Submit All Button -->
                <button type="submit" id="submit_all">Submit All</button>
            </form>
        </div>

        <!-- Show Hospitals -->
        <h2>Your Added Hospitals</h2>
        <div id="hospital_list">
            <?php
            if ($result->num_rows > 0) {
                echo "<table border='1'>";
                echo "<tr><th>Hospital Name</th><th>License Number</th><th>Status</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['Hospital_Name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Hospital_License']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Is_Accepted']) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>No hospitals found.</p>";
            }
            ?>
        </div>
    </div>

    <script>
        // Add New Hospital Form Dynamically
        document.getElementById('add_new_hospital').addEventListener('click', function () {
            const formContainer = document.querySelector('#hospital_forms form');
            const newHospitalForm = document.createElement('div');
            newHospitalForm.classList.add('hospital-form');
            newHospitalForm.innerHTML = `
                <label for="hospital_name">Hospital Name:</label>
                <input type="text" name="hospital_name[]" required>
                
                <label for="hospital_license">Hospital License Number:</label>
                <input type="text" name="hospital_license[]" required>
            `;
            formContainer.appendChild(newHospitalForm);
        });
    </script>
</body>
</html>
