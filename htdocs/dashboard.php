<?php
// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Include database connection
include 'db.php';

// Fetch user details
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Close the statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            color: #343a40;
        }
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
        .user-details {
            padding: 20px;
        }
        .user-details h3 {
            margin-bottom: 20px;
        }
        .recent-travelings {
            margin-top: 20px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .map-container {
            margin-top: 30px;
            height: 400px;
        }
        .floating-search {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            background-color: #28a745;
            color: white;
            padding: 12px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }
        .floating-search:hover {
            background-color: #218838;
            color: white;
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

<!-- Dashboard Content -->
<div class="container">
    <div class="user-details">
        <h3>Welcome, <?php echo htmlspecialchars($user['first_name']); ?>!</h3>

        <!-- Find Best Routes Section -->
        <div class="find-routes d-flex align-items-center mt-3">
            <p class="mb-0 me-3">Find best routes to your travel destinations</p>
            <a href="find.php" class="btn btn-primary">Find Routes</a>
        </div>

        <!-- Search Feature (Below Find Routes Section) -->
        <div class="search-section mt-4">
            <a href="search.php" class="btn btn-success">Search Travel Plans</a>
        </div>
    </div>
</div>

<!-- Floating Search Button -->
<a href="search.php" class="floating-search">🔍 Search Travel Plans</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

<script type="text/javascript" src="https://www.bing.com/api/maps/mapcontrol?key=AgK2hdTIO2Ik5RnuloQrRyT7N_MUEi254Xchmr-1fdF9Z7eCk-Nbvpz0Zr1wC_vR&callback=loadMap" async defer></script>

<script>
    function updateLocation(lat, lon) {
        fetch('update_location.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ latitude: lat, longitude: lon })
        })
        .then(response => response.json())
        .then(data => console.log('Location updated:', data))
        .catch(error => console.error('Error:', error));
    }

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;
                updateLocation(latitude, longitude);
            });
        } else {
            console.error("Geolocation is not supported by this browser.");
        }
    }

    // Call getLocation function on page load
    window.onload = getLocation;
</script>

</body>
</html>
