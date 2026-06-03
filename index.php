<?php
require_once "core/Router.php";
require_once "routes/web.php";
require_once  __DIR__ . "/bootstrap.php";
// require_once "config/test.php";
$router = Router::getInstance();
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
// $visitorCounter = new VisitorCounter();
// $visitorCounter->trackVisit($_SERVER['REQUEST_URI']);

// // Stocker les stats en session pour affichage rapide
// if (!isset($_SESSION['stats_cached']) || rand(1, 100) === 1) {
//     $_SESSION['stats'] = [
//         'total_visitors' => $visitorCounter->getTotalUniqueVisitors(),
//         'today_visitors' => $visitorCounter->getTodayUniqueVisitors(),
//         'online_now' => $visitorCounter->getOnlineVisitors(),
//         'today_views' => $visitorCounter->getTodayPageViews()
//     ];
//     $_SESSION['stats_cached'] = true;
// }

