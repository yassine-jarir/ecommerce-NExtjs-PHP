<?php
namespace Models;

use PDO;

class Order {
    private $conn;
    private $table = 'commades';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT c.*, u.name as user_name 
                  FROM {$this->table} c 
                  JOIN users u ON c.user_id = u.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMonthlyStats() {
        $query = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
                  COUNT(*) as order_count, SUM(total) as total_revenue 
                  FROM {$this->table} 
                  GROUP BY DATE_FORMAT(created_at, '%Y-%m') 
                  ORDER BY month DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($userId, $items) {
        $this->conn->beginTransaction();
        
        try {
            // Calculate total
            $total = array_reduce($items, function($sum, $item) {
                return $sum + ($item['prix'] * $item['quantity']);
            }, 0);

            // Create order
            $query = "INSERT INTO {$this->table} (user_id, total) VALUES (:user_id, :total)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':user_id' => $userId, ':total' => $total]);
            
            $orderId = $this->conn->lastInsertId();

            // Insert order items
            $itemQuery = "INSERT INTO order_items (commades_id, produits_id, quantity, prix) 
                         VALUES (:commades_id, :produits_id, :quantity, :prix)";
            $itemStmt = $this->conn->prepare($itemQuery);
            
            foreach ($items as $item) {
                $itemStmt->execute([
                    ':commades_id' => $orderId,
                    ':produits_id' => $item['produits_id'],
                    ':quantity' => $item['quantity'],
                    ':prix' => $item['prix']
                ]);
            }

            $this->conn->commit();
            return true;
        } catch (\Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
}