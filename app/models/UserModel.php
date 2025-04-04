<?php
/* require_once "config/database.php"; */
class UserModel {
    
    private $table_name = "users";
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " (nom, email, password) VALUES (:nom, :email, :password)";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(":nom", $data['nom']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":password", password_hash($data['password'], PASSWORD_DEFAULT));
        
        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " SET nom = :nom, email = :email WHERE id = :id";
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":nom", $data['nom']);
        $stmt->bindParam(":email", $data['email']);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
    
    /**
     * Authentifie un utilisateur par son username et mot de passe
     * 
     * @param string $username Nom d'utilisateur
     * @param string $password Mot de passe
     * @return array|false Données de l'utilisateur ou false si échec
     */
    public function authenticate($username, $password) {
        echo $username;
        echo $password;
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && 
        $password === $user['password']
      /*   password_verify($password, $user['password']) */
        ) {
            return $user;
        }
        
        return null;
    }
    public function findById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
} 