<?php
// classes/Budget.php

class Budget
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create($user_id, $category_id, $amount)
    {
        $query = "INSERT INTO budgets (user_id, category_id, amount) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iid", $user_id, $category_id, $amount);

        return $stmt->execute();
    }

    public function read($user_id)
    {
        $query = "
            SELECT b.*, 
                c.name AS category_name, 
                c.type AS category_type, 
                COALESCE(SUM(t.amount), 0) AS spent_amount
            FROM budgets b
            JOIN categories c ON b.category_id = c.id
            LEFT JOIN transactions t ON b.category_id = t.category_id AND t.user_id = b.user_id
            WHERE b.user_id = ?
            GROUP BY b.id, c.name, c.type
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $budgets = [];
        while ($row = $result->fetch_assoc()) {
            $budgets[] = $row;
        }
        return $budgets;
    }

    public function update($id, $amount)
    {
        $query = "UPDATE budgets SET amount = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("di", $amount, $id);

        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM budgets WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    public function getBudgetProgress($user_id, $category_id)
    {
        $query = "SELECT b.amount as budget_amount, 
                         COALESCE(SUM(t.amount), 0) as spent_amount
                  FROM budgets b
                  LEFT JOIN transactions t ON b.category_id = t.category_id 
                  WHERE b.user_id = ? AND b.category_id = ? 
                  GROUP BY b.id";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $category_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }
}
