<?php
session_start();
require 'config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username !== '' && $password !== '') {
        $stmt = $conn->prepare("SELECT id, password_hash FROM patients WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($patient_id, $password_hash);

        if ($stmt->fetch() && password_verify($password, $password_hash)) {
            $_SESSION['patient_id'] = $patient_id;
            $_SESSION['username'] = $username;
            header('Location: dashboard.php');
            exit();
        } else {
            $message = 'Invalid username or password.';
        }

        $stmt->close();
    } else {
        $message = 'Please enter a username and password.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Patient Login</title>
</head>
<body>
<h2>Patient Login</h2>

<?php if ($message !== ''): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<form method="post">
    <label>Username:
        <input type="text" name="username">
    </label>
    <br><br>

    <label>Password:
        <input type="password" name="password">
    </label>
    <br><br>

    <button type="submit">Login</button>
</form>

<p><a href="register.php">Create an account</a></p>
</body>
</html>