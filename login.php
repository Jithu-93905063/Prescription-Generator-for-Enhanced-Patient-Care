<?php
session_start(); // Start the session to manage user sessions

$servername = "localhost"; // Change if necessary
$username = "root"; // Change to your DB username
$password = ""; // Change to your DB password
$dbname = "prescription_db"; // Change to your DB name

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    echo "<script>alert('Connection failed: " . mysqli_connect_error() . "'); window.history.back();</script>";
    exit;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch the user data from the database
    $sql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);
    $_SESSION['username']=$user['name'];
    // Check if the user exists and if the password matches
    if ($user && $user['password'] === $password) { // Use password_verify() if passwords are hashed
        $_SESSION['user_id'] = $user['id']; // Store user ID in session
        $_SESSION['user_type'] = $user['user_type']; 
       // Store user type in session
        
        // Redirect to the appropriate dashboard
        if ($user['user_type'] === 'doctor') {
            echo "<script>alert('Login successful!'); window.location.href='doctor_dashboard.html';</script>";
        } else {
            echo "<script>alert('Login successful!'); window.location.href='patient_dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid email or password.'); window.history.back();</script>";
    }
}

mysqli_close($conn);
?>
