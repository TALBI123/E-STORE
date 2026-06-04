<?php

namespace App\Core;

abstract class Controller
{
    // public function __construct()
    // {
    //     // Démarrer la session automatiquement pour tous les contrôleurs
    //     if (session_status() === PHP_SESSION_NONE) {
    //         session_start();
    //     }
    // }

    protected function render(string $view, array $data = [])
    {
        extract($data);
        $base = dirname(__DIR__) . "/view/";
        $viewPath = $base . $view . ".php";
        if (!file_exists($viewPath)) {
            die("Vue introuvable : {$viewPath}");
        }
        require_once $base . 'layouts/header.php';
        require_once $viewPath;
        require_once $base . 'layouts/footer.php';
    }
    protected function redirect(string $url)
    {
        header("Location: {$url}");
        exit();
    }

    protected function isAuthenticated(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('/login');
        }
    }

    protected function isAdmin(): void
    {
        $this->isAuthenticated();
        if (($_SESSION['user_role'] ?? '') !== 'admin') {
            http_response_code(403);
            die("Accès refusé. Réservé aux administrateurs.");
        }
    }
}
