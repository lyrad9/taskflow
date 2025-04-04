<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $data['title']; ?></title>
  <?php echo $data['css']; ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse" style="min-height: 100vh;">
        <div class="position-sticky pt-3">
          <div class="text-center mb-4">
            <h5>Gestion de Projets</h5>
            <p>Espace Membre</p>
          </div>
          <ul class="nav flex-column">
            <li class="nav-item">
              <a class="nav-link active" href="/member">
                <i class="fas fa-chart-line"></i> Tableau de bord
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/member/projects">
                <i class="fas fa-folder"></i> Mes projets
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/member/tasks">
                <i class="fas fa-tasks"></i> Mes tâches
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/profil">
                <i class="fas fa-user"></i> Mon profil
              </a>
            </li>
            <li class="nav-item mt-5">
              <a class="nav-link text-danger" href="/auth/logout">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
              </a>
            </li>
          </ul>
        </div>
      </div>

      <!-- Main content -->
      <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">Tableau de bord</h1>
          <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
              <span class="text-muted">Bienvenue, <?php echo isset($_SESSION['user_username']) ? htmlspecialchars($_SESSION['user_username']) : 'Utilisateur'; ?></span>
            </div>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-cards">
          <!-- Projets assignés -->
          <div class="stat-card">
            <div class="stat-icon">
              <i class="fas fa-folder"></i>
            </div>
            <div class="stat-details">
              <h3>Projets assignés</h3>
              <?php if ($data['projectsCount'] > 0): ?>
                <p class="stat-number"><?php echo $data['projectsCount']; ?></p>
              <?php else: ?>
                <p class="stat-number text-muted">Aucun projet assigné</p>
              <?php endif; ?>
            </div>
          </div>

          <!-- Tâches en retard -->
          <div class="stat-card">
            <div class="stat-icon warning">
              <i class="fas fa-clock"></i>
            </div>
            <div class="stat-details">
              <h3>Tâches en retard</h3>
              <?php if ($data['delayedTasksCount'] > 0): ?>
                <p class="stat-number"><?php echo $data['delayedTasksCount']; ?></p>
              <?php else: ?>
                <p class="stat-number text-muted">Aucune tâche en retard</p>
              <?php endif; ?>
            </div>
          </div>

          <!-- Projets réalisés -->
          <div class="stat-card">
            <div class="stat-icon success">
              <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-details">
              <h3>Projets réalisés</h3>
              <?php if ($data['completedProjectsCount'] > 0): ?>
                <p class="stat-number"><?php echo $data['completedProjectsCount']; ?></p>
              <?php else: ?>
                <p class="stat-number text-muted">Aucun projet réalisé</p>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- Projets Récents -->
        <div class="recent-section">
          <h2><i class="fas fa-list-alt"></i> Projets récents</h2>
          <?php if (!empty($data['recentProjects'])): ?>
            <div class="table-responsive">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Nom du projet</th>
                    <th>Dates prévues</th>
                    <th>Dates réelles</th>
                    <th>Statut</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($data['recentProjects'] as $project): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($project['name']); ?></td>
                      <td>
                        <div>Début: <?php echo date('d/m/Y', strtotime($project['scheduled_start_date'])); ?></div>
                        <div>Fin: <?php echo date('d/m/Y', strtotime($project['scheduled_end_date'])); ?></div>
                      </td>
                      <td>
                        <?php if ($project['status'] === 'Cancelled'): ?>
                          <div>Aucune date</div>
                        <?php else: ?>
                          <div>Début: <?php echo $project['actual_start_date'] ? date('d/m/Y', strtotime($project['actual_start_date'])) : 'Pas encore de date définie'; ?></div>
                          <div>Fin: <?php echo $project['actual_end_date'] ? date('d/m/Y', strtotime($project['actual_end_date'])) : 'Pas encore de date définie'; ?></div>
                        <?php endif; ?>
                      </td>
                      <td><span class="badge <?php echo Constants::getProjectStatusClass($project['status']); ?>"><?php echo $project['status']; ?></span></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p class="text-muted">Aucun projet récemment</p>
          <?php endif; ?>
        </div>

        <!-- Tâches Récentes -->
        <div class="recent-section">
          <h2><i class="fas fa-tasks"></i> Tâches récentes</h2>
          <?php if (!empty($data['recentTasks'])): ?>
            <div class="table-responsive">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Tâche</th>
                    <th>Projet</th>
                    <th>Dates prévues</th>
                    <th>Statut</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($data['recentTasks'] as $task): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($task['name']); ?></td>
                      <td><?php echo htmlspecialchars($task['project_name']); ?></td>
                      <td>
                        <div>Début: <?php echo date('d/m/Y', strtotime($task['scheduled_start_date'])); ?></div>
                        <div>Fin: <?php echo date('d/m/Y', strtotime($task['scheduled_end_date'])); ?></div>
                      </td>
                      <td><span class="badge <?php echo Constants::getTaskStatusClass($task['status']); ?>"><?php echo $task['status']; ?></span></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p class="text-muted">Aucune tâche récente</p>
          <?php endif; ?>
        </div>

        <!-- Équipes -->
        <div class="recent-section">
          <h2><i class="fas fa-users"></i> Vos équipes</h2>
          <?php if (!empty($data['userTeams'])): ?>
            <div class="table-responsive">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Équipe</th>
                    <th>Projet</th>
                    <th>Tâches</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($data['userTeams'] as $team): ?>
                    <tr>
                      <td>
                        <div><?php echo htmlspecialchars($team['name']); ?></div>
                        <div class="text-muted"><?php echo $team['members_count']; ?> membres</div>
                      </td>
                      <td>
                        <?php if ($team['project_name']): ?>
                          <div><?php echo htmlspecialchars($team['project_name']); ?></div>
                          <div><span class="badge <?php echo Constants::getProjectStatusClass($team['project_status']); ?>"><?php echo $team['project_status']; ?></span></div>
                        <?php else: ?>
                          <div class="text-muted">Aucun projet assigné</div>
                        <?php endif; ?>
                      </td>
                      <td>
                        <div>Total: <?php echo $team['tasks_count']; ?></div>
                        <div>Réalisées: <?php echo $team['completed_tasks_count']; ?></div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p class="text-muted">Vous n'appartenez à aucune équipe</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <?php echo $data['js']; ?>
  <?php echo $data['jquery']; ?>
</body>
</html> 