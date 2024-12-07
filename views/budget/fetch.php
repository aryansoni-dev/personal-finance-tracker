<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == 'GET' && isset($_SESSION["isAuthenticated"]) && $_SESSION["isAuthenticated"]) {
    require_once __DIR__ . "../../../config/db.php";
    require_once __DIR__ . '../../../classes/Budget.php';

    $db = getDBConnection();
    if (!$db) {
        echo json_encode([
            "success" => false,
            "message" => "DB Connection Failed."
        ]);
        exit;
    }

    $budget = new Budget($db);
    $result = $budget->read($_SESSION['userID']);
    if ($result) {
        echo json_encode([
            "success" => true,
            "budgets" => $result
        ]);
        exit;
    } else {
        echo json_encode([
            "success" => true,
            "message" => "Failed to fetch budget data.",
            "budgets" => []
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
