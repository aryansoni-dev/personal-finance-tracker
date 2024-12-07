<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require __DIR__ . "../../../config/db.php";
require __DIR__ . "../../../classes/Transaction.php";
require __DIR__ . "../../../classes/Total.php";
require __DIR__ . "../../../classes/Category.php";

session_start();

header('Content-Type: application/json');

try {
    // Check if user is logged in
    if (!isset($_SESSION['userID'])) {
        http_response_code(401); // Unauthorized
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
        exit;
    }

    // Validate request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); // Method Not Allowed
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
        exit;
    }

    // Validate and sanitize inputs
    $userID = $_SESSION['userID'];
    $categoryID = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
    $categoryID = (int)$categoryID;
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    $desc = htmlspecialchars(trim($_POST['desc'] ?? 'None'));
    // $type = htmlspecialchars(trim($_POST['type'] ?? ''));
    $date = htmlspecialchars(trim($_POST['date'] ?? ''));

    // var_dump($userID);
    // var_dump($categoryID);
    // var_dump($amount);
    // var_dump($desc);
    // var_dump($type);
    // var_dump($date);

    if (!$categoryID || !$amount || empty($desc) || empty($date)) {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'Invalid or missing input data.']);
        exit;
    }

    // Establish DB connection
    $db = getDBConnection();
    if (!$db) {
        throw new Exception("Database connection failed.");
    }

    // Create transaction
    $transaction = new Transaction($db);
    $total = new Total($db);
    $category = new Category($db);
    $categoryData = $category->getCategory($categoryID);
    if (!$categoryData) {
        http_response_code(404); // Not Found
        echo json_encode(['status' => 'error', 'message' => 'Category not found.']);
        exit;
    }

    if ($categoryData['type'] == 'expense') {
        $balance = $total->getWalletBalance($userID);
        if ($balance < $amount) {
            http_response_code(400); // Bad Request
            echo json_encode(['status' => 'error', 'message' => 'Insufficient funds in wallet.']);
            exit;
        }
        $result = $transaction->create($userID, $categoryID, $amount, $desc, $date);
        $result2 = $total->setWalletBalance($userID, $balance - $amount);
    } else if ($categoryData['type'] == 'income') {
        $balance = $total->getWalletBalance($userID);
        $result = $transaction->create($userID, $categoryID, $amount, $desc, $date);
        $result2 = $total->setWalletBalance($userID, $balance + $amount);
    }


    if (!$result || !$result2) {
        throw new Exception('Could not create transaction.');
    }

    echo json_encode(['status' => 'success', 'message' => 'Transaction created successfully.']);
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
