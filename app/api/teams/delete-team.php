<?php
require_once '../../../config/database.php';
require_once '../../models/TeamModel.php';
require_once '../../helpers/AuthHelper.php';

// Vérifier que l'utilisateur est connecté et a les droits d'administration
AuthHelper::requireAdmin();

// Vérifier que la requête est bien en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Vérifier le jeton CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['error' => 'Jeton CSRF invalide']);
    exit;
}

// Récupérer l'ID de l'équipe
$teamId = (int)($_POST['team_id'] ?? 0);

if (empty($teamId)) {
    http_response_code(400);
    echo json_encode(['error' => 'ID d\'équipe invalide']);
    exit;
}

$teamModel = new TeamModel();

// Vérifier que l'équipe existe
$team = $teamModel->getById($teamId);
if (!$team) {
    http_response_code(404);
    echo json_encode(['error' => 'Équipe non trouvée']);
    exit;
}

// Vérifier si l'équipe a des projets associés
if (isset($team['has_project']) && $team['has_project']) {
    http_response_code(400);
    echo json_encode(['error' => 'Impossible de supprimer une équipe assignée à un projet']);
    exit;
}

// Supprimer l'équipe
if (!$teamModel->delete($teamId)) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la suppression de l\'équipe']);
    exit;
}

// Retourner un succès
echo json_encode([
    'success' => true,
    'message' => 'Équipe supprimée avec succès'
]); 