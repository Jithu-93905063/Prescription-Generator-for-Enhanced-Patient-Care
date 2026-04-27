<?php
session_start(); // Start the session to access session variables

// Check if the user is logged in as a patient
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'patient') {
    header("Location: login.php");
    exit;
}

// Database connection parameters
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "prescription_db"; 

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// If deletion request is made, process the deletion
if (isset($_POST['created_at'])) {
    $createdAtToDelete = $_POST['created_at']; // The created_at timestamp we want to delete
    $loggedInPatientId = $_SESSION['user_id']; // The logged-in patient's user_id

    // Prepare SQL query to find the record based on patient_id and created_at
    $sql = "SELECT * FROM history WHERE patient_id = ? AND created_at = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $loggedInPatientId, $createdAtToDelete);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the record exists for the logged-in patient, delete it
    if ($result->num_rows > 0) {
        // Proceed to delete the record based on created_at timestamp
        $deleteSql = "DELETE FROM history WHERE patient_id = ? AND created_at = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("is", $loggedInPatientId, $createdAtToDelete);

        // Execute the deletion
        if ($deleteStmt->execute()) {
            header("Location: view_history.php?message=Record+deleted+successfully");
            exit;
        } else {
            header("Location: view_history.php?error=Failed+to+delete+record");
            exit;
        }
    } else {
        // Record not found or unauthorized
        header("Location: view_history.php?error=Record+not+found+or+unauthorized");
        exit;
    }
}

// Fetch history for the logged-in patient
$patientId = $_SESSION['user_id'];
$sql = "SELECT medication, dosage, instructions, created_at FROM history WHERE patient_id = ?";
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
    <title>View History</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image:url('b.jpg');
            color: #333;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        .container {
            
            width: 80%;
            margin: 30px auto;
            padding: 30px;
            background:lightblue;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }
        h2 {
            text-align: center;
            font-size: 2.5rem;
            color: black;
            margin-bottom: 30px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            border-radius: 10px;
            overflow: hidden;
        
        }
        th, td {
            background-color: #f1f1f1;
            color:black;
            padding: 15px;
            text-align: left;
            font-size: 1rem;
            border-bottom: 1px solid #ccc;
            transition: all 0.3s ease-in-out;
        }
        th {
            background-color:purple;
            color: white;
            font-weight: bold;
        }
        tr:hover td {
            background-color: #f1f1f1;
        }
        .button {
            padding: 12px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .button:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
        .button-danger {
            padding: 12px 20px;
            background-color: #DC3545;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .button-danger:hover {
            background-color: #c82333;
            transform: scale(1.05);
        }
        .alert {
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
            font-size: 1.1rem;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .icon {
            font-size: 1.2rem;
            margin-right: 10px;
        }
        
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
    </style>
    <script>
        function printHistory() {
            window.print();
        }
    </script>
</head>
<body>

<div class="container">
    <h2>Your Prescription History</h2>

    <?php
    // Display success or error messages
    if (isset($_GET['message'])) {
        echo '<div class="alert alert-success"><i class="fas fa-check-circle icon"></i>' . htmlspecialchars($_GET['message']) . '</div>';
    }
    if (isset($_GET['error'])) {
        echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle icon"></i>' . htmlspecialchars($_GET['error']) . '</div>';
    }
    ?>

    <div class="text-center">
        <button class="button" onclick="printHistory()"><i class="fas fa-print"></i> Print History</button>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Medication</th>
                <th>Dosage</th>
                <th>Instructions</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['medication']); ?></td>
                        <td><?php echo htmlspecialchars($row['dosage']); ?></td>
                        <td><?php echo htmlspecialchars($row['instructions']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td>
                            <!-- Add a delete button that sends the created_at timestamp -->
                            <form action="view_history.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this entry?');">
                                <input type="hidden" name="created_at" value="<?php echo $row['created_at']; ?>">
                                <button type="submit" class="button-danger">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No history found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="text-center">
        <a href="patient_dashboard.php" class="button"><i class="fas fa-home"></i> Back to Dashboard</a>
    </div>
</div>



<?php
// Close the database connection
$stmt->close();
mysqli_close($conn);
?>

</body>
</html>
