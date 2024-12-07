<?php
// classes/Transaction.php

class Transaction
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create($user_id, $category_id, $amount, $description, $date)
    {
        $query = "INSERT INTO transactions (user_id, category_id, amount, description, date) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iidss", $user_id, $category_id, $amount, $description, $date);

        return $stmt->execute();
    }

    public function read($user_id, $start_date = null, $end_date = null)
    {
        $query = "SELECT t.*, c.name as category_name, c.type as category_type 
              FROM transactions t 
              JOIN categories c ON t.category_id = c.id 
              WHERE t.user_id = ?";

        if ($start_date && $end_date) {
            $query .= " AND t.date BETWEEN ? AND ?";
        }

        $query .= " ORDER BY t.date DESC";

        $stmt = $this->conn->prepare($query);

        if ($start_date && $end_date) {
            $stmt->bind_param("iss", $user_id, $start_date, $end_date);
        } else {
            $stmt->bind_param("i", $user_id);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch all rows into an array
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data; // Return the array of transactions
    }


    public function update($id, $category_id, $amount, $description, $date)
    {
        $query = "UPDATE transactions SET category_id = ?, amount = ?, description = ?, date = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("idssi", $category_id, $amount, $description, $date, $id);

        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM transactions WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    public function getMonthlyReport($user_id, $year, $month)
    {
        $query = "SELECT 
                    c.name as category_name,
                    c.type as category_type,
                    SUM(t.amount) as total_amount
                  FROM 
                    transactions t
                  JOIN 
                    categories c ON t.category_id = c.id
                  WHERE 
                    t.user_id = ? AND
                    YEAR(t.date) = ? AND
                    MONTH(t.date) = ?
                  GROUP BY 
                    t.category_id
                  ORDER BY 
                    c.type, total_amount DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iii", $user_id, $year, $month);
        $stmt->execute();

        $result = $stmt->get_result();
        $report = [
            'income' => [],
            'expense' => [],
            'total_income' => 0,
            'total_expense' => 0
        ];

        while ($row = $result->fetch_assoc()) {
            if ($row['category_type'] == 'income') {
                $report['income'][] = $row;
                $report['total_income'] += $row['total_amount'];
            } else {
                $report['expense'][] = $row;
                $report['total_expense'] += $row['total_amount'];
            }
        }

        $report['net'] = $report['total_income'] - $report['total_expense'];

        return $report;
    }
}
