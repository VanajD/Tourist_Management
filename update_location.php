<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['latitude']) && isset($data['longitude'])) {
    include 'db.php';

    $latitude = $data['latitude'];
    $longitude = $data['longitude'];

    $sql = "UPDATE users SET latitude = ?, longitude = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ddi", $latitude, $longitude, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => 'Location updated']);
    } else {
        echo json_encode(['error' => 'Failed to update location']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Invalid data']);
}
