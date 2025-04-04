<?php
require_once __DIR__ . '/../../helpers/formatNumber.php';
?>
  <style>
    .date-group {
      display: flex;
      margin-bottom: 5px;
      align-items: baseline;
    }
    .date-group:last-child {
      margin-bottom: 0;
    }
    .date-label {
      width: 50px;
      font-weight: 500;
      color: #666;
    }
    .date-value {
      flex: 1;
    }
  </style>

  <!-- Main Content -->   
 
    <div style="padding-left: 50px; padding-right: 50px;" class="member-container">
      <div class="page-header">
      <h1>Consulter vos statistiques</h1>
      </div>
      
      <!-- Stats Cards -->
      <div 
      style="
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      margin-bottom: 25px;" 
      class="stats-cards">
        <div class="stat-card">
          <div class="stat-icon">
            <i class="fas fa-folder"></i>
          </div>
          <div class="stat-details">
            <h3>Projets assignés</h3>
            <p class="stat-number"><?php echo $data['projectsCount'] == 0 ? 'Aucun projet assigné' : formatNumber($data['projectsCount']); ?></p>
            
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon warning">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <div class="stat-details">
            <h3>Tâches en retard</h3>
            <?php echo $data['delayedTasksCount'] == 0 ? "<p class='stat-message' >Aucune tâche en retard</p>" : "<p class='stat-number'>" . formatNumber($data['delayedTasksCount']) . "</p>"; 
            
            ?>
           
          </div>
        </div>
        
        <div class="stat-card">
          <div class="stat-icon success">
            <i class="fas fa-check-circle"></i>
          </div>
          <div class="stat-details">
            <h3>Projets terminés</h3>
            <?php echo $data['completedProjectsCount'] == 0 ? "<p class='stat-message' >Aucun projet terminé</p>" : "<p class='stat-number'>" . formatNumber($data['completedProjectsCount']) . "</p>"; 
            
            ?>
           
          </div>
        </div>
      </div>
      
      <!-- Recent Projects Section -->
      <div class="recent-section">
        <h2>Projets récents</h2>
        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>Nom du projet</th>
                <th>Client</th>
                <th>Dates prévues</th>
                <th>Dates réelles</th>
                <th>Statut</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($data['recentProjects'])): ?>
                <tr>
                  <td colspan="5" class="no-data">Aucun projet récent</td>
                </tr>
              <?php else: ?>
                <?php foreach ($data['recentProjects'] as $project): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($project['name']); ?></td>
                    <td><?php echo htmlspecialchars($project['client_name']); ?></td>
                    <td>
                      <div class="date-group">
                        <div class="date-label">Début:</div> 
                        <div class="date-value"><?php echo DateTimeHelper::formatShortDate($project['scheduled_start_date']); ?></div>
                      </div>
                      <div class="date-group">
                        <div class="date-label">Fin:</div>
                        <div class="date-value"><?php echo DateTimeHelper::formatShortDate($project['scheduled_end_date']); ?></div>
                      </div>
                    </td>
                    <td>
                      <?php if ($project['status'] === 'Cancelled'): ?>
                        <div class="date-group"><div class="date-value">Projet annulé</div></div>
                      <?php else: ?>
                        <div class="date-group">
                          <div class="date-label">Début:</div>
                          <div class="date-value"><?php echo $project['actual_start_date'] ? DateTimeHelper::formatShortDate($project['actual_start_date']) : '—'; ?></div>
                        </div>
                        <div class="date-group">
                          <div class="date-label">Fin:</div>
                          <div class="date-value"><?php echo $project['actual_end_date'] ? DateTimeHelper::formatShortDate($project['actual_end_date']) : '—'; ?></div>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span class="badge <?php echo Constants::getProjectStatusClass($project['status']) ; ?>">
                        <?php echo $project['status']; ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Recent Tasks Section -->
      <div class="recent-section">
        <h2>Tâches récentes</h2>
        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>Nom de la tâche</th>
                <th>Projet</th>
                <th>Dates prévues</th>
                <th>Dates réelles</th>
                <th>Statut</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($data['recentTasks'])): ?>
                <tr>
                  <td colspan="5" class="no-data">Aucune tâche récente</td>
                </tr>
              <?php else: ?>
                <?php foreach ($data['recentTasks'] as $task): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($task['name']); ?></td>
                    <td><?php echo htmlspecialchars($task['project_name']); ?></td>
                    <td>
                      <div class="date-group">
                        <div class="date-label">Début:</div>
                        <div class="date-value"><?php echo DateTimeHelper::formatShortDate($task['scheduled_start_date']); ?></div>
                      </div>
                      <div class="date-group">
                        <div class="date-label">Fin:</div>
                        <div class="date-value"><?php echo DateTimeHelper::formatShortDate($task['scheduled_end_date']); ?></div>
                      </div>
                    </td>
                    <td>
                      <?php if ($task['status'] === 'Cancelled'): ?>
                        <div class="date-group"><div class="date-value">Tâche annulée</div></div>
                      <?php else: ?>
                        <div class="date-group">
                          <div class="date-label">Début:</div>
                          <div class="date-value"><?php echo $task['actual_start_date'] ? DateTimeHelper::formatShortDate($task['actual_start_date']) : '—'; ?></div>
                        </div>
                        <div class="date-group">
                          <div class="date-label">Fin:</div>
                          <div class="date-value"><?php echo $task['actual_end_date'] ? DateTimeHelper::formatShortDate($task['actual_end_date']) : '—'; ?></div>
                        </div>
                      <?php endif; ?>
                    </td>
                    <td>
                      <span class="badge <?php echo Constants::getTaskStatusClass($task["status"]); ?>">
                        <?php echo $task['status']; ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Teams Section -->
      <div class="recent-section">
        <h2>Mes équipes</h2>
        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>Nom de l'équipe</th>
                <th>Projet assigné</th>
                <th>Nombre de membres</th>
                <th>Nombre de tâches</th>
                <th>Tâches terminées</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($data['userTeams'])): ?>
                <tr>
                  <td colspan="5" class="no-data">Aucune équipe assignée</td>
                </tr>
              <?php else: ?>
                <?php foreach ($data['userTeams'] as $team): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($team['name']); ?></td>
                    <td><?php echo htmlspecialchars($team['project_name']); ?></td>
                    <td><?php echo $team['members_count'] == 0 ? 'Aucun membre' : formatNumber($team['members_count']); ?></td>
                    <td><?php echo $team['tasks_count'] == 0 ? 'Aucune tâche' : formatNumber($team['tasks_count']); ?></td>
                    <td><?php echo $team['completed_tasks_count'] == 0 ? 'Aucune tâche terminée' : $team['completed_tasks_count']; ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
 
</body>
</html>