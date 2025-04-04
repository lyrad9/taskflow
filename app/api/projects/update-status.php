<?php
/* session_start(); */
require_once "../../helpers/Constants.php";
require_once "../../models/ProjectModel.php";
require_once "../../../config/database.php";
require_once '../../helpers/AuthHelper.php';

AuthHelper::requireAdmin();

  // Vérifier le jeton CSRF
 /*  if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['error' => 'Token CSRF invalide']);
    return;
} */

// Vérifier que les données nécessaires sont présentes
if (!isset($_POST['project_ids']) || !isset($_POST['status'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes']);
    return;
}

$projectIds = $_POST['project_ids'];
$status = $_POST['status'];
var_dump("status",$status);
// Vérifier que le statut est valide
if (!array_key_exists($status, Constants::PROJECT_STATUS)) {
    http_response_code(400);
    echo json_encode(['error' => 'Statut invalide']);
    return;
}

$projectModel = new ProjectModel();
$success = true;

// Mettre à jour le statut de chaque projet
foreach ($projectIds as $id) {
    if (!$projectModel->updateStatus($id, Constants::PROJECT_STATUS[$status])) {
        $success = false;
    }
}

if ($success === true) {
    echo json_encode(['success' => 'Statut mis à jour avec succès']);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Erreur lors de la mise à jour du statut']);
}