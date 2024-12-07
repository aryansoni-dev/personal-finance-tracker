<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/User.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['isAuthenticated']) || $_SESSION['isAuthenticated'] !== true) {
        echo json_encode([
            "success" => false,
            "message" => "Unauthorized access."
        ]);
        exit;
    }

    $db = getDBConnection();
    if (!$db) {
        echo json_encode([
            "success" => false,
            "message" => "Database connection failed."
        ]);
        exit;
    }

    $user = new User($db);
    $userId = $_SESSION['userID'];
    $username = $_POST['username'] ?? '';
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';

    if (empty($username) || empty($name) || empty($email)) {
        echo json_encode([
            "success" => false,
            "message" => "All fields are required."
        ]);
        exit;
    }

    $result = $user->updateUser($userId, $username, $name, $email);
    if ($result) {
        echo json_encode([
            "success" => true,
            "message" => "Profile updated successfully."
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed to update profile."
        ]);
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);
}
