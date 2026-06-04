<?php
namespace App\Controller;

use App\Core\Controller;
use App\Model\Product;
use App\Model\Category;
use App\Model\Order;
use App\Model\User;
use App\Model\Visit;

class AdminController extends Controller
{
    private Product  $productModel;
    private Category $categoryModel;
    private Order    $orderModel;
    private User     $userModel;
    private Visit    $visitModel;

    public function __construct()
    {
        $this->productModel  = new Product();
        $this->categoryModel = new Category();
        $this->orderModel    = new Order();
        $this->userModel     = new User();
        $this->visitModel    = new Visit();
    }

    // ─────────────────────────────────────────────
    // DASHBOARD
    // ─────────────────────────────────────────────

    /**
     * dashboard() — Tableau de bord principal de l'admin.
     * Collecte toutes les statistiques du site :
     * nombre de commandes, chiffre d'affaires du mois,
     * clients, produits, dernières commandes, top produits,
     * et répartition géographique des visites.
     */
    public function dashboard(): void
    {
        $this->isAdmin();

        $stats = [
            'total_orders'      => count($this->orderModel->findAll()),
            'total_users'       => count($this->userModel->getClients()),
            'total_products'    => count($this->productModel->findAll()),
            'revenue_month'     => $this->orderModel->getMonthlyRevenue(),
            'recent_orders'     => $this->orderModel->getRecent(10),
            'top_products'      => $this->productModel->getTopByViews(5),
            'visits_by_country' => $this->visitModel->getByCountry(),
            'orders_by_status'  => $this->orderModel->countByStatus(),
            'revenue_chart'     => $this->orderModel->getLast6MonthsRevenue(),
        ];

        $this->render('admin/dashboard', [
            'pageTitle' => 'Dashboard Admin',
            'stats'     => $stats,
        ]);
    }

    // ─────────────────────────────────────────────
    // PRODUITS
    // ─────────────────────────────────────────────

    /**
     * products() — Liste tous les produits dans le panel admin.
     * Supporte la pagination et la recherche par nom.
     */
    public function products(): void
    {
        $this->isAdmin();

        $page     = max(1, (int) ($_GET['page'] ?? 1));
        $search   = trim($_GET['search'] ?? '');

        // Si recherche active, utilise search(), sinon paginate()
        if ($search !== '') {
            $products   = $this->productModel->search($search);
            $totalPages = 1;
            $current    = 1;
        } else {
            $result     = $this->productModel->paginate($page, 15);
            $products   = $result['data'];
            $totalPages = $result['pages'];
            $current    = $result['current'];
        }

        $this->render('admin/products/index', [
            'pageTitle'   => 'Gestion des produits',
            'products'    => $products,
            'categories'  => $this->categoryModel->findAll(),
            'totalPages'  => $totalPages,
            'currentPage' => $current,
            'search'      => $search,
            'csrf'        => $this->csrfToken(),
        ]);
    }

    /**
     * storeProduct() — Enregistre un nouveau produit (POST).
     * Traite l'upload d'image, génère le slug, insère en BDD.
     * Après insertion, redirige vers la liste avec un message de succès.
     */
    public function storeProduct(): void
    {
        $this->isAdmin();
        $this->verifyCsrf();

        $name        = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8');
        $price       = (float)  ($_POST['price']       ?? 0);
        $stock       = (int)    ($_POST['stock']        ?? 0);
        $categoryId  = (int)    ($_POST['category_id']  ?? 0);

        // Validation minimale
        if (empty($name) || $price <= 0) {
            $this->redirect('/admin/products?error=champs_requis');
        }

        // Génère un slug unique depuis le nom
        $slug = Product::generateSlug($name);
        // Si le slug existe déjà, ajoute un suffixe unique
        $existing = $this->productModel->findBy('slug', $slug);
        if (!empty($existing)) {
            $slug .= '-' . uniqid();
        }

        // Upload de l'image principale
        $imageName = $this->handleImageUpload('image', 'products');

        $id = $this->productModel->create([
            'name'        => $name,
            'slug'        => $slug,
            'description' => $description,
            'price'       => $price,
            'stock'       => $stock,
            'category_id' => $categoryId ?: null,
            'image'       => $imageName,
            'is_active'   => 1,
        ]);

        $this->redirect("/admin/products?created={$id}");
    }

    /**
     * editProduct() — Affiche le formulaire d'édition d'un produit (GET).
     * Charge le produit existant et toutes les catégories pour le select.
     *
     * @param string $id  ID du produit passé dans l'URL
     */
    public function editProduct(string $id): void
    {
        $this->isAdmin();

        $product = $this->productModel->findById((int) $id);
        if (!$product) {
            $this->redirect('/admin/products?error=introuvable');
        }

        // Charge aussi les images et attributs existants
        $details = $this->productModel->getWithDetails((int) $id);

        $this->render('admin/products/edit', [
            'pageTitle'  => 'Modifier le produit',
            'product'    => $details['product'],
            'images'     => $details['images'],
            'attributes' => $details['attributes'],
            'categories' => $this->categoryModel->findAll(),
        ]);
    }

