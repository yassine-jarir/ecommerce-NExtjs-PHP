<?php
namespace Controllers;

use Models\Order;
use Config\Database;

class OrderController
{
    private $order;

    public function __construct()
    {
        $this->order = new Order((new Database())->connect());
    }

    public function create()
    {
        try {
            //  if (!isset($_SESSION['user_id'])) {
            //     throw new \Exception('User not authenticated');
            // }

            $data = json_decode(file_get_contents("php://input"), true);

            // Validate input
            if (empty($data['items'])) {
                throw new \Exception('No items in order');
            }

            foreach ($data['items'] as $item) {
                if (!isset($item['produits_id']) || !isset($item['quantity']) || !isset($item['prix'])) {
                    throw new \Exception('Invalid item data');
                }
            }

            $result = $this->order->create($_SESSION['user_id'], $data['items']);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getUserOrders()
    {
        try {
            // if (!isset($_SESSION['user_id'])) {
            //     throw new \Exception('User not authenticated');
            // }

            $orders = $this->order->getOrdersByUser($_SESSION['user_id']);

            echo json_encode([
                'success' => true,
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getAllOrders()
    {
        try {
            // Check if user is admin
            // if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            //     throw new \Exception('Unauthorized access');
            // }

            $orders = $this->order->getAllOrders();

            echo json_encode([
                'success' => true,
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getOrderStats()
    {
        try {
            // if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            //     throw new \Exception('Unauthorized access');
            // }

            $stats = $this->order->getMonthlyOrderStats();

            echo json_encode([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}