<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__."../../../config/db.php";
require __DIR__."../../../classes/Transaction.php";

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = getDBConnection();
    if(!$db) {
        throw new Exception("DB connection failed");
    }

    $transaction = new Transaction($db);

    $id = $_POST['id'];
    $category_id = $_POST['category_id'];
    $amount = $_POST['amount'];
    $desc = $_POST['desc'];
    $date = $_POST['date'];

    $result = $transaction->update($id, $category_id, $amount, $desc, $date);

    if(!$result) {
        throw new Exception('Failed to update transaction');
        echo "failure";
    }
    else {
        echo "success";
    }
}