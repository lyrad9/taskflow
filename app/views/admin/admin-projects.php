<!-- Section de gestion des projets pour les administrateurs -->
<?php
require_once "app/helpers/Constants.php";
?>
<div class="projects-container">
    <div class="page-header">
    <div style="display: flex; align-items: center">
    <a href="/admin/projects/add" class="back-button" title="Retour au tableau de bord">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1>Gestion des projets</h1>
    </div>
    
     <!-- Bouton d'ajout de projet -->
     <a href="/admin/projects/add" title="Retour au tableau de bord" class="add-record-btn">
            <i class="fas fa-plus"></i>
            Nouveau projet
        </a>  
    </div>
    

    </div>
    
    <!-- Outils de gestion: recherche, filtres, ajout -->
    <div style="align-items: end;" class="projects-tools">
        <div style="display: flex; gap: 5px;" class="projects-tools-left">
               <!-- Barre de recherche -->
        <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" id="search" placeholder="Rechercher un projet..." 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        </div>
        
    <!-- Bouton d'actions groupées (visible seulement quand des projets sont sélectionnés) -->
    <div class="bulk-actions" id="bulk-actions">
        <div class="dropdown-actions">
            <button style="color: var(--text-muted);" class="bulk-actions-btn" id="bulk-actions-btn">
                <i class="fas fa-clipboard"></i>
                Modifier statut
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="bulk-actions-menu" id="bulk-actions-menu">
                <?php foreach (Constants::PROJECT_STATUS as $key => $value): ?>
                <div class="dropdown-item-action bulk-status-option" data-status="<?php echo $key; ?>">
                    <i class="fas fa-circle" style="color: var(--status-<?php echo Constants::getProjectStatusClass($value); ?>)"></i>
                    <?php echo $value; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
        </div>
     
    
        <!-- Filtre par type de projet -->
        <div style="display: flex; flex-direction: column; gap: 5px;" class="project-type-filter type-filter">
        <span style="color: var(--text-muted);">
            Sélctionner le type de projet
        </span>
            <select style="color: var(--text-muted);" id="project-type-filter">
                <option value="all">Tous les projets</option>
                <?php foreach ($data["projectTypes"] as $type): ?>
                <option value="<?php echo htmlspecialchars($type); ?>" 
                        <?php echo (isset($_GET['project_type']) && $_GET['project_type'] === $type) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($type); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
       
    </div>
    
    <!-- Card contenant la liste des projets -->
    <div class="card">
        <div class="card-header">
            <h2>Liste des projets</h2>
          
        </div>
        <div class="card-body">
            <!-- Tableau des projets -->
            <div class="table-container">
                <table class="projects-table">
                    <thead>
                        <tr>
                            <th class="checkbox-cell">
                                <div class="checkbox-container">
                                    <input type="checkbox" id="select-all-records">
                                </div>
                            </th>
                            <th>Nom du projet</th>
                            <th>Client</th>
                            <th>Budget</th>
                            <th>Statut</th>
                            <th class="actions-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="projects-list">
                        <?php if (!empty($data["projects"])): ?>
                            <?php foreach ($data["projects"] as $project): ?>
                                <tr data-project-id="<?php echo $project['id']; ?>">
                                    <td class="checkbox-cell">
                                        <div class="checkbox-container">
                                            <input type="checkbox" class="d-checkbox" data-id="<?php echo $project['id']; ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <span class="project-name"><?php echo htmlspecialchars($project['name']); ?></span>
                                    </td>
                                    <td>
                                        <div class="client-info">
                                            <span class="client-name">
                                                <?php echo htmlspecialchars($project['client_first_name'] . ' ' . $project['client_last_name']); ?>
                                            </span>
                                            <span class="client-phone"><?php echo htmlspecialchars($project['client_phone'] ?? 'Non renseigné'); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php echo CurrencyHelper::formatAmount($project['budget']); ?>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo Constants::getProjectStatusClass($project['status']); ?>">
                                            <?php echo htmlspecialchars($project['status']); ?>
                                        </span>
                                    </td>
                                    <td class="actions-cell">
                                        <a href="/admin/project/<?php echo $project['id']; ?>" class="action-btn view-btn" title="Voir les détails">
                                            <i class="fas fa-link"></i>
                                        </a>
                                        <div class="dropdown-actions">
                                            <button class="action-btn dropdown-toggle" title="Plus d'actions">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu-actions">
                                                <a href="/admin/project/edit/<?php echo $project['id']; ?>" class="dropdown-item-action">
                                                    <i class="fas fa-edit"></i> Modifier
                                                </a>
                                                <div class="dropdown-item-action delete delete-project" data-id="<?php echo $project['id']; ?>">
                                                    <i class="fas fa-trash"></i> Supprimer
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 30px;">
                                    <p>Aucun projet trouvé.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($data["totalPages"] > 1): ?>
                <div class="pagination">
                    <a href="?page=1<?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['project_type']) ? '&project_type=' . htmlspecialchars($_GET['project_type']) : ''; ?>" 
                       class="pagination-btn <?php echo $data["currentPage"] == 1 ? 'disabled' : ''; ?>">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                    
                    <a href="?page=<?php echo max(1, $data["currentPage"] - 1); ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['project_type']) ? '&project_type=' . htmlspecialchars($_GET['project_type']) : ''; ?>" 
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
                        <a href="?page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['project_type']) ? '&project_type=' . htmlspecialchars($_GET['project_type']) : ''; ?>" 
                           class="pagination-btn <?php echo $i == $data["currentPage"] ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <a href="?page=<?php echo min($data["totalPages"], $data["currentPage"] + 1); ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['project_type']) ? '&project_type=' . htmlspecialchars($_GET['project_type']) : ''; ?>" 
                       class="pagination-btn <?php echo $data["currentPage"] == $data["totalPages"] ? 'disabled' : ''; ?>">
                        <i class="fas fa-angle-right"></i>
                    </a>
                    
                    <a href="?page=<?php echo $data["totalPages"]; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['project_type']) ? '&project_type=' . htmlspecialchars($_GET['project_type']) : ''; ?>" 
                       class="pagination-btn <?php echo $data["currentPage"] == $data["totalPages"] ? 'disabled' : ''; ?>">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal-overlay" id="delete-modal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">Supprimer le projet</div>
            <div class="modal-description">Êtes-vous sûr de vouloir supprimer ce projet ? </div>
        </div>
      
            <!-- Le contenu spécifique de la modal sera ici -->
        
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
<script src="public/assets/js/toat.js"></script>
<script>

