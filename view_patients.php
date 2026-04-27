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

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$loggedInUserId = $_SESSION['user_id'];
// Query to get patient details and symptoms
$sql = "
    SELECT u.id, u.name, u.number, u.email, 
           GROUP_CONCAT(s.symptom SEPARATOR ', ') AS symptoms
    FROM users u
    LEFT JOIN symptoms s ON u.id = s.user_id AND s.doctor_id=$loggedInUserId
    WHERE u.id = s.user_id
    GROUP BY u.id
";

$result = mysqli_query($conn, $sql);

// Check for query errors
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Patients</title>
    <style>
        /* Global styles */
        body {
            font-family: 'Arial', sans-serif;
            background: url('2.webp') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            transition: all 0.3s ease;
        }
        
        .container {
            background: rgba(255, 255, 255, 0.8);
            width: 80%;
            max-width: 1200px;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1.5s ease-in-out;
        }
        
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 2rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            transition: transform 0.3s ease;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border: 1px solid #ddd;
            font-size: 1rem;
            color:black;
        }

        th {
            background-color: #007BFF;
            color: white;
            color:black;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            transform: translateX(5px);
            background-color: #e8f4ff;
        }

        .button {
            display: inline-block;
            padding: 15px 30px;
            margin-top: 20px;
            background-color: #28a745;
            color: white;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            font-size: 1rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .button:hover {
            background-color: #218838;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        /* Animations */
        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Patient List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Symptoms</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($patient = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($patient['id']); ?></td>
                    <td><?php echo htmlspecialchars($patient['name']); ?></td>
                    <td><?php echo htmlspecialchars($patient['number']); ?></td>
                    <td><?php echo htmlspecialchars($patient['email']); ?></td>
                    <td><?php echo htmlspecialchars($patient['symptoms'] ?: 'No symptoms reported'); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="doctor_dashboard.html" class="button">Back to Dashboard</a>
</div>

<?php
// Close the database connection
mysqli_close($conn);
?>

</body>
</html>
