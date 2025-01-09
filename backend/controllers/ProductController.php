<?php
namespace Controllers;

use Models\Product;

class ProductController
{
    private $product;

    public function __construct()
    {
        $db = (new \Config\Database())->connect();
        $this->product = new Product($db);
    }

    public function index()
    {
        try {
            $products = $this->product->getAll();
            echo json_encode(['success' => true, 'data' => $products]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function store()
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            // Basic validation
            if (empty($data['name']) || empty($data['prix']) || empty($data['quantity']) ||empty($data['description'])  ) {
                throw new \Exception('Missing required fields');
            }

            $result = $this->product->create($data);
            echo json_encode(['success' => true, 'message' => 'Product created successfully']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function update($id)
    {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (empty($data['name']) || empty($data['prix']) || empty($data['quantity'])) {
                throw new \Exception('Missing required fields');
            }

            $this->product->update($id, $data);
            echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }

    }

    public function delete($id)
    {
        try {
            $result = $this->product->softDelete($id);
            echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}