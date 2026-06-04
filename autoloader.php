<?php

spl_autoload_register(function ($classe) {
    $classe = str_replace('App\\', '', $classe);

    $chemin = __DIR__ . '/' . str_replace('\\', '/', $classe) . '.php';
    // echo "Trying to load: $chemin<br>"; // Debugging line
    // Nettoyer
    $chemin = str_replace('//', '/', $chemin);
    // echo "Cleaned path: $chemin<br>"; // Debugging line

    if (file_exists($chemin)) {
        require_once $chemin;
        return true;
    }

    return false;
});
