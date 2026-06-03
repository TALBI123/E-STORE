<?php
abstract class Controller
{
    protected function render(string $view, array $data = [])
    {
        extract($data);
        $base = dirname(__DIR__, 2) . "/views/";
        $viewPath = $base . $view . ".php";
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("Vue introuvable : {$viewPath}");
        }
        // Charge le header commun (navbar, CSS...)
        require_once $base . 'layouts/header.php';
        // Charge la vue spécifique
        require_once $viewPath;
        // Charge le footer commun (scripts JS, fermeture HTML)
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
