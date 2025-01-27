
<?php include 'navbar.php'; ?>
<link rel="stylesheet" href="navbar.css">

<?php
// Connect to the database
$conn = mysqli_connect("localhost", "root", "", "medicare");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to get pending records
function getPendingRecords($conn, $table, $column) {
    $query = "SELECT * FROM $table WHERE $column = 'pending'";
    return mysqli_query($conn, $query);
}

// Retrieve pending data for all relevant tables
$doctors = getPendingRecords($conn, 'doctors', 'Is_Accepted');
$hospitals = getPendingRecords($conn, 'hospitals', 'Is_Accepted');
$hospital_reviews = getPendingRecords($conn, 'hospital_reviews', 'Is_Accepted');
$managers = getPendingRecords($conn, 'managers', 'Is_Accepted');

// Define primary key mapping for tables
$primaryKeys = [
    'doctors' => 'd_id',
    'hospitals' => 'id',
    'hospital_reviews' => 'Review_Id',
    'managers' => 'm_id'
];

// Handle update requests
$updateMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table = isset($_POST['table']) ? mysqli_real_escape_string($conn, $_POST['table']) : '';
    $id = isset($_POST['id']) ? mysqli_real_escape_string($conn, $_POST['id']) : '';
    $column = isset($_POST['column']) ? mysqli_real_escape_string($conn, $_POST['column']) : '';
    $status = isset($_POST['status']) ? mysqli_real_escape_string($conn, $_POST['status']) : '';

    if (!empty($table) && !empty($id) && !empty($column) && !empty($status) && isset($primaryKeys[$table])) {
        $primaryKey = $primaryKeys[$table];
        $query = "UPDATE $table SET $column = '$status' WHERE $primaryKey = '$id'";
        if (mysqli_query($conn, $query)) {
            $updateMessage = "Record updated successfully.";
        } else {
            $updateMessage = "Error updating record: " . mysqli_error($conn);
        }
    } else {
        $updateMessage = "Invalid input detected. Debug Info: Table = $table, ID = $id, Column = $column, Status = $status";
    }
}

$message = isset($updateMessage) ? $updateMessage : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 30px;
        }
        h1 {
            margin-bottom: 30px;
            text-align: center;
            color: #007BFF;
        }
        .message {
            color: green;
            text-align: center;
            margin-bottom: 20px;
        }
        .table-container {
            overflow-x: auto;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Admin Panel</h1>

    <?php if ($message): ?>
        <div class="alert alert-info text-center"> <?php echo $message; ?> </div>
    <?php endif; ?>

    <!-- Display Pending Data for Each Table -->
    <?php foreach ([
        'doctors' => $doctors,
        'hospitals' => $hospitals,
        'hospital_reviews' => $hospital_reviews,
        'managers' => $managers
    ] as $table => $records): ?>
        <h2>Pending <?php echo ucfirst($table); ?></h2>
        <?php if ($records && mysqli_num_rows($records) > 0): ?>
            <div class="table-container">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                    <tr>
                        <?php 
                        $columns = mysqli_fetch_fields($records);
                        foreach ($columns as $column) {
                            if ($column->name !== 'Password') {
                                echo "<th>{$column->name}</th>";
                            }
                        }
                        ?>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = mysqli_fetch_assoc($records)): ?>
                        <tr>
                            <?php foreach ($row as $key => $value): ?>
                                <?php if ($key !== 'Password'): ?>
                                    <td><?php echo htmlspecialchars($value); ?></td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <td>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="table" value="<?php echo htmlspecialchars($table); ?>">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row[$primaryKeys[$table]]); ?>">
                                    <input type="hidden" name="column" value="Is_Accepted">
                                    <button type="submit" name="status" value="accepted" class="btn btn-success btn-sm">Approve</button>
                                    <button type="submit" name="status" value="rejected" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No pending records found for <?php echo ucfirst($table); ?>.</p>
        <?php endif; ?>
    <?php endforeach; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
