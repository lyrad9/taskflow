<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Détails du projet - TASKFLOW</title>
  
  <!-- Custom fonts -->
  <link href="/public/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  
  <!-- Custom styles -->
  <link href="/public/assets/css/details-projects.admin.css" rel="stylesheet">
</head>
<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <?php include 'includes/sidebar.php'; ?>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <?php include 'includes/topbar.php'; ?>

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <?php if (isset($data['project']) && !empty($data['project'])): ?>
          <?php $project = $data['project']; ?>
          <!-- Project Header -->
          <div class="project-header">
            <div class="project-title">
              <div class="d-flex align-items-center">
                <a href="/admin/projects" class="btn btn-circle btn-sm btn-light mr-3">
                  <i class="fas fa-arrow-left"></i>
                </a>
                <h1><?php echo htmlspecialchars($project['name']); ?></h1>
                <span class="project-badge badge <?php echo Constants::getProjectStatusClass($project['status']); ?>">
                  <?php echo htmlspecialchars($project['status']); ?>
                </span>
              </div>
              <div>
                <button type="button" class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#editProjectModal">
                  <i class="fas fa-edit fa-sm"></i> Modifier
                </button>
                <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteProjectModal">
                  <i class="fas fa-trash fa-sm"></i> Supprimer
                </button>
              </div>
            </div>

            <!-- Client Info -->
            <div class="client-info">
              <div class="client-avatar">
                <i class="fas fa-user-tie"></i>
              </div>
              <div class="client-details">
                <h3>Client: <?php echo htmlspecialchars($project['client_first_name'] . ' ' . $project['client_last_name']); ?></h3>
                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($project['client_city']); ?> - <?php echo htmlspecialchars($project['client_residence'] ?? 'N/A'); ?></p>
                <div class="contact-info">
                  <a href="tel:<?php echo htmlspecialchars($project['client_phone']); ?>">
                    <i class="fas fa-phone"></i> <?php echo htmlspecialchars($project['client_phone']); ?>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Project Tabs -->
          <div class="project-tabs">
            <ul class="nav nav-tabs" id="projectTabs" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab">Détails</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="tasks-tab" data-toggle="tab" href="#tasks" role="tab">Tâches</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="team-tab" data-toggle="tab" href="#team" role="tab">Équipe</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="documents-tab" data-toggle="tab" href="#documents" role="tab">Documents</a>
              </li>
            </ul>
            <div class="tab-content" id="projectTabsContent">
              <!-- Details Tab -->
              <div class="tab-pane fade show active" id="details" role="tabpanel">
                <div class="project-description">
                  <h3>Description</h3>
                  <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                </div>
                
                <div class="project-info">
                  <div class="info-item">
                    <div class="info-label">Type de projet</div>
                    <div class="info-value"><?php echo htmlspecialchars(Constants::PROJECT_TYPES[$project['project_type']] ?? $project['project_type']); ?></div>
                  </div>
                  <div class="info-item">
                    <div class="info-label">Budget</div>
                    <div class="info-value"><?php echo CurrencyHelper::formatAmount($project['budget']); ?></div>
                  </div>
                  <div class="info-item">
                    <div class="info-label">Date de début prévue</div>
                    <div class="info-value"><?php echo DateTimeHelper::formatShortDate($project['scheduled_start_date']); ?></div>
                  </div>
                  <div class="info-item">
                    <div class="info-label">Date de fin prévue</div>
                    <div class="info-value"><?php echo DateTimeHelper::formatShortDate($project['scheduled_end_date']); ?></div>
                  </div>
                  <div class="info-item">
                    <div class="info-label">Date de début réelle</div>
                    <div class="info-value"><?php echo !empty($project['actual_start_date']) ? DateTimeHelper::formatShortDate($project['actual_start_date']) : 'Non démarrée'; ?></div>
                  </div>
                  <div class="info-item">
                    <div class="info-label">Date de fin réelle</div>
                    <div class="info-value"><?php echo !empty($project['actual_end_date']) ? DateTimeHelper::formatShortDate($project['actual_end_date']) : 'Non terminée'; ?></div>
                  </div>
                  <div class="info-item">
                    <div class="info-label">Équipe</div>
                    <div class="info-value"><?php echo htmlspecialchars($project['team_name'] ?? 'Non assignée'); ?></div>
                  </div>
                  <div class="info-item">
                    <div class="info-label">Date de création</div>
                    <div class="info-value"><?php echo DateTimeHelper::formatDateTime($project['created_at']); ?></div>
                  </div>
                  <div class="info-item">
                    <div class="info-label">Dernière mise à jour</div>
                    <div class="info-value"><?php echo DateTimeHelper::formatDateTime($project['updated_at']); ?></div>
                  </div>
                </div>
              </div>
              
              <!-- Tasks Tab -->
              <div class="tab-pane fade" id="tasks" role="tabpanel">
                <div class="project-tasks">
                  <h3>
                    Tâches associées
                    <a href="/admin/tasks/add?project_id=<?php echo $project['id']; ?>" class="btn btn-primary">
                      <i class="fas fa-plus fa-sm"></i> Ajouter une tâche
                    </a>
                  </h3>
                  
                  <?php if (isset($data['tasks']) && !empty($data['tasks'])): ?>
                  <div class="table-responsive">
                    <table class="task-table">
                      <thead>
                        <tr>
                          <th>Tâche</th>
                          <th>Priorité</th>
                          <th>Statut</th>
                          <th>Assignée à</th>
                          <th>Date de début</th>
                          <th>Date de fin</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($data['tasks'] as $task): ?>
                        <tr>
                          <td>
                            <a href="/admin/tasks/<?php echo $task['id']; ?>" class="font-weight-bold">
                              <?php echo htmlspecialchars($task['name']); ?>
                            </a>
                            <div class="small text-truncate" style="max-width: 300px;">
                              <?php echo htmlspecialchars($task['description']); ?>
                            </div>
                          </td>
                          <td>
                            <span class="task-priority <?php echo htmlspecialchars($task['priority']); ?>"></span>
                            <?php echo ucfirst(htmlspecialchars($task['priority'])); ?>
                          </td>
                          <td>
                            <span class="badge <?php echo Constants::getTaskStatusClass($task['status']); ?>">
                              <?php echo htmlspecialchars($task['status']); ?>
                            </span>
                          </td>
                          <td>
                            <?php if (!empty($task['assigned_to_name'])): ?>
                            <div class="d-flex align-items-center">
                              <img src="<?php echo !empty($task['assigned_to_image']) ? htmlspecialchars($task['assigned_to_image']) : '/public/static/user_placeholder.jpg'; ?>" alt="User" class="rounded-circle mr-2" style="width: 30px; height: 30px; object-fit: cover;">
                              <?php echo htmlspecialchars($task['assigned_to_name']); ?>
                            </div>
                            <?php else: ?>
                            <span class="text-muted">Non assignée</span>
                            <?php endif; ?>
                          </td>
                          <td><?php echo DateTimeHelper::formatShortDate($task['scheduled_start_date']); ?></td>
                          <td><?php echo DateTimeHelper::formatShortDate($task['scheduled_end_date']); ?></td>
                          <td>
                            <div class="btn-group">
                              <a href="/admin/tasks/<?php echo $task['id']; ?>" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                              </a>
                              <a href="/admin/tasks/edit/<?php echo $task['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                              </a>
                              <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteTaskModal" data-task-id="<?php echo $task['id']; ?>">
                                <i class="fas fa-trash"></i>
                              </button>
                            </div>
                          </td>
                        </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                  <?php else: ?>
                  <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Aucune tâche n'a encore été ajoutée à ce projet.
                  </div>
                  <?php endif; ?>
                </div>
              </div>
              
              <!-- Team Tab -->
              <div class="tab-pane fade" id="team" role="tabpanel">
                <div class="project-team">
                  <h3>Équipe assignée: <?php echo htmlspecialchars($project['team_name'] ?? 'Non assignée'); ?></h3>
                  <p><?php echo htmlspecialchars($project['team_description'] ?? ''); ?></p>
                  
                  <?php if (isset($data['team_members']) && !empty($data['team_members'])): ?>
                  <div class="team-members">
                    <?php foreach ($data['team_members'] as $member): ?>
                    <div class="team-member">
                      <div class="team-member-avatar">
                        <?php if (!empty($member['profile_picture'])): ?>
                        <img src="<?php echo htmlspecialchars($member['profile_picture']); ?>" alt="<?php echo htmlspecialchars($member['firstname'] . ' ' . $member['lastname']); ?>">
                        <?php else: ?>
                        <i class="fas fa-user"></i>
                        <?php endif; ?>
                      </div>
                      <div class="team-member-name"><?php echo htmlspecialchars($member['firstname'] . ' ' . $member['lastname']); ?></div>
                      <div class="team-member-role"><?php echo htmlspecialchars($member['skill'] ?? 'Membre'); ?></div>
                      <div class="team-member-contact">
                        <a href="mailto:<?php echo htmlspecialchars($member['email']); ?>" title="Email">
                          <i class="fas fa-envelope"></i>
                        </a>
                      </div>
                    </div>
                    <?php endforeach; ?>
                  </div>
                  <?php else: ?>
                  <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Aucun membre dans l'équipe ou aucune équipe assignée.
                  </div>
                  <?php endif; ?>
                </div>
              </div>
              
              <!-- Documents Tab -->
              <div class="tab-pane fade" id="documents" role="tabpanel">
                <div class="project-documents">
                  <h3>Documents du projet</h3>
                  
                  <?php 
                  $documents = [];
                  if (!empty($project['documents'])) {
                      $documents = json_decode($project['documents'], true);
                  }
                  ?>
                  
                  <?php if (!empty($documents)): ?>
                  <div class="document-list">
                    <?php foreach ($documents as $document): ?>
                    <div class="document-item">
                      <?php 
                      $extension = FileUploadHelper::getFileExtension($document['filename']);
                      $iconClass = 'fa-file';
                      
                      // Set appropriate icon based on file extension
                      if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                          $iconClass = 'fa-file-image';
                      } elseif ($extension === 'pdf') {
                          $iconClass = 'fa-file-pdf';
                      } elseif (in_array($extension, ['doc', 'docx'])) {
                          $iconClass = 'fa-file-word';
                      } elseif (in_array($extension, ['xls', 'xlsx'])) {
                          $iconClass = 'fa-file-excel';
                      }
                      ?>
                      <div class="document-icon">
                        <i class="fas <?php echo $iconClass; ?>"></i>
                      </div>
                      <div class="document-name"><?php echo htmlspecialchars($document['original_name']); ?></div>
                      <div class="document-size"><?php echo FileUploadHelper::formatFileSize($document['filesize']); ?></div>
                      <div class="document-actions">
                        <a href="<?php echo htmlspecialchars($document['filepath']); ?>" target="_blank" download>
                          <i class="fas fa-download"></i> Télécharger
                        </a>
                      </div>
                    </div>
                    <?php endforeach; ?>
                  </div>
                  <?php else: ?>
                  <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Aucun document n'a été ajouté à ce projet.
                  </div>
                  <?php endif; ?>
                  
                  <!-- Upload Form -->
                  <div class="mt-4">
                    <form action="/admin/projects/<?php echo $project['id']; ?>/upload" method="post" enctype="multipart/form-data">
                      <div class="card shadow">
                        <div class="card-header">
                          <h6 class="m-0 font-weight-bold text-primary">Ajouter des documents</h6>
                        </div>
                        <div class="card-body">
                          <div class="file-upload">
                            <input type="file" id="projectDocuments" name="documents[]" class="file-upload-input" multiple>
                            <label for="projectDocuments" class="file-upload-label">
                              <span id="fileUploadText">Choisir des fichiers</span>
                            </label>
                            <div class="file-upload-info">
                              Formats acceptés: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG (max. 10 Mo)
                            </div>
                          </div>
                          <div class="uploaded-files" id="uploadedFiles">
                            <!-- Preview of uploaded files will appear here -->
                          </div>
                          <button type="submit" class="btn btn-primary mt-3">
                            <i class="fas fa-upload"></i> Téléverser les documents
                          </button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php else: ?>
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> Projet non trouvé ou inaccessible.
          </div>
          <?php endif; ?>

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; TASKFLOW 2023</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Edit Project Modal-->
  <div class="modal fade" id="editProjectModal" tabindex="-1" role="dialog" aria-labelledby="editProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editProjectModalLabel">Modifier le projet</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <form action="/admin/projects/edit/<?php echo $project['id'] ?? ''; ?>" method="post">
          <div class="modal-body">
            <!-- Form content would be here -->
            <div class="form-group">
              <label for="name" class="form-label required">Nom du projet</label>
              <input type="text" class="form-control" id="name" name="name" required 
                value="<?php echo htmlspecialchars($project['name'] ?? ''); ?>">
            </div>
            <div class="form-group">
              <label for="description" class="form-label required">Description</label>
              <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($project['description'] ?? ''); ?></textarea>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="budget" class="form-label required">Budget (FCFA)</label>
                  <input type="number" class="form-control" id="budget" name="budget" required
                    value="<?php echo htmlspecialchars($project['budget'] ?? ''); ?>">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="status" class="form-label required">Statut</label>
                  <select class="form-select" id="status" name="status" required>
                    <?php foreach (Constants::PROJECT_STATUS as $key => $value): ?>
                    <option value="<?php echo htmlspecialchars($value); ?>" <?php echo ($project['status'] ?? '') === $value ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($value); ?>
                    </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="scheduled_start_date" class="form-label required">Date de début prévue</label>
                  <input type="date" class="form-control" id="scheduled_start_date" name="scheduled_start_date" required
                    value="<?php echo htmlspecialchars($project['scheduled_start_date'] ?? ''); ?>">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="scheduled_end_date" class="form-label required">Date de fin prévue</label>
                  <input type="date" class="form-control" id="scheduled_end_date" name="scheduled_end_date" required
                    value="<?php echo htmlspecialchars($project['scheduled_end_date'] ?? ''); ?>">
                </div>
              </div>
            </div>
            <!-- More form fields would be here -->
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Delete Project Modal-->
  <div class="modal fade" id="deleteProjectModal" tabindex="-1" role="dialog" aria-labelledby="deleteProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteProjectModalLabel">Confirmez la suppression</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Êtes-vous sûr de vouloir supprimer ce projet ? Cette action est irréversible et supprimera toutes les tâches associées.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
          <form action="/admin/projects/delete/<?php echo $project['id'] ?? ''; ?>" method="post">
            <button type="submit" class="btn btn-danger">Supprimer</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Task Modal-->
  <div class="modal fade" id="deleteTaskModal" tabindex="-1" role="dialog" aria-labelledby="deleteTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteTaskModalLabel">Confirmez la suppression</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Êtes-vous sûr de vouloir supprimer cette tâche ? Cette action est irréversible.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Annuler</button>
          <form action="/admin/tasks/delete/" method="post" id="deleteTaskForm">
            <input type="hidden" name="task_id" id="deleteTaskId">
            <button type="submit" class="btn btn-danger">Supprimer</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="/public/assets/js/jquery.min.js"></script>
  <script src="/public/assets/js/bootstrap.bundle.min.js"></script>

  <!-- Core plugin JavaScript-->
  <script src="/public/assets/js/jquery.easing.min.js"></script>

  <!-- Custom scripts -->
  <script>
    // File upload preview
    document.getElementById('projectDocuments')?.addEventListener('change', function(e) {
      const fileUploadText = document.getElementById('fileUploadText');
      const uploadedFiles = document.getElementById('uploadedFiles');
      uploadedFiles.innerHTML = '';
      
      if (this.files.length > 0) {
        fileUploadText.textContent = `${this.files.length} fichier(s) sélectionné(s)`;
        
        for (let i = 0; i < this.files.length; i++) {
          const file = this.files[i];
          const fileSize = (file.size / 1024).toFixed(2);
          const fileExtension = file.name.split('.').pop().toLowerCase();
          
          let iconClass = 'fa-file';
          
          // Set appropriate icon based on file extension
          if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
            iconClass = 'fa-file-image';
          } else if (['pdf'].includes(fileExtension)) {
            iconClass = 'fa-file-pdf';
          } else if (['doc', 'docx'].includes(fileExtension)) {
            iconClass = 'fa-file-word';
          } else if (['xls', 'xlsx'].includes(fileExtension)) {
            iconClass = 'fa-file-excel';
          }
          
          const fileItem = document.createElement('div');
          fileItem.className = 'uploaded-file';
          fileItem.innerHTML = `
            <div class="uploaded-file-icon">
              <i class="fas ${iconClass}"></i>
            </div>
            <div class="uploaded-file-info">
              <div class="uploaded-file-name">${file.name}</div>
              <div class="uploaded-file-size">${fileSize} KB</div>
            </div>
          `;
          
          uploadedFiles.appendChild(fileItem);
        }
      } else {
        fileUploadText.textContent = 'Choisir des fichiers';
      }
    });
    
    // Delete task modal
    $('#deleteTaskModal').on('show.bs.modal', function (event) {
      const button = $(event.relatedTarget);
      const taskId = button.data('task-id');
      const form = document.getElementById('deleteTaskForm');
      document.getElementById('deleteTaskId').value = taskId;
      form.action = `/admin/tasks/delete/${taskId}`;
    });
    
    // Toggle sidebar
    document.getElementById("sidebarToggle").addEventListener("click", function(e) {
      e.preventDefault();
      document.body.classList.toggle("sidebar-toggled");
      document.querySelector(".sidebar").classList.toggle("toggled");
    });
    
    // Scroll to top button appear
    document.addEventListener("scroll", function() {
      var scrollToTop = document.querySelector(".scroll-to-top");
      if (document.documentElement.scrollTop > 100) {
        scrollToTop.style.display = "block";
      } else {
        scrollToTop.style.display = "none";
      }
    });
  </script>
</body>
</html>