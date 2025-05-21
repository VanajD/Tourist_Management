<?php
// Start the session
session_start();

// Include the database connection
require 'db.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $placeName = $_POST['placeName'];
    $phoneNo = $_POST['phoneNo'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password for security

    // Prepare the SQL query to insert the data
    $sql = "INSERT INTO admin (place_name, phone_number, email, password, created_at) 
            VALUES ('$placeName', '$phoneNo', '$email', '$password', NOW())";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        // If the insert is successful, redirect to the login page or another page
        $_SESSION['email'] = $email;
        header('Location: signin.php'); // Redirect to the signin page
        exit();
    } else {
        // If there is an error with the query
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
    <style>
        body {
            background-image: url('https://ieltscaptoc.com.vn/wp-content/uploads/2023/02/talk-about-travelling-4.jpg');
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
            align-items: center;
            height: 100vh;
            position: relative;
        }

        #signup-form {
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 2.0);
            margin-left: 20px;
            width: 300px;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border-radius: 20px;
        }

        button {
            background-color: #4caf50;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 18px;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div id="signup-form">
        <h1>Signup</h1>
        <form action="#" method="POST">
            <input type="text" id="placeName" name="placeName" placeholder="Name" required>
            <input type="tel" id="phoneNo" name="phoneNo" placeholder="Phone Number" required>
            <input type="email" id="email" name="email" placeholder="Email" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            
            <button type="submit">Next</button>
        </form>
        <p>Already have an account? <a href="signin.php">Signin</a></p>
    </div>
</body>
</html>
