<?php
class Router
{

    private array $routes = [];
    private static ?Router $instance = null;

    private function __construct()
    {
        echo "Hi Ana howa Router<br>";
    }

    public static function getInstance(): Router
    {
        if (self::$instance === null) {
            self::$instance = new Router();
        }
        return self::$instance;
    }

    /**
     * get() — Enregistre une route pour la méthode HTTP GET.
     * @param string $path    Ex: '/products/:id'
     * @param string $action  Ex: 'ProductController@show'
     */
    public function get(string $path, string $action): void
    {
        $this->routes['GET'][$path] = $action;
    }

    /**
     * post() — Enregistre une route pour la méthode HTTP POST.
     * Utilisé pour les formulaires (login, ajout au panier, commande...).
     */
    public function post(string $path, string $action): void
    {
        $this->routes['POST'][$path] = $action;
    }

    /**
     * dispatch() — Cœur du routeur.
     * Parcourt toutes les routes enregistrées, compare l'URL courante,
     * instancie le contrôleur et appelle la méthode correspondante.
     *
     * @param string $requestUri  $_SERVER['REQUEST_URI'] (ex: '/products/chaussure-sport')
     * @param string $method      $_SERVER['REQUEST_METHOD'] (GET ou POST)
     */
    public function dispatch(string $requestUri, string $method): void
    {
        // Nettoie l'URL : supprime les query strings (?page=2) et le slash final
        $BASE_PATH = "/tps_php/projet";
        $uri = parse_url($requestUri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';
        $uri = substr($uri, strlen($BASE_PATH));
        foreach ($this->routes[$method] ?? [] as $pattern => $action) {
            // Convertit ':id' ou ':slug' en groupe de capture regex
            // Ex: '/products/:slug' → '#^/products/([^/]+)$#'
            $regex = preg_replace('/:([a-z_]+)/', '([^/]+)', $pattern);
            $regex = '#^' . $regex . '$#';

            if (preg_match($regex, $uri, $matches)) {
                // $matches[0] = URL complète, $matches[1..n] = paramètres capturés
                array_shift($matches);

                // Sépare 'ProductController@show' en ['ProductController', 'show']
                [$controllerName, $actionMethod] = explode('@', $action);
                $controllerClass = "\\controller\\{$controllerName}";
                echo "Controller class: {$controllerClass}<br>";
                // CORRECTION 4 : Vérifier que la classe existe
                if (!class_exists($controllerClass)) {
                    http_response_code(500);
                    die("Classe contrôleur introuvable : {$controllerClass}");
                }

                // Instancie le contrôleur et appelle la méthode avec les paramètres
                $controller = new $controllerClass();
                $controller->$actionMethod(...$matches);
                return;
            }
        }
        echo dirname(__DIR__, 1);
        echo "<br>URI demandée : {$uri}";

        http_response_code(404);
        require_once dirname(__DIR__, 1) . '\view\errors\404.php';
    }
}
