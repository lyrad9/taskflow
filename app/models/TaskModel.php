<?php
/* require_once __DIR__ . '/../config/database.php'; */
/* require_once 'config/database.php'; */
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/helpers/DateTimeHelper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/helpers/Constants.php';

class TaskModel {
    private $db;
    
    public function __construct() {
      $database  = new Database();
        $this->db = $database->getConnection();
    }
    
    /**
     * Récupère toutes les tâches avec filtrage et pagination
     * 
     * @param array $filters Filtres à appliquer
     * @param int $limit Nombre d'éléments par page
     * @param int $offset Offset pour la pagination
     * @return array Liste des tâches
     */
    public function getAllWithFilters($filters = [], $limit = 10, $offset = 0) {
        
        
        $query = "SELECT t.*, 
                  p.name as project_name, 
                  u.first_name as assigned_first_name, 
                  u.last_name as assigned_last_name,
                  u.email as assigned_email,
                  c.first_name as creator_first_name, 
                  c.last_name as creator_last_name
                  FROM tasks t
                  LEFT JOIN projects p ON t.project_id = p.id
                  LEFT JOIN users u ON t.assigned_to = u.id
                  LEFT JOIN users c ON t.created_by = c.id
                  WHERE 1=1";
        
        $params = [];
        
        // Appliquer les filtres
        if (!empty($filters['search'])) {
            $search = "%{$filters['search']}%";
            $query .= " AND (t.name LIKE :search 
                      OR t.description LIKE :search 
                      OR p.name LIKE :search 
                      OR CONCAT(u.first_name, ' ', u.last_name) LIKE :search)";
            $params[':search'] = $search;
        }
        
        if (!empty($filters['project_id']) && $filters['project_id'] !== 'all') {
            $query .= " AND t.project_id = :project_id";
            $params[':project_id'] = $filters['project_id'];
        }
        
        // Trier par date de création (les plus récentes en premier)
        $query .= " ORDER BY t.created_at DESC";
        
        // Ajouter la pagination
        if ($limit > 0) {
            $query .= " LIMIT :limit OFFSET :offset";
            $params[':limit'] = $limit;
            $params[':offset'] = $offset;
        }
        
        $stmt = $this->db->prepare($query);
        
