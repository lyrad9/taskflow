<?php

class MemberModel {
    private $conn;
    private $table_name = "users";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Récupère tous les membres avec le rôle demandé
     * 
     * @param string $role Rôle des membres à récupérer (optionnel)
     * @return array Liste des membres
     */
    public function getAllByRole($role = null) {
        $query = "SELECT * FROM " . $this->table_name;
        
        if ($role) {
            $query .= " WHERE role = :role";
        }
        
        $query .= " ORDER BY first_name, last_name";
        
        $stmt = $this->conn->prepare($query);
        
        if ($role) {
            $stmt->bindParam(":role", $role);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère tous les membres avec pagination et filtres
     * 
     * @param array $filters Critères de filtrage
     * @param int $limit Nombre de résultats par page
     * @param int $offset Décalage pour la pagination
     * @return array Liste des membres filtrés et paginés
     */
    public function getAllWithFilters($filters = [], $limit = 10, $offset = 0) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        
        $params = [];
        
        // Filtre par rôle
        if (isset($filters['role']) && $filters['role'] !== 'all') {
            $query .= " AND role = :role";
            $params[':role'] = $filters['role'];
        } else {
            // Par défaut, afficher uniquement les membres (USER)
            $query .= " AND role = 'USER'";
        }
        
        // Filtre de recherche
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query .= " AND (
                LOWER(first_name) LIKE :search 
                OR LOWER(last_name) LIKE :search 
                OR LOWER(email) LIKE :search
                OR LOWER(username) LIKE :search
            )";
            $params[':search'] = '%' . strtolower($filters['search']) . '%';
        }
        
        $query .= " ORDER BY first_name, last_name LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind des paramètres pour les filtres
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        // Bind des paramètres de pagination
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compte le nombre total de membres avec les filtres appliqués
     * 
     * @param array $filters Critères de filtrage
     * @return int Nombre total de membres
     */
    public function countWithFilters($filters = []) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE 1=1";
        
        $params = [];
        
        // Filtre par rôle
        if (isset($filters['role']) && $filters['role'] !== 'all') {
            $query .= " AND role = :role";
            $params[':role'] = $filters['role'];
        } else {
            // Par défaut, compter uniquement les membres (USER)
            $query .= " AND role = 'USER'";
        }
        
