<?php
require_once 'config/database.php';
require_once 'app/helpers/AuthHelper.php';
require_once 'config/globals.php';
require_once 'app/core/Controller.php';
require_once 'app/models/ProjectModel.php';
require_once 'app/models/ClientModel.php';
require_once 'app/models/TaskModel.php';
require_once 'app/helpers/DateTimeHelper.php';
require_once 'app/helpers/CurrencyHelper.php';
require_once 'app/helpers/Constants.php';
require_once 'app/models/MemberModel.php';
require_once 'app/models/TeamModel.php';

class AdminController extends Controller {
    public $tabs = [
        '/admin/dashboard' => ['label' => 'Dashboard', 'icon' => 'fas fa-chart-line'],
        '/admin/projects' => ['label' => 'Projets', 'icon' => 'fas fa-folder'],
        '/admin/tasks' => ['label' => 'Tâches', 'icon' => 'fas fa-tasks'],
        '/admin/teams' => ['label' => 'Équipes', 'icon' => 'fas fa-users'],
        '/admin/members' => ['label' => 'Membres', 'icon' => 'fas fa-user'],
        '/admin/clients' => ['label' => 'Clients', 'icon' => 'fas fa-handshake'],
        '/admin/notify' => ['label' => 'Notifications', 'icon' => 'fas fa-bell'],
        

    ];
    
        
    public function __construct() {
        // Vérifier que l'utilisateur est un administrateur
        AuthHelper::requireAdmin();
        
        // Générer un jeton CSRF si nécessaire
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
    
    public function adminDashboard() {
        $this->view('admin/dashboard', [
            'title' => 'Tableau de bord Admin',
            'activeTab' => '/admin/dashboard',
            'content' => 'admin-dashboard',
            'user' => AuthHelper::getUser(),
            'tabs' => $this->tabs
            
        ]);
       
    }
    
    // Pour voir les projets
    public function adminProjects() {      
        $projectModel = new ProjectModel();
        
        // Récupérer les paramètres de pagination et de filtrage
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;
        
        // Récupérer les filtres
        $filters = [];
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }
        
        if (isset($_GET['project_type']) && !empty($_GET['project_type']) && $_GET['project_type'] !== 'all') {
            $filters['project_type'] = $_GET['project_type'];
        }
        
        // Récupérer les projets avec filtrage et pagination
        $projects = $projectModel->getAllWithFilters($filters, $limit, $offset);
        
        // Récupérer le nombre total de projets pour la pagination
        $totalProjects = $projectModel->countWithFilters($filters);
        $totalPages = ceil($totalProjects / $limit);
        
        // Récupérer les types de projets distincts
        $projectTypes = $this->getProjectTypes();
       
        $this->view('admin/dashboard', [
            'title' => 'Gestion des projets',           
            'activeTab' => '/admin/projects',
            'content' => 'admin-projects',
            'projects' => $projects,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'projectTypes' => $projectTypes,
            'user' => AuthHelper::getUser(),
            'tabs' => $this->tabs
        ]);  
    }
    
    /**
     * Récupère tous les types de projets distincts de la base de données
     * 
     * @return array Liste des types de projets
     */
    private function getProjectTypes() {
        $db = new Database();
        $conn = $db->getConnection();
        
        $query = "SELECT DISTINCT project_type FROM projects ORDER BY project_type";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    public function adminProjectDetails($id) {
        
        $this->view('admin/dashboard', [
            'title' => 'Détails du projet',            
            'activeTab' => '/admin/dashboard',
            'tabs' => $this->tabs
        ]);        
    }
    // Pour ajouter un projet
    public function addProject() {       
        $this->view('admin/dashboard', [
            'activeTab' => '/admin/projects',
            'tabs' => $this->tabs,
            'title' => 'Ajouter un projet',
            'content' => 'admin-projects-add'
        ]);
        
    }
    
    public function adminTasks() {
        $taskModel = new TaskModel();
        
        // Récupérer les paramètres de pagination et de filtrage
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;
        
        // Récupérer les filtres
        $filters = [];
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }
        
        if (isset($_GET['project_id']) && !empty($_GET['project_id']) && $_GET['project_id'] !== 'all') {
            $filters['project_id'] = $_GET['project_id'];
        }
        
        // Récupérer les tâches avec filtrage et pagination
        $tasks = $taskModel->getAllWithFilters();
        
        // Récupérer le nombre total de tâches pour la pagination
        $totalTasks = $taskModel->countWithFilters($filters);
        $totalPages = ceil($totalTasks / $limit);
        
        // Récupérer tous les projets pour le filtre
        $projects = $taskModel->getAllProjects();
        
        // Récupérer tous les utilisateurs pour l'assignation
        $users = $taskModel->getAllUsers();
        
        $this->view('admin/dashboard', [
            'activeTab' => '/admin/tasks',
            'tabs' => $this->tabs,
            'title' => 'Gestion des tâches',
            'content' => 'admin-tasks',
            'tasks' => $tasks,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'projects' => $projects,
            'users' => $users,
            'user' => AuthHelper::getUser(),
        ]);
    }
    
