<?php
// classes/Category.php

class Category
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create($user_id, $name, $type)
    {
        $query = "INSERT INTO categories (user_id, name, type) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iss", $user_id, $name, $type);

        return $stmt->execute();
    }

    public function read()
    {
        $query = "SELECT * FROM categories";
        $stmt = $this->conn->prepare($query);
        // $stmt->bind_param("i", $user_id);
        $stmt->execute();

        return $stmt->get_result();
    }

    public function update($id, $name, $type)
    {
        $query = "UPDATE categories SET name = ?, type = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssi", $name, $type, $id);

        return $stmt->execute();
    }

    public function delete($id)
    {
        $query = "DELETE FROM categories WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    public function getCategory($id) {
        $query = "SELECT * FROM categories WHERE id =?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        return $stmt->get_result()->fetch_assoc();
    }
}
