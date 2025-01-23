<?php
$conn = mysqli_connect("localhost", "root", "", "medicare");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $phone = mysqli_real_escape_string($conn, $_POST["phone"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $hashed_password = hash("sha256", $password);

    $query = "SELECT * FROM admin WHERE phone = '$phone' AND password = '$hashed_password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        session_start();
        $_SESSION["phone"] = $phone;
        header("Location: adminDashboard.php");
        exit();
    } else {
        $error = "Invalid phone or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="POST" action="">
            <input type="text" id="phone" name="phone" placeholder="Phone Number" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <a href="register.php">Don't have an account? Register here.</a>
    </div>
</body>
</html>
