<?php
session_start();
include '../../Php_Code/db_connection.php'; // Corrected path to db_connection.php

if (!isset($_SESSION['manager_mobile_no'])) {
    die("Error: User not logged in.");
}

$manager_mobile_no = $_SESSION['manager_mobile_no'];
$message = "";

// Handle form submission to add hospitals
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST['hospital_name'], $_POST['hospital_license'], $_POST['hospital_mobile'], $_POST['location']) &&
        count($_POST['hospital_name']) === count($_POST['hospital_license']) &&
        count($_POST['hospital_name']) === count($_POST['hospital_mobile']) &&
        count($_POST['hospital_name']) === count($_POST['location'])
    ) {
        $hospital_names = $_POST['hospital_name'];
        $hospital_licenses = $_POST['hospital_license'];
        $hospital_mobiles = $_POST['hospital_mobile'];
        $locations = $_POST['location'];

        // Prepare SQL query to check for duplicate license
        $stmt_check_duplicate = $conn->prepare("SELECT Hospital_License FROM hospital_server WHERE Hospital_License = ?");
        
        // Prepare SQL query to insert hospital records
        $stmt_insert = $conn->prepare("INSERT INTO hospital_server (Hospital_Name, Hospital_License, Manager_Mobile_No, Hospital_Mobile, Location, Is_Accepted) 
                                      VALUES (?, ?, ?, ?, ?, 'pending')");

        try {
            for ($i = 0; $i < count($hospital_names); $i++) {
                $hospital_name = $hospital_names[$i];
                $hospital_license = $hospital_licenses[$i];
                $hospital_mobile = $hospital_mobiles[$i];
                $location = $locations[$i];

                // Check if the hospital license already exists
                $stmt_check_duplicate->bind_param("s", $hospital_license);
                $stmt_check_duplicate->execute();
                $result_check = $stmt_check_duplicate->get_result();

                if ($result_check->num_rows > 0) {
                    $message = "Error: Duplicate hospital license found.";
                } else {
                    // Insert into database if no duplicate found
                    $stmt_insert->bind_param("sssss", $hospital_name, $hospital_license, $manager_mobile_no, $hospital_mobile, $location);
                    $stmt_insert->execute();
                    $message = "Hospital added successfully!";
                }
            }

            // Redirect to avoid duplicate submissions after refreshing
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (mysqli_sql_exception $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        $message = "Error: Missing or invalid hospital data.";
    }
}

// Fetch hospitals created by the current manager
$stmt = $conn->prepare("SELECT Hospital_ID, Hospital_Name, Hospital_License, Hospital_Mobile, Location, Is_Accepted 
                        FROM hospital_server WHERE Manager_Mobile_No = ?");
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
    <style>
     
  /* Navbar Styling */
  .navbar {
            background-color: #333; /* Dark color for contrast */
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            position: sticky;
            top: 0;
            z-index: 1000;
            width: 100%;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        }

        .navbar-logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: #00d9ff; /* Vibrant blue */
            text-decoration: none;
        }

        .navbar-menu {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        .navbar-menu li {
            margin: 0;
        }
        body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 800px;
    margin: 20px auto;
    background: #ffffff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

h1, h2 {
    text-align: center;
    color: #333;
}

label {
    display: block;
    margin: 10px 0 5px;
    color: #555;
}

input {
    width: 100%;
    padding: 8px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

button {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background-color: #0056b3;
}

ul {
    list-style-type: none;
    padding: 0;
}

ul li {
    padding: 8px;
    background:#00d9ff;
    margin: 5px 0;
    border-radius: 5px;
}


        .navbar-menu a {
            text-decoration: none;
            color: white;
            font-weight: 500;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .navbar-menu a:hover {
            background-color: #00d9ff;
            color: #333;
        }

        /* Responsive Navbar */
        .navbar-toggle {
            display: none;
            flex-direction: column;
            justify-content: space-between;
            width: 30px;
            height: 20px;
            cursor: pointer;
        }

        .navbar-toggle .bar {
            height: 3px;
            width: 100%;
            background-color: white;
        }

        @media screen and (max-width: 768px) {
            .navbar-menu {
                flex-direction: column;
                gap: 10px;
                position: absolute;
                top: 60px;
                right: 0;
                background-color: #333;
                padding: 10px;
                display: none;
                width: 200px;
            }

            .navbar-menu.active {
                display: flex;
            }

            .navbar-toggle {
                display: flex;
            }
        }

        /* Form and Container Styling */
        .container {
            width: 90%;
            max-width: 800px;
            margin: 20px auto;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        .hospital-form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #00d9ff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px 0;
        }

        button:hover {
            background-color: #00a3cc;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #00d9ff;
            color: white;
        }

    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <a href="#" class="navbar-logo">MediCare</a>
        <ul class="navbar-menu">
            <li><a href="../../html_Code/Manager/add_hospital_server.php">Add Hospital</a></li>
            <li><a href="../../html_Code/Manager/hospital_form.html">Hospital Form</a></li>
            <li><a href="../../html_Code/Manager/managerDashboard.html">Dashboard</a></li>
            <li><a href="../../Php_Code/logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Container -->
    <div class="container">
        <h1>Add Hospital</h1>

        <!-- Display message -->
        <div id="message">
            <?php if (!empty($message)) echo "<p style='color:green;'>$message</p>"; ?>
        </div>

        <!-- Hospital Form -->
        <div id="hospital_forms">
            <form method="POST" id="submit_form">
                <div class="hospital-form">
                    <label for="hospital_name">Hospital Name:</label>
                    <input type="text" name="hospital_name[]" required>

                    <label for="hospital_license">Hospital License Number:</label>
                    <input type="text" name="hospital_license[]" required>

                    <label for="hospital_mobile">Hospital Mobile Number:</label>
                    <input type="text" name="hospital_mobile[]" required>

                    <label for="location">Location:</label>
                    <select name="location[]" required>
                        <option value="Dhaka">Dhaka</option>
                        <option value="Rajshahi">Rajshahi</option>
                        <option value="Chittagong">Chittagong</option>
                        <option value="Other">Other</option>
                    </select>
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
            // Display fetched hospital data
            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<tr><th>Hospital Name</th><th>License Number</th><th>Mobile Number</th><th>Location</th><th>Status</th></tr>";

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['Hospital_Name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Hospital_License']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Hospital_Mobile']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Location']) . "</td>";
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

                <label for="hospital_mobile">Hospital Mobile Number:</label>
                <input type="text" name="hospital_mobile[]" required>

                <label for="location">Location:</label>
                <select name="location[]" required>
                    <option value="Dhaka">Dhaka</option>
                    <option value="Rajshahi">Rajshahi</option>
                    <option value="Chittagong">Chittagong</option>
                    <option value="Other">Other</option>
                </select>
            `;
            formContainer.appendChild(newHospitalForm);
        });
    </script>
</body>
</html>
