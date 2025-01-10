<?php
namespace Models;

use PDO;
use Exception;

class Order {
    private $conn;
    private $table = 'commades';
    private $items_table = 'order_items';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($userId, $items) {
        try {
            $this->conn->beginTransaction();

            // Calculate total
            $total = array_reduce($items, function($sum, $item) {
                return $sum + ($item['prix'] * $item['quantity']);
            }, 0);

            // Create main order
            $query = "INSERT INTO " . $this->table . " (user_id, total) VALUES (:user_id, :total)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':user_id' => $userId,
                ':total' => $total
            ]);

            $orderId = $this->conn->lastInsertId();

            // Insert order items
            $itemQuery = "INSERT INTO " . $this->items_table . 
                        " (commades_id, produits_id, quantity, prix) VALUES 
                        (:commades_id, :produits_id, :quantity, :prix)";
            $itemStmt = $this->conn->prepare($itemQuery);

            foreach ($items as $item) {
                $itemStmt->execute([
                    ':commades_id' => $orderId,
                    ':produits_id' => $item['produits_id'],
                    ':quantity' => $item['quantity'],
                    ':prix' => $item['prix']
                ]);

                // Update product quantity
                $this->updateProductQuantity($item['produits_id'], $item['quantity']);
            }

            $this->conn->commit();
            return [
                'order_id' => $orderId,
                'total' => $total
            ];
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw new Exception($e->getMessage());
        }
    }

    private function updateProductQuantity($productId, $quantity) {
        $query = "UPDATE produits 
                 SET quantity = quantity - :quantity 
                 WHERE id = :id AND quantity >= :quantity";
        $stmt = $this->conn->prepare($query);
        $result = $stmt->execute([
            ':id' => $productId,
            ':quantity' => $quantity
        ]);

        if ($stmt->rowCount() === 0) {
            throw new Exception("Insufficient stock for product ID: " . $productId);
        }
    }

    public function getOrdersByUser($userId) {
        $query = "SELECT c.*, u.name as user_name 
                 FROM " . $this->table . " c
                 JOIN users u ON c.user_id = u.id 
                 WHERE c.user_id = :user_id 
                 ORDER BY c.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $userId]);
        
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get items for each order
        foreach ($orders as &$order) {
            $order['items'] = $this->getOrderItems($order['id']);
        }
        
        return $orders;
    }

    public function getAllOrders() {
        $query = "SELECT c.*, u.name as user_name 
                 FROM " . $this->table . " c
                 JOIN users u ON c.user_id = u.id 
                 ORDER BY c.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get items for each order
        foreach ($orders as &$order) {
            $order['items'] = $this->getOrderItems($order['id']);
        }
        
        return $orders;
    }

    private function getOrderItems($orderId) {
        $query = "SELECT oi.*, p.name as product_name 
                 FROM " . $this->items_table . " oi
                 JOIN produits p ON oi.produits_id = p.id 
                 WHERE oi.commades_id = :order_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':order_id' => $orderId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMonthlyOrderStats() {
        $query = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as order_count,
                    SUM(total) as total_revenue
                 FROM " . $this->table . "
                 GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                 ORDER BY month DESC
                 LIMIT 12";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}