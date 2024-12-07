<?php

class Total
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getWalletBalance($userId)
    {
        $query = "SELECT balance AS total_balance FROM totals WHERE user_id =?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId); // Bind user ID
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc()['total_balance'] ?? 0;
    }

    public function setWalletBalance($userId, $amount)
    {
        $query = "UPDATE totals SET balance = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("di", $amount, $userId); // Bind amount and user ID
        return $stmt->execute();
    }

    public function updateTotals($userId, $balance, $income, $expenses, $savings)
    {
        $query = "UPDATE totals 
            SET balance = ?, income = ?, expenses = ?, savings = ? 
            WHERE user_id = ?";

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("ddddi", $balance, $income, $expenses, $savings, $userId);
        return $stmt->execute();
    }

    public function getTransactionTotalByType($userId, $type)
    {
        $query = "
        SELECT SUM(t.amount) AS total_amount 
        FROM transactions t
        JOIN categories c ON t.category_id = c.id
        WHERE c.type = ? AND t.user_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $type, $userId); // Bind type (income/expense) and user ID
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        // Safely handle null and ensure float type
        return isset($row['total_amount']) ? (float)$row['total_amount'] : 0.00;
    }

    public function getTotals($userId)
    {
        $query = "SELECT * FROM totals WHERE user_id =?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId); // Bind user ID
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    public function setTotals($userId)
    {
        $query = "INSERT INTO totals (user_id, balance, income, expenses, savings) VALUES (?, 0, 0, 0, 0)";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            // Log or handle the prepare error
            error_log("Prepare failed: " . $this->conn->error);
            return false;
        }
        $stmt->bind_param("i", $userId); // Bind user ID
        if ($stmt->execute()) {
            // If needed, you can return the ID of the inserted record
            return true;
        } else {
            // Log or handle the execution error
            error_log("Execute failed: " . $stmt->error);
            return false;
        }
    }


    public function getRecentTransactions($userId)
    {
        $query = "SELECT t.*, c.name as category_name, c.type as category_type 
                  FROM transactions t
                  JOIN categories c ON t.category_id = c.id
                  WHERE t.user_id = ?
                  ORDER BY t.date DESC
                  LIMIT 5";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
