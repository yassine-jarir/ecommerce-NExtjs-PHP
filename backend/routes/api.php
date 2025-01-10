<?php
// routes/api.php - Update the requires at the top
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../controllers/OrderController.php';
// Add error reporting
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

use Controllers\OrderController;
use Controllers\UserController;
use Controllers\ProductController;
$route = $_GET['route'] ?? '';
$id = $_GET['id'] ?? null;
try {

    switch ($route) {
        case 'signup':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new UserController();
                $controller->signin();
            } else {
                echo json_encode(['error' => 'Invalid request method. Expected POST.']);
            }
            break;

        case 'login':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new UserController();
                $controller->login();
            } else {
                echo json_encode(['error' => 'Invalid request method. Expected POST.']);
            }
            break;

        case 'logout':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new UserController();
                $controller->logout();
            } else {
                echo json_encode(['error' => 'Invalid request method. Expected POST.']);
            }
            break;

        case 'check-auth':
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $controller = new UserController();
                $controller->checkAuth();
            } else {
                echo json_encode(['error' => 'Invalid request method. Expected GET.']);
            }
            break;


        case 'products':

            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $controller = new ProductController();
                $controller->index();
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new ProductController();

                $controller->store();
            }
            break;

        case 'products/update':
            if ($_SERVER['REQUEST_METHOD'] === 'PUT' && $id) {
                $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

                $controller = new ProductController();
                $controller->update($id);
            }
            break;

        case 'products/delete':
            if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && $id) {
                $controller = new ProductController();
                $controller->delete($id);
            }
            break;

        default:
            echo json_encode(['error' => 'Route not found.']);
            break;
        // active desactive
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
        // order
        case 'orders/create':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new OrderController();
                $controller->create();
            }
            break;

        case 'orders/user':
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $controller = new OrderController();
                $controller->getUserOrders();
            }
            break;

        case 'orders/all':
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $controller = new OrderController();
                $controller->getAllOrders();
            }
            break;

        case 'orders/stats':
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $controller = new OrderController();
                $controller->getOrderStats();
            }
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}