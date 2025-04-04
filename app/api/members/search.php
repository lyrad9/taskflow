<?php
require_once '../../../config/database.php';
require_once '../../../app/models/MemberModel.php';
require_once '../../../app/helpers/AuthHelper.php';

// Vérifier que la requête est en GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
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

// Récupérer les paramètres de recherche
$searchTerm = trim($_GET['search'] ?? '');
$role = trim($_GET['role'] ?? 'USER');

// Créer une instance du modèle
$memberModel = new MemberModel();

// Effectuer la recherche
$members = $memberModel->search($searchTerm, $role);

// Retourner les résultats
header('Content-Type: application/json');
echo json_encode(['members' => $members]);
