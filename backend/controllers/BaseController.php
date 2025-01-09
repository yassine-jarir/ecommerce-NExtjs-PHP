<?php
namespace Controllers;

abstract class BaseController
{
    protected $db;

    public function __construct()
    {
        $database = new \Config\Database();
        $this->db = $database->getConnection();
    }

    protected function sendJson($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}