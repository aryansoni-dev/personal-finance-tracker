<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION["isAuthenticated"]) && $_SESSION["isAuthenticated"]) {
    require_once __DIR__ . "../../../config/db.php";
    require_once __DIR__ . '../../../classes/Total.php';
    
    $balance = $_POST['balance'] ?? '';

    $db = getDBConnection();
    if (!$db) {
        echo json_encode([
            "success" => false,
            "message" => "DB Connection Failed."
        ]);
        exit;
    }

    $total = new Total($db);
    $result = $total->setWalletBalance($_SESSION['userID'], $balance);
    if ($result) {
        echo json_encode([
            "success" => true,
            "message" => "Wallet Balance successfully updated."
        ]);
        exit;
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed to update wallet balance."
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
