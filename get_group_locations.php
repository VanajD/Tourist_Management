<?php
include 'db.php';

$group_id = isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0;

if ($group_id > 0) {
    // Get the members list from the group
    $sql_group = "SELECT members FROM travelgroups WHERE id = ?";
    $stmt_group = $conn->prepare($sql_group);
    $stmt_group->bind_param("i", $group_id);
    $stmt_group->execute();
    $group_result = $stmt_group->get_result();

    if ($group_result->num_rows > 0) {
        $group = $group_result->fetch_assoc();
        $member_ids = array_map('trim', explode(',', $group['members']));
        
        // Create placeholders for the IN clause
        $placeholders = str_repeat('?,', count($member_ids) - 1) . '?';
        
        // Get all members' locations in a single query
        $sql_users = "SELECT first_name, last_name, latitude, longitude 
                     FROM users 
                     WHERE id IN ($placeholders)";
        
        $stmt_users = $conn->prepare($sql_users);
        
        // Create the types string for bind_param
        $types = str_repeat('i', count($member_ids));
        $stmt_users->bind_param($types, ...$member_ids);
        
        $stmt_users->execute();
        $users_result = $stmt_users->get_result();
        
        $locations = [];
        while ($user = $users_result->fetch_assoc()) {
            $locations[] = [
                'name' => "{$user['first_name']} {$user['last_name']}",
                'latitude' => $user['latitude'],
                'longitude' => $user['longitude']
            ];
        }
        
        echo json_encode($locations);
        $stmt_users->close();
    } else {
        echo json_encode([]);
    }
    
    $stmt_group->close();
}

$conn->close();
?>