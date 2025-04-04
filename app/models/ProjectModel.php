<?php
/* require_once "../../config/database.php";
 */

class ProjectModel {
    private $conn;
    private $table_name = "projects";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Récupère tous les projets avec leurs informations associées (client, équipe, statut)
     * 
     * @return array Liste des projets
     */
    public function getAll() {
        $query = "SELECT p.*, c.first_name as client_first_name, c.last_name as client_last_name, 
                 t.name as team_name, COUNT(tm.user_id) as team_members_count
                 FROM " . $this->table_name . " p
                 LEFT JOIN clients c ON p.client_id = c.id
                 LEFT JOIN teams t ON p.team_id = t.id
                 LEFT JOIN team_members tm ON t.id = tm.team_id
                 GROUP BY p.id, c.first_name, c.last_name, t.name
                 ORDER BY p.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère tous les projets avec pagination et filtres
     * 
     * @param array $filters Critères de filtrage
     * @param int $limit Nombre de résultats par page
     * @param int $offset Décalage pour la pagination
     * @return array Liste des projets filtrés et paginés
     */
    public function getAllWithFilters($filters = [], $limit = 10, $offset = 0) {
        $query = "SELECT p.*, c.first_name as client_first_name, c.last_name as client_last_name, 
                 t.name as team_name
                 FROM " . $this->table_name . " p
                 LEFT JOIN clients c ON p.client_id = c.id
                 LEFT JOIN teams t ON p.team_id = t.id
                 WHERE 1=1";
        
        $params = [];
        
        // Appliquer les filtres
        $query = $this->applyFilters($query, $filters, $params);
        
        $query .= " ORDER BY p.created_at DESC
                   LIMIT :limit OFFSET :offset";
        
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
     * Compte le nombre total de projets avec les filtres appliqués
     * 
     * @param array $filters Critères de filtrage
     * @return int Nombre total de projets
     */
    public function countWithFilters($filters = []) {
        $query = "SELECT COUNT(*) as total
                 FROM " . $this->table_name . " p
                 LEFT JOIN clients c ON p.client_id = c.id
                 LEFT JOIN teams t ON p.team_id = t.id
                 WHERE 1=1";
        
        $params = [];
        
        // Appliquer les filtres
        $query = $this->applyFilters($query, $filters, $params);
        
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
     * Applique des filtres à une requête SQL
     * 
     * @param string $query Requête SQL de base
     * @param array $filters Critères de filtrage
     * @param array &$params Paramètres de la requête préparée
     * @return string Requête SQL modifiée
     */
    private function   applyFilters($query, $filters, &$params) {
        // Filtre de recherche
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $query .= " AND (p.name LIKE :search OR p.description LIKE :search OR 
                       c.first_name LIKE :search OR c.last_name LIKE :search)";
            $params[':search'] = $searchTerm;
        }
        
        // Filtre de statut
        if (!empty($filters['status'])) {
            $query .= " AND p.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        // Filtre de client
        if (!empty($filters['client_id'])) {
            $query .= " AND p.client_id = :client_id";
            $params[':client_id'] = $filters['client_id'];
        }
        
        // Filtre de type de projet
        if (!empty($filters['project_type'])) {
            $query .= " AND p.project_type = :project_type";
            $params[':project_type'] = $filters['project_type'];
        }
        
        // Filtres de date
        if (!empty($filters['date_from'])) {
            $query .= " AND p.created_at >= :date_from";
            $params[':date_from'] = $filters['date_from'] . ' 00:00:00';
        }
        
        if (!empty($filters['date_to'])) {
            $query .= " AND p.created_at <= :date_to";
            $params[':date_to'] = $filters['date_to'] . ' 23:59:59';
        }
        
        return $query;
    }

    /**
     * Récupère un projet par son ID avec toutes ses informations associées
     * 
     * @param int $id ID du projet
     * @return array Données du projet
     */
    public function getById($id) {
        $query = "SELECT p.*, c.first_name as client_first_name, c.last_name as client_last_name, 
                 c.city as client_city, c.phone_number as client_phone, c.residence as client_residence,
                 t.name as team_name, t.description as team_description  
                 FROM " . $this->table_name . " p
                 LEFT JOIN clients c ON p.client_id = c.id
                 LEFT JOIN teams t ON p.team_id = t.id
                 WHERE p.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère tous les projets récents (limités à un nombre spécifique)
     * 
     * @param int $limit Nombre maximum de projets à récupérer
     * @return array Liste des projets récents
     */
    public function getRecent($limit = 5) {
        $query = "SELECT p.*, c.first_name as client_first_name, c.last_name as client_last_name 
                 FROM " . $this->table_name . " p
                 LEFT JOIN clients c ON p.client_id = c.id
                 ORDER BY p.created_at DESC
                 LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les projets par leur statut
     * 
     * @param string $status Statut des projets à récupérer
     * @return array Liste des projets correspondants
     */
    public function getByStatus($status) {
        $query = "SELECT p.*, c.first_name as client_first_name, c.last_name as client_last_name, 
                 t.name as team_name
                 FROM " . $this->table_name . " p
                 LEFT JOIN clients c ON p.client_id = c.id
                 LEFT JOIN teams t ON p.team_id = t.id
                 WHERE p.status = :status
                 ORDER BY p.updated_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les projets assignés à une équipe
     * 
     * @param int $teamId ID de l'équipe
     * @return array Liste des projets assignés
     */
    public function getByTeam($teamId) {
        $query = "SELECT p.*, c.first_name as client_first_name, c.last_name as client_last_name
                 FROM " . $this->table_name . " p
                 LEFT JOIN clients c ON p.client_id = c.id
                 WHERE p.team_id = :team_id
                 ORDER BY p.updated_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":team_id", $teamId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recherche de projets selon des critères (nom, client, statut)
     * 
     * @param string $searchTerm Terme de recherche
     * @return array Résultats de la recherche
     */
    public function search($searchTerm) {
        $searchTerm = "%{$searchTerm}%";
        
        $query = "SELECT p.*, c.first_name as client_first_name, c.last_name as client_last_name, 
                 t.name as team_name
                 FROM " . $this->table_name . " p
                 LEFT JOIN clients c ON p.client_id = c.id
                 LEFT JOIN teams t ON p.team_id = t.id
                 WHERE p.name LIKE :search_term 
                 OR p.description LIKE :search_term
                 OR c.first_name LIKE :search_term
                 OR c.last_name LIKE :search_term
                 OR p.status LIKE :search_term
                 ORDER BY p.updated_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":search_term", $searchTerm);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crée un nouveau projet
     * 
     * @param array $data Données du projet
     * @return int|bool ID du projet créé ou false en cas d'échec
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                 (name, description, client_id, team_id, budget, project_type, 
                 documents, status, scheduled_start_date, scheduled_end_date)
                 VALUES 
                 (:name, :description, :client_id, :team_id, :budget, :project_type, 
                 :documents, :status, :scheduled_start_date, :scheduled_end_date)";
        
        $stmt = $this->conn->prepare($query);
        // Transformation du tableau PHP en literal d'array PostgreSQL
        $documentPathsLiteral = null;
        if (isset($data['documents']) && !empty($data['documents'])) {
            // Essayons d'abord d'encoder en JSON
            $documents = json_encode($data['documents']);
            
            // Si l'encodage JSON a échoué ou si les documents sont de simples chemins, utiliser le format PostgreSQL
            if (json_last_error() !== JSON_ERROR_NONE || (is_array($data['documents']) && count($data['documents']) > 0 && is_string($data['documents'][0]))) {
                $documentPathsLiteral = '{' . implode(',', array_map(function($path) {
                    return '"' . addslashes(is_array($path) ? $path['filepath'] : $path) . '"';
                }, $data['documents'])) . '}';
            } else {
                $documentPathsLiteral = $documents; // Utiliser le JSON si possible
            }
        }
        
        // Vérification de team_id pour éviter d'envoyer une chaîne vide
        if (isset($data['team_id']) && $data['team_id'] !== '') {
            $team_id = $data['team_id'];
            $stmt->bindValue(":team_id", $team_id, PDO::PARAM_INT);
        } else {
            $stmt->bindValue(":team_id", null, PDO::PARAM_NULL);
        }
        $status = isset($data['status']) ? $data['status'] : 'In progress';
        
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":description", $data['description']);
        $stmt->bindParam(":client_id", $data['client_id']);
        $stmt->bindParam(":budget", $data['budget']);
        $stmt->bindParam(":project_type", $data['project_type']);
        $stmt->bindParam(":documents", $documentPathsLiteral);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":scheduled_start_date", $data['scheduled_start_date']);
        $stmt->bindParam(":scheduled_end_date", $data['scheduled_end_date']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }

    /**
     * Met à jour un projet existant
     * 
     * @param int $id ID du projet
     * @param array $data Nouvelles données du projet
     * @return bool Résultat de la mise à jour
     */
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " SET 
                 name = :name, 
                 description = :description, 
                 client_id = :client_id, 
                 team_id = :team_id, 
                 budget = :budget, 
                 project_type = :project_type, 
                 documents = :documents, 
                 status = :status, 
                 scheduled_start_date = :scheduled_start_date, 
                 scheduled_end_date = :scheduled_end_date,
                 updated_at = CURRENT_TIMESTAMP";
        
        // Ajouter les dates actuelles si elles sont présentes
        if (isset($data['actual_start_date'])) {
            $query .= ", actual_start_date = :actual_start_date";
        }
        
        if (isset($data['actual_end_date'])) {
            $query .= ", actual_end_date = :actual_end_date";
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Convertir le tableau de documents en tableau JSON
        $documents = isset($data['documents']) ? json_encode($data['documents']) : null;
        
        // Si le encodage JSON a échoué ou n'est pas approprié, utiliser le format littéral PostgreSQL
        if ((isset($data['documents']) && json_last_error() !== JSON_ERROR_NONE) || 
            (isset($data['documents']) && is_array($data['documents']) && count($data['documents']) > 0 && is_string($data['documents'][0]))) {
            $documentPathsLiteral = '{' . implode(',', array_map(function($path) {
                return '"' . addslashes(is_array($path) ? $path['filepath'] : $path) . '"';
            }, $data['documents'])) . '}';
            $documents = $documentPathsLiteral;
        }
        
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":name", $data['name']);
        $stmt->bindParam(":description", $data['description']);
        $stmt->bindParam(":client_id", $data['client_id']);
        $stmt->bindParam(":team_id", $data['team_id']);
        $stmt->bindParam(":budget", $data['budget']);
        $stmt->bindParam(":project_type", $data['project_type']);
        $stmt->bindParam(":documents", $documents);
        $stmt->bindParam(":status", $data['status']);
        $stmt->bindParam(":scheduled_start_date", $data['scheduled_start_date']);
        $stmt->bindParam(":scheduled_end_date", $data['scheduled_end_date']);
        
        if (isset($data['actual_start_date'])) {
            $stmt->bindParam(":actual_start_date", $data['actual_start_date']);
        }
        
        if (isset($data['actual_end_date'])) {
            $stmt->bindParam(":actual_end_date", $data['actual_end_date']);
        }
        
        return $stmt->execute();
    }

    /**
     * Met à jour le statut d'un projet
     * 
     * @param int $id ID du projet
     * @param string $status Nouveau statut
     * @return bool Résultat de la mise à jour
     */
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET 
                 status = :status, 
                 updated_at = CURRENT_TIMESTAMP";
        
        // Si le statut est "Terminé", ajouter la date de fin réelle
        if ($status === 'Completed') {
            $query .= ", actual_end_date = CURRENT_DATE";
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":status", $status);
        
        return $stmt->execute();
    }

    /**
     * Supprime un projet
     * 
     * @param int $id ID du projet
     * @return bool Résultat de la suppression
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }

    /**
     * Compte le nombre total de projets
     * 
     * @param string $status Statut optionnel pour filtrer
     * @return int Nombre de projets
     */
    public function count($status = null) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        
        if ($status) {
            $query .= " WHERE status = :status";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":status", $status);
        } else {
            $stmt = $this->conn->prepare($query);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'];
    }

    /**
     * Compte le nombre de projets assignés à un utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @return int Nombre de projets assignés
     */
    public function getAssignedProjectsCount($userId) {
        $query = "SELECT COUNT(DISTINCT p.id) as total
                 FROM " . $this->table_name . " p
                 JOIN teams t ON p.team_id = t.id
                 JOIN team_members tm ON t.id = tm.team_id
                 WHERE tm.user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    /**
     * Compte le nombre de projets terminés pour un utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @return int Nombre de projets terminés
     */
    public function getCompletedProjectsCount($userId) {
        $query = "SELECT COUNT(DISTINCT p.id) as total
                 FROM " . $this->table_name . " p
                 JOIN teams t ON p.team_id = t.id
                 JOIN team_members tm ON t.id = tm.team_id
                 WHERE tm.user_id = :user_id
                 AND p.status = 'Completed'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    /**
     * Récupère les projets récents d'un utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @param int $limit Nombre maximum de projets à récupérer
     * @return array Liste des projets récents
     */
    public function getRecentProjects($userId, $limit = 5) {
        $query = "SELECT p.*, 
                 c.first_name as client_first_name, 
                 c.last_name as client_last_name,
                 CONCAT(c.first_name, ' ', c.last_name) as client_name
                 FROM " . $this->table_name . " p
                 JOIN teams t ON p.team_id = t.id
                 JOIN team_members tm ON t.id = tm.team_id
                 LEFT JOIN clients c ON p.client_id = c.id
                 WHERE tm.user_id = :user_id
                 ORDER BY p.created_at DESC
                 LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les projets assignés à un membre avec pagination et filtres
     * 
     * @param array $filters Critères de filtrage incluant 'user_id'
     * @param int $limit Nombre de résultats par page
     * @param int $offset Décalage pour la pagination
     * @return array Liste des projets filtrés et paginés
     */
    public function getMemberProjectsWithFilters($filters = [], $limit = 10, $offset = 0) {
        $query = "SELECT p.*, c.first_name as client_first_name, c.last_name as client_last_name,
                 t.name as team_name 
                 FROM " . $this->table_name . " p
                 JOIN teams t ON p.team_id = t.id
                 JOIN team_members tm ON t.id = tm.team_id
                 LEFT JOIN clients c ON p.client_id = c.id
                 WHERE tm.user_id = :user_id";
        
        $params = [':user_id' => $filters['user_id']];
        
        // Appliquer les filtres
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $query .= " AND (p.name LIKE :search OR p.description LIKE :search OR 
                       c.first_name LIKE :search OR c.last_name LIKE :search)";
            $params[':search'] = $searchTerm;
        }
        
        // Filtre de statut
        if (!empty($filters['status'])) {
            $query .= " AND p.status = :status";
            $params[':status'] = $filters['status'];
        }
        
        $query .= " GROUP BY p.id, c.first_name, c.last_name, t.name
                   ORDER BY p.created_at DESC
                   LIMIT :limit OFFSET :offset";
        
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
     * Compte le nombre total de projets assignés à un membre avec les filtres appliqués
     * 
     * @param array $filters Critères de filtrage incluant 'user_id'
     * @return int Nombre total de projets
     */
    public function countMemberProjectsWithFilters($filters = []) {
        $query = "SELECT COUNT(DISTINCT p.id) as total
                 FROM " . $this->table_name . " p
                 JOIN teams t ON p.team_id = t.id
                 JOIN team_members tm ON t.id = tm.team_id
                 LEFT JOIN clients c ON p.client_id = c.id
                 WHERE tm.user_id = :user_id";
        
        $params = [':user_id' => $filters['user_id']];
        
        // Appliquer les filtres
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $query .= " AND (p.name LIKE :search OR p.description LIKE :search OR 
                       c.first_name LIKE :search OR c.last_name LIKE :search)";
            $params[':search'] = $searchTerm;
        }
        
        // Filtre de statut
        if (!empty($filters['status'])) {
            $query .= " AND p.status = :status";
            $params[':status'] = $filters['status'];
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
}
