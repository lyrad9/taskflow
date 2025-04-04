<?php

/**
 * Helper pour la validation des tâches
 */
class TaskValidationHelper {
    
    /**
     * Valide les données d'une nouvelle tâche
     * 
     * @param array $data Données de la tâche à valider
     * @param array $projectDates Dates du projet associé
     * @return array Erreurs de validation ou tableau vide si tout est valide
     */
    public static function validateTaskData($data, $projectDates = null) {
        $errors = [];
        
        // Vérifier les champs obligatoires
        $requiredFields = [
            'name' => 'Le nom de la tâche est obligatoire',
            'description' => 'La description est obligatoire',
            'project_id' => 'Veuillez sélectionner un projet',
            'assigned_to' => 'Veuillez assigner la tâche à un utilisateur',
            'priority' => 'La priorité est obligatoire',
            'scheduled_start_date' => 'La date de début prévue est obligatoire',
            'scheduled_end_date' => 'La date de fin prévue est obligatoire'
        ];
        
        foreach ($requiredFields as $field => $message) {
            if (empty($data[$field])) {
                $errors[$field] = $message;
            }
        }
        
        // Si des erreurs de champs obligatoires existent, on arrête ici
        if (!empty($errors)) {
            return $errors;
        }
        
        // Validation de la priorité
        $validPriorities = ['low', 'medium', 'high', 'immediate'];
        if (!in_array($data['priority'], $validPriorities)) {
            $errors['priority'] = 'La priorité sélectionnée est invalide';
        }
        
        // Validation des dates
        if (!empty($data['scheduled_start_date']) && !empty($data['scheduled_end_date'])) {
            $startDate = new DateTime($data['scheduled_start_date']);
            $endDate = new DateTime($data['scheduled_end_date']);
            
            // Vérifier que la date de fin est après la date de début
            if ($endDate < $startDate) {
                $errors['scheduled_end_date'] = 'La date de fin doit être postérieure à la date de début';
            }
            
            // Vérifier les dates par rapport au projet si fournies
            if ($projectDates) {
                $projectStartDate = new DateTime($projectDates['scheduled_start_date']);
                $projectEndDate = new DateTime($projectDates['scheduled_end_date']);
                
                // La tâche doit commencer après le début du projet
                if ($startDate < $projectStartDate) {
                    $errors['scheduled_start_date'] = 'La date de début doit être postérieure ou égale à la date de début du projet';
                }
                
                // La tâche doit se terminer avant la fin du projet
                if ($endDate > $projectEndDate) {
                    $errors['scheduled_end_date'] = 'La date de fin doit être antérieure ou égale à la date de fin du projet';
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Vérifie que l'utilisateur est bien membre de l'équipe du projet
     * 
     * @param int $userId ID de l'utilisateur
     * @param int $projectId ID du projet
     * @return bool True si l'utilisateur est membre de l'équipe, sinon false
     */
    public static function validateUserInProjectTeam($userId, $projectId) {
        $db = new Database();
        $conn = $db->getConnection();
        
        $query = "SELECT COUNT(*) as count FROM projects p
                  JOIN teams t ON p.team_id = t.id
                  JOIN team_members tm ON t.id = tm.team_id
                  WHERE p.id = :project_id AND tm.user_id = :user_id";
                  
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':project_id' => $projectId,
            ':user_id' => $userId
        ]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }
    
    /**
     * Récupère les dates prévues du projet
     * 
     * @param int $projectId ID du projet
     * @return array|null Dates du projet ou null si le projet n'existe pas
     */
    public static function getProjectDates($projectId) {
        $db = new Database();
        $conn = $db->getConnection();
        
        $query = "SELECT scheduled_start_date, scheduled_end_date
                  FROM projects WHERE id = :project_id";
                  
        $stmt = $conn->prepare($query);
        $stmt->execute([':project_id' => $projectId]);
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ?: null;
    }
} 