<?php
class Supplier {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function add($name, $phone, $category_id) {
    // Phone must be exactly 11 digits and numeric
    

    // Check if category is already assigned to a different supplier
    $stmt = $this->conn->prepare("SELECT id FROM suppliers WHERE category_id = ? AND name != ?");
    $stmt->bind_param("is", $category_id, $name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        return "category_taken";
    }

    // Add supplier
    $stmt = $this->conn->prepare("INSERT INTO suppliers (name, phone, category_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $name, $phone, $category_id);
    return $stmt->execute() ? true : false;
}


    public function getAll() {
        return $this->conn->query("
            SELECT s.id, s.name, s.phone, c.name AS category_name
            FROM suppliers s
            JOIN categories c ON s.category_id = c.id
        ");
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM suppliers WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
