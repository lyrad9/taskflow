<?php

require_once "../../../config/database.php";
require_once "../../models/ClientModel.php";




// Vérifier que la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

// Récupérer le terme de recherche
$searchTerm = isset($_POST['search']) ? trim($_POST['search']) : '';

if (empty($searchTerm)) {
    echo json_encode(['success' => false, 'error' => 'Terme de recherche manquant']);
    exit;
}

try {
    // Instancier le modèle client
    $clientModel = new ClientModel();
    
    // Rechercher les clients correspondants
    $clients = $clientModel->search($searchTerm);
    
    // Retourner les résultats
    echo json_encode([
        'success' => true,
        'data' => $clients
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} 