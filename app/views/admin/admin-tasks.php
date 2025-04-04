<?php
require_once "app/helpers/Constants.php";
?>



<div class="tasks-container">
    <div class="page-header">

    <div style="display: flex; align-items: center">
    <a href="/admin/dashboard" class="back-button" title="Retour au tableau de bord">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1>Gestion des tâches</h1>
    </div>  
            <!-- Bouton d'ajout de tâche -->
            <button class="add-record-btn" id="add-task-btn">
            <i class="fas fa-plus"></i>
            Nouvelle tâche
        </button>
    </div>
    
    <!-- Outils de gestion: recherche, filtres, ajout -->
    <div class="tasks-tools">
        <div class="tasks-tools-left">
            <!-- Barre de recherche -->
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" id="search-tasks" placeholder="Rechercher une tâche..." 
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </div>
            
            <!-- Bouton d'actions groupées (visible seulement quand des tâches sont sélectionnées) -->
            <div class="bulk-actions" id="bulk-actions">
                <div class="dropdown-actions">
                    <button style="color: var(--text-muted);" class="bulk-actions-btn" id="bulk-actions-btn">
                        <i class="fas fa-clipboard"></i>
                        Modifier statut
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="bulk-actions-menu" id="bulk-actions-menu">
                        <?php foreach (Constants::TASK_STATUS as $key => $value): ?>
                        <div class="dropdown-item-action bulk-status-option" data-status="<?php echo $key; ?>">
                            <i class="fas fa-circle" style="color: var(--status-<?php echo Constants::getTaskStatusClass($value); ?>)"></i>
                            <?php echo $value; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filtrage par projet -->
        <div class="type-filter">
            <span>Sélectionner le projet</span>
            <div class="custom-select">
                <input list="projects-list" id="project-filter" name="project_id" placeholder="Tous les projets">
                <datalist id="projects-list">
                    <option value="all">Tous les projets</option>
                    <?php foreach ($data["projects"] as $project): ?>
                    <option value="<?php echo htmlspecialchars($project['id']); ?>"><?php echo htmlspecialchars($project['name']); ?></option>
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>
        
    
    </div>
    
    <!-- Card contenant la liste des tâches -->
    <div class="card">
        <div class="card-header">
            <h2>Liste des tâches</h2>
        </div>
        <div class="card-body">
            <!-- Tableau des tâches -->
            <div class="table-container">
                <table class="tasks-table">
                    <thead>
                        <tr>
                            <th class="checkbox-cell">
                                <div class="checkbox-container">
                                    <input type="checkbox" id="select-all-records">
                                </div>
                            </th>
                            <th>Nom de la tâche</th>
                            <th>Assigné à</th>
                            <th>Projet</th>
                            <th>Statut</th>
                            <th>Priorité</th>
                            <th class="actions-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tasks-list">
                        <?php if (!empty($data["tasks"])): ?>
                            <?php foreach ($data["tasks"] as $task): ?>
                                <tr data-task-id="<?php echo $task['id']; ?>">
                                    <td class="checkbox-cell">
                                        <div class="checkbox-container">
                                            <input type="checkbox" class="d-checkbox" data-id="<?php echo $task['id']; ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <span class="task-name"><?php echo htmlspecialchars($task['name']); ?></span>
                                    </td>
                                    <td>
                                        <div style="display: flex; flex-direction: column; gap: 2px;">
                                            <span class="">
                                                <?php echo htmlspecialchars($task['assigned_first_name'] . ' ' . $task['assigned_last_name']); ?>
                                            </span>
                                            <span style="color: var(--text-muted);" class=""><?php echo htmlspecialchars($task['assigned_email'] ?? 'Non assigné'); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="project-info">
                                            <span class="project-name"><?php echo htmlspecialchars($task['project_name']); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge status <?php echo Constants::getTaskStatusClass($task['status']); ?>">
                                            <?php echo htmlspecialchars($task['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo Constants::getTaskPriorityClass($task['priority']); ?>">
                                            <?php echo htmlspecialchars($task['priority']); ?>
                                        </span>
                                    </td>
                                    <td class="actions-cell">
                                        <a href="/admin/task/<?php echo $task['id']; ?>" class="action-btn view-btn" title="Voir les détails">
                                            <i class="fas fa-link"></i>
                                        </a>
                                        <div class="dropdown-actions">
                                            <button class="action-btn dropdown-toggle" title="Plus d'actions">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu-actions">
                                                <a href="/admin/task/edit/<?php echo $task['id']; ?>" class="dropdown-item-action">
                                                    <i class="fas fa-edit"></i> Modifier
                                                </a>
                                                <div class="dropdown-item-action delete delete-task" data-id="<?php echo $task['id']; ?>">
                                                    <i class="fas fa-trash"></i> Supprimer
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 30px;">
                                    <p>Aucune tâche trouvée.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if (isset($data["totalPages"]) && $data["totalPages"] > 1): ?>
                <div class="pagination">
                    <a href="?page=1<?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['project_id']) ? '&project_id=' . htmlspecialchars($_GET['project_id']) : ''; ?>" 
                       class="pagination-btn <?php echo $data["currentPage"] == 1 ? 'disabled' : ''; ?>">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                    
                    <a href="?page=<?php echo max(1, $data["currentPage"] - 1); ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['project_id']) ? '&project_id=' . htmlspecialchars($_GET['project_id']) : ''; ?>" 
                       class="pagination-btn <?php echo $data["currentPage"] == 1 ? 'disabled' : ''; ?>">
                        <i class="fas fa-angle-left"></i>
                    </a>
                    
                    <?php
                    $startPage = max(1, $data["currentPage"] - 2);
                    $endPage = min($startPage + 4, $data["totalPages"]);
                    
                    if ($endPage - $startPage < 4 && $startPage > 1) {
                        $startPage = max(1, $endPage - 4);
                    }
                    
                    for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                        <a href="?page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['project_id']) ? '&project_id=' . htmlspecialchars($_GET['project_id']) : ''; ?>" 
                           class="pagination-btn <?php echo $i == $data["currentPage"] ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <a href="?page=<?php echo min($data["totalPages"], $data["currentPage"] + 1); ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['project_id']) ? '&project_id=' . htmlspecialchars($_GET['project_id']) : ''; ?>" 
                       class="pagination-btn <?php echo $data["currentPage"] == $data["totalPages"] ? 'disabled' : ''; ?>">
                        <i class="fas fa-angle-right"></i>
                    </a>
                    
                    <a href="?page=<?php echo $data["totalPages"]; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['project_id']) ? '&project_id=' . htmlspecialchars($_GET['project_id']) : ''; ?>" 
                       class="pagination-btn <?php echo $data["currentPage"] == $data["totalPages"] ? 'disabled' : ''; ?>">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal d'ajout de tâche -->
<div class="modal-overlay" id="add-task-modal">
    <div class="modal" style="max-width: 700px;">
        <div class="modal-header">
            <div style="color: var(--accent-color);"  class="modal-title">Ajouter une nouvelle tâche</div>
            <div class="modal-description">Remplissez les informations ci-dessous pour créer une nouvelle tâche.</div>
        </div>
        <div class="modal-body">
            <form id="add-task-form" class="task-form-grid">
                <!-- Nom de la tâche -->
                <div class="form-group full-width">
                    <label for="task-name">Nom de la tâche</label>
                    <input type="text" id="task-name" name="name">
                    <div class="form-error" id="name-error"></div>
                </div>
                
                <!-- Description -->
                <div class="form-group full-width">
                    <label for="task-description">Description</label>
                    <textarea id="task-description" name="description"></textarea>
                    <div class="form-error" id="description-error"></div>
                </div>
                
                <!-- Projet -->
                <div class="form-group">
                    <label for="task-project">Projet</label>
                    <div class="custom-select">
                        <input list="task-projects-list" id="task-project" name="project_id" placeholder="Sélectionner un projet">
                        <datalist id="task-projects-list">
                            <?php foreach ($data["projects"] as $project): ?>
                            <option value="<?php echo htmlspecialchars($project['id']); ?>"><?php echo htmlspecialchars($project['name']); ?></option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="form-error" id="project_id-error"></div>
                </div>
                
                <!-- Personne assignée -->
                <div class="form-group">
                    <label for="task-assigned">Assigné à</label>
                    <div class="custom-select">
                        <input list="task-users-list" id="task-assigned" name="assigned_to" placeholder="Sélectionner un utilisateur">
                        <datalist id="task-users-list">
                            <?php foreach ($data["users"] as $user): ?>
                            <option value="<?php echo htmlspecialchars($user['id']); ?>"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?> (<?php echo htmlspecialchars($user['email']); ?>)</option>
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    <div class="form-error" id="assigned_to-error"></div>
                </div>
                
                <!-- Priorité -->
                <div class="form-group">
                    <label for="task-priority">Priorité</label>
                    <select id="task-priority" name="priority">
                        <option value="">Sélectionner une priorité</option>
                        <?php foreach (Constants::TASK_PRIORITY as $key => $value): ?>
                        <option value="<?php echo strtolower($value); ?>"><?php echo ucfirst($value); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-error" id="priority-error"></div>
                </div>
                
                <!-- Date de début prévue -->
                <div class="form-group">
                    <label for="task-start-date">Date de début prévue *</label>
                    <div class="date-input-container">
                        <input type="date" id="task-start-date" name="scheduled_start_date">
                    </div>
                    <div class="form-error" id="scheduled_start_date-error"></div>
                </div>
                
                <!-- Date de fin prévue -->
                <div class="form-group">
                    <label for="task-end-date">Date de fin prévue *</label>
                    <div class="date-input-container">
                        <input type="date" id="task-end-date" name="scheduled_end_date">
                    </div>
                    <div class="form-error" id="scheduled_end_date-error"></div>
                </div>
                
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancel-add-task">Annuler</button>
            <button class="btn btn-primary" id="submit-add-task">Ajouter la tâche</button>
        </div>
        <button class="modal-close" id="close-add-task-modal">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal-overlay" id="delete-modal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">Supprimer la tâche</div>
            <div class="modal-description">Êtes-vous sûr de vouloir supprimer cette tâche ? Cette action est irréversible.</div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancel-delete">Annuler</button>
            <button class="btn btn-danger" id="confirm-delete">Supprimer</button>
        </div>
        <button class="modal-close" id="close-delete-modal">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

            
        <!-- Notification Toast -->
        <div id="toast" class="toast">
            <div class="toast-icon">
              <i class=""></i>
            </div>
            <div class="toast-message"></div>
          </div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<script>
$(document).ready(function() {
    // Constantes JS depuis PHP
    const TASK_STATUS = <?php echo json_encode(Constants::TASK_STATUS); ?>;
    const TASK_STATUS_CLASSES = <?php echo json_encode(Constants::TASK_STATUS_CLASSES); ?>;
   /*  const TASK_PRIORITY = <?php echo json_encode(Constants::TASK_PRIORITY); ?>;
    const TASK_PRIORITY_CLASSES = <?php echo json_encode(Constants::TASK_PRIORITY_CLASSES); ?>; */
    const USER_ROLE = "<?php echo $data['user']['role']; ?>";
    const USER_ID = "<?php echo $data['user']['id']; ?>";
  
    // Recherche en temps réel
    $('#search-tasks').on('keyup', function() {
        const searchTerm = $(this).val().trim();
        
        // Si le terme de recherche a au moins 2 caractères ou est vide
        if (searchTerm.length >= 2 || searchTerm.length === 0) {
            // Récupérer le filtre de projet actuel
            const projectId = $('#project-filter').val();
            
            // Construire l'URL avec les paramètres
            let url = '/app/api/tasks/search.php?search=' + encodeURIComponent(searchTerm);
            if (projectId && projectId !== 'all') {
                url += '&project_id=' + encodeURIComponent(projectId);
            }
            
            // Ajouter le numéro de page actuel
            const urlParams = new URLSearchParams(window.location.search);
            const page = urlParams.get('page') || 1;
            url += '&page=' + page;
            
            // Faire la requête AJAX
            $.ajax({
                url: url,
                type: 'GET',
                beforeSend: function() {
                    // Ajouter un indicateur de chargement
                    $('#tasks-list').html('<tr><td colspan="7" style="text-align: center; padding: 30px;"><div class="loading-spinner"></div></td></tr>');
                },
                success: function(response) {
                    // Mettre à jour l'URL de la page sans recharger
                    let newUrl = window.location.pathname + '?';
                    if (searchTerm.length > 0) {
                        newUrl += 'search=' + encodeURIComponent(searchTerm) + '&';
                    }
                    if (projectId && projectId !== 'all') {
                        newUrl += 'project_id=' + encodeURIComponent(projectId) + '&';
                    }
                    newUrl += 'page=' + page;
                    
                    history.pushState({}, '', newUrl);
                    
                    // Mettre à jour le tableau des tâches
                    $('#tasks-list').html(response.html);
                    
                    // Mettre à jour la pagination si nécessaire
                    if (response.pagination) {
                        $('.pagination').html(response.pagination);
                    }
                },
                error: function(xhr) {
                    const response = JSON.parse(xhr.responseText);
                    showToast(response.error || 'Une erreur est survenue', 'error');
                }
            });
        }
    });
    
    // Filtre par projet
    $('#project-filter').on('change', function() {
        const projectId = $(this).val();
        const searchTerm = $('#search-tasks').val().trim();
        
        // Construire l'URL avec les paramètres
        let url = '/app/api/tasks/search.php?';
        if (searchTerm.length > 0) {
            url += 'search=' + encodeURIComponent(searchTerm) + '&';
        }
        if (projectId && projectId !== 'all') {
            url += 'project_id=' + encodeURIComponent(projectId);
        }
        
        // Faire la requête AJAX
        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function() {
                // Ajouter un indicateur de chargement
                $('#tasks-list').html('<tr><td colspan="7" style="text-align: center; padding: 30px;"><div class="loading-spinner"></div></td></tr>');
            },
            success: function(response) {
                // Mettre à jour l'URL de la page sans recharger
                let newUrl = window.location.pathname + '?';
                if (searchTerm.length > 0) {
                    newUrl += 'search=' + encodeURIComponent(searchTerm) + '&';
                }
                if (projectId && projectId !== 'all') {
                    newUrl += 'project_id=' + encodeURIComponent(projectId) + '&';
                }
                newUrl += 'page=1';
                
                history.pushState({}, '', newUrl);
                
                // Mettre à jour le tableau des tâches
                $('#tasks-list').html(response.html);
                
                // Mettre à jour la pagination si nécessaire
                if (response.pagination) {
                    $('.pagination').html(response.pagination);
                }
            },
            error: function(xhr) {
                const response = JSON.parse(xhr.responseText);
                showToast(response.error || 'Une erreur est survenue', 'error');
            }
        });
    });
    
    // Ouvrir la modal d'ajout de tâche
    $('#add-task-btn').on('click', function() {
        $('#add-task-modal').addClass('active');
        // Réinitialiser le formulaire
        $('#add-task-form')[0].reset();
        // Réinitialiser les messages d'erreur
        $('.form-error').text('');
    });
    
    // Fermer les modals
    $('.modal-close, #cancel-add-task, #cancel-delete').on('click', function() {
        $('.modal-overlay').removeClass('active');
    });
    
    // Sélection/désélection de toutes les tâches
    $('#select-all-records').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.d-checkbox').prop('checked', isChecked);
        updateBulkActionsVisibility();
    });
    
    // Mise à jour de la visibilité du bouton d'actions groupées
    $(document).on('change', '.d-checkbox', function() {
        updateBulkActionsVisibility();
    });
    
    function updateBulkActionsVisibility() {
        const hasCheckedItems = $('.d-checkbox:checked').length > 0;
        if (hasCheckedItems) {
            $('#bulk-actions').addClass('active');
        } else {
            $('#bulk-actions').removeClass('active');
        }
    }
    
    // Récupérer les IDs des tâches sélectionnées
    function getSelectedTaskIds() {
        const selectedIds = [];
        $('.d-checkbox:checked').each(function() {
            selectedIds.push($(this).data('id'));
        });
        return selectedIds;
    }
    
    // Appliquer un statut à plusieurs tâches
    $('.bulk-status-option').on('click', function() {
        const newStatus = $(this).data('status');
        const selectedTasks = getSelectedTaskIds();
        
        if (selectedTasks.length > 0) {
            // Envoyer la requête AJAX pour mettre à jour les statuts
            $.ajax({
                url: '/app/api/tasks/update-status-bulk.php',
                type: 'POST',
                data: {
                    csrf_token: '<?php echo $_SESSION['csrf_token']; ?>',
                    task_ids: selectedTasks,
                    status: newStatus,
                    user_id: USER_ID,
                    user_role: USER_ROLE
                },
                success: function(response) {
                    // Fermer le menu
                    $('#bulk-actions-menu').removeClass('active');
                    
                    // Mettre à jour l'affichage des statuts
                    if (response.success.length > 0) {
                        response.success.forEach(function(id) {
                            const statusCell = $(`tr[data-task-id="${id}"] .status`);
                            statusCell.attr('class', `badge status ${TASK_STATUS_CLASSES[TASK_STATUS[newStatus]]}`);
                            statusCell.text(TASK_STATUS[newStatus]);
                        });
                        
                        // Décocher toutes les cases
                        $('.d-checkbox, #select-all-records').prop('checked', false);
                        // Cacher le bouton de changement de statut
                        updateBulkActionsVisibility();
                        
                        showToast(`Statut mis à jour pour ${response.success.length} tâche(s)`,"success");
                    }
                    
                    if (response.failed.length > 0) {
                        showToast(`Impossible de mettre à jour ${response.failed.length} tâche(s). Vérifiez vos droits.`, 'error');
                    }
                },
                error: function(xhr) {
                    const response = JSON.parse(xhr.responseText);
                    showToast(response.error || 'Une erreur est survenue', 'error');
                }
            });
        }
    });
    
    // Ouvrir/Fermer le menu d'actions pour une tâche
    $(document).on('click', '.dropdown-toggle', function(e) {
        e.stopPropagation();
        
        // Fermer tous les autres menus
        $('.dropdown-menu-actions').not($(this).next()).removeClass('active');
        
        // Afficher/Masquer le menu actuel
        $(this).next('.dropdown-menu-actions').toggleClass('active');
    });
    
    // Ouvrir/Fermer le menu d'actions groupées
    $('#bulk-actions-btn').on('click', function(e) {
        e.stopPropagation();
        $('#bulk-actions-menu').toggleClass('active');
    });
    
    // Fermer les menus au clic ailleurs sur la page
    $(document).on('click', function() {
        $('.dropdown-menu-actions, #bulk-actions-menu').removeClass('active');
    });
    
    // Empêcher la fermeture des menus au clic à l'intérieur
    $('.dropdown-menu-actions, #bulk-actions-menu').on('click', function(e) {
        e.stopPropagation();
    });
    
    // Gestion de la suppression d'une tâche
    $(document).on('click', '.delete-task', function() {
        const taskId = $(this).data('id');
        
        // Stocker l'ID de la tâche à supprimer
        sessionStorage.setItem('taskId', taskId);
        
        // Afficher la modal de confirmation
        $('#delete-modal').addClass('active');
    });
    
    // Confirmer la suppression
    $('#confirm-delete').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        
        const taskId = sessionStorage.getItem('taskId');
        
        if (taskId) {
            // Désactiver le bouton pour prévenir les doubles clics
            $btn.prop('disabled', true);
            $btn.html('<i class="fa-spin fa-solid fa-circle-notch"></i> Suppression...');
            
            // Envoyer la requête AJAX pour supprimer la tâche
            $.ajax({
                url: '/app/api/tasks/delete-task.php',
                type: 'POST',
                data: {
                    csrf_token: '<?php echo $_SESSION['csrf_token']; ?>',
                    task_id: taskId,
                    user_id: USER_ID,
                    user_role: USER_ROLE
                },
                success: function() {
                    // Fermer la modal
                    $('#delete-modal').removeClass('active');
                    
                    // Supprimer la ligne du tableau
                    $(`tr[data-task-id="${taskId}"]`).fadeOut(300, function() {
                        $(this).remove();
                        
                        // Afficher une notification de succès
                        showToast('Tâche supprimée avec succès', 'success');
                        
                        // Si le tableau est vide, ajouter un message
                        if ($('#tasks-list tr').length === 0) {
                            $('#tasks-list').html('<tr><td colspan="7" style="text-align: center; padding: 30px;"><p>Aucune tâche trouvée.</p></td></tr>');
                        }
                    });
                    
                    sessionStorage.removeItem('taskId');
                },
                error: function(xhr) {
                    const response = JSON.parse(xhr.responseText);
                    showToast(response.error || 'Une erreur est survenue lors de la suppression', 'error');
                    $('#delete-modal').removeClass('active');
                },
                complete: function() {
                    // Réactiver le bouton
                    $btn.prop('disabled', false);
                    $btn.html(originalText);
                }
            });
        }
    });
    
    // Soumission du formulaire d'ajout de tâche
    $('#submit-add-task').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        
        // Réinitialiser les messages d'erreur
        $('.form-error').text('');
        
        // Récupérer les données du formulaire
        const formData = {
            name: $('#task-name').val(),
            description: $('#task-description').val(),
            project_id: $('#task-project').val(),
            assigned_to: $('#task-assigned').val(),
            priority: $('#task-priority').val(),
            scheduled_start_date: $('#task-start-date').val(),
            scheduled_end_date: $('#task-end-date').val(),
            csrf_token: $('input[name="csrf_token"]').val(),
            created_by: USER_ID
        };
        console.log(formData);
        // Désactiver le bouton pour prévenir les doubles clics
        $btn.prop('disabled', true);
        $btn.html('<i class="fa-spin fa-solid fa-circle-notch"></i> Ajout en cours...');
        
        // Envoyer la requête AJAX
        $.ajax({
            url: '/app/api/tasks/add-task.php',
            type: 'POST',
            data: formData,
            success: function(response) {
                // Fermer la modal
                $('#add-task-modal').removeClass('active');
                
                // Afficher une notification de succès
                showToast('Tâche ajoutée avec succès', 'success');
                
                // Recharger la page pour afficher la nouvelle tâche
                location.reload();
            },
            error: function(xhr) {
                const response = JSON.parse(xhr.responseText);
                
                // Afficher les erreurs de validation
                if (response.validation_errors) {
                    Object.keys(response.validation_errors).forEach(function(field) {
                        $(`#${field}-error`).text(response.validation_errors[field]);
                    });
                } else {
                    showToast(response.error || 'Une erreur est survenue', 'error');
                }
            },
            complete: function() {
                // Réactiver le bouton
                $btn.prop('disabled', false);
                $btn.html(originalText);
            }
        });
    });
});
</script>
