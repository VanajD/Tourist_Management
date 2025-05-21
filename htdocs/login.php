<?php
// Start session
session_start();

// Include database connection
include 'db.php';


// Initialize error variable
$error = "";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize user inputs
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // SQL query to fetch user by email
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists and verify password
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Login successful, store session data
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            header("Location: dashboard.php"); // Redirect to dashboard after successful login
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "No user found with that email!";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #007bff;
        }
        .form-control {
            border-radius: 20px;
            padding: 10px;
        }
        .btn-primary {
            border-radius: 20px;
            padding: 10px;
            font-size: 1.1rem;
            transition: 0.3s;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .text-center p a {
            color: #007bff;
            text-decoration: none;
        }
        .text-center p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <form action="" method="POST">
            <h2 class="mb-4">Login</h2>

            <!-- Display error message if any -->
            <?php if ($error): ?>
                <div class="alert alert-danger text-center"><?= $error ?></div>
            <?php endif; ?>

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>

            <div class="text-center mt-3">
                <p>Don't have an account? <a href="signup.php">Signup</a></p>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>