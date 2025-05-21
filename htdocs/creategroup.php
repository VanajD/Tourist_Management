<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db.php';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $group_name = trim($_POST['group_name']);
    $description = trim($_POST['description']);
    $phone_numbers_input = trim($_POST['phone_numbers']);

    // Convert input string to an array, removing extra spaces
    $phone_numbers = array_map('trim', explode(',', $phone_numbers_input));

    // Validate phone numbers against database
    $valid_phones = [];
    foreach ($phone_numbers as $phone) {
        $sql = "SELECT id FROM users WHERE phone = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $valid_phones[] = $row['id']; // Store user ID instead of phone
        } else {
            echo "<div class='alert alert-warning text-center'>Phone not found: $phone</div>";
        }
    }

    if (count($valid_phones) > 0) {
        // Convert valid user IDs to a comma-separated string
        $members = implode(', ', $valid_phones);

        // Insert new group into database
        $insert_sql = "INSERT INTO travelgroups (group_name, description, created_by, members) VALUES (?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ssis", $group_name, $description, $_SESSION['user_id'], $members);

        if ($insert_stmt->execute()) {
            echo "<div class='alert alert-success text-center'>Group created successfully!</div>";
        } else {
            echo "<div class='alert alert-danger text-center'>Error: " . $insert_stmt->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger text-center'>No valid phone numbers found.</div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Group</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar { background-color: #007bff; }
        .navbar .navbar-brand, .navbar .nav-link { color: #fff; padding: 20px 40px; }
        .navbar .nav-link:hover { color: #d1d1d1; }
        .container { margin-top: 30px; }
        .form-container {
            max-width: 600px;
            margin: auto;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
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
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="creategroup.php">Create Group</a></li>
                    <li class="nav-item"><a class="nav-link" href="mygroups.php">My Groups</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Create Group Form -->
    <div class="container">
        <div class="form-container">
            <h2 class="text-center">Create Group</h2>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="group_name" class="form-label">Group Name:</label>
                    <input type="text" id="group_name" name="group_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Group Description:</label>
                    <textarea id="description" name="description" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="phone_numbers" class="form-label">Enter the Phone Numbers of Members:</label>
                    <input type="text" id="phone_numbers" name="phone_numbers" class="form-control"
                           placeholder="Enter phone numbers separated by commas (e.g., 1234567890, 9876543210)" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Create Group</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