        // Filtre de recherche
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query .= " AND (
                LOWER(first_name) LIKE :search 
                OR LOWER(last_name) LIKE :search 
                OR LOWER(email) LIKE :search
                OR LOWER(username) LIKE :search
            )";
            $params[':search'] = '%' . strtolower($filters['search']) . '%';
        }
        
        $stmt = $this->conn->prepare($query);
        
        // Bind des paramètres pour les filtres
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'];
    }

    /**
     * Récupère un membre par son ID
     * 
     * @param int $id ID du membre
     * @return array Données du membre
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crée un nouveau membre
     * 
     * @param array $data Données du membre
     * @return int|bool ID du membre créé ou false en cas d'échec
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (username, password, email, first_name, last_name, role, fonction, created_at, updated_at)
                 VALUES 
                 (:username, :password, :email, :first_name, :last_name, :role, :fonction, NOW(), NOW())";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":username", $data['username']);
        $stmt->bindParam(":password", $data['password']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":first_name", $data['first_name']);
        $stmt->bindParam(":last_name", $data['last_name']);
        $stmt->bindParam(":role", $data['role']);
        $stmt->bindParam(":fonction", $data['fonction']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    /**
     * Met à jour un membre
     * 
     * @param int $id ID du membre
     * @param array $data Données à mettre à jour
     * @return bool Résultat de l'opération
     */
    public function update($id, $data) {
        $updateFields = [];
        $params = [':id' => $id];
        
        // Construire dynamiquement la requête de mise à jour
        foreach ($data as $key => $value) {
            if ($key !== 'id' && $key !== 'created_at') {
                $updateFields[] = "$key = :$key";
                $params[":$key"] = $value;
            }
        }
        
        // Ajouter la date de mise à jour
        $updateFields[] = "updated_at = NOW()";
        
        $query = "UPDATE " . $this->table_name . " SET " . implode(", ", $updateFields) . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        return $stmt->execute();
    }

    /**
     * Met à jour le rôle d'un ou plusieurs membres
     * 
     * @param array $ids IDs des membres
     * @param string $role Nouveau rôle
     * @return bool Résultat de l'opération
     */
    public function updateRole($ids, $role) {
        if (!is_array($ids)) {
            $ids = [$ids];
        }
        
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        $query = "UPDATE " . $this->table_name . " SET role = ?, updated_at = NOW() WHERE id IN ($placeholders)";
        
        $stmt = $this->conn->prepare($query);
        
        $bindValues = array_merge([$role], $ids);
        $index = 1;
        
        foreach ($bindValues as $value) {
            $stmt->bindValue($index++, $value);
        }
        
        return $stmt->execute();
    }

    /**
     * Supprime un membre
     * 
     * @param int $id ID du membre à supprimer
     * @return bool Résultat de l'opération
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }

    /**
     * Supprime plusieurs membres
     * 
     * @param array $ids IDs des membres à supprimer
     * @return bool Résultat de l'opération
     */
    public function deleteMultiple($ids) {
        if (empty($ids)) {
            return false;
        }
        
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        $query = "DELETE FROM " . $this->table_name . " WHERE id IN ($placeholders)";
        
        $stmt = $this->conn->prepare($query);
        
        $index = 1;
        foreach ($ids as $id) {
            $stmt->bindValue($index++, $id);
        }
        
        return $stmt->execute();
    }

    /**
     * Génère un nom d'utilisateur unique
     * 
     * @return string Nom d'utilisateur généré
     */
    public function generateUniqueUsername() {
        $prefix = 'member';
        $isUnique = false;
        $username = '';
        
        while (!$isUnique) {
            // Générer un nombre aléatoire à 5 chiffres
            $randomNumber = mt_rand(10000, 99999);
            $username = $prefix . $randomNumber;
            
            // Vérifier si le nom d'utilisateur existe déjà
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE username = :username";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":username", $username);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] == 0) {
                $isUnique = true;
            }
        }
        
        return $username;
    }

    /**
     * Génère un mot de passe aléatoire
     * 
     * @param int $length Longueur du mot de passe
     * @return string Mot de passe généré
     */
    public function generatePassword($length = 10) {
        // Générer un mot de passe au format "pass" suivi de 5 chiffres aléatoires
        $prefix = 'pass';
        $randomNumber = mt_rand(10000, 99999);
        
        return $prefix . $randomNumber;
    }

    /**
     * Recherche des membres selon un terme de recherche et un rôle
     * 
     * @param string $searchTerm Terme de recherche
     * @param string $role Rôle des membres à rechercher (optionnel)
     * @return array Résultats de la recherche
     */
    public function search($searchTerm, $role = null) {
        $searchTerm = '%' . strtolower($searchTerm) . '%';
        
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        
        if ($role) {
            $query .= " AND role = :role";
        }
        
        $query .= " AND (
            LOWER(first_name) LIKE :search_term
            OR LOWER(last_name) LIKE :search_term
            OR LOWER(email) LIKE :search_term
            OR LOWER(CONCAT(first_name, ' ', last_name)) LIKE :search_term
        )";
        
        $query .= " ORDER BY first_name, last_name LIMIT 10";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":search_term", $searchTerm);
        
        if ($role) {
            $stmt->bindParam(":role", $role);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Vérifie si une adresse email existe déjà
     * 
     * @param string $email Adresse email à vérifier
     * @param int $excludeId ID du membre à exclure de la vérification (pour les mises à jour)
     * @return bool True si l'email existe déjà, false sinon
     */
    public function emailExists($email, $excludeId = null) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE email = :email";
        
        if ($excludeId) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        
        if ($excludeId) {
            $stmt->bindParam(":exclude_id", $excludeId);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }
    public function getAllMembers() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 