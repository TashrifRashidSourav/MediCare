<?php
$conn = mysqli_connect("localhost", "root", "", "medicare");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $phone = mysqli_real_escape_string($conn, $_POST["phone"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $hashed_password = hash("sha256", $password);

    $query = "INSERT INTO admin (phone, password) VALUES ('$phone', '$hashed_password')";
    if (mysqli_query($conn, $query)) {
        $success = "Registration successful. You can now log in.";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form method="POST" action="">
            <input type="text" id="phone" name="phone" placeholder="Phone Number" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <a href="login.php">Already have an account? Login here.</a>
    </div>
</body>
</html>
