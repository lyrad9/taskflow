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
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$members = isset($_POST['members']) ? array_map('intval', explode(',', $_POST['members'])) : [];


// Validation
$errors = [];

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

// Créer l'équipe
$teamModel = new TeamModel();
$teamData = [
    'name' => $name,
    'description' => $description
];

$teamId = $teamModel->create($teamData);

if (!$teamId) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la création de l\'équipe']);
    exit;
}

// Ajouter les membres
$success = true;
foreach ($members as $memberId) {
    if (!$teamModel->addMember($teamId, $memberId)) {
        $success = false;
    }
}

if (!$success) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de l\'ajout des membres à l\'équipe']);
    exit;
}

// Retourner un succès
echo json_encode([
    'success' => true,
    'message' => 'Équipe créée avec succès',
    'team_id' => $teamId
]); 