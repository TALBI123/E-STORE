<?php

namespace App\Repository;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        $results = $this->findBy("email", $email);
        return $results ? $results[0] : null;
    }
    public function emailIsExist(string $email): bool{
        return $this->findByEmail($email) !== null;
    }
    public function getClinets ():array{
        return $this->findBy("role", "client");
    }
}
