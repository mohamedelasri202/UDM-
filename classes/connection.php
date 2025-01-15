<?php 
class database {
    private static $instance = null; // Fixed typo
    private $pdo;

    public function __construct($dsn, $username, $password) {
        try {
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new Exception("Database connection failed.");
        }
    }

    public static function getinstance($dsn = null, $username = null, $password = null) {
        if (self::$instance === null) {
            $dsn = $dsn ?? 'mysql:host=localhost;dbname=udm';
            $username = $username ?? 'root';
            $password = $password ?? '';
            self::$instance = new database($dsn, $username, $password);
        }
        return self::$instance;
    }

    public function getconnection() {
        var_dump($this->pdo); // var_dump before return
        return $this->pdo;
    }
}
