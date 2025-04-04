<?php
require_once '../../../config/database.php';
require_once '../../models/TeamModel.php';
require_once '../../helpers/AuthHelper.php';

// Vérifier que l'utilisateur est connecté et a les droits d'administration
AuthHelper::requireAdmin();

// Vérifier que l'ID de l'équipe est fourni
if (!isset($_GET['id']) || empty($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID d\'équipe non fourni']);
    exit;
}

$teamId = (int)$_GET['id'];

// Instancier le modèle d'équipe
$teamModel = new TeamModel();

// Récupérer les détails de l'équipe
$team = $teamModel->getById($teamId);

if (!$team) {
    http_response_code(404);
    echo json_encode(['error' => 'Équipe non trouvée']);
    exit;
}

// Récupérer les membres de l'équipe
$members = $teamModel->getMembers($teamId);

// Retourner les données au format JSON
echo json_encode([
    'success' => true,
    'team' => $team,
    'members' => $members
]); 