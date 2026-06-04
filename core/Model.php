<?php

namespace App\Core;

use App\Core;
use PDO;

abstract class Model
{
    protected PDO $pdo;
    protected string $table;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }
    //findAll() — Récupère tous les enregistrements de la table.
    public function findAll(): array
    {
        return  $this->pdo->query("SELECT * FROM {$this->table}")->fetchAll();
    }
    //
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function findBy(string $column, mixed $value): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM {$this->table} WHERE `{$column}` = ?"
        );
        $stmt->execute([$value]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $columns      = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $stmt = $this->pdo->prepare(
            "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})"
        );
        $stmt->execute(array_values($data));
        return (int) $this->pdo->lastInsertId();
    }
    public function update(int $id, array $data): bool
    {
        $set  = implode(', ', array_map(fn($col) => "`{$col}` = ?", array_keys($data)));
        $stmt = $this->pdo->prepare(
            "UPDATE {$this->table} SET {$set} WHERE id = ?"
        );
        return $stmt->execute([...array_values($data), $id]);
    }

    /**
     * delete() — Supprime un enregistrement par son ID.
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * paginate() — Récupère une page de résultats (pour la pagination).
     * Ex: paginate(2, 12) → les 12 enregistrements de la page 2 (offset 12)
     *
     * @param int $page     Numéro de page (commence à 1)
     * @param int $perPage  Nombre d'éléments par page
     * @return array  ['data' => [...], 'total' => 48, 'pages' => 4, 'current' => 2]
     */
    public function paginate(int $page = 1, int $perPage = 5): array
    {
        $offset = ($page - 1) * $perPage;
        $total  = (int) $this->pdo
            ->query("SELECT COUNT(*) FROM {$this->table}")
            ->fetchColumn();

        $stmt = $this->pdo->prepare(
            "SELECT * FROM {$this->table} LIMIT ? OFFSET ?"
        );
        $stmt->execute([$perPage, $offset]);

        return [
            'data'    => $stmt->fetchAll(),
            'total'   => $total,
            'pages'   => (int) ceil($total / $perPage),
            'current' => $page,
        ];
    }
}
