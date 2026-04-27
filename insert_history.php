<?php
session_start(); // Start the session to access session variables

// Check if the user is logged in as a patient
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    echo "User not logged in.";
    exit;
}

// Database connection parameters
$servername = "localhost"; // Change if necessary
$username = "root"; // Change to your DB username
$password = ""; // Change to your DB password
$dbname = "prescription_db"; // Change to your DB name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the posted data
$data = json_decode(file_get_contents('php://input'), true);
$patientId = $_SESSION['user_id'];

// Prepare statement for inserting into history

// Clear symptoms after copying
$clearSymptomsSql = "DELETE FROM symptoms WHERE user_id = ?";
$clearSymptomsStmt = $conn->prepare($clearSymptomsSql);
$clearSymptomsStmt->bind_param("i", $patientId);
$clearSymptomsStmt->execute();

$stmt->close();
$clearSymptomsStmt->close();
mysqli_close($conn);

echo "Prescription cleared successfully.";
?>
