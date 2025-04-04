<?php
/* require_once "config/database.php"; */

class TeamModel {
    private $conn;
    private $table_name = "teams";
    private $members_table = "team_members";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Récupère toutes les équipes avec le nombre de membres
     * 
     * @return array Liste des équipes
     */
    public function getAll() {
        $query = "SELECT t.*, COUNT(tm.user_id) as members_count 
                  FROM " . $this->table_name . " t
                  LEFT JOIN " . $this->members_table . " tm ON t.id = tm.team_id
                  GROUP BY t.id
                  ORDER BY t.name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère une équipe par son ID
     * 
     * @param int $id ID de l'équipe
     * @return array Données de l'équipe
     */
    public function getById($id) {
        $query = "SELECT t.*, COUNT(tm.user_id) as members_count 
                  FROM " . $this->table_name . " t
                  LEFT JOIN " . $this->members_table . " tm ON t.id = tm.team_id
                  WHERE t.id = :id
                  GROUP BY t.id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les membres d'une équipe
     * 
     * @param int $teamId ID de l'équipe
     * @return array Liste des membres
     */
    public function getMembers($teamId) {
        $query = "SELECT u.* 
                  FROM users u
                  JOIN " . $this->members_table . " tm ON u.id = tm.user_id
                  WHERE tm.team_id = :team_id
                  ORDER BY u.first_name, u.last_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":team_id", $teamId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crée une nouvelle équipe
     * 
     * @param array $data Données de l'équipe
     * @return int|bool ID de l'équipe créée ou false en cas d'échec
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (name, description)
                 VALUES 
                 (:name, :description)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":description", $data['description']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    /**
     * Met à jour une équipe existante
     * 
     * @param int $id ID de l'équipe
     * @param array $data Nouvelles données de l'équipe
     * @return bool Résultat de la mise à jour
     */
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " SET 
                 name = :name, 
                 description = :description,
                 updated_at = CURRENT_TIMESTAMP
                 WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":description", $data['description']);
        
        return $stmt->execute();
    }

    /**
     * Supprime une équipe
     * 
     * @param int $id ID de l'équipe
     * @return bool Résultat de la suppression
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }

    /**
     * Ajoute un membre à une équipe
     * 
     * @param int $teamId ID de l'équipe
     * @param int $userId ID de l'utilisateur à ajouter
     * @return bool Résultat de l'opération
     */
    public function addMember($teamId, $userId) {
        $query = "INSERT INTO " . $this->members_table . " 
                 (team_id, user_id)
                 VALUES 
                 (:team_id, :user_id)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":team_id", $teamId);
        $stmt->bindParam(":user_id", $userId);
        
        return $stmt->execute();
    }

    /**
     * Retire un membre d'une équipe
     * 
     * @param int $teamId ID de l'équipe
     * @param int $userId ID de l'utilisateur à retirer
     * @return bool Résultat de l'opération
     */
    public function removeMember($teamId, $userId) {
        $query = "DELETE FROM " . $this->members_table . " 
                 WHERE team_id = :team_id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":team_id", $teamId);
        $stmt->bindParam(":user_id", $userId);
        
