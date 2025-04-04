<?php
require_once '../../../config/database.php';
require_once '../../../app/models/MemberModel.php';
require_once '../../../app/helpers/AuthHelper.php';

// Vérifier que la requête est en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Vérifier que l'utilisateur est connecté et est admin
session_start();
if (!AuthHelper::isAdmin()) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Accès non autorisé']);
    exit;
}

// Vérifier le token CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Token CSRF invalide']);
    exit;
}

// Récupérer l'ID du membre à supprimer
$memberId = isset($_POST['memberId']) ? (int)$_POST['memberId'] : 0;

if ($memberId <= 0) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'ID de membre invalide']);
    exit;
}

// Vérifier le rôle de l'utilisateur connecté
$userRole = isset($_POST['userRole']) ? $_POST['userRole'] : '';
if ($userRole !== 'SUPER_ADMIN' && $userRole !== 'ADMIN') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Vous n\'avez pas les droits nécessaires pour supprimer un membre']);
    exit;
}

// Créer une instance du modèle
$memberModel = new MemberModel();

// Vérifier que le membre existe et a un rôle USER
$member = $memberModel->getById($memberId);
if (!$member) {
    header('HTTP/1.1 404 Not Found');
    echo json_encode(['error' => 'Membre non trouvé']);
    exit;
}

if ($member['role'] !== 'USER') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Vous ne pouvez supprimer que des membres avec le rôle USER']);
    exit;
}

// Supprimer le membre
if ($memberModel->delete($memberId)) {
    echo json_encode([
        'success' => true,
        'message' => 'Membre supprimé avec succès'
    ]);
} else {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Erreur lors de la suppression du membre']);
}
