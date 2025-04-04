<?php
/* require_once 'app/views/partials/header-admin.php'; */
// core/Controller.php
class Controller {
    protected function view(string $viewName, array $data = []) {
        // Extraction des variables
        /* extract($data); */
        
        // Inclusion du template principal
        require_once "app/views/layouts/base-admin.php";
    }
    protected function viewMember(string $viewName, array $data = []) {
        require_once "app/views/layouts/base-member.php";
    }
    
    protected function renderPartial(string $partialName, array $data = []) {
        /* extract($data); */
        require "app/views/{$partialName}.php";
    }

}