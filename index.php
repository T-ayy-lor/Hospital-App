<?php
// start session, load database
session_start();
require 'config.php';
$message = '';

// handles login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // look up patient by username
    $sql = "SELECT id, password_hash FROM patients WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // verify credentials
        if (password_verify($password, $row['password_hash'])) {
            // store user info in session and go to dashboard
            $_SESSION['patient_id'] = $row['id'];
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "No user found.";
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