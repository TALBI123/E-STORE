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
        $this->render("/products/index", [
            'products' => $products,
            'totalPages' => $products['total'],
            'currentPage' => $products['current'],
            'categories' => $categories,

            // 'currentPage' => $page,
        ]);
    }
}
