<?php
require_once "../../../config/database.php";
require_once "../../models/ProjectModel.php";
require_once "../../models/ClientModel.php";
require_once "../../models/TeamModel.php";
require_once "../../helpers/ProjectValidationHelper.php";
require_once "../../helpers/FileUploadHelper.php";

// Vérifier que la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

// Données du formulaire
$data = $_POST;
$files = $_FILES;

// Validation des données
$validation = ProjectValidationHelper::validateProject($data, $files);

if (!$validation['isValid']) {
    echo json_encode(['success' => false, 'error' => $validation['errors'][0]]);
    exit;
}

try {
    // Instancier les modèles
    $projectModel = new ProjectModel();
    $clientModel = new ClientModel();
    $teamModel = new TeamModel();
    
    // Créer un nouveau client si nécessaire
    $clientId = 0;
    if (isset($data['client_tab']) && $data['client_tab'] === 'new') {
        $clientData = [
            'first_name' => $data['client_first_name'],
            'last_name' => $data['client_last_name'],
            'city' => $data['client_city'],
            'residence' => $data['client_residence'] ?? '',
            'phone_number' => $data['client_phone_number']
        ];
        
        $clientId = $clientModel->create($clientData);
        if (!$clientId) {
            throw new Exception("Erreur lors de la création du client");
        }
    } else {
        $clientId = $data['client_id'];
    }
    
    // Télécharger les documents
    $documentPaths = [];
    if (isset($files['documents']) && !empty($files['documents']['name'][0])) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/projects';
        $allowedExtensions = ['pdf'];
        $maxFileSize = 7340032; // 7MB en octets
        
        $uploadResults = FileUploadHelper::uploadMultipleFiles($files['documents'], $uploadDir, $allowedExtensions, $maxFileSize);
        
        foreach ($uploadResults as $result) {
            if ($result['success']) {
                $documentPaths[] = $result['filepath'];
            } else {
                throw new Exception("Erreur lors du téléchargement des documents: " . $result['error']);
            }
        }
    }
    
    // Préparer les données du projet
    $projectData = [
        'name' => $data['name'],
        'description' => $data['description'],
        'client_id' => $clientId,
        'team_id' => $data['team_id'],
        'budget' => $data['budget'],
        'project_type' => $data['project_type'],
        'documents' => $documentPaths,
        'status' => 'In progress',
        'scheduled_start_date' => $data['scheduled_start_date'],
        'scheduled_end_date' => $data['scheduled_end_date']
    ];
    
    // Créer le projet
    $projectId = $projectModel->create($projectData);
    
    if (!$projectId) {
        throw new Exception("Erreur lors de la création du projet");
    }
    
    // Retourner une réponse de succès
    echo json_encode([
        'success' => true,
        'message' => 'Projet créé avec succès',
        'project_id' => $projectId
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} 