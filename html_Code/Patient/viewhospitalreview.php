<?php include 'navbar.php'; ?>
<link rel="stylesheet" href="navbar.css">
<?php
include '../../Php_Code/db_connection.php';

$reviews = [];
$search_error = "";

// Handle search functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_hospital'])) {
    $hospital_name = trim($_POST['hospital_name']);

    // Step 1: Find the hospital by name, ensuring unique names
    $hospital_query = "SELECT DISTINCT id, Hospital_Name, Location FROM hospitals WHERE Hospital_Name LIKE ?";
    $stmt = $conn->prepare($hospital_query);
    $search_term = '%' . $hospital_name . '%';
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $hospital_result = $stmt->get_result();

    if ($hospital_result->num_rows > 0) {
        $hospital_reviews = [];
        while ($row = $hospital_result->fetch_assoc()) {
            $hospital_reviews[] = $row;
        }

        $review_query = "
            SELECT hr.Review_Id, h.Hospital_Name, hr.Review_Text, hr.Image, hr.Created_At, h.Location
            FROM hospital_reviews hr
            JOIN hospitals h ON hr.Hospital_Id = h.id
            WHERE hr.Is_Accepted = 'accepted' AND hr.Hospital_Id IN (
                SELECT id FROM hospitals WHERE Hospital_Name LIKE ?
            )
        ";

        $stmt = $conn->prepare($review_query);
        $stmt->bind_param("s", $search_term);
        $stmt->execute();
        $result = $stmt->get_result();
        $reviews = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        $search_error = "No hospitals found with the provided name.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hospital Reviews</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #444;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        form input[type="text"] {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 300px;
        }

        form button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 15px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #0056b3;
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

        .no-reviews {
            text-align: center;
            margin-top: 20px;
            color: #888;
        }

        .review-details {
            color: #555;
            font-style: italic;
        }

        .hospital-name {
            font-weight: bold;
            font-size: 18px;
            color: #007bff;
        }

        .location {
            font-size: 14px;
            color: #666;
        }

        .review-image {
            max-width: 100px;
            border-radius: 5px;
        }

        .error-message {
            text-align: center;
            color: #a94442;
            background-color: #f2dede;
            padding: 10px;
            border: 1px solid #ebccd1;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hospital Reviews</h2>

        <!-- Search Form -->
        <form method="POST">
            <input type="text" name="hospital_name" placeholder="Search by hospital name" required>
            <button type="submit" name="search_hospital">Search</button>
        </form>

        <!-- Error Message -->
        <?php if (!empty($search_error)): ?>
            <div class="error-message"><?= htmlspecialchars($search_error) ?></div>
        <?php endif; ?>

        <!-- Reviews Table -->
        <?php if (!empty($reviews)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Hospital Name</th>
                        <th>Location</th>
                        <th>Review</th>
                        <th>Image</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td class="hospital-name"><?= htmlspecialchars($review['Hospital_Name']) ?></td>
                            <td class="location"><?= htmlspecialchars($review['Location']) ?></td>
                            <td class="review-details"><?= htmlspecialchars($review['Review_Text']) ?></td>
                            <td>
                                <?php if ($review['Image']): ?>
                                    <img src="data:image/jpeg;base64,<?= base64_encode($review['Image']) ?>" class="review-image" alt="Review Image">
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($review['Created_At']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif (empty($search_error)): ?>
            <p class="no-reviews">No reviews found for the selected hospital.</p>
        <?php endif; ?>
    </div>
</body>
</html>
