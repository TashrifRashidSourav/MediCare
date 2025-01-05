<?php include 'appointment.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search and Book Appointment</title>
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
        form input, form select, form button {
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
        .checkbox-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .checkbox-container label {
            display: flex;
            align-items: center;
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background: #f9f9f9;
            cursor: pointer;
        }
        .checkbox-container input {
            margin-right: 5px;
        }
        .doctor-list {
            margin-top: 20px;
        }
        .doctor-item {
            border: 1px solid #ddd;
            background: #f9f9f9;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 4px;
        }
        .doctor-item button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 5px 10px;
            margin-top: 5px;
            border-radius: 4px;
            cursor: pointer;
        }
        .doctor-item button:hover {
            background-color: #0056b3;
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
        <h2>Search and Book Appointment</h2>
        <?php if (!empty($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <!-- Search Form -->
        <form method="POST">
            <h3>Filter by Location:</h3>
            <div class="checkbox-container">
                <label><input type="checkbox" name="locations[]" value="Dhaka"> Dhaka</label>
                <label><input type="checkbox" name="locations[]" value="Rajshahi"> Rajshahi</label>
                <label><input type="checkbox" name="locations[]" value="Chittagong"> Chittagong</label>
                <label><input type="checkbox" name="locations[]" value="Other"> Other</label>
            </div>

            <h3>Filter by Categories:</h3>
            <div class="checkbox-container">
                <label><input type="checkbox" name="categories[]" value="Gynecologist"> Gynecologist</label>
                <label><input type="checkbox" name="categories[]" value="Cardiologist"> Cardiologist</label>
                <label><input type="checkbox" name="categories[]" value="Nephrologist"> Nephrologist</label>
                <label><input type="checkbox" name="categories[]" value="Dermatologist"> Dermatologist</label>
                <label><input type="checkbox" name="categories[]" value="Neurologist"> Neurologist</label>
                <label><input type="checkbox" name="categories[]" value="Pediatrician"> Pediatrician</label>
                <label><input type="checkbox" name="categories[]" value="Orthopedic Surgeon"> Orthopedic Surgeon</label>
                <label><input type="checkbox" name="categories[]" value="Gastroenterologist"> Gastroenterologist</label>
                <label><input type="checkbox" name="categories[]" value="Endocrinologist"> Endocrinologist</label>
                <label><input type="checkbox" name="categories[]" value="Oncologist"> Oncologist</label>
            </div>

            <button type="submit" name="search_doctors">Search</button>
        </form>

        <!-- Display Search Results -->
        <div class="doctor-list">
            <h3>Search Results:</h3>
            <?php if (!empty($search_results)): ?>
                <?php foreach ($search_results as $doctor): ?>
                    <div class="doctor-item">
                        <p><strong>Doctor Name:</strong> <?= htmlspecialchars($doctor['Doctor_Name']) ?></p>
                        <p><strong>License Number:</strong> <?= htmlspecialchars($doctor['Doctor_License_Number']) ?></p>
                        <p><strong>Category:</strong> <?= htmlspecialchars($doctor['Dr_Categories']) ?></p>
                        <p><strong>Location:</strong> <?= htmlspecialchars($doctor['Location']) ?></p>
                        <form method="POST">
                            <input type="hidden" name="doctor_id" value="<?= $doctor['id'] ?>">
                            <h4>Book an Appointment:</h4>
                            <label for="appointment_date">Select Date:</label>
                            <select name="appointment_date" required>
                                <?php
                                $visit_days = explode(',', $doctor['Visit_Day_Start']);
                                foreach ($visit_days as $day):
                                    ?>
                                    <option value="<?= date('Y-m-d', strtotime("next $day")) ?>"><?= date('l, F j, Y', strtotime("next $day")) ?></option>
                                <?php endforeach; ?>
                            </select>

                            <label for="appointment_time">Select Time:</label>
                            <select name="appointment_time" required>
                                <?php
                                $start_time = strtotime($doctor['Visit_Time_Start']);
                                $end_time = strtotime($doctor['Visit_Time_End']);
                                while ($start_time < $end_time): ?>
                                    <option value="<?= date('H:i:s', $start_time) ?>"><?= date('h:i A', $start_time) ?></option>
                                    <?php $start_time = strtotime('+30 minutes', $start_time); ?>
                                <?php endwhile; ?>
                            </select>

                            <button type="submit" name="book_appointment_submit">Book Appointment</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No doctors found matching your criteria.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
