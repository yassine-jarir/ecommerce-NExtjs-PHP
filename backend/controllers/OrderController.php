<?php
class OrderController {
    private $order;

    public function __construct($db) {
        $this->order = new Order($db);
    }

    public function create($data) {
        if($this->validateOrder($data)) {
            $this->order->user_id = $data['user_id'];
            $this->order->total = floatval($data['total']);
            $this->order->status = 'pending';
            return $this->order->create();
        }
        return false;
    }

    public function getStats() {
        return $this->order->getMonthlyStats();
    }

    private function validateOrder($data) {
        return !empty($data['user_id']) && 
               is_numeric($data['total']);
    }
}