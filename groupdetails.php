<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Details</title>
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
        .group-details {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .group-details h4 {
            margin-bottom: 15px;
        }
        .group-details p {
            margin-bottom: 10px;
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

  <?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db.php';

// Get the group ID from the query parameter
$group_id = isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0;

// Fetch the group details
$sql_group = "SELECT * FROM travelgroups WHERE id = ?";
$stmt_group = $conn->prepare($sql_group);
$stmt_group->bind_param("i", $group_id);
$stmt_group->execute();
$group_result = $stmt_group->get_result();

if ($group_result->num_rows == 0) {
    echo "<div class='alert alert-danger text-center'>Group not found.</div>";
    exit();
}

$group = $group_result->fetch_assoc();
$member_ids = explode(', ', $group['members']);

// Fetch user details for all members in a single query
$member_placeholders = str_repeat('?,', count($member_ids) - 1) . '?';
$sql_users = "SELECT id, first_name, last_name, phone, email, latitude, longitude 
              FROM users 
              WHERE id IN ($member_placeholders)";

$stmt_users = $conn->prepare($sql_users);
$types = str_repeat('i', count($member_ids));
$stmt_users->bind_param($types, ...$member_ids);
$stmt_users->execute();
$users_result = $stmt_users->get_result();

echo "<div class='container mt-5'>";
echo "<h3 class='text-center'>Group: {$group['group_name']}</h3>";
echo "<p class='text-center'><strong>Description:</strong> {$group['description']}</p>";
echo "<div class='row'>";

while ($user = $users_result->fetch_assoc()) {
    echo "
        <div class='col-md-4'>
            <div class='card mb-4'>
                <div class='card-body'>
                    <h5 class='card-title'>{$user['first_name']} {$user['last_name']}</h5>
                    <p class='card-text'><strong>Phone:</strong> {$user['phone']}</p>
                    <p class='card-text'><strong>Email:</strong> {$user['email']}</p>
                    <a href='track_location.php?user_id={$user['id']}' class='btn btn-success mt-2'>Track Location</a>
                </div>
            </div>
        </div>
    ";
}

echo "</div></div>";

$stmt_users->close();
$stmt_group->close();
$conn->close();
?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
