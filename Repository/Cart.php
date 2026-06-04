<?php

namespace App\Repository;

use App\Core\Model;

class Cart extends Model
{
    protected string $table = 'carts';

    public function getByUser(int $userId): ?array
    {
        $results = $this->findBy('user_id', $userId);
        return $results[0] ?? null;
    }

    public function getOrCreate(int $userId): int
    {
        $cart = $this->getByUser($userId);
        if ($cart) return $cart['id'];
        return $this->create(['user_id' => $userId]);
    }
}