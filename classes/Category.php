<?php
class Category {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        return $this->conn->query("SELECT * FROM categories");
    }

    public function add($name) {
    // Check if category already exists
    $stmt = $this->conn->prepare("SELECT id FROM categories WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        return false; // Category exists
    }

    // Insert new category
    $stmt = $this->conn->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    return $stmt->execute();
}


    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function getById($id) {
    $stmt = $this->conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result();
}

}
?>
