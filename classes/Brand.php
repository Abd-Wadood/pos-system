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
    if ($quantity > 500) {
        return false; // Reject if more than 500
    }

    // Check if brand with same name and category exists
    $stmt = $this->conn->prepare("SELECT id, quantity FROM brands WHERE name = ? AND category_id = ?");
    $stmt->bind_param("si", $name, $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $new_quantity = $row['quantity'] + $quantity;
        if ($new_quantity > 500) {
            return false; // Reject if total exceeds 500
        }

        // Update existing brand quantity
        $stmt = $this->conn->prepare("UPDATE brands SET quantity = ?, price = ?, expiration_date = ? WHERE id = ?");
        $stmt->bind_param("idsi", $new_quantity, $price, $expiration_date, $row['id']);
        return $stmt->execute();
    }

    // Insert new brand if not exists
    $stmt = $this->conn->prepare("INSERT INTO brands (name, price, expiration_date, quantity, category_id) VALUES (?, ?, ?, ?, ?)");
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

