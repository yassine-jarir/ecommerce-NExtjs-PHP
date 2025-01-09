<?php
namespace Utils;

class Response
{
    // Send JSON response
    public static function sendResponse(
        int $statusCode = 200,
        string $message = '',
        $data = null
    ): void {
        // Clear previous output
        ob_clean();

        // Set HTTP response code
        http_response_code($statusCode);

        // Set headers
    

        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        // Prepare response
        $response = [
            'status' => $statusCode,
            'message' => $message
        ];

        // Add data if exists
        if ($data !== null) {
            $response['data'] = $data;
        }

        // Output JSON response
        echo json_encode($response);
        exit;
    }
}