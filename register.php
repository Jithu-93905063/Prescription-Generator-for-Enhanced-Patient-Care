<?php
$servername = "localhost"; // Change if necessary
$username = "root"; // Change to your DB username
$password = ""; // Change to your DB password
$dbname = "prescription_db"; // Change to your DB name

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    echo "<script>alert('Connection failed: " . mysqli_connect_error() . "'); window.history.back();</script>";
    exit;
}

// Get form inputs
$name = $_POST['name'];
$number = $_POST['num'];
$email = $_POST['email'];
$password = $_POST['password'];
$user_type = $_POST['user_type'];

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('Invalid email format.'); window.history.back();</script>";
    exit;
}

// Validate mobile number format (assuming 10-digit numbers)
if (!preg_match('/^\d{10}$/', $number)) {
    echo "<script>alert('Mobile number must be 10 digits.'); window.history.back();</script>";
    exit;
}

// Check if the email already exists
$email_check_query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
$result = mysqli_query($conn, $email_check_query);
$user = mysqli_fetch_assoc($result);

if ($user) { // If the email already exists
    echo "<script>alert('Email already exists: $email'); window.history.back();</script>";
    exit;
}

// Proceed with the registration
$sql = "INSERT INTO users (name, number, email, password, user_type) VALUES ('$name', '$number', '$email', '$password', '$user_type')";

$res = mysqli_query($conn, $sql);

if ($res) {
    echo "<script>alert('Registration successful!'); window.location.href='login.html';</script>";
} else {
    echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.history.back();</script>";
}

mysqli_close($conn);
?>
