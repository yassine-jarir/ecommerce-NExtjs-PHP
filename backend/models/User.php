<?php
namespace Models;

use PDO;
use Config\Database;
use Exception;
class User
{
    private $conn;
    private $table = 'users';

    public $id;
    public $name;
    public $email;
    public $password;
    public $role;
    public $is_active;
 
  
    public function __construct($db)
    {
        $this->conn = $db;

    }


    public function signin($name, $email, $password)
    {
        // Check if the user already exists
        $query = "SELECT id FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return false;   
        }

        // Insert the new user
        $query = "INSERT INTO " . $this->table . " (name, email, password, role, is_active) 
                  VALUES (:name, :email, :password, 'client', 1)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', password_hash($password, PASSWORD_BCRYPT));  

        if ($stmt->execute()) {
            
            $id = $this->conn->lastInsertId();
            return [
                'id' => $id,
                'name' => $name,
                'email' => $email
            ];
        }

        return false;  
    }
    // Login  
    public function login($email, $password)
    {
        $query = "SELECT id, name, email, password, role, is_active FROM " . $this->table . " WHERE email = :email AND is_active = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (password_verify($password, $row['password'])) {
                return [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'role' => $row['role']
                ];
            }
        }

        return false;
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