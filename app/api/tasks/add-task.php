<?php
require_once '../../../config/Database.php';
require_once '../../helpers/AuthHelper.php';
require_once '../../models/TaskModel.php';
require_once '../../helpers/TaskValidationHelper.php';
require_once '../../helpers/Constants.php';


// Vérifier que l'utilisateur est authentifié
AuthHelper::requireLogin();

// Vérifier que l'utilisateur est un administrateur
if (!AuthHelper::isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Vous n\'avez pas les droits pour effectuer cette action']);
    exit;
}

// Initialiser la réponse
header('Content-Type: application/json');

// Vérifier le jeton CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['error' => 'Token CSRF invalide']);
    exit;
}

try {
    // Récupérer les données du formulaire
    $taskData = [
        'name' => isset($_POST['name']) ? trim($_POST['name']) : '',
        'description' => isset($_POST['description']) ? trim($_POST['description']) : '',
        'project_id' => isset($_POST['project_id']) ? (int)$_POST['project_id'] : 0,
        'assigned_to' => isset($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : 0,
        'priority' => isset($_POST['priority']) ? trim($_POST['priority']) : '',
        'scheduled_start_date' => isset($_POST['scheduled_start_date']) ? trim($_POST['scheduled_start_date']) : '',
        'scheduled_end_date' => isset($_POST['scheduled_end_date']) ? trim($_POST['scheduled_end_date']) : '',
        'created_by' => isset($_POST['created_by']) ? (int)$_POST['created_by'] : $_SESSION['user_id']
    ];
    
    // Récupérer les dates du projet pour validation
    $projectDates = TaskValidationHelper::getProjectDates($taskData['project_id']);
    
    // Valider les données
    $errors = TaskValidationHelper::validateTaskData($taskData, $projectDates);
    
    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'validation_errors' => $errors
        ]);
        exit;
    }
    
    // Vérifier que l'utilisateur est bien dans l'équipe du projet
    if (!TaskValidationHelper::validateUserInProjectTeam($taskData['assigned_to'], $taskData['project_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'validation_errors' => [
                'assigned_to' => 'L\'utilisateur sélectionné n\'est pas membre de l\'équipe du projet'
            ]
        ]);
        exit;
    }
    
    // Ajouter la tâche à la base de données
    $taskModel = new TaskModel();
    $taskId = $taskModel->add($taskData);
    
    if ($taskId) {
        // Créer une notification pour l'utilisateur assigné
        createTaskAssignmentNotification($taskData['created_by'], $taskData['assigned_to'], $taskId, $taskData['name']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Tâche ajoutée avec succès',
            'task_id' => $taskId
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Erreur lors de l\'ajout de la tâche'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Une erreur est survenue : ' . $e->getMessage()
    ]);
}

/**
 * Crée une notification pour l'assignation d'une tâche
 * 
 * @param int $senderId ID de l'utilisateur qui assigne la tâche
 * @param int $recipientId ID de l'utilisateur assigné à la tâche
 * @param int $taskId ID de la tâche
 * @param string $taskName Nom de la tâche
 * @return bool Succès ou échec
 */
function createTaskAssignmentNotification($senderId, $recipientId, $taskId, $taskName) {
    $db = new Database();
    $conn = $db->getConnection();
    
    $message = "Vous avez été assigné à la tâche '{$taskName}'";
    $action = "task_assignment";
    
    $query = "INSERT INTO notifications (sender_id, recipient_id, task_id, action, message) 
              VALUES (:sender_id, :recipient_id, :task_id, :action, :message)";
    
    $stmt = $conn->prepare($query);
    $params = [
        ':sender_id' => $senderId,
        ':recipient_id' => $recipientId,
        ':task_id' => $taskId,
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