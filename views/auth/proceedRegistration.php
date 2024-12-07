<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../../classes/Total.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate that POST data exists
    if (!isset($_POST['username']) || !isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['passwd'])) {
        echo "Missing required fields";
    }
    // Sanitize inputs
    $username = htmlspecialchars(trim($_POST['username']));
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $passwd = $_POST['passwd'];
    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format";
    }
    // Get DB Connection
    $db = getDBConnection();
    if(!$db) {
        echo "DB connection failed";
    }
    // Register the user
    $user = new User($db);
    $registration_result = $user->register($username, $name, $email, $passwd);
    $userId = $user->login($username, $passwd);
    $totals = new Total($db);
    $createWallet_result = $totals->setTotals($userId);
    if (!$registration_result) {
        echo "Registration failed!";
    }
    if (!$createWallet_result) {
        echo "Wallet creation failed!";
    }
    if ($registration_result && $createWallet_result) {
        echo "Registration successful!";
        $_SESSION['isRegistered'] = true;
        header("Location: ./login.php");
        exit();
    }
    else {
        echo "Registration failed!";
        $_SESSION['isRegistered'] = false;
        header("Location:./register.php");
        exit();
    }
}
