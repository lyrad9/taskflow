<?php
require_once "../../../config/database.php";
require_once "../../models/TeamModel.php";



// Vérifier le jeton CSRF
/* if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'error' => 'Token CSRF invalide']);
    exit;
} */

// Vérifier que la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
  exit;
}

    // Récupérer l'ID de l'équipe
    $teamId = isset($_POST['team_id']) ? intval($_POST['team_id']) : 0;

if ($teamId <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID d\'équipe invalide']);
    exit;
}

try {
    // Instancier le modèle team
    $teamModel = new TeamModel();
    
    // Vérifier si l'équipe existe
    $team = $teamModel->getById($teamId);
    
    if (!$team) {
        echo json_encode(['success' => false, 'error' => 'Équipe non trouvée']);
        exit;
    }
    
    // Récupérer les membres de l'équipe
    $members = $teamModel->getMembers($teamId);
    
    // Retourner les résultats
    echo json_encode([
        'success' => true,
        'data' => $members
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} 