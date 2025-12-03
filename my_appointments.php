<?php
// start session, load database
session_start();
require 'config.php';

// redirect if not logged in
if (!isset($_SESSION['patient_id'])) {
    echo "You are not logged in.";
    header("Location: index.php");
    exit();
}

$patient_id = $_SESSION['patient_id'];

// get user appointment info
$sql = "SELECT a.id,
               a.appointment_datetime,
               a.notes,
               d.name AS doctor_name
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.id
        WHERE a.patient_id = '$patient_id'
        ORDER BY a.appointment_datetime DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>My Appointments</title>
</head>
<body>
<h2>My Appointments</h2>

<table border="1" cellpadding="5" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Doctor</th>
        <th>Date/Time</th>
        <th>Notes</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
            <td><?php echo $row['appointment_datetime']; ?></td>
            <td><?php echo nl2br(htmlspecialchars($row['notes'])); ?></td>
        </tr>
    <?php endwhile; ?>
</table>

<p><a href="dashboard.php">Back to dashboard</a></p>
</body>
</html>
<?php
$stmt->close();
?>