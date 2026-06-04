<?php
// app/Repository/CartItem.php
namespace App\Repository;

use App\Core\Model;

class CartItem extends Model
{
    protected string $table = 'cart_items';


    //   getCartItems() — Récupère les articles du panier avec les détails produit.
    //  Fait un JOIN pour avoir le nom, l'image du produit dans la même requête.

    public function getCartItems(int $cartId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT ci.*, p.name AS product_name, p.image AS product_image,
                    p.stock AS product_stock
             FROM cart_items ci
             INNER JOIN products p ON ci.product_id = p.id
             WHERE ci.cart_id = ?"
        );
        $stmt->execute([$cartId]);
        return $stmt->fetchAll();
    }


    //  addOrUpdate() — Ajoute un produit ou incrémente sa quantité si déjà présent.
    //  Utilise INSERT ... ON DUPLICATE KEY UPDATE (nécessite UNIQUE(cart_id, product_id)).
    public function addOrUpdate(int $cartId, int $productId, int $quantity, float $price): void
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO cart_items (cart_id, product_id, quantity, price_snapshot)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                quantity = quantity + VALUES(quantity)"
        );
        $stmt->execute([$cartId, $productId, $quantity, $price]);
    }


    //  updateQuantity() — Met à jour la quantité d'un article.
    //  Si quantité = 0, supprime l'article du panier.
    public function updateQuantity(int $itemId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->delete($itemId);
            return;
        }
        $this->update($itemId, ['quantity' => $quantity]);
    }

    
    //   clearCart() — Vide complètement le panier.
    //   Appelé après la validation d'une commande.
 
    public function clearCart(int $cartId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM cart_items WHERE cart_id = ?");
        $stmt->execute([$cartId]);
    }

    
    //   getTotal() — Calcule le montant total du panier.
    //   Utilise price_snapshot (le prix au moment de l'ajout, pas le prix actuel).
     
    public function getTotal(int $cartId): float
    {
        $stmt = $this->pdo->prepare(
            "SELECT SUM(quantity * price_snapshot) FROM cart_items WHERE cart_id = ?"
        );
        $stmt->execute([$cartId]);
        return (float) $stmt->fetchColumn();
    }
}
