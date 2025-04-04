<?php
require_once '../../../config/database.php';
require_once '../../models/ProjectModel.php';
require_once '../../helpers/AuthHelper.php';
require_once '../../helpers/Constants.php';

// Vérifier que l'utilisateur est connecté et est un membre
AuthHelper::requireLogin();

// Initialiser la réponse
header('Content-Type: application/json');

try {
    // Récupérer les paramètres de recherche
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $status = isset($_GET['status']) ? $_GET['status'] : 'all';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;
    $userId = $_SESSION['id'];
    
    // Construire les filtres
    $filters = ['user_id' => $userId];
    if (!empty($search)) {
        $filters['search'] = $search;
    }
    if ($status !== 'all') {
        $filters['status'] = $status;
    }
    
    // Récupérer les projets filtrés
    $projectModel = new ProjectModel();
    
    // Adapter cette méthode pour récupérer uniquement les projets du membre
    $projects = $projectModel->getMemberProjectsWithFilters($filters, $limit, $offset);
    
    // Compter le nombre total de résultats pour la pagination
    $totalProjects = $projectModel->countMemberProjectsWithFilters($filters);
    $totalPages = ceil($totalProjects / $limit);
    
    // Préparer les données pour la réponse
    $response = [
        'success' => true,
        'data' => [
            'projects' => $projects,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalResults' => $totalProjects
            ]
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} 