        return $stmt->execute();
    }

    /**
     * Recherche d'équipes selon un terme de recherche
     * 
     * @param string $searchTerm Terme de recherche
     * @return array Résultats de la recherche
     */
    public function search($searchTerm) {
        $searchTerm = "%{$searchTerm}%";
        
        $query = "SELECT t.*, COUNT(tm.user_id) as members_count 
              FROM " . $this->table_name . " t
              LEFT JOIN " . $this->members_table . " tm ON t.id = tm.team_id
              WHERE LOWER(t.name) LIKE :search_term 
              OR LOWER(t.description) LIKE :search_term
                  GROUP BY t.id
                  ORDER BY t.name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":search_term", $searchTerm);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les équipes d'un utilisateur avec des informations détaillées
     * 
     * @param int $userId ID de l'utilisateur
     * @return array Équipes de l'utilisateur
     */
    public function getUserTeamsWithDetails($userId) {
        $query = "SELECT t.*, 
                  (SELECT COUNT(*) FROM " . $this->members_table . " WHERE team_id = t.id) as members_count,
                  p.id as project_id, p.name as project_name, p.status as project_status,
                  (SELECT COUNT(*) FROM tasks WHERE project_id = p.id) as tasks_count,
                  (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'Completed') as completed_tasks_count
                  FROM " . $this->table_name . " t
                  JOIN " . $this->members_table . " tm ON t.id = tm.team_id
                  LEFT JOIN projects p ON t.id = p.team_id
                  WHERE tm.user_id = :user_id
                  GROUP BY t.id, p.id, p.name, p.status";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère toutes les équipes avec pagination et filtres
     * 
     * @param array $filters Critères de filtrage
     * @param int $limit Nombre de résultats par page
     * @param int $offset Décalage pour la pagination
     * @return array Liste des équipes filtrées et paginées
     */
    public function getAllWithFilters($filters = [], $limit = 10, $offset = 0) {
        $query = "SELECT t.*, 
                 COUNT(DISTINCT tm.user_id) as members_count,
                 EXISTS(SELECT 1 FROM projects p WHERE p.team_id = t.id) as has_project,
                 (SELECT status FROM projects WHERE team_id = t.id ORDER BY id DESC LIMIT 1) as project_status
                 FROM " . $this->table_name . " t
                 LEFT JOIN " . $this->members_table . " tm ON t.id = tm.team_id
                 WHERE 1=1";
        
        $params = [];
        
        // Filtre de recherche
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query .= " AND (
                LOWER(t.name) LIKE :search 
                OR LOWER(t.description) LIKE :search
            )";
            $params[':search'] = '%' . strtolower($filters['search']) . '%';
        }
        
        // Filtre pour les équipes assignées/non assignées
        if (isset($filters['assigned']) && $filters['assigned'] !== 'all') {
            if ($filters['assigned'] === 'yes') {
                $query .= " AND EXISTS(SELECT 1 FROM projects p WHERE p.team_id = t.id)";
            } else if ($filters['assigned'] === 'no') {
                $query .= " AND NOT EXISTS(SELECT 1 FROM projects p WHERE p.team_id = t.id)";
            }
        }
        
        $query .= " GROUP BY t.id ORDER BY t.name LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind des paramètres pour les filtres
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        // Bind des paramètres de pagination
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        
        $teams = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Récupérer les membres pour chaque équipe séparément
        foreach ($teams as &$team) {
            // Requête pour obtenir les membres de l'équipe
            $membersQuery = "SELECT CONCAT(u.first_name, ' ', u.last_name) AS full_name 
                            FROM users u
                            JOIN " . $this->members_table . " tm ON u.id = tm.user_id
                            WHERE tm.team_id = :team_id
                            ORDER BY u.first_name, u.last_name";
            
            $memberStmt = $this->conn->prepare($membersQuery);
            $memberStmt->bindParam(':team_id', $team['id']);
            $memberStmt->execute();
            
            $memberNames = [];
            while ($member = $memberStmt->fetch(PDO::FETCH_ASSOC)) {
                $memberNames[] = $member['full_name'];
            }
            
            $team['member_names'] = $memberNames;
        }
        
        return $teams;
    }

    /**
     * Compte le nombre total d'équipes avec les filtres appliqués
     * 
     * @param array $filters Critères de filtrage
     * @return int Nombre total d'équipes
     */
    public function countWithFilters($filters = []) {
        $query = "SELECT COUNT(DISTINCT t.id) as total 
                 FROM " . $this->table_name . " t
                 LEFT JOIN " . $this->members_table . " tm ON t.id = tm.team_id
                 LEFT JOIN users u ON tm.user_id = u.id
                 WHERE 1=1";
        
        $params = [];
        
        // Filtre de recherche
        if (isset($filters['search']) && !empty($filters['search'])) {
            $query .= " AND (
                LOWER(t.name) LIKE :search 
                OR LOWER(t.description) LIKE :search
            )";
            $params[':search'] = '%' . strtolower($filters['search']) . '%';
        }
        
        // Filtre pour les équipes assignées/non assignées
        if (isset($filters['assigned']) && $filters['assigned'] !== 'all') {
            if ($filters['assigned'] === 'yes') {
                $query .= " AND EXISTS(SELECT 1 FROM projects p WHERE p.team_id = t.id)";
            } else if ($filters['assigned'] === 'no') {
                $query .= " AND NOT EXISTS(SELECT 1 FROM projects p WHERE p.team_id = t.id)";
            }
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
     * Vérifie si un utilisateur est déjà membre d'une équipe
     * 
     * @param int $teamId ID de l'équipe
     * @param int $userId ID de l'utilisateur
     * @return bool True si l'utilisateur est déjà membre, False sinon
     */
    public function isMember($teamId, $userId) {
        $query = "SELECT COUNT(*) as count FROM " . $this->members_table . " 
                 WHERE team_id = :team_id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":team_id", $teamId);
        $stmt->bindParam(":user_id", $userId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }

    /**
     * Compte le nombre de membres dans une équipe
     * 
     * @param int $teamId ID de l'équipe
     * @return int Nombre de membres
     */
    public function countMembers($teamId) {
        $query = "SELECT COUNT(*) as count FROM " . $this->members_table . " 
                 WHERE team_id = :team_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":team_id", $teamId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'];
    }
} 