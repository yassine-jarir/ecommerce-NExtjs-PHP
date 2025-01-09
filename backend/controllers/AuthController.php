<?php

namespace Controllers;

use Models\User;

class AuthController
{
    private $user;

    public function __construct($db)
    {
        $this->user = new User($db);
    }

    public function login($email, $password)
    {
        $user = $this->user->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return ['status' => 'error', 'message' => 'Invalid email or password'];
        }

        // Generate session or token (example with session)
        session_start();
        $_SESSION['user_id'] = $user['id'];

        return ['status' => 'success', 'message' => 'Login successful'];
    }
}