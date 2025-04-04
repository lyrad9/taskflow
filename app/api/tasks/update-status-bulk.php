<?php
require_once '../../helpers/AuthHelper.php';
require_once '../../models/TaskModel.php';
require_once '../../helpers/Constants.php';
require_once '../../../config/database.php';

// Vérifier que l'utilisateur est authentifié
AuthHelper::requireAdmin();

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
    if (!isset($_POST['task_ids']) || !is_array($_POST['task_ids']) || empty($_POST['task_ids'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Aucune tâche sélectionnée']);
        exit;
    }
    
    if (!isset($_POST['status']) || empty($_POST['status'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Le statut est obligatoire']);
        exit;
    }
    
    // Récupérer les paramètres
    $taskIds = $_POST['task_ids'];
    $status = $_POST['status'];
    $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : $_SESSION['user_id'];
    $userRole = isset($_POST['user_role']) ? $_POST['user_role'] : $_SESSION['user_role'];
    
    // Vérifier que le statut est valide
if (!array_key_exists($status, Constants::TASK_STATUS)) {
    http_response_code(400);
    echo json_encode(['error' => 'Statut invalide']);
    return;
}

    
    // Mettre à jour le statut des tâches
    $taskModel = new TaskModel();
    $results = $taskModel->updateStatusBulk($taskIds, Constants::TASK_STATUS[$status], $userId, $userRole);
    
    echo json_encode([
        'success' => true,
        'message' => 'Statuts mis à jour',
        'success' => $results['success'],
        'failed' => $results['failed']
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Une erreur est survenue : ' . $e->getMessage()
    ]);
} 