    /**
     * updateProduct() — Met à jour un produit existant (POST).
     * Si une nouvelle image est uploadée, remplace l'ancienne.
     * Sinon, conserve l'image actuelle.
     *
     * @param string $id
     */
    public function updateProduct(string $id): void
    {
        $this->isAdmin();

        $intId = (int) $id;
        $product = $this->productModel->findById($intId);
        if (!$product) {
            $this->redirect('/admin/products?error=introuvable');
        }

        $name        = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8');
        $price       = (float) ($_POST['price']      ?? 0);
        $stock       = (int)   ($_POST['stock']       ?? 0);
        $categoryId  = (int)   ($_POST['category_id'] ?? 0);
        $isActive    = isset($_POST['is_active']) ? 1 : 0;

        // Nouvelle image uploadée ?
        $imageName = $product['image']; // Garde l'ancienne par défaut
        if (!empty($_FILES['image']['name'])) {
            // Supprime l'ancienne image si elle existe
            $this->deleteImageFile($product['image'], 'products');
            $imageName = $this->handleImageUpload('image', 'products');
        }

        $this->productModel->update($intId, [
            'name'        => $name,
            'description' => $description,
            'price'       => $price,
            'stock'       => $stock,
            'category_id' => $categoryId ?: null,
            'image'       => $imageName,
            'is_active'   => $isActive,
        ]);

        $this->redirect("/admin/products?updated={$intId}");
    }

    public function deleteProduct(string $id): void
    {
        $this->isAdmin();

        $intId   = (int) $id;
        $product = $this->productModel->findById($intId);

        if ($product) {
            // Supprime le fichier image du serveur
            $this->deleteImageFile($product['image'], 'products');
            // Supprime le produit (ON DELETE CASCADE supprime images et attributs liés)
            $this->productModel->delete($intId);
        }

        $this->redirect('/admin/products?deleted=1');
    }

    public function orders(): void
    {
        $this->isAdmin();

        $status = $_GET['status'] ?? '';
        $page   = max(1, (int) ($_GET['page'] ?? 1));

        if ($status !== '') {
            $orders     = $this->orderModel->findByStatus($status);
            $totalPages = 1;
        } else {
            $result     = $this->orderModel->paginateWithUsers($page, 20);
            $orders     = $result['data'];
            $totalPages = $result['pages'];
        }

        $this->render('admin/orders/index', [
            'pageTitle'  => 'Gestion des commandes',
            'orders'     => $orders,
            'totalPages' => $totalPages,
            'currentPage'=> $page,
            'status'     => $status,
        ]);
    }

    public function updateOrderStatus(string $id): void
    {
        $this->isAdmin();

        $intId  = (int) $id;
        $status = $_POST['status'] ?? '';

        // Vérifie que le statut est valide
        $allowed = ['pending','confirmed','processing','shipped','delivered','cancelled'];
        if (!in_array($status, $allowed, true)) {
            $this->redirect("/admin/orders?error=statut_invalide");
        }

        $order = $this->orderModel->findById($intId);
        if (!$order) {
            $this->redirect('/admin/orders?error=introuvable');
        }

        $this->orderModel->update($intId, ['status' => $status]);

        // Si annulée, remet les stocks en place
        if ($status === 'cancelled') {
            $this->orderModel->restoreStock($intId);
        }

        $this->redirect("/admin/orders?updated={$intId}");
    }

    public function users(): void
    {
        $this->isAdmin();

        $search = trim($_GET['search'] ?? '');
        $page   = max(1, (int) ($_GET['page'] ?? 1));

        if ($search !== '') {
            $users      = $this->userModel->searchUsers($search);
            $totalPages = 1;
        } else {
            $result     = $this->userModel->paginate($page, 20);
            $users      = $result['data'];
            $totalPages = $result['pages'];
        }

        $this->render('admin/users/index', [
            'pageTitle'   => 'Gestion des utilisateurs',
            'users'       => $users,
            'totalPages'  => $totalPages,
            'currentPage' => $page,
            'search'      => $search,
        ]);
    }

    public function toggleUser(string $id): void
    {
        $this->isAdmin();

        $intId = (int) $id;
        $user  = $this->userModel->findById($intId);

        if ($user) {
            // Inverse l'état actuel : 1→0 ou 0→1
            $this->userModel->update($intId, ['is_active' => $user['is_active'] ? 0 : 1]);
        }

        $this->redirect('/admin/users');
    }

    public function stats(): void
    {
        $this->isAdmin();

        $this->render('admin/stats', [
            'pageTitle'       => 'Statistiques',
            'revenue_chart'   => $this->orderModel->getLast6MonthsRevenue(),
            'top_products'    => $this->productModel->getTopByViews(10),
            'visits_country'  => $this->visitModel->getByCountry(),
            'visits_daily'    => $this->visitModel->getLast30Days(),
            'orders_status'   => $this->orderModel->countByStatus(),
        ]);
    }

    // ─────────────────────────────────────────────
    // MÉTHODES PRIVÉES (helpers)
    // ─────────────────────────────────────────────

    private function handleImageUpload(string $fieldName, string $subFolder): string
    {
        if (empty($_FILES[$fieldName]['name'])) {
            return '';
        }

        $file     = $_FILES[$fieldName];
        $allowed  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $mimeType = mime_content_type($file['tmp_name']);

        if (!in_array($mimeType, $allowed, true)) {
            $this->redirect('/admin/products?error=type_image_invalide');
        }

        if ($file['size'] > 5 * 1024 * 1024) { // 5 Mo max
            $this->redirect('/admin/products?error=image_trop_grande');
        }

        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('img_') . '.' . strtolower($ext);
        $dest     = dirname(__DIR__, 2) . "/public/assets/img/{$subFolder}/{$filename}";

        // Crée le dossier si inexistant
        if (!is_dir(dirname($dest))) {
            mkdir(dirname($dest), 0755, true);
        }

        move_uploaded_file($file['tmp_name'], $dest);
        return $filename;
    }

    private function deleteImageFile(string $filename, string $subFolder): void
    {
        if (empty($filename)) return;

        $path = dirname(__DIR__, 2) . "/public/assets/img/{$subFolder}/{$filename}";
        if (file_exists($path)) {
            unlink($path);
        }
    }
}