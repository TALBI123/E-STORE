<?php
namespace App\Repository;
use App\Core\Model;
class Category extends Model
{
    protected string $table = 'categories';

    public function findBySlug(string $slug): ?array
    {
        $results = $this->findBy("slug", $slug);
        return $results ? $results[0] : null;
    }
}