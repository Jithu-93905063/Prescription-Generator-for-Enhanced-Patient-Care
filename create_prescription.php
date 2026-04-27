<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "prescription_db"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_POST['patient_id'];
    $medication = $_POST['medication'];
    $dosage = $_POST['dosage'];
    $duration = $_POST['duration'];

    $sql = "INSERT INTO prescriptions (patient_id, medication, dosage, duration, date_prescribed) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $patient_id, $medication, $dosage, $duration);

    if ($stmt->execute()) {
        echo "Prescription created successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
