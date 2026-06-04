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

    public function get(string $path, string $action): void
    {
        $this->routes['GET'][$path] = $action;
        // echo "GET route added: {$path} => {$action}<br>";
    }
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
        $uri = $uri ?: '/';
        // echo "URI traitée pour le routage : {$uri}<br>";
        // echo "Méthode HTTP : {$method}<br>";
        // echo $this->routes[$method][$uri] ?? "Aucune route trouvée pour cette URI et méthode.<br>";
        // echo print_r($this->routes[$method], true);
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
                
                // echo "Controller: {$controllerName}, Action: {$actionMethod}  waa3<br>";
                $controllerClass = "\\Controller\\{$controllerName}";
                // echo "Controller class: {$controllerClass}<br>";
                // CORRECTION 4 : Vérifier que la classe existe
                if (!class_exists($controllerClass)) {
                    http_response_code(500);
                    die("Classe contrôleur introuvable : {$controllerClass}");
                }

                $controller = new $controllerClass();
                $controller->$actionMethod(...$matches);
                return;
            }
        }
        // echo dirname(__DIR__, 1);
        // echo "<br>URI demandée : {$uri}";

        http_response_code(404);
        require_once dirname(__DIR__, 1) . '\view\errors\404.php';
    }
}
