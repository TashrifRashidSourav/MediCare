<?php
session_start();
include '../../Php_Code/db_connection.php';

$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patientId = $_SESSION['patient_id']; // Assume patient_id is stored in session
    $hospitalId = isset($_POST['hospital_id']) ? intval($_POST['hospital_id']) : null;
    $reviewText = $_POST['review_text'];
    $image = null;

    // Validate inputs
    if (is_null($hospitalId)) {
        $message = "Error: Please select a valid hospital.";
    } else {
        // Handle image upload
        if (isset($_FILES['review_image']) && $_FILES['review_image']['error'] == 0) {
            $image = file_get_contents($_FILES['review_image']['tmp_name']);
        }

        // Insert the review into the database
        $sql = "INSERT INTO hospital_reviews (Patient_Id, Hospital_Id, Review_Text, Image, Is_Accepted)
                VALUES (?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $patientId, $hospitalId, $reviewText, $image);

        if ($stmt->execute()) {
            $message = "Review submitted successfully! Pending admin approval.";
        } else {
            $message = "Error submitting review: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write a Review</title>
    <?php include 'navbar.php'; ?>
    <link rel="stylesheet" href="navbar.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
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

        form button {
            background-color: #5cb85c;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        form button:hover {
            background-color: #4cae4c;
        }

        .message {
            text-align: center;
            color: green;
            margin-bottom: 15px;
        }

        .error {
            text-align: center;
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Write a Review for a Hospital</h2>
        <?php if (!empty($message)): ?>
            <p class="<?= strpos($message, 'Error') !== false ? 'error' : 'message' ?>">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>
        <form action="review.php" method="post" enctype="multipart/form-data">
            <label for="hospital_id">Select Hospital:</label>
            <select name="hospital_id" id="hospital_id" required>
                <option value="">Select a hospital</option>
                <?php
                $query = "SELECT id, Hospital_Name FROM hospitals";
                $result = $conn->query($query);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['Hospital_Name']) . "</option>";
                    }
                } else {
                    echo "<option value=''>No hospitals available</option>";
                }
                ?>
            </select>

            <label for="review_text">Your Review:</label>
            <textarea name="review_text" id="review_text" rows="5" required></textarea>

            <label for="review_image">Upload an Image:</label>
            <input type="file" name="review_image" id="review_image" accept="image/*">

            <button type="submit">Submit Review</button>
        </form>
    </div>
</body>
</html>
