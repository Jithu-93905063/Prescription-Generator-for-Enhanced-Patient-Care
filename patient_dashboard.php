<?php
session_start(); // Start the session to access session variables

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard</title>
    <!-- Adding Google Fonts for better typography -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- FontAwesome CDN Link for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <style>
        /* Body Background - Sliding Image Effect */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: url('download3.webp') repeat-x;
            background-size: cover;
            animation: slideBackground 20s linear infinite;
        }

        @keyframes slideBackground {
            0% {
                background-position: 0 0;
            }
            100% {
                background-position: 100% 0;
            }
        }

        /* Header */
        header {
            background-color: rgba(0, 0, 0.6, 0.6); /* Semi-transparent background */
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: fadeInHeader 1s ease-out;
        }

        header h1 {
            margin: 0;
            font-size: 2.5em;
            font-weight: 500;
            
            animation: fadeInText 1.5s forwards;
        }

        @keyframes fadeInHeader {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeInText {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Card Styling */
        .card {
            display: inline-block;
            width: 220px;
            margin: 20px;
            padding: 20px;
            background-size: cover;
            background-position: center;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            text-align: center;
            color: black;
            transition: all 0.3s ease-in-out;
            position: relative;
            overflow: hidden;
            animation: fadeInCard 1s ease-out forwards;
        }

        @keyframes fadeInCard {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .card i {
            font-size: 3em;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }

        /* Hover effect on icon */
        .card:hover i {
            transform: scale(1.1);
            color:blue;
        }

        .card .card-text {
            opacity: 1;
            transform: translateY(20px);
            transition: all 0.3s ease;
        }

        .card:hover .card-text {
            opacity: 2;
            transform: translateY(0);
            
        }

        .card p {
            font-size: 1.1em;
            font-weight: 500;
            color: black;
        }

        /* Button Styling */
        .button {
            padding: 15px 20px;
            margin: 12px 10px;
            background-color: purple;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.2em;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.3s ease-in-out, background-color 0.3s ease;
            display: inline-flex;
            align-items: center;
        }

        .button:hover {
            background-color:darkgray;
            transform: scale(1.05);
        }

        .button:active {
            transform: scale(1);
        }

        .button i {
            margin-right: 8px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            header h1 {
                font-size: 2em;
            }

            .card {
                width: 90%;
                margin: 10px 0;
            }

            .button {
                font-size: 1em;
                padding: 12px 15px;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>Welcome <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
</header>

<div style="text-align: center; margin: 30px auto;">
    <h2>Your Dashboard</h2>

    <div class="card" >
        <i class="fas fa-stethoscope"></i>
        <div class="card-text">
            <p>Enter your symptoms and get a diagnosis.</p>
        </div>
        <p><a href="enter_symptoms.php" class="button"><i class="fas fa-stethoscope"></i> Enter Symptoms</a></p>
    </div>

    <div class="card" >
        <i class="fas fa-prescription-bottle-alt"></i>
        <div class="card-text">
            <p>View your current prescriptions.</p>
        </div>
        <p><a href="view_prescriptions.php" class="button"><i class="fas fa-prescription-bottle-alt"></i> View Prescriptions</a></p>
    </div>

    <div class="card" >
        <i class="fas fa-shopping-cart"></i>
        <div class="card-text">
            <p>Order your prescription online.</p>
        </div>
        <p><a href="order_prescription.html" class="button"><i class="fas fa-shopping-cart"></i> Order Prescription</a></p>
    </div>

    <div class="card">
        <i class="fas fa-history"></i>
        <div class="card-text">
            <p>View your medical history.</p>
        </div>
        <p><a href="view_history.php" class="button"><i class="fas fa-history"></i> History</a></p>
    </div>

    <div class="card">
        <i class="fas fa-sign-out-alt"></i>
        <div class="card-text">
            <p>Log out of your account.</p>
        </div>
        <p><a href="logout.php" class="button"><i class="fas fa-sign-out-alt"></i> Logout</a></p>
    </div>

</div>

</body>
</html>
