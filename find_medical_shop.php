<?php
// Database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "prescription_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user input from the form
    $name = $_POST['name'];
    $pincode = $_POST['pincode'];

    // Sanitize the user input to avoid SQL injection
    $name = mysqli_real_escape_string($conn, $name);
    $pincode = mysqli_real_escape_string($conn, $pincode);

    // Query the database to get medical shops based on the pincode
    $sql = "SELECT * FROM medical_shops WHERE pincode = '$pincode'";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Shop Finder</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Global reset and body styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(45deg,white,skyblue); /* Beautiful gradient */
            min-height: 100vh;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            animation: fadeIn 2s ease-out;
        }

        /* Form container styling */
       

        

        
        
        /* Animations for fading and sliding */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Result section styling */
        .result {
            margin-top: 30px;
            background-color: rgba(0, 0, 0, 0.6);
            padding: 20px;
            border-radius: 8px;
            width: 100%;
            max-width: 500px;
            text-align: left;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.3);
        }

        .result h2 {
            font-size: 1.8em;
            margin-bottom: 20px;
        }

        .result p {
            margin: 10px 0;
            font-size: 1.1em;
        }

        .result a {
            color:yellow;
            text-decoration: none;
        }

        .result a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>


    <!-- Result section -->
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if ($result->num_rows > 0) {
            echo "<div class='result'><h2>Nearby Medical Shops</h2>";
            while ($row = $result->fetch_assoc()) {
                $shop_name = $row['shop_name'];
                $phone_number = $row['phone_number'];
                $whatsapp_link = "https://wa.me/$phone_number?text=Hello%2C%20I%20would%20like%20to%20order%20a%20prescription";
                echo "<p><strong>$shop_name</strong><br>";
                echo "Phone: <a href='$whatsapp_link' target='_blank'>$phone_number</a></p>";
            }
            echo "</div>";
        } else {
            echo "<div class='result'><p>No medical shops found for the entered pincode.</p></div>";
        }
    }
    ?>
</body>
</html>
