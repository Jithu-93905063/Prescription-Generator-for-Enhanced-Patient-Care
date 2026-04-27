<?php
session_start(); // Start the session to access session variables

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

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch prescriptions for the logged-in patient
$patientId = $_SESSION['user_id'];
$sql = "SELECT p.patient_id, p.medication, p.dosage, p.instructions, d.name AS doctor_name, d.email AS doctor_email, p.created_at
        FROM prescriptions p
        JOIN users d ON p.doctor_id = d.id
        WHERE p.patient_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patientId);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Prescriptions</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-image: url('w.webp'); /* You can replace this with your desired background image */
            background-size: cover;
            background-position: center;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }
        
        .container {
            width: 90%;
            max-width: 1000px;
            background-image: url('4.webp'); /* Slightly transparent white */
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }

        h2 {
            color:white;
            text-align: center;
            margin-bottom: 20px;
            font-size: 2em;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            animation: slideIn 1s ease-out;
            color: white;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
            font-size: 1.1em;
            color: black;
        }

        th {
            background-color: white;
            color: black;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color:gray;
            transition: background-color 0.3s ease;
        }

        .button {
            padding: 12px 20px;
            background-color:blue;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            display: block;
            width: 100%;
            margin-top: 20px;
            font-size: 1.1em;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: purple;
        }

        .back-button {
            width: auto;
            background-color: blue;
            margin-top: 20px;
        }

        /* Animations */
        @keyframes fadeIn {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        @keyframes slideIn {
            0% {
                transform: translateX(-100%);
            }
            100% {
                transform: translateX(0);
            }
        }

    </style>
</head>
<body>

<div class="container">
    <h2>Your Prescriptions</h2>
    <form method="POST" action="delete_symptoms.php"  onsubmit="window.print();">
        <input type="hidden" name="patient_id" value="<?php echo htmlspecialchars($patientId); ?>">
        <button type="submit" class="button">Print Prescriptions and Clear</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Medication</th>
                <th>Dosage</th>
                <th>Instructions</th>
                <th>Doctor Name</th>
                <th>Doctor Email</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['patient_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['medication']); ?></td>
                        <td><?php echo htmlspecialchars($row['dosage']); ?></td>
                        <td><?php echo htmlspecialchars($row['instructions']); ?></td>
                        <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['doctor_email']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center;">No prescriptions found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="patient_dashboard.php" class="button back-button">Back to Dashboard</a>
</div>

<?php
// Close the database connection
$stmt->close();
mysqli_close($conn);
?>

</body>
</html>
