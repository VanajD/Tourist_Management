<?php
// Include database connection
include 'db.php';

$phone = isset($_GET['phone']) ? $_GET['phone'] : '';

if ($phone) {
    // Fetch user location from database
    $sql_user = "SELECT latitude, longitude FROM users WHERE phone = ?";
    $stmt_user = $conn->prepare($sql_user);
    $stmt_user->bind_param("s", $phone);
    $stmt_user->execute();
    $user_result = $stmt_user->get_result();

    if ($user_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();
        echo json_encode($user);  // Return latitude and longitude as JSON
    } else {
        echo json_encode(['latitude' => 0, 'longitude' => 0]);  // Default location if not found
    }

    $stmt_user->close();
}
$conn->close();
?>
