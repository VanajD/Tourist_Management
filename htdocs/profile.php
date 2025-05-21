<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Navbar Styling */
        .navbar {
            background-color: #007bff;
        }

        .navbar .navbar-brand,
        .navbar .nav-link {
            color: #fff;
            padding: 20px 30px;
        }

        .navbar .nav-link:hover {
            color: #d1d1d1;
        }

        /* Container and Profile Card */
        .container {
            margin-top: 50px;
        }

        .profile-card {
            max-width: 650px;
            margin: auto;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
            padding: 25px;
            margin-top: 30px;
        }

        .profile-card h2 {
            color: #333;
            font-size: 2rem;
        }

        .profile-card hr {
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .profile-card p {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 10px;
        }

        /* Button Styling */
        .btn-custom {
            font-size: 1rem;
            padding: 10px 20px;
            border-radius: 30px;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #0056b3;
            color: #fff;
            cursor: pointer;
        }

        .btn-custom:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .btn-primary {
            background: linear-gradient(to right, #1e90ff, #32cd32);
        }

        .btn-danger {
            background: linear-gradient(to right, #ff4b5c, #ff6d70);
        }

        /* Text Styling */
        .text-muted {
            color: #777;
            font-size: 0.9rem;
        }
        
        .navbar .navbar-toggler-icon {
            background-color: white;
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

<!-- Profile Content -->
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

$conn->close();
?>

<div class="container">
    <div class="profile-card">
        <h2 class="text-center">Profile</h2>
        <hr>
        <p><strong>First Name:</strong> <?php echo htmlspecialchars($user['first_name']); ?></p>
        <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user['last_name']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
        <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user['dob']); ?></p>
        <p><strong>Gender:</strong> <?php echo htmlspecialchars(ucfirst($user['gender'])); ?></p>
        <p><strong>Interests:</strong> <?php echo htmlspecialchars($user['interests']); ?></p>
        <hr>
        <div class="text-center">
            <a href="edit_profile.php" class="btn btn-primary btn-custom">Edit Profile <i class="bi bi-pencil"></i></a>
            <a href="logout.php" class="btn btn-danger btn-custom">Logout <i class="bi bi-box-arrow-right"></i></a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
