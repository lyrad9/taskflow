<?php
require_once '../../../config/database.php';
require_once '../../models/MemberModel.php';
require_once '../../helpers/AuthHelper.php';
require_once '../../helpers/MailService.php';

// Vérifier que la requête est en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

// Vérifier que l'utilisateur est connecté et est admin
session_start();
if (!AuthHelper::isAdmin()) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Accès non autorisé']);
    exit;
}

// Vérifier le token CSRF
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => 'Token CSRF invalide']);
    exit;
}

// Récupérer et valider les données
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$fonction = trim($_POST['fonction'] ?? '');
$role = 'USER'; // Le rôle est toujours "USER" pour l'ajout via ce formulaire

// Validation des données
if (empty($first_name)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Le prénom est requis']);
    exit;
}

if (empty($last_name)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Le nom est requis']);
    exit;
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Adresse email invalide']);
    exit;
}

// Créer une instance du modèle
$memberModel = new MemberModel();

// Vérifier si l'email existe déjà
if ($memberModel->emailExists($email)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Cette adresse email est déjà utilisée']);
    exit;
}

// Générer un nom d'utilisateur et un mot de passe
$username = $memberModel->generateUniqueUsername();
$password = $memberModel->generatePassword();

// Préparer les données pour la création
//password_hash($password, PASSWORD_DEFAULT)
$data = [
    'username' => $username,
    'password' => $password,
    'email' => $email,
    'first_name' => $first_name,
    'last_name' => $last_name,
    'role' => $role,
    'fonction' => $fonction
];

// Enregistrer le membre dans la base de données
$memberId = $memberModel->create($data);

if ($memberId) {
    // Envoi des identifiants par email
    $emailService = new EmailService();
    $result = $emailService->sendCredentialsMail($email, $first_name, $last_name, $username, $password);
    
    // Réponse de succès
    echo json_encode([
        'success' => true,
        'message' => 'Membre ajouté avec succès',
        'member_id' => $memberId,
        'email_sent' => $result
    ]);
} else {
    // Échec de la création
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Erreur lors de la création du membre']);
}
