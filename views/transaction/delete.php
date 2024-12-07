<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__."../../../config/db.php";
require __DIR__."../../../classes/Transaction.php";

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $transactionID = $_POST['transactionID'];

    $db = getDBConnection();
    if(!$db) {
        throw new Exception("DB connection failed");
    }

    $transaction = new Transaction($db);
    $result = $transaction->delete($transactionID);

    if(!$result) {
        throw new Exception("Failed to delete transaction");
        echo "deletion failed";
    }
    else {
        echo "deletion successful";
    }
}