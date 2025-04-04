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

// Récupérer les données
$memberIds = isset($_POST['member_ids']) ? $_POST['member_ids'] : [];
$role = isset($_POST['role']) ? $_POST['role'] : '';

// Validation
if (empty($memberIds) || !is_array($memberIds)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Aucun membre sélectionné']);
    exit;
}

if (!in_array($role, ['ADMIN', 'SUPER_ADMIN', 'USER'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Rôle invalide']);
    exit;
}

// Vérifier que l'utilisateur connecté a le droit de modifier le rôle
$currentUserRole = AuthHelper::getUserRole();

// Seul un SUPER_ADMIN peut promouvoir au rôle de SUPER_ADMIN
if ($role === 'SUPER_ADMIN' && $currentUserRole !== 'SUPER_ADMIN') {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Seul un Super Administrateur peut promouvoir au rôle de Super Administrateur']);
    exit;
}

// Créer une instance du modèle
$memberModel = new MemberModel();

// Mettre à jour le rôle des membres
if ($memberModel->updateRole($memberIds, $role)) {
    echo json_encode([
        'success' => true,
        'message' => 'Rôle mis à jour pour ' . count($memberIds) . ' membre(s)'
    ]);
} else {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Erreur lors de la mise à jour du rôle']);
}
