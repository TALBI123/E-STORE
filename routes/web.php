<?php
// use core\Router;
require_once "core/Router.php";

$router = Router::getInstance();

// ── Test ──────────────────────────────────
$router->get("/test","TestController@test");
// ── Pages publiques ──────────────────────────────────
$router->get('/',                          'HomeController@index');
$router->get('/products',                  'ProductController@index');
$router->get('/products/:slug',            'ProductController@show');
$router->get('/search',                    'ProductController@search');
$router->get('/categories/:slug',          'CategoryController@show');

// ── Authentification ─────────────────────────────────
$router->get('/login',                     'AuthController@showLogin');
$router->post('/login',                    'AuthController@login');
$router->get('/register',                  'AuthController@showRegister');
$router->post('/register',                 'AuthController@register');
$router->get('/logout',                    'AuthController@logout');

// ── Panier ────────────────────────────────────────────
$router->get('/cart',                      'CartController@index');
$router->post('/cart/add',                 'CartController@add');
$router->post('/cart/update/:id',          'CartController@update');
$router->get('/cart/remove/:id',           'CartController@remove');

// ── Commandes ─────────────────────────────────────────
$router->get('/orders',                    'OrderController@index');
$router->post('/orders/checkout',          'OrderController@checkout');
$router->get('/orders/:id',               'OrderController@show');

// ── Administration ────────────────────────────────────
$router->get('/admin/dashboard',           'AdminController@dashboard');
$router->get('/admin/products',            'AdminController@products');
$router->post('/admin/products',           'AdminController@storeProduct');
$router->get('/admin/products/:id/edit',   'AdminController@editProduct');
$router->post('/admin/products/:id',       'AdminController@updateProduct'); 
$router->get('/admin/products/:id/delete', 'AdminController@deleteProduct');
$router->get('/admin/orders',              'AdminController@orders');
$router->post('/admin/orders/:id/status',  'AdminController@updateOrderStatus');
$router->get('/admin/users',               'AdminController@users');
$router->get('/admin/stats',               'AdminController@stats');
