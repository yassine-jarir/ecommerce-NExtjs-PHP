<?php

namespace Controllers;

use Models\User;
use Config\Database;
session_start();
class UserController
{
    private $user;
    private $userModel;
    public function __construct()
    {
        $this->user = new User((new Database())->connect());
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 86400,
                'path' => '/',
                'domain' => 'localhost',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);

        }
    }

    public function signin()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->name) || empty($data->email) || empty($data->password)) {
            echo json_encode(['error' => 'Name, email, and password are required']);
            return;
        }

        $result = $this->user->signin($data->name, $data->email, $data->password);

        if ($result) {
            // Create session after successful signup
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['user_name'] = $data->name;
            $_SESSION['user_email'] = $data->email;
            $_SESSION['user_role'] = 'client';

            // Generate CSRF token
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            echo json_encode([
                'message' => 'Signup successful',
                'user' => [
                    'name' => $data->name,
                    'email' => $data->email,
                    'role' => 'client'
                ],
                'csrf_token' => $_SESSION['csrf_token']
            ]);
        } else {
            echo json_encode(['error' => 'User already exists']);
        }
    }

    public function login()
    {
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->email) || empty($data->password)) {
            echo json_encode(['error' => 'Email and password are required']);
            return;
        }

        $result = $this->user->login($data->email, $data->password);

        if ($result) {
            // Set session cookie to remember the user
            setcookie('user', json_encode($result), time() + 86400, '/', 'localhost', true, true); // 1-day expiration

            // Create session
            $_SESSION['user_id'] = $result['id'];
            $_SESSION['user_name'] = $result['name'];
            $_SESSION['user_email'] = $result['email'];
            $_SESSION['user_role'] = $result['role'];

            // Generate CSRF token
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            echo json_encode([
                'message' => 'Login successful',
                'user' => $result,
                'csrf_token' => $_SESSION['csrf_token']
            ]);
        } else {
            echo json_encode(['error' => 'Invalid credentials']);
        }
    }

    public function checkAuth()
    {
        if (isset($_SESSION['user_id'])) {
            echo json_encode([
                'authenticated' => true,
                'user' => [
                    'id' => $_SESSION['user_id'],
                    'name' => $_SESSION['user_name'],
                    'email' => $_SESSION['user_email'],
                    'role' => $_SESSION['user_role']
                ]
            ]);
        } else {
            echo json_encode(['authenticated' => false]);
        }
    }

    public function logout()
    {
        // Clear the session cookie
        if (isset($_COOKIE['user'])) {
            setcookie('user', '', time() - 3600, '/', 'localhost', true, true);
        }

        // Clear all session variables
        $_SESSION = array();

        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/', 'localhost', true, true);
        }

        // Destroy the session
        session_destroy();

        echo json_encode([
            'success' => true,
            'message' => 'Logout successful'
        ]);
    }

    // active desactive 

    public function toggleUserActivation()
    {
        try {
            $data = json_decode(file_get_contents("php://input"));
            $userId = $data->user_id;

            if (!$data->user_id) {
                throw new \Exception($data->user_id);
            }

            $result = $this->user->toggleActivation($userId);

            http_response_code(200);
            echo json_encode($result);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getUsers()
    {
        try {
            $users = $this->user->getAllUsers();

            http_response_code(200);
            echo json_encode([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}