<?php
require_once 'config/globals.php';
require_once 'config/database.php';
require_once 'app/models/UserModel.php';
require_once 'app/helpers/AuthHelper.php';

class AuthController {
 
 
  private $userModel;
  
  public function __construct() {
    $database = new Database();
    $db = $database->getConnection();
    $this->userModel = new UserModel($db);
  }
  
  public function login() {
    // Vérifier si l'utilisateur est déjà connecté
    if (AuthHelper::isLoggedIn()) {
      if (AuthHelper::isAdmin()) {
        AuthHelper::redirect('/admin/dashboard');
      } else {
        AuthHelper::redirect('/member/dashboard');
      }
    }

    // Initialisation des variables
    $error = '';
    
    // Traitement du formulaire de connexion
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = htmlspecialchars($_POST['username'] ?? '');
      $password = $_POST['password'] ?? '';
      
      // Validation des données
      if (empty($username)) {
        $_SESSION['login_error'] = 'Le nom d\'utilisateur est requis';
        AuthHelper::redirect('/auth/login');
        exit;
      } elseif (empty($password)) {
        $_SESSION['login_error'] = 'Le mot de passe est requis';
        AuthHelper::redirect('/auth/login');
        exit;
      }
      
      // Authentification de l'utilisateur
      $user = $this->userModel->authenticate($username, $password);
      
      if (isset($user)) {
        // Connexion réussie
        AuthHelper::login($user);
        
        // Supprimer toute erreur précédente
        if (isset($_SESSION['login_error'])) {
          unset($_SESSION['login_error']);
        }
        
        // Redirection selon le rôle
        if (AuthHelper::isAdmin()) {
          AuthHelper::redirect('/admin/dashboard');
        } else {
          AuthHelper::redirect('/member/dashboard');
        }
      } else {
        // Échec de connexion
        $_SESSION['login_error'] = 'Nom d\'utilisateur ou mot de passe incorrect';
        AuthHelper::redirect('/auth/login');
        exit;
      }
    }
    
    // Récupérer l'erreur de session s'il y en a une
    if (isset($_SESSION['login_error'])) {
      $error = $_SESSION['login_error'];
      // On supprime l'erreur après l'avoir récupérée pour éviter qu'elle ne s'affiche après un rafraîchissement
      unset($_SESSION['login_error']);
    }
    
    $data = [
      'title' => 'Connexion',   
     
      'error' => $error
    ];
    
    require_once 'app/views/login1.php';
  }
  
  public function logout() {    
    AuthHelper::logout();
    AuthHelper::redirect('/auth/login');
    exit;
  }
} 
?>