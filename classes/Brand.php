<?php
class Brand {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        return $this->conn->query("SELECT * FROM brands");
    }

    public function add($name, $price, $expiration_date, $quantity) {
        $stmt = $this->conn->prepare("INSERT INTO brands (name, price, expiration_date, quantity) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdsi", $name, $price, $expiration_date, $quantity);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM brands WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
