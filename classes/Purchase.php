<?php
class Purchase {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Add a new purchase (with optional detail description)
    public function add($supplier_id, $quantity, $price_per_unit, $detail_description = null) {
        // Insert into purchases
        $stmt = $this->conn->prepare("
            INSERT INTO purchases (supplier_id, quantity, price_per_unit) 
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iid", $supplier_id, $quantity, $price_per_unit);
        if (!$stmt->execute()) {
            return false;
        }

        $purchase_id = $this->conn->insert_id;

        // Optionally insert into purchase_details
        if ($detail_description) {
            $stmt = $this->conn->prepare("
                INSERT INTO purchase_details (purchase_id, detail_description) 
                VALUES (?, ?)
            ");
            $stmt->bind_param("is", $purchase_id, $detail_description);
            $stmt->execute();
        }

        return $purchase_id;
    }

    // Get all purchases with supplier info
    public function getAll() {
        $sql = "
            SELECT 
                p.id, 
                s.name AS supplier_name, 
                p.quantity, 
                p.price_per_unit, 
                (p.quantity * p.price_per_unit) AS total_cost,
                p.purchase_date
            FROM purchases p
            JOIN suppliers s ON p.supplier_id = s.id
            ORDER BY p.purchase_date DESC
        ";
        return $this->conn->query($sql);
    }

    // Get purchase detail (if any) for a specific purchase
    public function getDetail($purchase_id) {
        $stmt = $this->conn->prepare("
            SELECT detail_description 
            FROM purchase_details 
            WHERE purchase_id = ?
        ");
        $stmt->bind_param("i", $purchase_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Delete a purchase (details will auto-delete due to foreign key cascade)
    public function delete($purchase_id) {
        $stmt = $this->conn->prepare("DELETE FROM purchases WHERE id = ?");
        $stmt->bind_param("i", $purchase_id);
        return $stmt->execute();
    }
}
?>
