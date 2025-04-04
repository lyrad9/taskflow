<?php
session_start();
require_once "../../helpers/Constants.php";
require_once "../../models/ProjectModel.php";
require_once "../../../config/database.php";

      // Vérifier le jeton CSRF
      if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
          http_response_code(403);
          echo json_encode(['error' => 'Token CSRF invalide']);
          return;
      }
   
      if(!isset($_POST["userRole"]) || !isset($_POST["projectId"])){
        http_response_code(400);
        echo json_encode(['error' => 'Erreur lors de la suppression du projet']);
      }
      if($_POST["userRole"] !== Constants::USER_ROLES["SUPER_ADMIN"]){
        http_response_code(403);
        echo json_encode(['error' => "Vous n'avez pas les permissions nécessaires pour supprimer ce projet"]);
        return;
      }
      $projectModel = new ProjectModel();
      
      // Tenter de supprimer le projet
      if ($projectModel->delete($_POST["projectId"])) {
        http_response_code(201);
          echo json_encode(['success' => 'Projet supprimé avec succès']);
      } else {
          http_response_code(500);
          echo json_encode(['error' => 'Erreur lors de la suppression du projet']);
      }
  
  