<?php
session_start(); // Start the session to access session variables

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Sample list of symptoms (this can be fetched from the database)
$symptoms = [
    "fever",
    "cough",
    "headache",
    "nausea",
    "fatigue",
    "muscle pain",
    "sore throat",
    "shortness of breath",
    "chest pain",
    "joint pain",
    "skin rash",
    "dizziness",
    "loss of taste",
    "chills",
    "congestion"
];


// Initialize variables to prevent undefined variable errors
$otherSymptom = '';
$generatedPrescriptions = [];
$notificationMessage = "";

// Define prescription logic
    $prescriptionMap = [
        "fever" => ["medication" => "Paracetamol", "dosage" => "500mg every 8 hours", "instructions" => "Stay hydrated and rest."],
        "cough" => ["medication" => "Cough syrup", "dosage" => "as needed", "instructions" => "Take with water."],
    
    "headache" => ["medication" => "Ibuprofen", "dosage" => "200mg as needed", "instructions" => "Take with food."],
    "nausea" => ["medication" => "Anti-nausea medication", "dosage" => "as needed", "instructions" => "Avoid heavy meals."],
    "fatigue" => ["medication" => "Rest", "dosage" => "and hydration", "instructions" => "Ensure adequate sleep."],
    "muscle pain" => ["medication" => "Muscle relaxant", "dosage" => "as needed", "instructions" => "Rest the affected area."],
    "sore throat" => ["medication" => "Throat lozenges", "dosage" => "as needed", "instructions" => "Gargle with warm salt water."],
    "shortness of breath" => ["medication" => "Bronchodilator", "dosage" => "as prescribed", "instructions" => "Use inhaler as directed."],
    "chest pain" => ["medication" => "Antacid", "dosage" => "as needed", "instructions" => "Consult a doctor if persistent."],
    "joint pain" => ["medication" => "NSAIDs", "dosage" => "as needed", "instructions" => "Apply topical pain relievers."],
    "skin rash" => ["medication" => "Hydrocortisone cream", "dosage" => "apply twice daily", "instructions" => "Avoid scratching."],
    "dizziness" => ["medication" => "Antihistamines", "dosage" => "as needed", "instructions" => "Sit or lie down until symptoms resolve."],
    "loss of taste" => ["medication" => "Zinc supplements", "dosage" => "once daily", "instructions" => "Consult a doctor if persistent."],
    "chills" => ["medication" => "Paracetamol", "dosage" => "500mg as needed", "instructions" => "Stay warm and hydrated."],
    "congestion" => ["medication" => "Decongestant", "dosage" => "as needed", "instructions" => "Use a humidifier in your room."]
];

$doctorEmail = '';
if (isset($_POST['doctor-select']) && !empty($_POST['doctor-select'])) {
    $doctorId = $_POST['doctor-select'];
    
    // Fetch the selected doctor's email from the database
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "prescription_db";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sqlDoctor = "SELECT email FROM users WHERE id = ?";
    $stmt = $conn->prepare($sqlDoctor);
    $stmt->bind_param("i", $doctorId);
    $stmt->execute();
    $stmt->bind_result($doctorEmail);
    $stmt->fetch();
    $stmt->close();

    $conn->close();
}

// Prepare the email subject and body
$emailSubject = "Patient Symptoms Notification";
$emailBody = "Dear Doctor,\n\nPatient has reported the following symptoms:\n\n";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $selectedSymptoms = $_POST['symptom-select'] ?? []; // Retrieve selected symptoms
    $otherSymptom = $_POST['other-symptom-textarea'] ?? ''; // Get the "Other" symptom from the textarea
    $doctorId = $_POST['doctor-select'] ?? null; // Retrieve selected doctor
    $userId = $_SESSION['user_id'];
    $insertedSymptoms = false; // Flag to check if any symptoms were inserted
    $savedToHistory = false; // Flag for saved history

    // Check if at least one symptom is selected
    if (empty($selectedSymptoms) && empty($otherSymptom)) {
        echo "<script>alert('Please select at least one symptom.');</script>";
    } else {
        // Database connection settings
        $servername = "localhost"; // Change if necessary
        $username = "root"; // Change to your DB username
        $password = ""; // Change to your DB password
        $dbname = "prescription_db"; // Change to your DB name

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        

        // Generate prescriptions for selected symptoms
        foreach ($selectedSymptoms as $symptomToSend) {
            if (array_key_exists($symptomToSend, $prescriptionMap)) {
                $medication = $prescriptionMap[$symptomToSend]['medication'];
                $dosage = $prescriptionMap[$symptomToSend]['dosage'];
                $instructions = $prescriptionMap[$symptomToSend]['instructions'];

                // Insert into history
                 // Set flag for saved history

                // Store the generated prescription for display
                $generatedPrescriptions[] = [
                    'symptom' => $symptomToSend,
                    'medication' => $medication,
                    'dosage' => $dosage,
                    'instructions' => $instructions
                ];
            }
        }

        // Check for "Other" symptom and handle accordingly
        if (in_array('other', $selectedSymptoms) && !empty($otherSymptom)) {
            // Check if the other symptom is in the predefined list
            if (in_array($otherSymptom, $symptoms)) {
                $notificationMessage = "Please select the symptom above from the list.";
            } else {
                // Send to selected doctor
                if ($doctorId) {
                    $sqlSendToDoctor = "INSERT INTO symptoms (user_id, symptom, doctor_id, created_at) VALUES (?, ?, ?, NOW())";
                    $stmt = $conn->prepare($sqlSendToDoctor);
                    $stmt->bind_param("isi", $userId, $otherSymptom, $doctorId);
                    $stmt->execute();
                    $stmt->close();
                    $notificationMessage = " other Symptom sent to doctor successfully!";
                } else {
                    $notificationMessage = "Please select a doctor to send the symptom.";
                }
            }
        }

        // Set notifications based on the flags
       

        // Close the prescription statement and connection
      
        $conn->close();
    }
}

