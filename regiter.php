<?php
$servername = "localhost"; // Change if necessary
$username = "root"; // Change to your DB username
$password = ""; // Change to your DB password
$dbname = "prescription_db"; // Change to your DB name

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
    $name = $_POST['name'];
    $number=$_POST['num'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];

    $sql = "INSERT INTO users (name,number,email, password, user_type) VALUES ('$name','$number', '$email', '$password', '$user_type')";

  $res= mysqli_query($conn,$sql);

    if ($res>0) {
        echo "Registration successful! <a href='login.php'>Login here</a>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    mysqli_close();
}
?>
