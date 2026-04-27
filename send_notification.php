<?php
session_start(); // Start the session to access session variables

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id']; // Get the patient ID

// Database connection
$servername = "localhost"; // Your DB server
$username = "root"; // Your DB username
$password = ""; // Your DB password
$dbname = "prescription_db"; // Your DB name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the doctor ID from the POST request (ensure you have a form for selecting the doctor)
$doctorId = $_POST['doctor-select'] ?? null;

if ($doctorId) {
    // Fetch the doctor's email from the database
    $sql = "SELECT email FROM users WHERE id = ? AND user_type = 'doctor'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doctorId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $doctor = $result->fetch_assoc();
        $doctorEmail = $doctor['email'];
    } else {
        echo "<script>alert('Doctor not found.'); window.location.href = 'patient_dashboard.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('Please select a doctor to notify.'); window.location.href = 'patient_dashboard.php';</script>";
    exit;
}

$conn->close(); // Close connection
?>
