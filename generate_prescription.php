<?php
session_start(); // Start the session to access session variables

// Check if the user is logged in as a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'doctor') {
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

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patientId = $_POST['patient_id'];
    $medications = $_POST['medications']; // Array of medications
    $dosages = $_POST['dosages']; // Array of dosages
    $instructions = $_POST['instructions'];

    // Insert each medication and its corresponding dosage into the prescriptions table
    foreach ($medications as $index => $medication) {
        $dosage = isset($dosages[$index]) ? $dosages[$index] : ''; // Get corresponding dosage
        
        // Insert prescription into the prescriptions table
        $sql = "INSERT INTO prescriptions (patient_id, medication, dosage, instructions, doctor_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssi", $patientId, $medication, $dosage, $instructions, $_SESSION['user_id']);
        if (!$stmt->execute()) {
            die("Error executing prescription insert: " . $stmt->error);
        }

        // Now insert the same data into the history table
        $historySql = "INSERT INTO history (patient_id, medication, dosage, instructions, created_at) VALUES (?, ?, ?, ?, NOW())";
        $historyStmt = $conn->prepare($historySql);
        $historyStmt->bind_param("isss", $patientId, $medication, $dosage, $instructions);
        if (!$historyStmt->execute()) {
            die("Error executing history insert: " . $historyStmt->error);
        }

        $stmt->close(); // Close the prescriptions statement
        $historyStmt->close(); // Close the history statement
    }

    echo "<script>alert('Prescription generated and history updated successfully!'); window.location.href='doctor_dashboard.html';</script>";
}

// Get list of patients for the dropdown
$patients = [];
$result = mysqli_query($conn, "SELECT id, name FROM users WHERE user_type = 'patient'");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $patients[] = $row;
    }
} else {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Prescription</title>
    <style>
        /* Global styles */
        body {
            font-family: 'Arial', sans-serif;
            background: url('med.webp') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Moving Gradient Background */
        @keyframes gradientAnimation {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }

        .container {
            width: 100%;
    max-width: 550px;
    margin: 10px auto;
    background: linear-gradient(45deg, white, skyblue, rgb(103, 147, 147), rgb(128, 140, 188), lightyellow);
    background-size: 400% 400%;
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    animation: fadeIn 1s ease-out, gradientMotion 8s ease infinite;
}

/* Gradient Motion Animation */
@keyframes gradientMotion {
    0% {
        background-position: 0% 0%;
    }
    25% {
        background-position: 100% 0%;
    }
    50% {
        background-position: 100% 100%;
    }
    75% {
        background-position: 0% 100%;
    }
    100% {
        background-position: 0% 0%;
    }
}

/* Existing Fade-in Animation */
@keyframes fadeIn {
    0% { opacity: 0; }
    100% { opacity: 1; }
}

        h2 {
            text-align: center;
            color: #333;
            font-size: 2.5rem;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        label {
            color:black;
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
            font-size: 1.5rem;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
        }

        .button {
            display: inline-block;
            padding: 15px 30px;
            background-color: #28a745;
            color: white;
            text-align: center;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1rem;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .button:hover {
            background-color: red;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
            transform: translateY(-2px);
        }

        .medication-container {
            margin-bottom: 20px;
        }

        .medication-input {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            align-items: center;
        }

        .medication-input input {
            flex: 1;
            margin-right: 10px;
        }

        .medication-input button {
            padding: 8px 15px;
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .medication-input button:hover {
            background-color: #c82333;
        }

        /* Animations */
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

    </style>
    <script>
        function addMedicationField() {
            const container = document.getElementById('medicationFields');
            const newField = document.createElement('div');
            newField.className = 'medication-input';
            newField.innerHTML = `
                <input type="text" name="medications[]" placeholder="Enter medication name" required>
                <input type="text" name="dosages[]" placeholder="Enter dosage" required>
                <button type="button" onclick="removeMedicationField(this)">Remove</button>
            `;
            container.appendChild(newField);
        }

        function removeMedicationField(button) {
            const field = button.parentElement;
            field.remove();
        }
    </script>
</head>
<body>

<div class="container">
    <h2>Generate Prescription</h2>
    <form method="POST" action="">
        <label for="patient_id">Select Patient:</label>
        <select name="patient_id" id="patient_id" required>
            <option value="">Select a patient</option>
            <?php foreach ($patients as $patient): ?>
                <option value="<?php echo htmlspecialchars($patient['id']); ?>"><?php echo htmlspecialchars($patient['name']); ?></option>
            <?php endforeach; ?>
        </select>

        <label for="medications">Medications:</label>
        <div id="medicationFields" class="medication-container">
            <div class="medication-input">
                <input type="text" name="medications[]" placeholder="Enter medication name" required>
                <input type="text" name="dosages[]" placeholder="Enter dosage" required>
                <button type="button" onclick="removeMedicationField(this)">Remove</button>
            </div>
        </div>
        <button type="button" class="button" onclick="addMedicationField()">Add Another Medication</button>

        <label for="instructions">Instructions:</label>
        <textarea name="instructions" id="instructions" rows="4" placeholder="Provide instructions for the patient" required></textarea>

        <button type="submit" class="button">Generate Prescription</button>
        <a href="doctor_dashboard.html" class="button">Back to Dashboard</a>
    </form>
</div>

<?php
// Close the database connection
mysqli_close($conn);
?>

</body>
</html>
