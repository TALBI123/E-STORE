<?php
    require_once "core/Router.php";

    $route = Router::getInstance();
    // $route->get("/tps_php/projet/index.php?controller=auth&action=login", "AuthController@login");
    echo "Router instance created";