    public function adminMembers() {
        $memberModel = new MemberModel();
        
        // Récupérer les paramètres de pagination et de filtrage
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;
        
        // Récupérer les filtres
        $filters = [];
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }
        
        if (isset($_GET['role']) && !empty($_GET['role']) && $_GET['role'] !== 'all') {
            $filters['role'] = $_GET['role'];
        }
        
        // Récupérer les membres avec filtrage et pagination
        $members = $memberModel->getAllWithFilters($filters, $limit, $offset);
        
        // Récupérer le nombre total de membres pour la pagination
        $totalMembers = $memberModel->countWithFilters($filters);
        $totalPages = ceil($totalMembers / $limit);
        
        $this->view('admin/dashboard', [
            'title' => 'Gestion des membres',           
            'activeTab' => '/admin/members',
            'content' => 'admin-members',
            'members' => $members,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'user' => AuthHelper::getUser(),
            'tabs' => $this->tabs
        ]);
    }

    public function adminTeams() {
        $teamModel = new TeamModel();
        $memberModel = new MemberModel();
        $members = $memberModel->getAllMembers();
        
        // Récupérer les paramètres de pagination et de filtrage
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;
        
        // Récupérer les filtres
        $filters = [];
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }
        
        if (isset($_GET['assigned']) && !empty($_GET['assigned']) && $_GET['assigned'] !== 'all') {
            $filters['assigned'] = $_GET['assigned'];
        }
        
        // Récupérer les équipes avec filtrage et pagination
        $teams = $teamModel->getAllWithFilters($filters, $limit, $offset);
        
        // Récupérer le nombre total d'équipes pour la pagination
        $totalTeams = $teamModel->countWithFilters($filters);
        $totalPages = ceil($totalTeams / $limit);
        
        $this->view('admin/dashboard', [
            'title' => 'Gestion des équipes',
            'activeTab' => '/admin/teams',
            'content' => 'admin-teams', 
            'user' => AuthHelper::getUser(),
            'tabs' => $this->tabs,
            'teams' => $teams,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'members' => $members
        ]);
    }
    
    /**
     * Ajoute une nouvelle équipe
     */
    public function addTeam() {
        // Vérifier que la requête est bien en POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
            exit;
        }
        
        // Vérifier le jeton CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            http_response_code(403);
            echo json_encode(['error' => 'Jeton CSRF invalide']);
            exit;
        }
        
        // Récupérer et valider les données
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $members = isset($_POST['members']) ? (array)$_POST['members'] : [];
        
        // Validation
        $errors = [];
        
        if (empty($name)) {
            $errors['name'] = 'Le nom de l\'équipe est requis';
        }
        
        if (count($members) > 3) {
            $errors['members'] = 'Une équipe ne peut pas avoir plus de 3 membres';
        }
        
        // Si des erreurs, retourner les erreurs
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['errors' => $errors]);
            exit;
        }
        
        // Créer l'équipe
        $teamModel = new TeamModel();
        $teamData = [
            'name' => $name,
            'description' => $description
        ];
        
        $teamId = $teamModel->create($teamData);
        
        if (!$teamId) {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur lors de la création de l\'équipe']);
            exit;
        }
        
        // Ajouter les membres
        $success = true;
        foreach ($members as $memberId) {
            if (!$teamModel->addMember($teamId, $memberId)) {
                $success = false;
            }
        }
        
        if (!$success) {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur lors de l\'ajout des membres à l\'équipe']);
            exit;
        }
        
        // Retourner un succès
        echo json_encode([
            'success' => true,
            'message' => 'Équipe créée avec succès',
            'team_id' => $teamId
        ]);
    }
    
    /**
     * Met à jour une équipe existante
     */
    public function updateTeam() {
        // Vérifier que la requête est bien en POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
            exit;
        }
        
        // Vérifier le jeton CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            http_response_code(403);
            echo json_encode(['error' => 'Jeton CSRF invalide']);
            exit;
        }
        
        // Récupérer et valider les données
        $teamId = (int)($_POST['team_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $members = isset($_POST['members']) ? (array)$_POST['members'] : [];
        
        // Validation
        $errors = [];
        
        if (empty($teamId)) {
            $errors['team_id'] = 'ID d\'équipe invalide';
        }
        
        if (empty($name)) {
            $errors['name'] = 'Le nom de l\'équipe est requis';
        }
        
        if (count($members) > 3) {
            $errors['members'] = 'Une équipe ne peut pas avoir plus de 3 membres';
        }
        
        // Si des erreurs, retourner les erreurs
        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['errors' => $errors]);
            exit;
        }
        
        $teamModel = new TeamModel();
        
        // Vérifier que l'équipe existe
        $team = $teamModel->getById($teamId);
        if (!$team) {
            http_response_code(404);
            echo json_encode(['error' => 'Équipe non trouvée']);
            exit;
        }
        
        // Mettre à jour l'équipe
        $teamData = [
            'name' => $name,
            'description' => $description
        ];
        
        if (!$teamModel->update($teamId, $teamData)) {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur lors de la mise à jour de l\'équipe']);
            exit;
        }
        
        // Récupérer les membres actuels
        $currentMembers = $teamModel->getMembers($teamId);
        $currentMemberIds = array_column($currentMembers, 'id');
        
        // Déterminer les membres à ajouter et à supprimer
        $membersToAdd = array_diff($members, $currentMemberIds);
        $membersToRemove = array_diff($currentMemberIds, $members);
        
        // Supprimer les membres qui ne sont plus dans l'équipe
        foreach ($membersToRemove as $memberId) {
            $teamModel->removeMember($teamId, $memberId);
        }
        
        // Ajouter les nouveaux membres
        foreach ($membersToAdd as $memberId) {
            $teamModel->addMember($teamId, $memberId);
        }
        
        // Retourner un succès
        echo json_encode([
            'success' => true,
            'message' => 'Équipe mise à jour avec succès'
        ]);
    }
    
    /**
     * Supprime une équipe
     */
    public function deleteTeam() {
        // Vérifier que la requête est bien en POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
            exit;
        }
        
        // Vérifier le jeton CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            http_response_code(403);
            echo json_encode(['error' => 'Jeton CSRF invalide']);
            exit;
        }
        
        // Récupérer l'ID de l'équipe
        $teamId = (int)($_POST['team_id'] ?? 0);
        
        if (empty($teamId)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID d\'équipe invalide']);
            exit;
        }
        
        $teamModel = new TeamModel();
        
        // Vérifier que l'équipe existe
        $team = $teamModel->getById($teamId);
        if (!$team) {
            http_response_code(404);
            echo json_encode(['error' => 'Équipe non trouvée']);
            exit;
        }
        
        // Vérifier si l'équipe a des projets associés
        if ($team['has_project']) {
            http_response_code(400);
            echo json_encode(['error' => 'Impossible de supprimer une équipe assignée à un projet']);
            exit;
        }
        
        // Supprimer l'équipe
        if (!$teamModel->delete($teamId)) {
            http_response_code(500);
            echo json_encode(['error' => 'Erreur lors de la suppression de l\'équipe']);
            exit;
        }
        
        // Retourner un succès
        echo json_encode([
            'success' => true,
            'message' => 'Équipe supprimée avec succès'
        ]);
    }
    
    /**
     * Recherche des membres pour l'assignation aux équipes
     */
    public function searchMembers() {
        $searchTerm = $_GET['term'] ?? '';
        
        if (empty($searchTerm)) {
            echo json_encode([]);
            exit;
        }
        
        $memberModel = new MemberModel();
        $members = $memberModel->search($searchTerm, 'USER');
        
        $results = [];
        foreach ($members as $member) {
            $results[] = [
                'id' => $member['id'],
                'value' => $member['first_name'] . ' ' . $member['last_name'],
                'label' => $member['first_name'] . ' ' . $member['last_name'] . ' (' . $member['email'] . ')',
                'email' => $member['email'],
                'fonction' => $member['fonction']
            ];
        }
        
        echo json_encode($results);
    }

    public function adminClients() {
        $this->view('admin/dashboard', [
            'title' => 'Gestion des clients',
            'activeTab' => '/admin/clients',
            'content' => 'admin-clients',
            'user' => AuthHelper::getUser(),
            'tabs' => $this->tabs
        ]); 
    }

    public function adminNotify() {
        $this->view('admin/dashboard', [
            'title' => 'Gestion des notifications',
            'activeTab' => '/admin/notify',
            'content' => 'admin-notify',
            'user' => AuthHelper::getUser(),
            'tabs' => $this->tabs
        ]);
    }
}