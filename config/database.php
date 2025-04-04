<?php

class Database {
  private $host = 'localhost';
  private $port = '5432'; // Port PostgreSQL (par défaut : 5432)
    private $dbname = 'gestion_projet';
    private $username = 'postgres';
    private $password = 'darylboris';
    private $conn;

    public function __construct() {
        try {
          $dsn = "pgsql:host=$this->host;port=$this->port;dbname=$this->dbname";
          $this->conn  = new PDO($dsn, $this->username, $this->password);          
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            /* echo "Connexion réussie"; */
        } catch(PDOException $e) {
            /* echo "Erreur de connexion: " . $e->getMessage(); */
        }
    }

    public function getConnection() {
        return $this->conn;
    }

    public function query($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    public function closeConnection() {
        $this->conn = null;
    }
 
}
