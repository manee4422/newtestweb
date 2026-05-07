<?php
class Item {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function addItem($name, $quantity) {
        $stmt = $this->db->prepare("INSERT INTO items (name, quantity) VALUES (?, ?)");
        $stmt->bind_param("si", $name, $quantity);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function getItems() {
        $result = $this->db->query("SELECT * FROM items");
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }

    public function getItem($id) {
        $stmt = $this->db->prepare("SELECT * FROM items WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateItem($id, $name, $quantity) {
        $stmt = $this->db->prepare("UPDATE items SET name = ?, quantity = ? WHERE id = ?");
        $stmt->bind_param("sii", $name, $quantity, $id);
        return $stmt->execute();
    }

    public function deleteItem($id) {
        $stmt = $this->db->prepare("DELETE FROM items WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function searchItems($term) {
        $term = "%$term%";
        $stmt = $this->db->prepare("SELECT * FROM items WHERE name LIKE ?");
        $stmt->bind_param("s", $term);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

