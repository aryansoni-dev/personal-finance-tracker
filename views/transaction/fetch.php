<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require __DIR__ . "../../../config/db.php";
require __DIR__ . "../../../classes/Transaction.php";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $db = getDBConnection();
    if (!$db) {
        echo json_encode(['status' => 'error', 'message' => 'DB connection failed']);
        exit;
    }

    if (!isset($_SESSION['userID'])) {
        echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
        exit;
    }

    $userID = $_SESSION['userID'];
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;

    $transaction = new Transaction($db);
    if ($start_date == null || $end_date == null) {
        $result = $transaction->read($userID);
    } else {
        $result = $transaction->read($userID, $start_date, $end_date);
    }

    if ($result) {
        echo json_encode(['status' => 'success', 'data' => $result]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No transactions found']);
    }
}
