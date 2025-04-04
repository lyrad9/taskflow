<?php

require_once __DIR__ . '/../models/UserModel.php';
/**
 * Helper pour gérer l'authentification des utilisateurs
 */
class AuthHelper {
    /**
     * Démarre une session si elle n'est pas déjà active
     */
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
     /**
     * Concatène le nom et le prénom d'un utilisateur
     * 
     * @param array $user Les données de l'utilisateur
     * @return string Le nom complet de l'utilisateur
     */
    public static function getFullName($first_name, $last_name) {
        if (empty($last_name)) {
            return htmlspecialchars(ucfirst($first_name));
        }
        return htmlspecialchars(ucfirst($first_name) . ' ' . ucfirst($last_name));
    }

    
    /**
     * Connecte un utilisateur en sauvegardant ses infos en session
     * 
     * @param array $user Données de l'utilisateur
     * @return void
     */
    public static function login($user) {
        self::init();
        $_SESSION['user'] = [
          'id' => $user['id'],
          'username' => $user['username'],
          'email' => $user['email'],
          'firstname' => $user['first_name'],
          'lastname' => $user['last_name'],
          'profile_picture' => $user['profile_picture'],
          'role' => $user['role'],
          
      ];
      
    }
    
    /**
     * Déconnecte l'utilisateur en détruisant sa session
     * 
     * @return void
     */
    public static function logout() {
        self::init();
        session_unset();
        session_destroy();
    }
    
    /**
     * Vérifie si un utilisateur est connecté
     * 
     * @return boolean
     */
    public static function isLoggedIn() {
      self::init();
      return isset($_SESSION['user']);
    }
    
    /**
     * Récupère l'ID de l'utilisateur connecté
     * 
     * @return int|null
     */
    public static function getUser() {
      self::init();
      //$userModel = new UserModel();
      
      if (isset($_SESSION['user'])) {
      // Récupérer l'utilisateur de la base de données          
      return $_SESSION["user"] ?? null;
      /* return $userModel->findById($_SESSION["user"]['id']) ?? null; */
      }
      return null;
      
    }
    
    /**
     * Récupère le rôle de l'utilisateur connecté
     * 
     * @return string|null
     */
    public static function getUserRole() {
      self::init();
      $user = self::getUser();
      if (isset($user)) {
        return $user['role'];
      }
      return null;
      /*   self::init();
        return isset($_SESSION["user"]["role"]) ? $_SESSION["user"]["role"] : null; */
    }
    
    /**
     * Vérifie si l'utilisateur connecté a un rôle spécifique
     * 
     * @param string $role Le rôle à vérifier
     * @return boolean
     */
    public static function hasRole($role) {
        self::init();
        return self::getUserRole() === $role;
       /*  return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === $role; */
    }
    
    /**
     * Vérifie si l'utilisateur est un administrateur
     * 
     * @return boolean
     */
    public static function isAdmin() {
        return self::hasRole('ADMIN') || self::hasRole('SUPER_ADMIN');
    }
    
    /**
     * Redirige l'utilisateur si conditions remplies
     * 
     * @param string $url URL de redirection
     * @return void
     */
    public static function redirect($url) {
         // Assurez-vous qu'aucun output n'a été envoyé
         if (headers_sent()) {
            die('Redirection impossible : headers déjà envoyés.');
        }
        header("Location: $url");
        exit;
    }

    /**
     * Détruire la session
     */
    public static function destroy() {
        self::init();
        session_unset();
        session_destroy();
    }
    
    /**
     * Protège une page pour les utilisateurs non connectés
     * 
     * @param string $redirect URL de redirection si non connecté
     * @return void
     */
    public static function requireLogin($redirect = '/auth/login') {
        if (!self::isLoggedIn()) {
            self::redirect($redirect);
        }
    }
    
    /**
     * Protège une page pour les utilisateurs sans le rôle admin
     * 
     * @param string $redirect URL de redirection si non admin
     * @return void
     */
    public static function requireAdmin($redirect = '/auth/login') {
        self::requireLogin();
        if (!self::isAdmin()) {
            self::redirect($redirect);
        }
    }
} 