        // Binder les paramètres
        foreach ($params as $param => $value) {
            if ($param == ':limit' || $param == ':offset') {
                $stmt->bindValue($param, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($param, $value);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Compte le nombre total de tâches selon les filtres
     * 
     * @param array $filters Filtres à appliquer
     * @return int Nombre total de tâches
     */
    public function countWithFilters($filters = []) {
        
        
        $query = "SELECT COUNT(*) as total FROM tasks t
                  LEFT JOIN projects p ON t.project_id = p.id
                  LEFT JOIN users u ON t.assigned_to = u.id
                  WHERE 1=1";
        
        $params = [];
        
        // Appliquer les filtres
        if (!empty($filters['search'])) {
            $search = "%{$filters['search']}%";
            $query .= " AND (t.name LIKE :search 
                      OR t.description LIKE :search 
                      OR p.name LIKE :search 
                      OR CONCAT(u.first_name, ' ', u.last_name) LIKE :search)";
            $params[':search'] = $search;
        }
        
        if (!empty($filters['project_id']) && $filters['project_id'] !== 'all') {
            $query .= " AND t.project_id = :project_id";
            $params[':project_id'] = $filters['project_id'];
        }
        
        $stmt = $this->db->prepare($query);
        
        // Binder les paramètres
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'];
    }
    
    /**
     * Récupère tous les projets pour le filtre
     * 
     * @return array Liste des projets
     */
    public function getAllProjects() {
        
        $query = "SELECT id, name FROM projects ORDER BY name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère tous les utilisateurs pour l'assignation
     * 
     * @return array Liste des utilisateurs
     */
    public function getAllUsers() {
        
        $query = "SELECT id, first_name, last_name, email FROM users  ORDER BY first_name, last_name";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Ajoute une nouvelle tâche
     * 
     * @param array $taskData Données de la tâche
     * @return int|bool ID de la tâche ajoutée ou false en cas d'erreur
     */
    public function add($taskData) {
        
        $query = "INSERT INTO tasks (name, description, project_id, assigned_to, priority, 
                  status, created_by, scheduled_start_date, scheduled_end_date) 
                  VALUES (:name, :description, :project_id, :assigned_to, :priority, 
                  :status, :created_by, :scheduled_start_date, :scheduled_end_date)";
        
        $stmt = $this->db->prepare($query);
        
        $params = [
            ':name' => $taskData['name'],
            ':description' => $taskData['description'],
            ':project_id' => $taskData['project_id'],
            ':assigned_to' => $taskData['assigned_to'],
            ':priority' => $taskData['priority'],
            ':status' => Constants::TASK_STATUS['TODO'],
            ':created_by' => $taskData['created_by'],
            ':scheduled_start_date' => $taskData['scheduled_start_date'],
            ':scheduled_end_date' => $taskData['scheduled_end_date']
        ];
        
        try {
            $stmt->execute($params);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Met à jour le statut d'une tâche
     * 
     * @param int $taskId ID de la tâche
     * @param string $status Nouveau statut
     * @param int $userId ID de l'utilisateur qui modifie
     * @param string $userRole Rôle de l'utilisateur
     * @return bool Succès ou échec
     */
    public function updateStatus($taskId, $status, $userId, $userRole) {
        
        // Vérifier si la tâche existe
        $task = $this->getById($taskId);
        if (!$task) {
            return false;
        }
        
        // Règles de validation selon le rôle et le statut actuel
        if ($userRole === Constants::USER_ROLES['USER']) {
            // Un utilisateur standard ne peut définir que IN_PROGRESS et COMPLETED
            if (!in_array($status, [Constants::TASK_STATUS['IN_PROGRESS'], Constants::TASK_STATUS['COMPLETED']])) {
                return false;
            }
            
            // Vérifier que la tâche est assignée à cet utilisateur
            if ($task['assigned_to'] != $userId) {
                return false;
            }
            
            // Ne peut pas passer directement de TODO à COMPLETED
            if ($task['status'] === Constants::TASK_STATUS['TODO'] && $status === Constants::TASK_STATUS['COMPLETED']) {
                return false;
            }
        } elseif ($userRole === Constants::USER_ROLES['ADMIN'] || $userRole === Constants::USER_ROLES['SUPER_ADMIN']) {
            // Admin peut définir CANCELLED ou DELAYED seulement pour les tâches qui ne lui sont pas assignées
            if (in_array($status, [Constants::TASK_STATUS['CANCELLED'], Constants::TASK_STATUS['DELAYED']])) {
                // Vérifier que la tâche n'est pas assignée à cet administrateur
                if ($task['assigned_to'] == $userId) {
                    return false;
                }
            } else {
                // Pour les autres statuts, l'admin doit être l'assigné
                if ($task['assigned_to'] != $userId) {
                    return false;
                }
            }
        }
        
        $query = "UPDATE tasks SET status = :status";
        
        // Mise à jour des dates réelles en fonction du statut
        if ($status === Constants::TASK_STATUS['IN_PROGRESS'] && empty($task['actual_start_date'])) {
            $query .= ", actual_start_date = CURRENT_DATE";
        } elseif ($status === Constants::TASK_STATUS['COMPLETED'] && empty($task['actual_end_date'])) {
            $query .= ", actual_end_date = CURRENT_DATE";
        }
        
        $query .= ", updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $params = [
            ':status' => $status,
            ':id' => $taskId
        ];
        
        try {
            $stmt->execute($params);
            
            // Créer une notification pour le changement de statut
            $this->createStatusNotification($task, $status, $userId);
            
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Met à jour le statut de plusieurs tâches
     * 
     * @param array $taskIds IDs des tâches
     * @param string $status Nouveau statut
     * @param int $userId ID de l'utilisateur qui modifie
     * @param string $userRole Rôle de l'utilisateur
     * @return array Résultats de l'opération
     */
    public function updateStatusBulk($taskIds, $status, $userId, $userRole) {
        $results = ['success' => [], 'failed' => []];
        
        foreach ($taskIds as $taskId) {
            if ($this->updateStatus($taskId, $status, $userId, $userRole)) {
                $results['success'][] = $taskId;
            } else {
                $results['failed'][] = $taskId;
            }
        }
        
        return $results;
    }
    
    /**
     * Supprime une tâche
     * 
     * @param int $id ID de la tâche à supprimer
     * @param int $userId ID de l'utilisateur qui supprime
     * @param string $userRole Rôle de l'utilisateur
     * @return bool Succès ou échec
     */
    public function delete($id, $userId, $userRole) {
        
        // Vérifier si la tâche existe et si l'utilisateur a les droits
        $task = $this->getById($id);
        
        if (!$task) {
            return false;
        }
        
        // Vérifier si l'utilisateur a les droits de suppression
        if ($userRole !== Constants::USER_ROLES['ADMIN'] && 
            $userRole !== Constants::USER_ROLES['SUPER_ADMIN'] && 
            $task['created_by'] != $userId) {
            return false;
        }
        
        $query = "DELETE FROM tasks WHERE id = :id";
        $stmt = $this->db->prepare($query);
        
        try {
            $stmt->execute([':id' => $id]);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    /**
     * Récupère une tâche par son ID
     * 
     * @param int $id ID de la tâche
     * @return array|bool Données de la tâche ou false
     */
    public function getById($id) {
        
        $query = "SELECT t.*, 
                  p.name as project_name, 
                  u.first_name as assigned_first_name, 
                  u.last_name as assigned_last_name,
                  u.email as assigned_email,
                  c.first_name as creator_first_name, 
                  c.last_name as creator_last_name
                  FROM tasks t
                  LEFT JOIN projects p ON t.project_id = p.id
                  LEFT JOIN users u ON t.assigned_to = u.id
                  LEFT JOIN users c ON t.created_by = c.id
                  WHERE t.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: false;
    }
    
    /**
     * Crée une notification lors du changement de statut d'une tâche
     * 
     * @param array $task Données de la tâche
     * @param string $newStatus Nouveau statut
     * @param int $userId ID de l'utilisateur qui effectue l'action
     * @return bool Succès ou échec
     */
    private function createStatusNotification($task, $newStatus, $userId) {
        
        // Déterminer le destinataire de la notification
        $recipientId = ($userId == $task['created_by']) ? $task['assigned_to'] : $task['created_by'];
        
        if (empty($recipientId)) {
            return false; // Pas de destinataire, pas de notification
        }
        
        $message = "";
        $action = "status_change";
        
        // Construire le message
        switch ($newStatus) {
            case Constants::TASK_STATUS['IN_PROGRESS']:
                $message = "La tâche '{$task['name']}' a été démarrée";
                break;
            case Constants::TASK_STATUS['COMPLETED']:
                $message = "La tâche '{$task['name']}' a été marquée comme terminée";
                break;
            case Constants::TASK_STATUS['DELAYED']:
                $message = "La tâche '{$task['name']}' a été signalée comme retardée";
                break;
            case Constants::TASK_STATUS['CANCELLED']:
                $message = "La tâche '{$task['name']}' a été annulée";
                break;
            default:
                $message = "Le statut de la tâche '{$task['name']}' a été modifié";
        }
        
        $query = "INSERT INTO notifications (sender_id, recipient_id, task_id, action, message) 
                  VALUES (:sender_id, :recipient_id, :task_id, :action, :message)";
        
        $stmt = $this->db->prepare($query);
        $params = [
            ':sender_id' => $userId,
            ':recipient_id' => $recipientId,
            ':task_id' => $task['id'],
            ':action' => $action,
            ':message' => $message
        ];
        
        try {
            $stmt->execute($params);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Récupère le nombre de tâches en retard d'un utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @return int Nombre de tâches en retard
     */
    public function getDelayedTasksCount($userId) {
        $query = "SELECT COUNT(*) as count 
                  FROM tasks
                  WHERE assigned_to = :user_id
                  AND status = :status";
        
        $stmt = $this->db->prepare($query);
        $status = Constants::TASK_STATUS['DELAYED'];
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    /**
     * Récupère les tâches récentes d'un utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @param int $limit Nombre de tâches à récupérer
     * @return array Tâches récentes
     */
    public function getRecentTasks($userId, $limit = 5) {
        $query = "SELECT t.*, p.name as project_name
                  FROM tasks t
                  JOIN projects p ON t.project_id = p.id
                  WHERE t.assigned_to = :user_id
                  AND t.status != :cancelled_status
                  ORDER BY t.created_at DESC
                  LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $cancelledStatus = Constants::TASK_STATUS['CANCELLED'];
        $stmt->bindParam(':cancelled_status', $cancelledStatus);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 