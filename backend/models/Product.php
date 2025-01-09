<?php
namespace Models;

use PDO;

class Product
{
    private $conn;
    private $table = 'produits';

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $query = "SELECT * FROM {$this->table} WHERE deleted_at IS NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $query = "INSERT INTO {$this->table} (name, description, prix, quantity) 
                  VALUES (:name, :description, :prix, :quantity)";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':name' => htmlspecialchars(strip_tags($data['name'])),
            ':description' => htmlspecialchars(strip_tags($data['description'])),
            ':prix' => floatval($data['prix']),
            ':quantity' => intval($data['quantity'])
        ]);
    }

    public function update($id, $data) {
        try {
            // First check if product exists
            $checkQuery = "SELECT id FROM {$this->table} WHERE id = :id AND deleted_at IS NULL";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->execute([':id' => $id]);
            
            if (!$checkStmt->fetch(PDO::FETCH_ASSOC)) {
                return false;
            }

            $query = "UPDATE {$this->table} 
                     SET name = :name, 
                         description = :description, 
                         prix = :prix, 
                         quantity = :quantity 
                     WHERE id = :id AND deleted_at IS NULL";
            
            $stmt = $this->conn->prepare($query);
            
            return $stmt->execute([
                ':id' => $id,
                ':name' => htmlspecialchars(strip_tags($data['name'])),
                ':description' => htmlspecialchars(strip_tags($data['description'])),
                ':prix' => floatval($data['prix']),
                ':quantity' => intval($data['quantity'])
            ]);
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function softDelete($id)
    {
        $query = "UPDATE {$this->table} SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([':id' => $id]);
    }
}