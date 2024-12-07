<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_SESSION["isAuthenticated"]) && $_SESSION["isAuthenticated"]) {
    require_once __DIR__ . "../../../config/db.php";
    require_once __DIR__ . '../../../classes/Budget.php';

    $budgetID = $_POST['budgetID'];

    $db = getDBConnection();
    if (!$db) {
        echo json_encode([
            "success" => false,
            "message" => "DB Connection Failed."
        ]);
        exit;
    }

    $budget = new Budget($db);
    $result = $budget->delete($budgetID);
    if ($result) {
        echo json_encode([
            "success" => true,
            "message" => "Budget successfully deleted."
        ]);
        exit;
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Failed to delete budget."
        ]);
        exit;
    }
}