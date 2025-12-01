<?php
session_start();
require 'config.php';

if (!isset($_SESSION['patient_id'])) {
    echo "You are not logged in.";
    exit();
}

$patient_id = $_SESSION['patient_id'];
$message = '';

// allowed 30-minute time slots from 8:00 to 4:30 (covers 8:00–5:00 workday)
$time_slots = [
    '08:00:00' => '8:00 AM',
    '08:30:00' => '8:30 AM',
    '09:00:00' => '9:00 AM',
    '09:30:00' => '9:30 AM',
    '10:00:00' => '10:00 AM',
    '10:30:00' => '10:30 AM',
    '11:00:00' => '11:00 AM',
    '11:30:00' => '11:30 AM',
    '12:00:00' => '12:00 PM',
    '12:30:00' => '12:30 PM',
    '13:00:00' => '1:00 PM',
    '13:30:00' => '1:30 PM',
    '14:00:00' => '2:00 PM',
    '14:30:00' => '2:30 PM',
    '15:00:00' => '3:00 PM',
    '15:30:00' => '3:30 PM',
    '16:00:00' => '4:00 PM',
    '16:30:00' => '4:30 PM', // last slot: 4:30–5:00
];

// handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor_id = intval($_POST['doctor_id'] ?? 0);
    $date      = trim($_POST['appointment_date'] ?? '');
    $time      = $_POST['appointment_time'] ?? '';
    $notes     = $_POST['notes'] ?? '';

    // validate inputs
    if ($doctor_id <= 0 || $date === '' || $time === '') {
        $message = 'Please choose a doctor, date, and time.';
    } elseif (!isset($time_slots[$time])) {
        // time not in allowed set (e.g., tampering)
        $message = 'Invalid appointment time selected.';
    } else {
        // build full datetime: "YYYY-MM-DD HH:MM:SS"
        $datetime = $date . ' ' . $time;

        // check for overlapping appointment with same doctor (30-minute slots)
        // existing slot: [appointment_datetime, appointment_datetime + 30min)
        // requested slot: [$datetime, $datetime + 30min)
        $check = $conn->prepare(
            "SELECT COUNT(*) FROM appointments
             WHERE doctor_id = ?
               AND appointment_datetime < DATE_ADD(?, INTERVAL 30 MINUTE)
               AND DATE_ADD(appointment_datetime, INTERVAL 30 MINUTE) > ?"
        );
        $check->bind_param("iss", $doctor_id, $datetime, $datetime);
        $check->execute();
        $check->bind_result($count);
        $check->fetch();
        $check->close();

        if ($count > 0) {
            $message = 'This doctor already has an appointment within that 30-minute time slot.';
        } else {
            $stmt = $conn->prepare(
                "INSERT INTO appointments (patient_id, doctor_id, appointment_datetime, notes)
                 VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("iiss", $patient_id, $doctor_id, $datetime, $notes);

            if ($stmt->execute()) {
                $message = 'Appointment booked successfully.';
            } else {
                $message = 'Error booking appointment.';
            }

            $stmt->close();
        }
    }
}

// load doctors for dropdown
$doctors = [];
$result = $conn->query("SELECT id, name FROM doctors ORDER BY name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
    $result->free();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Book Appointment</title>
</head>
<body>
<h2>Book an Appointment</h2>

<?php if ($message !== ''): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<form method="post">
    <label>Doctor:
        <select name="doctor_id">
            <option value="">-- Select doctor --</option>
            <?php foreach ($doctors as $doc): ?>
                <option value="<?php echo $doc['id']; ?>">
                    <?php echo htmlspecialchars($doc['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <br><br>

    <label>Date:
        <input type="date" name="appointment_date">
    </label>
    <br><br>

    <label>Time:
        <select name="appointment_time">
            <option value="">-- Select time --</option>
            <?php foreach ($time_slots as $value => $label): ?>
                <option value="<?php echo $value; ?>">
                    <?php echo $label; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <br><br>

    <label>Notes (optional):<br>
        <textarea name="notes" rows="3" cols="30"></textarea>
    </label>
    <br><br>

    <button type="submit">Book appointment</button>
</form>

<p><a href="dashboard.php">Back to dashboard</a></p>
</body>
</html>