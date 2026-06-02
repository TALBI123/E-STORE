<?php


class Router {
    private $routes = [];
    private static $instance ;
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Router();
        }
        return self::$instance;
    }
    private function __construct() {
        echo "Router instance created";
    }
    public function get($path,$action){
        $this->routes["GET"][$path] = $action;
        echo "Route GET $path added";
    }   
}