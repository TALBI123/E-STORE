<?php

spl_autoload_register(function ($classe) {
    $classe = str_replace('App\\', '', $classe);

    $chemin = __DIR__ . '/' . str_replace('\\', '/', $classe) . '.php';

    // Nettoyer
    $chemin = str_replace('//', '/', $chemin);

    if (file_exists($chemin)) {
        require_once $chemin;
        return true;
    }

    return false;
});
