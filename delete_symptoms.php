<?php
session_start();

// Check if the user is logged in as a patient
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: login.php");
    exit;
}

// Database connection parameters
$servername = "localhost"; // Change if necessary
$username = "root"; // Change to your DB username
$password = ""; // Change to your DB password
$dbname = "prescription_db"; // Change to your DB name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['patient_id'])) {
    $patientId = $_POST['patient_id'];

    // Prepare the SQL statement to delete symptoms
    $sql = "DELETE FROM symptoms WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $patientId);

    $deletePrescriptionsSql = "DELETE FROM prescriptions WHERE patient_id = ?";
    $stmtPrescriptions = $conn->prepare($deletePrescriptionsSql);
    $stmtPrescriptions->bind_param("i", $patientId);

    if ($stmt->execute()) {
        echo"<script>window.history.back();</script>";
        
    } else {
        echo "Error deleting " . $stmt->error;
    }
    if ($stmtPrescriptions->execute()) {
        echo"<script>window.history.back();</script>";
        
    } else {
        echo "Error delete " . $stmtPrescriptions->error;
    }

    // Close the statement
    $stmt->close();
    $stmtPrescriptions->close();
} else {
    echo "No patient ID provided.";
}

// Close the database connection
mysqli_close($conn);
?>
