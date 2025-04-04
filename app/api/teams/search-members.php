<?php
require_once '../../config/database.php';
require_once '../../models/MemberModel.php';
require_once '../../helpers/AuthHelper.php';

// Vérifier que l'utilisateur est connecté et a les droits d'administration
AuthHelper::requireAdmin();

// Récupérer le terme de recherche
$searchTerm = $_GET['term'] ?? '';

if (empty($searchTerm)) {
    echo json_encode([]);
    exit;
}

// Rechercher des membres avec le rôle USER
$memberModel = new MemberModel();
$members = $memberModel->search($searchTerm, 'USER');

// Formater les résultats pour l'autocomplete
$results = [];
foreach ($members as $member) {
    $results[] = [
        'id' => $member['id'],
        'value' => $member['first_name'] . ' ' . $member['last_name'],
        'label' => $member['first_name'] . ' ' . $member['last_name'],
        'name' => $member['first_name'] . ' ' . $member['last_name'],
        'email' => $member['email'],
        'fonction' => $member['fonction'] ?: 'Non spécifié',
        'profile_picture' => $member['profile_picture'] ?: '/public/assets/img/default-profile.jpg'
    ];
}

// Retourner les résultats au format JSON
echo json_encode($results); 