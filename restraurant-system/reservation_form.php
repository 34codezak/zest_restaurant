<!DOCTYPE html>
<html>
<head>
    <title>Make Reservation</title>
    <link rel="stylesheet" href="assets/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
    
    <div class="form">
    <h2>Reserve a Table</h2>
    <form action="check_availability.php" method="POST">
        <label for="name">Full Name:</label><br>
        <input type="text" name="full_name" required> <br>

        <label for="email">Email Address:</label><br>
        <input name="email" type="text" required><br>

        <label>Phone:</label> <br>
        <input type="text" name="phone" required><br>

        <label>Reservation Date:</label><br>
        <input type="date" name="reservation_time" required> <br>

        <label>Time Slot:</label><br>
        <input type="time"  name="reservation_time" required><br>

        <label>Party Size:</label><br>
        <input type="number" name="party_size" min="1" max="9" required><br>

        <button type="submit">Check Availability</button>
    </form>
    </div>

</body>
</html>