<?php
namespace App\Core;
use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;
    private  function __construct() {
        $config = require_once "config/db.php";
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']}";
        try{
            $this->pdo =  new PDO($dsn, $config['username'], $config['password'],[
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }catch(PDOException $e){
            die("Erreur de connexion a bd : " . $e->getMessage());
        }
    }
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    // permet d'acceder a la connexion pdo depuis l'exterieur
    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}