// Récupération des constantes PHP en JavaScript
const PHP_CONSTANTS = {
    projectStatus: <?php echo json_encode(Constants::PROJECT_STATUS) ?>,
    projectStatusClasses: <?php echo json_encode(Constants::PROJECT_STATUS_CLASSES) ?>
};
function getStatusText(statusKey) {
    return PHP_CONSTANTS.projectStatus[statusKey] || 'Statut inconnu';
}

function getStatusClass(statusKey) {
    const statusText = PHP_CONSTANTS.projectStatus[statusKey];
    return PHP_CONSTANTS.projectStatusClasses[statusText] || 'bg-secondary';
}

$(document).ready(function() {   
    
    // Ouvrir/Fermer le menu d'actions pour un projet(supprimer, modifier)
   /*  $(document).on('click', '.dropdown-toggle', function(e) {
        console.log("dropdown-toggle");
         e.stopPropagation();
        
        // Fermer tous les autres menus
        $('.dropdown-menu-actions').not($(this).next()).removeClass('active');
        
        // Afficher/Masquer le menu actuel
        $(this).next('.dropdown-menu-actions').toggleClass('active');
    }); */
    $(document).on('click', function(e) {
    // Fermer tous les menus si on clique en dehors
    if (!$(e.target).closest('.dropdown-toggle, .dropdown-menu-actions').length) {
        $('.dropdown-menu-actions').removeClass('active');
    }
});

$(document).on('click', '.dropdown-toggle', function(e) {
    e.stopPropagation();

    let menu = $(this).next('.dropdown-menu-actions');

    // Fermer tous les autres menus
    $('.dropdown-menu-actions').not(menu).removeClass('active');

    // Basculer le menu actuel
    menu.toggleClass('active');
});
    
    // Ouvrir/Fermer le menu d'actions groupées(change the status of the projects)
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
        /*  e.stopPropagation(); */
    });

    
    // Appliquer un statut à plusieurs projets
    
    $('.bulk-status-option').on('click', function() {
        const newStatus = $(this).data('status');
        console.log("newStatus", newStatus);
        const selectedProjects = getSelectedProjectIds();
        console.log(selectedProjects)
        console.log("selectedProjects", selectedProjects);
        if (selectedProjects.length > 0) {
            // Envoyer la requête AJAX pour mettre à jour les statuts
            $.ajax({
                url: '/app/api/projects/update-status.php',
                type: 'POST',
                data: {
                    
                    project_ids: selectedProjects,
                    status: newStatus,
                  
                },
                
                success: function(xhr) {  
                                 
                    // Fermer le menu
                    $('#bulk-actions-menu').removeClass('active');
                    
                    // Mettre à jour l'affichage des statuts
                    selectedProjects.forEach(function(id) {
                        const statusCell = $(`tr[data-project-id="${id}"] .badge`);
                        statusCell.attr('class', `badge ${getStatusClass(newStatus)}`);
                        statusCell.text(getStatusText(newStatus));
                        console.log("statusCell", statusCell);
                    });
                          
                    // Afficher une notification de succès
                    showToast('Statut mis à jour pour ' + selectedProjects.length + ' projet(s)', 'success');
                    // Décocher toutes les cases
                    $('.d-checkbox, #select-all-records').prop('checked', false);
                    // cacher le bouton de chqnge;ent de status
                    updateBulkActionsVisibility();
              
                },
                error: function(xhr) {
                    // Afficher une notification d'erreur
                    let errorMessage = "Une erreur est survenue";
        
            const response = JSON.parse(xhr.responseText);
            errorMessage = response.message || errorMessage;
        
        showToast(response.error, 'error');
                }
            });
        }
    });
    
 
