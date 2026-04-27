<?php
session_start(); // Start the session to access session variables

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Database connection parameters
$servername = "localhost"; // Change if necessary
$username = "root"; // Change to your DB username
$password = ""; // Change to your DB password
$dbname = "prescription_db"; // Change to your DB name

$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selectedSymptom = $_POST['symptom-select'];
    $otherSymptom = $_POST['other-symptom-textarea'];
    $additionalInfo = $_POST['additional-info'];
    $userId = $_SESSION['user_id'];

    // Use the other symptom if "Other" is selected
    $symptomToSend = $selectedSymptom === 'other' ? $otherSymptom : $selectedSymptom;

    // Insert symptoms into the database
    $sql = "INSERT INTO symptoms (user_id, symptom, additional_info) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $userId, $symptomToSend, $additionalInfo);

    if ($stmt->execute()) {
        // Optionally notify doctors
        $doctorQuery = "SELECT name, email FROM users WHERE user_type = 'doctor'";
        $doctorResult = $conn->query($doctorQuery);

        $subject = "New Symptoms Submitted";
        $message = "A patient has submitted the following symptoms:\n\n" .
                    "Symptom: $symptomToSend\n" .
                    "Additional Info: $additionalInfo\n" .
                    "User ID: $userId";

        $headers = "From: indra15101005@gmail.com"; // Replace with your domain or email

        // Send email to each doctor
        if ($doctorResult->num_rows > 0) {
            while ($doctor = $doctorResult->fetch_assoc()) {
                $doctorName = $doctor['name'];
                $doctorEmail = $doctor['email'];
                $personalizedMessage = "Dear $doctorName,\n\n" . $message;

            if( mail($doctorEmail, $subject, $personalizedMessage, $headers))
            {
                echo "<script>alert('mail sent successfully!');</script>";
            }else{
                echo "<script>alert('mail not sent');</script>";
            }
            }
        }

        echo "<script>alert('Symptoms sent successfully!'); window.location.href='patient_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error submitting symptoms: " . $conn->error . "');</script>";
    }

    $stmt->close();
}

$conn->close();
?>
