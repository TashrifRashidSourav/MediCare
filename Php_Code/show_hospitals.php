<?php
session_start();
include 'db_connection.php';

// Check if manager is logged in
if (!isset($_SESSION['manager_mobile_no'])) {
    die("Access Denied.");
}

$manager_mobile_no = $_SESSION['manager_mobile_no'];

// Fetch hospitals
$sql = "SELECT Hospital_Name, Hospital_License FROM Hospital_Server WHERE Manager_Mobile_No = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $manager_mobile_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>Hospital: " . htmlspecialchars($row['Hospital_Name']) . " - License: " . htmlspecialchars($row['Hospital_License']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No hospitals added yet.</p>";
}
?>
