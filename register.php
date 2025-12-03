<?php
// start session, load database
session_start();
require 'config.php';
$message = '';

// handles registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // insert new patient into the database
    $sql = "INSERT INTO patients (username, password_hash) VALUES ('$username', '$password')";
    
    if ($conn->query($sql) === TRUE) {
        $message = "Account created. You can now log in.";
    } else {
        $message = "Error: username may already exist.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Patient Registration</title>
</head>
<body>
<h2>Patient Registration</h2>

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
    <button type="submit">Register</button>
</form>

<p><a href="index.php">Back to login</a></p>
</body>
</html>