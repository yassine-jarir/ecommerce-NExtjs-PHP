// models/User.php
<?php

namespace Models;

use PDO;
use Config\Database;
use Exception;

class User {
    private $conn;
    private $table = 'users';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function toggleActivation($userId) {
        try {
            // First get the current status
            $query = "SELECT is_active FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$user) {
                throw new Exception("User not found");
            }

            // Toggle the status
            $newStatus = !$user['is_active'];
            
            $query = "UPDATE " . $this->table . " 
                     SET is_active = :is_active 
                     WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':is_active', $newStatus, PDO::PARAM_BOOL);
            $stmt->bindParam(':id', $userId);

            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'User status updated successfully',
                    'new_status' => $newStatus
                ];
            }
            
            throw new Exception("Error updating user status");
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getAllUsers() {
        try {
            $query = "SELECT id, name, email, role, is_active, created_at 
                     FROM " . $this->table . " 
                     ORDER BY created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}

// controllers/UserController.php
<?php

namespace Controllers;

use Models\User;
use Exception;

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function toggleUserActivation() {
        try {
            // Verify if the request is from an admin
            session_start();
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
                throw new Exception("Unauthorized access");
            }

            // Get user ID from request
            $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
            if (!$userId) {
                throw new Exception("Invalid user ID");
            }

            $result = $this->userModel->toggleActivation($userId);
            
            http_response_code(200);
            echo json_encode($result);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getUsers() {
        try {
            // Verify if the request is from an admin
            session_start();
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
                throw new Exception("Unauthorized access");
            }

            $users = $this->userModel->getAllUsers();
            
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $users
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
<!-- } -->
// Add these cases to your switch statement in routes/api.php

case 'users':
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller = new UserController();
        $controller->getUsers();
    } else {
        echo json_encode(['error' => 'Invalid request method. Expected GET.']);
    }
    break;

case 'users/toggle-activation':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new UserController();
        $controller->toggleUserActivation();
    } else {
        echo json_encode(['error' => 'Invalid request method. Expected POST.']);
    }
    break;



    // To toggle user activation
await fetch('http://your-api/index.php?route=users/toggle-activation', {
  method: 'POST',
  credentials: 'include',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({ user_id: userId })
});

// To get all users
await fetch('http://your-api/index.php?route=users', {
  method: 'GET',
  credentials: 'include'
});