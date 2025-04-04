<?php
require_once '../../helpers/AuthHelper.php';
require_once '../../models/TaskModel.php';
require_once '../../helpers/Constants.php';
require_once '../../..//config/database.php';

// Vérifier que l'utilisateur est authentifié
AuthHelper::requireAdmin();

// Initialiser la réponse
header('Content-Type: application/json');

try {
    // Récupérer les paramètres de recherche
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $projectId = isset($_GET['project_id']) ? $_GET['project_id'] : 'all';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;
    
    // Construire les filtres
    $filters = [];
    if (!empty($search)) {
        $filters['search'] = $search;
    }
    if ($projectId !== 'all') {
        $filters['project_id'] = $projectId;
    }
    
    // Récupérer les tâches filtrées
    $taskModel = new TaskModel();
    $tasks = $taskModel->getAllWithFilters($filters, $limit, $offset);
    
    // Compter le nombre total de résultats pour la pagination
    $totalTasks = $taskModel->countWithFilters($filters);
    $totalPages = ceil($totalTasks / $limit);
    
    // Générer le HTML pour le tableau de tâches
    $html = '';
    
    if (!empty($tasks)) {
        foreach ($tasks as $task) {
            $html .= '<tr data-task-id="' . $task['id'] . '">';
            $html .= '<td class="checkbox-cell">';
            $html .= '<div class="checkbox-container">';
            $html .= '<input type="checkbox" class="d-checkbox" data-id="' . $task['id'] . '">';
            $html .= '</div>';
            $html .= '</td>';
            $html .= '<td>';
            $html .= '<span class="task-name">' . htmlspecialchars($task['name']) . '</span>';
            $html .= '</td>';
            $html .= '<td>';
            $html .= '<div style="display: flex; flex-direction: column; gap: 2px;" class="user-info">';
            $html .= '<span class="">' . htmlspecialchars($task['assigned_first_name'] . ' ' . $task['assigned_last_name']) . '</span>';
            $html .= '<span style="color: var(--text-muted); class="">' . htmlspecialchars($task['assigned_email'] ?? 'Non assigné') . '</span>';
            $html .= '</div>';
            $html .= '</td>';
            $html .= '<td>';
            $html .= '<div class="project-info">';
            $html .= '<span class="project-name">' . htmlspecialchars($task['project_name']) . '</span>';
            $html .= '</div>';
            $html .= '</td>';
            $html .= '<td>';
            $html .= '<span class="badge status ' . Constants::getTaskStatusClass($task['status']) . '">';
            $html .= htmlspecialchars($task['status']);
            $html .= '</span>';
            $html .= '</td>';
            $html .= '<td>';
            $html .= '<span class="badge ' . Constants::getTaskPriorityClass($task['priority']) . '">';
            $html .= htmlspecialchars($task['priority']);
            $html .= '</span>';
            $html .= '</td>';
            $html .= '<td class="actions-cell">';
            $html .= '<a href="/admin/task/' . $task['id'] . '" class="action-btn view-btn" title="Voir les détails">';
            $html .= '<i class="fas fa-link"></i>';
            $html .= '</a>';
            $html .= '<div class="dropdown-actions">';
            $html .= '<button class="action-btn dropdown-toggle" title="Plus d\'actions">';
            $html .= '<i class="fas fa-ellipsis-v"></i>';
            $html .= '</button>';
            $html .= '<div class="dropdown-menu-actions">';
            $html .= '<a href="/admin/task/edit/' . $task['id'] . '" class="dropdown-item-action">';
            $html .= '<i class="fas fa-edit"></i> Modifier';
            $html .= '</a>';
            $html .= '<div class="dropdown-item-action delete delete-task" data-id="' . $task['id'] . '">';
            $html .= '<i class="fas fa-trash"></i> Supprimer';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</td>';
            $html .= '</tr>';
        }
    } else {
        $html = '<tr><td colspan="7" style="text-align: center; padding: 30px;"><p>Aucune tâche trouvée.</p></td></tr>';
    }
    
    // Générer la pagination si nécessaire
    $pagination = '';
    if ($totalPages > 1) {
        $pagination .= '<a href="?page=1' . (!empty($search) ? '&search=' . urlencode($search) : '') . ($projectId != 'all' ? '&project_id=' . urlencode($projectId) : '') . '" ';
        $pagination .= 'class="pagination-btn ' . ($page == 1 ? 'disabled' : '') . '">';
        $pagination .= '<i class="fas fa-angle-double-left"></i>';
        $pagination .= '</a>';
        
        $pagination .= '<a href="?page=' . max(1, $page - 1) . (!empty($search) ? '&search=' . urlencode($search) : '') . ($projectId != 'all' ? '&project_id=' . urlencode($projectId) : '') . '" ';
        $pagination .= 'class="pagination-btn ' . ($page == 1 ? 'disabled' : '') . '">';
        $pagination .= '<i class="fas fa-angle-left"></i>';
        $pagination .= '</a>';
        
        $startPage = max(1, $page - 2);
        $endPage = min($startPage + 4, $totalPages);
        
        if ($endPage - $startPage < 4 && $startPage > 1) {
            $startPage = max(1, $endPage - 4);
        }
        
        for ($i = $startPage; $i <= $endPage; $i++) {
            $pagination .= '<a href="?page=' . $i . (!empty($search) ? '&search=' . urlencode($search) : '') . ($projectId != 'all' ? '&project_id=' . urlencode($projectId) : '') . '" ';
            $pagination .= 'class="pagination-btn ' . ($i == $page ? 'active' : '') . '">';
            $pagination .= $i;
            $pagination .= '</a>';
        }
        
        $pagination .= '<a href="?page=' . min($totalPages, $page + 1) . (!empty($search) ? '&search=' . urlencode($search) : '') . ($projectId != 'all' ? '&project_id=' . urlencode($projectId) : '') . '" ';
        $pagination .= 'class="pagination-btn ' . ($page == $totalPages ? 'disabled' : '') . '">';
        $pagination .= '<i class="fas fa-angle-right"></i>';
        $pagination .= '</a>';
        
        $pagination .= '<a href="?page=' . $totalPages . (!empty($search) ? '&search=' . urlencode($search) : '') . ($projectId != 'all' ? '&project_id=' . urlencode($projectId) : '') . '" ';
        $pagination .= 'class="pagination-btn ' . ($page == $totalPages ? 'disabled' : '') . '">';
        $pagination .= '<i class="fas fa-angle-double-right"></i>';
        $pagination .= '</a>';
    }
    
    // Renvoyer la réponse
    echo json_encode([
        'success' => true,
        'html' => $html,
        'pagination' => $pagination,
        'total' => $totalTasks,
        'pages' => $totalPages,
        'current_page' => $page
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Une erreur est survenue lors de la recherche : ' . $e->getMessage()
    ]);
} 