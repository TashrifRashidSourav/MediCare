<?php
session_start();
include '../../Php_Code/db_connection.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patientId = $_SESSION['patient_id']; // Assume patient_id is stored in session
    $hospitalId = $_POST['hospital_id'];
    $reviewText = $_POST['review_text'];
    $image = null;

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
        $message = "Error submitting review: " . $conn->error;
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Write a Review for a Hospital</h2>
        <?php if (!empty($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form action="review.php" method="post" enctype="multipart/form-data">
            <label for="hospital_id">Select Hospital:</label>
            <select name="hospital_id" id="hospital_id" required>
                <?php
                $query = "SELECT id, Hospital_Name FROM hospitals";
                $result = $conn->query($query);
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['id'] . "'>" . $row['Hospital_Name'] . "</option>";
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
