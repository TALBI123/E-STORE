<?php

namespace App\Repository;

use App\Core\Model;

class Product extends Model
{
    protected string $table = 'products';

    public function findBySlug(string $slug): ?array
    {
        $results = $this->findBy("slug", $slug);
        return $results ? $results[0] : null;
    }
    /**
     * search() — Recherche de produits par mot-clé et/ou filtres.
     * Utilise MATCH...AGAINST pour la recherche full-text (index FULLTEXT en BDD).
     * Les filtres sont appliqués dynamiquement selon ce qui est fourni.
     *
     * @param string $query    Terme de recherche (peut être vide)
     * @param array  $filters  Filtres optionnels :
     *                         - category_id : int
     *                         - min_price   : float
     *                         - max_price   : float
     *                         - sort        : 'price_asc'|'price_desc'|'newest'
     * @return array  Liste des produits correspondants
     */
    public function search(string $query = '', array $filters = []): array
    {
        $sql    = "SELECT p.*, c.name AS category_name
                   FROM products p
                   LEFT JOIN categories c ON p.category_id = c.id
                   WHERE p.is_active = 1";
        $params = [];

        if (!empty($query)) {
            $sql     .= " AND MATCH(p.name, p.description) AGAINST(? IN BOOLEAN MODE)";
            $params[] = $query . '*';
        }
        if (!empty($filters['category_id'])) {
            $sql     .= " AND p.category_id = ?";
            $params[] = (int) $filters['category_id'];
        }
        if (isset($filters['min_price'])) {
            $sql     .= " AND p.price >= ?";
            $params[] = (float) $filters['min_price'];
        }
        if (isset($filters['max_price'])) {
            $sql     .= " AND p.price <= ?";
            $params[] = (float) $filters['max_price'];
        }

        // Tri
        $sql .= match ($filters['sort'] ?? '') {
            'price_asc'  => " ORDER BY p.price ASC",
            'price_desc' => " ORDER BY p.price DESC",
            'newest'     => " ORDER BY p.created_at DESC",
            default      => " ORDER BY p.views_count DESC",
        };

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    /**
     * getWithDetails() — Récupère un produit avec ses images et attributs.
     * Fait 3 requêtes groupées pour éviter les jointures complexes.
     *
     * @param int $id
     * @return array|null  ['product' => [...], 'images' => [...], 'attributes' => [...]]
     */
    public function getWithDetails(int $id): ?array
    {
        $product = $this->findById($id);
        if (!$product) return null;

        $imgStmt = $this->pdo->prepare(
            "SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, sort_order ASC"
        );
        $imgStmt->execute([$id]);

        $attrStmt = $this->pdo->prepare(
            "SELECT * FROM product_attributes WHERE product_id = ?"
        );
        $attrStmt->execute([$id]);

        return [
            'product'    => $product,
            'images'     => $imgStmt->fetchAll(),
            'attributes' => $attrStmt->fetchAll(),
        ];
    }
    public function incrementViews(int $id): void
    {
        $this->pdo->prepare(
            "UPDATE products SET views_count = views_count + 1 WHERE id = ?"
        )->execute([$id]);
    }

    /**
     * getTopByViews() — Retourne les N produits les plus vus.
     * Utilisé sur la page d'accueil et dans le dashboard admin.
     *
     * @param int $limit  Nombre de produits à retourner
     * @return array
     */
    public function getTopByViews(int $limit = 8): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM products WHERE is_active = 1
             ORDER BY views_count DESC LIMIT ?"
        );
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    /**
     * generateSlug() — Génère un slug URL-friendly depuis un nom.
     * Ex: "Chaussure Sport Nike" → "chaussure-sport-nike"
     * Gère les caractères accentués français.
     *
     * @param string $name
     * @return string
     */
    public static function generateSlug(string $name): string
    {
        $name = mb_strtolower(trim($name), 'UTF-8');
        $map  = [
            'à' => 'a',
            'â' => 'a',
            'é' => 'e',
            'è' => 'e',
            'ê' => 'e',
            'î' => 'i',
            'ô' => 'o',
            'ù' => 'u',
            'û' => 'u',
            'ç' => 'c',
            'œ' => 'oe',
            'æ' => 'ae'
        ];
        $name = strtr($name, $map);
        $name = preg_replace('/[^a-z0-9\s-]/', '', $name);
        $name = preg_replace('/[\s-]+/', '-', $name);
        return trim($name, '-');
    }
}
