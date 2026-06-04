<?php

namespace App\Repository;

use App\Core\Model;

class OrderItem extends Model
{
    protected string $table = 'order_items';
    public function getByOrder(int $orderId): array
    {
        $stmt = $this->pdo->prepare("SELECT oi.*, p.name AS product_name FROM
         {$this->table} oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
}
