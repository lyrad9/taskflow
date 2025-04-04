<?php
require_once '../../helpers/AuthHelper.php';
require_once '../../models/TaskModel.php';

// Vérifier que l'utilisateur est authentifié
AuthHelper::requireLogin();

// Initialiser la réponse
header('Content-Type: application/json');

// Vérifier le jeton CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['error' => 'Token CSRF invalide']);
    exit;
}

try {
    // Vérifier les paramètres obligatoires
    if (!isset($_POST['task_id']) || empty($_POST['task_id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de tâche manquant']);
        exit;
    }
    
    // Récupérer les paramètres
    $taskId = (int)$_POST['task_id'];
    $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : $_SESSION['user_id'];
    $userRole = isset($_POST['user_role']) ? $_POST['user_role'] : $_SESSION['user_role'];
    
    // Supprimer la tâche
    $taskModel = new TaskModel();
    $result = $taskModel->delete($taskId, $userId, $userRole);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Tâche supprimée avec succès'
        ]);
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Impossible de supprimer la tâche. Vérifiez vos droits.'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Une erreur est survenue : ' . $e->getMessage()
    ]);
} 