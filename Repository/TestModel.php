<?php

namespace App\Repository;

use App\Core\Model;

class TestModel extends Model
{
    protected string $table = 'test';
    public function getAllTest(): array
    {
        return $this->findAll();
    }
    public function getById(int $id): ?array
    {
        return $this->findById($id);
    }
}
