<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar {
            background-color: #007bff;
        }
        .navbar .navbar-brand,
        .navbar .nav-link {
            color: #fff;
            padding: 20px 40px;
        }
        .navbar .nav-link:hover {
            color: #d1d1d1;
        }
        .container {
            margin-top: 30px;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">TravelApp</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="creategroup.php">Create Group</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="mygroups.php">My Groups</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Edit Profile Content -->
    <?php
    session_start();

    // Ensure the user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // Include database connection
    include 'db.php';

    // Fetch user data
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT first_name, last_name, email, phone, dob, gender, interests FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger text-center'>User not found!</div>";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sanitize the inputs and update the database
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $dob = $_POST['dob'];
        $gender = $_POST['gender'];
        $interests = isset($_POST['interests']) ? implode(', ', $_POST['interests']) : '';

        // Update the user profile
        $update_sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, dob = ?, gender = ?, interests = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssssssi", $first_name, $last_name, $email, $phone, $dob, $gender, $interests, $user_id);

        if ($update_stmt->execute()) {
            echo "<div class='alert alert-success text-center'>Profile updated successfully!</div>";
        } else {
            echo "<div class='alert alert-danger text-center'>Error: " . $update_stmt->error . "</div>";
        }
    }

    $conn->close();
    ?>

    <div class="container">
        <div class="form-container">
            <h2 class="text-center">Edit Profile</h2>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name:</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number:</label>
                    <input type="tel" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="dob" class="form-label">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" class="form-control" value="<?php echo htmlspecialchars($user['dob']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="gender" class="form-label">Gender:</label>
                    <select id="gender" name="gender" class="form-select" required>
                        <option value="male" <?php echo $user['gender'] == 'male' ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo $user['gender'] == 'female' ? 'selected' : ''; ?>>Female</option>
                        <option value="other" <?php echo $user['gender'] == 'other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Travel Interests:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="interests[]" value="adventure" id="adventure" <?php echo in_array('adventure', explode(', ', $user['interests'])) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="adventure">Adventure</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="interests[]" value="nature" id="nature" <?php echo in_array('nature', explode(', ', $user['interests'])) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="nature">Nature</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="interests[]" value="beach" id="beach" <?php echo in_array('beach', explode(', ', $user['interests'])) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="beach">Beach</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="interests[]" value="culture" id="culture" <?php echo in_array('culture', explode(', ', $user['interests'])) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="culture">Culture</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="interests[]" value="food" id="food" <?php echo in_array('food', explode(', ', $user['interests'])) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="food">Food</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Update Profile</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
