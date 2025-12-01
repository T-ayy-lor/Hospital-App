<?php
session_start();

if (!isset($_SESSION['patient_id'])) {
    echo "You are not logged in.";
    exit();
}

$username = $_SESSION['username'] ?? 'Patient';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Patient Dashboard</title>
</head>
<body>
<h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>

<ul>
    <li><a href="book_appointment.php">Book an appointment</a></li>
    <li><a href="my_appointments.php">View my appointments</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>
</body>
</html>