// Fetch doctor list from the users table
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "prescription_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sqlDoctors = "SELECT id, name,email FROM users WHERE user_type = 'doctor'";
$result = $conn->query($sqlDoctors);
$doctors = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Symptoms</title>
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background: url('he.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
            line-height: 1.6;
        }

        header {
            background-color: rgba(0, 123,0, 0.8);
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .container{
    width: 100%;
    max-width: 600px;
    margin: 10px auto;
    background: linear-gradient(45deg, white, skyblue, rgb(103, 147, 192), rgb(128, 140, 188), white);
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

        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        label {
            font-size: 1.1rem;
            margin-bottom: 10px;
            display: block;
        }

        .button {
            padding: 12px 18px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: red;
        }

        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }

        .checkbox-group label {
            width: 45%;
        }

        textarea {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: vertical;
        }

        select {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .prescription {
            margin-top: 30px;
            padding: 15px;
            background: #f4f4f4;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .notification {
            margin-top: 20px;
            padding: 12px;
            background-color: #d4edda;
            color: #155724;
            border-radius: 5px;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
    </style>
</head>
<body>

<header>
    <h1>Enter Your Symptoms</h1>
</header>

<div class="container">
    <form method="POST" action="">
        <label>Select Symptoms:</label><br>
        <?php foreach ($symptoms as $symptom): ?>
            <label>
                <input type="checkbox" name="symptom-select[]" value="<?php echo htmlspecialchars($symptom); ?>" <?php if (in_array($symptom, $_POST['symptom-select'] ?? [])) echo 'checked'; ?>>
                <?php echo htmlspecialchars($symptom); ?>
            </label><br>
        <?php endforeach; ?>
        <label>
            <input type="checkbox" name="symptom-select[]" value="other" id="other-checkbox" <?php if (in_array('other', $_POST['symptom-select'] ?? [])) echo 'checked'; ?>>
            Other (please specify)
        </label>

        <div id="other-symptom" style="display: <?php echo (in_array('other', $_POST['symptom-select'] ?? []) ? 'block' : 'none'); ?>;">
            <label for="other-symptom-textarea">Please specify:</label>
            <textarea id="other-symptom-textarea" name="other-symptom-textarea" rows="4" placeholder="Enter other symptoms..."><?php echo htmlspecialchars($otherSymptom); ?></textarea>
        </div>

        <?php if (!empty($doctors)): ?>
            <label>Select Doctor to Send Symptoms:</label>
            <select name="doctor-select">
                <option value="">-- Select Doctor --</option>
                <?php foreach ($doctors as $doctor): ?>
                    <option value="<?php echo $doctor['id']; ?>"><?php echo htmlspecialchars($doctor['name']); ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
        

        <button class="button" type="submit">Submit Symptoms</button>
        <a href="patient_dashboard.php" class="button">Back to Dashboard</a>

    </form>
    <?php if ($notificationMessage): ?>
        <div class="notification"><?php echo $notificationMessage; ?></div>
    <?php endif; ?>

    <?php if (!empty($generatedPrescriptions)): ?>
        <div class="prescription">
            <h3>Your Prescription:</h3>
            <form method="POST" action="">
                <?php foreach ($generatedPrescriptions as $prescription): ?>
                    <label>
                        <input type="checkbox" name="print-select[]" value="<?php echo htmlspecialchars($prescription['symptom']); ?>" checked>
                        <?php echo htmlspecialchars($prescription['symptom']); ?> - Medication: <?php echo htmlspecialchars($prescription['medication']); ?>, Dosage: <?php echo htmlspecialchars($prescription['dosage']); ?>; Instructions: <?php echo htmlspecialchars($prescription['instructions']); ?>
                    </label><br>
                <?php endforeach; ?>
                <button class="button" type="button" onclick="window.print()">Print Prescription</button>
                
        
            </form>
        </div>
    <?php endif; ?>
    <div>
    <?php if ($doctorEmail): ?>
        <a href="mailto:<?php echo htmlspecialchars($doctorEmail) ?>?subject=<?php echo $emailSubject; ?>&body=<?php echo $emailBody; ?>" class="button">Notify</a>
    <?php endif; ?>
</div>
</div>
    


<script>
    const otherCheckbox = document.getElementById('other-checkbox');
    const otherSymptomDiv = document.getElementById('other-symptom');

    otherCheckbox.addEventListener('change', function() {
        if (this.checked) {
            otherSymptomDiv.style.display = 'block';
            document.getElementById('other-symptom-textarea').focus();
        } else {
            otherSymptomDiv.style.display = 'none';
            document.getElementById('other-symptom-textarea').value = ''; // Clear the textarea
        }
    });
</script>
</body>
</html>