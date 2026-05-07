<?php
class Quotation {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function addQuotation($itemId, $supplierId, $cost) {
        $stmt = $this->db->prepare("INSERT INTO quotations (item_id, supplier_id, cost) VALUES (?, ?, ?)");
        $stmt->bind_param("iid", $itemId, $supplierId, $cost);
        return $stmt->execute();
    }

    public function getQuotations($itemId) {
        $stmt = $this->db->prepare("SELECT q.*, s.name as supplier_name FROM quotations q JOIN suppliers s ON q.supplier_id = s.id WHERE q.item_id = ?");
        $stmt->bind_param("i", $itemId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllQuotations() {
        $result = $this->db->query("SELECT * FROM quotations");
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function deleteQuotationsForItem($itemId) {
        $stmt = $this->db->prepare("DELETE FROM quotations WHERE item_id = ?");
        $stmt->bind_param("i", $itemId);
        return $stmt->execute();
    }

    public function selectQuotation($itemId, $supplierId, $cost) {
        $stmt = $this->db->prepare("INSERT INTO selected_quotations (item_id, supplier_id, cost) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE supplier_id = ?, cost = ?");
        $stmt->bind_param("iidid", $itemId, $supplierId, $cost, $supplierId, $cost);
        return $stmt->execute();
    }
}
?>

