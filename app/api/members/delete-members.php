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

// Récupérer les IDs des membres à supprimer
$memberIds = isset($_POST['member_ids']) ? $_POST['member_ids'] : [];

if (empty($memberIds) || !is_array($memberIds)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Aucun membre sélectionné']);
    exit;
}

// Vérifier le rôle de l'utilisateur connecté
$userRole = isset($_POST['userRole']) ? $_POST['userRole'] : '';
if ($userRole !== 'SUPER_ADMIN' && $userRole !== 'ADMIN') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Vous n\'avez pas les droits nécessaires pour supprimer des membres']);
    exit;
}

// Créer une instance du modèle
$memberModel = new MemberModel();

// Vérifier que tous les membres ont un rôle USER
$nonUserMembers = [];
foreach ($memberIds as $id) {
    $member = $memberModel->getById($id);
    if ($member && $member['role'] !== 'USER') {
        $nonUserMembers[] = $id;
    }
}

if (!empty($nonUserMembers)) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode([
        'error' => 'Certains membres sélectionnés ne peuvent pas être supprimés car ils n\'ont pas le rôle USER',
        'non_user_members' => $nonUserMembers
    ]);
    exit;
}

// Supprimer les membres
if ($memberModel->deleteMultiple($memberIds)) {
    echo json_encode([
        'success' => true,
        'message' => count($memberIds) . ' membre(s) supprimé(s) avec succès'
    ]);
} else {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Erreur lors de la suppression des membres']);
}
