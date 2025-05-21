<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signin Page</title>
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
            position: relative; /* Add position relative to make ::before absolute relative to body */
        }


        #signin-form {
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 2.0);
            margin-left: 20px;
            width: 300px;
            text-align: center;
            position: relative; /* Add position relative to create stacking context */
            z-index: 1; /* Set a higher z-index to place the form above the blurred background */
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border-radius: 20px;
        }

        button {
            background-color: #4caf508f;
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
   <div id="signin-form">
    <h1>Sign In</h1>
    <?php
        session_start(); // Start the session to store session variables
        require "db.php"; // Include the database connection file

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Retrieve form data
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Perform a query to check if the user exists
            $sql = "SELECT * FROM admin WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // User found, check the password
                $row = $result->fetch_assoc();
                if (password_verify($password, $row['password'])) {
                    // Password is correct, user is authenticated
                    $_SESSION['email'] = $row['email']; // Store admin email in session
                    $_SESSION['admin_id'] = $row['id']; // Optionally, store the admin ID
                    header("Location: index.php"); // Redirect to admin dashboard or home page
                    exit(); // Stop further script execution
                } else {
                    // Password is incorrect
                    echo "<p style='color:red;'>Incorrect password!</p>";
                }
            } else {
                // User not found
                echo "<p style='color:red;'>User not found!</p>";
            }

            // Close the database connection
            $conn->close();
        }
        ?>
    <form action="#" method="post">
        <input type="email" id="email" name="email" placeholder="Email" required>
        <input type="password" id="password" name="password" placeholder="Password" required>
        <button type="submit">Sign In</button>
    </form>
    <br>
    <p class="text-center mt-3">Don't have an account? <a href="signup.php">Sign Up</a></p>
</div>

    <script>
        function signin() {
            var email = document.getElementById('email').value;
            var password = document.getElementById('password').value;

            console.log('Email:', email);
            console.log('Password:', password);

            // Add your logic here for handling the signin process
            // You can use AJAX to send the form data to a server for authentication
        }
    </script>
</body>
</html>
