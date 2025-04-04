<?php
require_once 'config/database.php';
require_once 'app/helpers/AuthHelper.php';
require_once 'config/globals.php';
require_once 'app/core/Controller.php';
require_once 'app/models/ProjectModel.php';
require_once 'app/models/TeamModel.php';
require_once 'app/models/TaskModel.php';
require_once 'app/helpers/DateTimeHelper.php';
require_once 'app/helpers/CurrencyHelper.php';
require_once 'app/helpers/Constants.php';

class MemberController extends Controller {
    private $projectModel;
    private $taskModel;
    private $teamModel;

    public $tabs = [
        '/member/dashboard' => ['label' => 'Dashboard', 'icon' => 'fas fa-chart-line'],
        '/member/projects' => ['label' => 'Projets', 'icon' => 'fas fa-folder'],
        '/member/tasks' => ['label' => 'Tâches', 'icon' => 'fas fa-tasks'],     
        '/member/notify' => ['label' => 'Notifications', 'icon' => 'fas fa-bell'],
        

    ];
  
    
    public function __construct() {
        // Vérifier que l'utilisateur est connecté
        AuthHelper::requireLogin();
        
        // Initialiser les modèles
        $this->projectModel = new ProjectModel();
        $this->taskModel = new TaskModel();
        $this->teamModel = new TeamModel();
        
        // Générer un jeton CSRF si nécessaire
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
    
    public function memberDashboard() {
        
        $user = AuthHelper::getUser();
        if(!isset($user)) {
            AuthHelper::redirect('/auth/login');
        }
        $userId = $user['id'];
        
        // Récupérer les statistiques
        $stats = [
            'projectsCount' => $this->projectModel->getAssignedProjectsCount($userId),
            'delayedTasksCount' => $this->taskModel->getDelayedTasksCount($userId),
            'completedProjectsCount' => $this->projectModel->getCompletedProjectsCount($userId)
        ];
        
        // Récupérer les données récentes
        $recentData = [
            'recentProjects' => $this->projectModel->getRecentProjects($userId, 5),
            'recentTasks' => $this->taskModel->getRecentTasks($userId, 5),
            'userTeams' => $this->teamModel->getUserTeamsWithDetails($userId)
        ];
        
        // Préparer les données pour la vue
        $viewData = array_merge($stats, $recentData, [
            'title' => 'Tableau de bord Member',
            'activeTab' => '/member/dashboard',
            'content' => 'member-dashboard',
            'user' => AuthHelper::getUser(),
            'tabs' => $this->tabs
        ]);
        
        $this->viewMember('admin/dashboard', $viewData);
    }
    
    public function memberProjects() {
        $user = AuthHelper::getUser();
        if(!isset($user)) {
            AuthHelper::redirect('/auth/login');
        }
        $userId = $user['id'];
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        // Récupérer les filtres depuis l'URL
        $filters = ['user_id' => $userId];
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }
        if (isset($_GET['status']) && $_GET['status'] !== 'all') {
            $filters['status'] = $_GET['status'];
        }
        
        // Récupérer les projets du membre avec pagination
        $projects = $this->projectModel->getMemberProjectsWithFilters($filters, $limit, $offset);
        $totalProjects = $this->projectModel->countMemberProjectsWithFilters($filters);
        $totalPages = ceil($totalProjects / $limit);
        
        $this->viewMember('member/member-projects', [
            'title' => 'Mes Projets',
            'activeTab' => '/member/projects',
            'content' => 'member-projects',
            'user' => AuthHelper::getUser(),
            'tabs' => $this->tabs,
            'projects' => $projects,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalProjects' => $totalProjects
        ]);
    }
    
    public function memberTasks() {
        $this->viewMember('member/tasks', [
            'title' => 'Mes Tâches',
            'activeTab' => '/member/tasks',
            'content' => 'member-tasks',
            'user' => AuthHelper::getUser(),
            'tabs' => $this->tabs
        ]);
    }
    
    public function memberNotify() {
        $this->viewMember('member/notifications', [
            'title' => 'Mes Notifications',
            'activeTab' => '/member/notify',
            'content' => 'member-notify',
            'user' => AuthHelper::getUser(),
            'tabs' => $this->tabs
        ]);
    }
}