// Confirmer la suppression
$('#confirm-delete').on('click', function() {
    const $btn = $(this); // Référence au bouton
    console.log("btn", $btn);
  const originalText = $btn.html(); // Sauvegarde du texte original du bouton
    
    const projectId = sessionStorage.getItem("projectId");
    console.log("projectId", projectId);
  if (projectId) {
    // Désactiver le bouton pour prévenir les doubles clics
    $btn.prop('disabled', true);
    $btn.html('<i class="fa-spin fa-solid fa-circle-notch"></i>');
    console.log("yessss");
   
      // Envoyer la requête AJAX pour supprimer le projet
      $.ajax({
          url: '/app/api/projects/delete-project.php',
          type: 'POST',
          data: {
              csrf_token: '<?php echo $_SESSION['csrf_token']; ?>',
              projectId: projectId,
              userRole: '<?php echo $data['user']['role']; ?>'
          },
          success: function(xhr) {
              
              // Fermer la modal
              $('#delete-modal').removeClass('active');
              
              // Supprimer la ligne du tableau
             /*  $('tr[data-project-id="' + projectIdToDelete + '"]') */
              $(`tr[data-project-id="${projectId}"]`)
              .fadeOut(300, function() {
                  $(this).remove();
                  
                  // Afficher une notification de succès
                  showToast('Projet supprimé avec succès', 'success');
                  
                  // Si le tableau est vide, ajouter un message
                  if ($('#projects-list tr').length === 0) {
                      $('#projects-list').html('<tr><td colspan="6" style="text-align: center; padding: 30px;"><p>Aucun projet trouvé.</p></td></tr>');
                  }
              });
              sessionStorage.removeItem("projectId");              
          },
          error: function(xhr) {
            const response = JSON.parse(xhr.responseText);
              // Afficher une notification d'erreur
              showToast(response.error, 'error');
              $('#delete-modal').removeClass('active');
          },
          complete: function() {
            // Réactiver le bouton
            $btn.prop('disabled', false);
            $btn.html(originalText);
          },

      });
  }
});
    
});
</script>
<script src="/public/assets/js/selectedIds.js"></script>
