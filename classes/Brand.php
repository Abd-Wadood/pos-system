<?php
class Brand {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

   public function getAll() {
    $query = "SELECT brands.*, categories.name AS category_name
              FROM brands
              JOIN categories ON brands.category_id = categories.id";
    return $this->conn->query($query);
}


 public function add($name, $price, $expiration_date, $quantity, $category_id) {
    $stmt = $this->conn->prepare(
        "INSERT INTO brands (name, price, expiration_date, quantity, category_id) VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sdsii", $name, $price, $expiration_date, $quantity, $category_id);
    return $stmt->execute();
}


    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM brands WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
