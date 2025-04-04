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

// Récupérer et valider les données
$teamId = (int)($_POST['team_id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$members = isset($_POST['members']) && !empty($_POST['members']) ? explode(',', $_POST['members']) : [];

// Validation
$errors = [];

if (empty($teamId)) {
    $errors['team_id'] = 'ID d\'équipe invalide';
}

if (empty($name)) {
    $errors['name'] = 'Le nom de l\'équipe est requis';
}

if (count($members) > 3) {
    $errors['members'] = 'Une équipe ne peut pas avoir plus de 3 membres';
}

// Si des erreurs, retourner les erreurs
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['errors' => $errors]);
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

// Mettre à jour l'équipe
$teamData = [
    'name' => $name,
    'description' => $description
];

if (!$teamModel->update($teamId, $teamData)) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la mise à jour de l\'équipe']);
    exit;
}

// Récupérer les membres actuels
$currentMembers = $teamModel->getMembers($teamId);
$currentMemberIds = array_column($currentMembers, 'id');

// Déterminer les membres à ajouter et à supprimer
$membersToAdd = array_diff($members, $currentMemberIds);
$membersToRemove = array_diff($currentMemberIds, $members);

// Supprimer les membres qui ne sont plus dans l'équipe
foreach ($membersToRemove as $memberId) {
    $teamModel->removeMember($teamId, $memberId);
}

// Ajouter les nouveaux membres
foreach ($membersToAdd as $memberId) {
    $teamModel->addMember($teamId, $memberId);
}

// Retourner un succès
echo json_encode([
    'success' => true,
    'message' => 'Équipe mise à jour avec succès'
]); 