<?php
/* require_once "config/database.php"; */

class ClientModel {
    private $conn;
    private $table_name = "clients";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Récupère tous les clients
     * 
     * @return array Liste des clients
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY first_name, last_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un client par son ID
     * 
     * @param int $id ID du client
     * @return array Données du client
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crée un nouveau client
     * 
     * @param array $data Données du client
     * @return int|bool ID du client créé ou false en cas d'échec
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (first_name, last_name, city, residence, phone_number)
                 VALUES 
                 (:first_name, :last_name, :city, :residence, :phone_number)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":first_name", $data['first_name']);
        $stmt->bindParam(":last_name", $data['last_name']);
        $stmt->bindParam(":city", $data['city']);
        $stmt->bindParam(":residence", $data['residence']);
        $stmt->bindParam(":phone_number", $data['phone_number']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    /**
     * Met à jour un client existant
     * 
     * @param int $id ID du client
     * @param array $data Nouvelles données du client
     * @return bool Résultat de la mise à jour
     */
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " SET 
                 first_name = :first_name, 
                 last_name = :last_name, 
                 city = :city, 
                 residence = :residence, 
                 phone_number = :phone_number,
                 updated_at = CURRENT_TIMESTAMP
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":first_name", $data['first_name']);
        $stmt->bindParam(":last_name", $data['last_name']);
        $stmt->bindParam(":city", $data['city']);
        $stmt->bindParam(":residence", $data['residence']);
        $stmt->bindParam(":phone_number", $data['phone_number']);
        
        return $stmt->execute();
    }

    /**
     * Supprime un client
     * 
     * @param int $id ID du client
     * @return bool Résultat de la suppression
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }

    /**
     * Recherche de clients selon des critères (nom, prénom, ville)
     * 
     * @param string $searchTerm Terme de recherche
     * @return array Résultats de la recherche
     */
    public function search($searchTerm) {
        $searchTerm = "%{$searchTerm}%";
        
        $query = "SELECT * FROM " . $this->table_name . " 
                 WHERE LOWER(first_name) LIKE :search_term 
                 OR LOWER(last_name) LIKE :search_term
                 OR LOWER(city) LIKE :search_term
                 OR phone_number LIKE :search_term
                 ORDER BY first_name, last_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":search_term", $searchTerm);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compte le nombre total de clients
     * 
     * @return int Nombre de clients
     */
    public function count() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'];
    }
} 