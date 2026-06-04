<?php

namespace Controller;

use App\Core\Controller;
use App\Repository\Category;
use App\Repository\Product;

class CategoryController extends Controller
{
    private Category $categoryModel;
    private Product $productModel;

    public function __construct()
    {
        $this->categoryModel = new Category();
        $this->productModel = new Product();
    }

    public function show(string $slug): void
    {
        $category = $this->categoryModel->findBySlug($slug);

        if (!$category) {
            http_response_code(404);
            require_once dirname(__DIR__) . '/view/errors/404.php';
            return;
        }

        $this->render('products/index', [
            'products'    => $this->productModel->findBy('category_id', $category['id']),
            'categories'  => $this->categoryModel->findAll(),
            'filters'     => ['category_id' => $category['id']],
            'query'       => '',
            'totalPages'  => 1,
            'currentPage' => 1,
        ]);
    }
}