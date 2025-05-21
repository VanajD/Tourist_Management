<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Groups</title>
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

        .container {
            margin-top: 50px;
        }

        /* Group Card Styling */
        .group-card {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .group-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .group-card h4 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 15px;
        }

        .group-card p {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 10px;
        }

        /* Button Styling */
        .btn-custom {
            font-size: 1rem;
            padding: 12px 20px;
            border-radius: 30px;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-info {
            background: linear-gradient(to right, #1e90ff, #32cd32);
            border: none;
            color: white;
        }

        .btn-primary {
            background: linear-gradient(to right, #ff4b5c, #ff6d70);
            border: none;
            color: white;
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
<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db.php';

$user_id = $_SESSION['user_id'];

// Modified query to handle spaces in the members list
$sql_groups = "SELECT tg.*, GROUP_CONCAT(u.phone) as member_phones 
               FROM travelgroups tg
               LEFT JOIN users u ON FIND_IN_SET(u.id, REPLACE(tg.members, ', ', ','))
               WHERE FIND_IN_SET(?, REPLACE(tg.members, ', ', ','))
               GROUP BY tg.id";
$stmt_groups = $conn->prepare($sql_groups);
$stmt_groups->bind_param("i", $user_id);
$stmt_groups->execute();
$groups_result = $stmt_groups->get_result();

if ($groups_result->num_rows > 0) {
    echo "<div class='container'>";
    echo "<div class='row'>";
    while ($group = $groups_result->fetch_assoc()) {
        // Convert member IDs to a more readable format
        $member_phones = $group['member_phones'] ? $group['member_phones'] : 'No members';
        
        echo "
            <div class='col-md-6'>
                <div class='group-card'>
                    <h4>{$group['group_name']}</h4>
                    <p><strong>Description:</strong> {$group['description']}</p>
                    <p><strong>Members:</strong> {$member_phones}</p>
                    <p>
                        <a href='groupdetails.php?group_id={$group['id']}' class='btn btn-info btn-custom'>View Group <i class='bi bi-eye'></i></a>
                        <a href='track_all.php?group_id={$group['id']}' class='btn btn-primary btn-custom'>Track All <i class='bi bi-geo-alt'></i></a>
                    </p>
                </div>
            </div>
        ";
    }
    echo "</div>";
    echo "</div>";
} else {
    echo "<div class='container'><div class='alert alert-info text-center'>You are not part of any groups yet.</div></div>";
}

$conn->close();
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
