<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            color: #343a40;
        }
        form {
            max-width: 600px;
            margin: auto;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #007bff;
        }
        label {
            color: rgb(13, 13, 14);
        }
        button {
            background-color: #007bff;
            border-color: #007bff;
        }
        button:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Include database connection
    include 'db.php';

    // Retrieve and sanitize form inputs
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);
    $dob = $conn->real_escape_string($_POST['dob']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $password = password_hash($conn->real_escape_string($_POST['password']), PASSWORD_BCRYPT);
    $interests = isset($_POST['interests']) ? implode(', ', $_POST['interests']) : '';
    $latitude = $conn->real_escape_string($_POST['latitude']);
    $longitude = $conn->real_escape_string($_POST['longitude']);

    // Insert into database
    $sql = "INSERT INTO users (first_name, last_name, phone, email, dob, gender, password, interests, latitude, longitude)
            VALUES ('$first_name', '$last_name', '$phone', '$email', '$dob', '$gender', '$password', '$interests', '$latitude', '$longitude')";

    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success text-center'>Signup successful! <a href='login.php'>Login here</a></div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Error: " . $conn->error . "</div>";
    }

    // Close connection
    $conn->close();
}
    ?>

    <div class="container mt-5">
        <form action="" method="POST">
            <h2 class="mb-4">Signup Form</h2>

            <div class="mb-3">
                <label for="first_name" class="form-label">First Name:</label>
                <input type="text" id="first_name" name="first_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="last_name" class="form-label">Last Name:</label>
                <input type="text" id="last_name" name="last_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number:</label>
                <input type="tel" id="phone" name="phone" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="dob" class="form-label">Date of Birth:</label>
                <input type="date" id="dob" name="dob" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="gender" class="form-label">Gender:</label>
                <select id="gender" name="gender" class="form-select" required>
                    <option value="">Select</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Travel Interests:</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="interests[]" value="adventure" id="adventure">
                    <label class="form-check-label" for="adventure">Adventure</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="interests[]" value="nature" id="nature">
                    <label class="form-check-label" for="nature">Nature</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="interests[]" value="beach" id="beach">
                    <label class="form-check-label" for="beach">Beach</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="interests[]" value="culture" id="culture">
                    <label class="form-check-label" for="culture">Culture</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="interests[]" value="food" id="food">
                    <label class="form-check-label" for="food">Food</label>
                </div>
            </div>
			
			<input type="hidden" id="latitude" name="latitude" value="">
    <input type="hidden" id="longitude" name="longitude" value="">

            <button type="submit" class="btn btn-primary w-100">Sign Up</button>

            <div class="text-center mt-3">
                <p>Have an account? <a href="login.php">Login</a></p>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
