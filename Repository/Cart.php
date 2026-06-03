<?php

namespace App\Model;

use App\Core\Model;

class Cart extends Model
{
    protected string $table = 'carts';

    /**
     * getByUser() — Retourne le panier d'un utilisateur.
     * Chaque utilisateur a un seul panier (contrainte UNIQUE user_id en BDD).
     *
     * @param int $userId
     * @return array|null
     */
    public function getByUser(int $userId): ?array
    {
        $results = $this->findBy('user_id', $userId);
        return $results[0] ?? null;
    }

    /**
     * getOrCreate() — Retourne le panier existant ou en crée un nouveau.
     * Garantit qu'un panier existe toujours pour l'utilisateur connecté.
     *
     * @param int $userId
     * @return int  L'ID du panier
     */
    public function getOrCreate(int $userId): int
    {
        $cart = $this->getByUser($userId);
        if ($cart) return $cart['id'];
        return $this->create(['user_id' => $userId]);
    }
}