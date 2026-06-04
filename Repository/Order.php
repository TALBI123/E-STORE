<?php

namespace App\Repository;

use App\Core\Model;

class Order extends Model
{
    private string $table = 'orders';

    public function createOrder(int $userId, float $total): int
    {
        return $this->create([
            'user_id' => $userId,
            'total' => $total,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
    public function getOrdersWithItems(int $userId): array
    {
        $stmt = $this->pdo->prepare('SElECT * FROM orders WHERE user_id = ? ORDER BY ordered_at DESC');
        // $stmt = $this->pdo->prepare("SELECT o.*, oi.*, p.name AS product_name FROM {$this->table} o 
        // JOIN order_items oi ON o.id = oi.order_id 
        // JOIN products p ON oi.product_id = p.id 
        // WHERE o.user_id = ?");
        // $stmt->execute([$userId]);
        // return $stmt->fetchAll();
    }
}
