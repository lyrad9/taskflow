<?php

require_once 'app/core/Router.php';

// Initialisation du routeur
$router = new Router();

// Définition des routes
$router->addRoute('auth/login', 'AuthController@login');

$router->addRoute('auth/logout', 'AuthController@logout');
$router->addRoute('', 'AuthController@login');
$router->addRoute('/auth/reset-password', 'AuthController@restPasswotrd');
$router->addRoute('/profil', 'ProfilController@profil');
$router->addRoute('member/dashboard', 'MemberController@memberDashboard');
$router->addRoute('member/projects', 'MemberController@memberProjects');
$router->addRoute('member/tasks', 'MemberController@memberTasks');
$router->addRoute('member/notify', 'MemberController@memberNotifiy');

$router->addRoute('admin/dashboard', 'AdminController@adminDashboard');

$router->addRoute('admin/projects', 'AdminController@adminProjects');
$router->addRoute('admin/projects/add', 'AdminController@addProject');
$router->addRoute('admin/tasks', 'AdminController@adminTasks');
$router->addRoute('admin/project/{id}', 'AdminController@adminProjectDetails');
$router->addRoute("admin/teams", "AdminController@adminTeams");
$router->addRoute('admin/members', 'AdminController@adminMembers');
$router->addRoute('admin/clients', 'AdminController@adminClients');
$router->addRoute('admin/notify', 'AdminController@adminNotify');
// Récupération de l'URL actuelle

/* $url = $_SERVER['REQUEST_URI']; */

// Dispatch de la requête
$router->dispatch(); 