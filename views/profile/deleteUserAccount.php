<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_SESSION["isAuthenticated"]) && $_SESSION["isAuthenticated"]) {
    require_once __DIR__ . "../../../config/db.php";
    require_once __DIR__ . '../../../classes/User.php';

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
    $userID = $_SESSION['userID'];
    $result = $user->deleteUser($userID);
    if ($result) {
        session_unset();
        session_destroy();
        echo json_encode([
            "success" => true,
            "message" => "Profile successfully deleted!"
        ]);
        exit;
    } else {
        echo json_encode([
            "success" => true,
            "message" => "Failed to delete user profile!"
        ]);
        exit;
    }
} else {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method, or unauthorized request"
    ]);
    exit;
}
