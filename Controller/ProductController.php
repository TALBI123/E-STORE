<?php

namespace Controller;

use App\Core\Controller;
// use App\Repository;
use App\Repository\Product;
use App\Repository\Category;

class ProductController  extends Controller
{
    private Product  $productModel;
    private Category $categoryModel;
    private const PRODUCTS_PER_PAGE = 10;
    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }
    public function index(){
        $page = max(1,(int)($_GET['page'] ?? 1));
        $products = $this->productModel->paginate($page, self::PRODUCTS_PER_PAGE);
        $categories = $this->categoryModel->findAll();
        $this->render('products/index', [
            'products' => $products["data"],
            'totalPages' => $products['pages'],
            'currentPage' => $products['current'],
            'categories' => $categories,

            // 'currentPage' => $page,
        ]);
    }

    public function show(string $slug): void
    {
        $product = $this->productModel->findBySlug($slug);

        if (!$product) {
            http_response_code(404);
            require_once dirname(__DIR__) . '/view/errors/404.php';
            return;
        }

        $this->render('products/show', [
            'product' => $product,
        ]);
    }

    public function search(): void
    {
        $query = trim($_GET['q'] ?? '');
        $filters = [
            'category_id' => (int) ($_GET['category'] ?? 0),
            'min_price'   => isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float) $_GET['min_price'] : null,
            'max_price'   => isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float) $_GET['max_price'] : null,
            'sort'        => $_GET['sort'] ?? '',
        ];

        $filters = array_filter($filters, static fn ($value) => $value !== null && $value !== 0 && $value !== '');

        $this->render('products/index', [
            'products'    => $this->productModel->search($query, $filters),
            'categories'  => $this->categoryModel->findAll(),
            'query'       => $query,
            'filters'     => $filters,
            'totalPages'  => 1,
            'currentPage' => 1,
        ]);
    }
    
}
