<?php
try {
    $config = require_once "config/db.php";
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
    $pdo = new PDO($dsn, $config['username'], $config['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // print_r($pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC));
    // echo "safi hadxi t executa !";
} catch (PDOException $e) {
    echo "Erreur de connexion a bd : " . $e->getMessage();
}
