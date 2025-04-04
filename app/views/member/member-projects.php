<?php
require_once "app/helpers/Constants.php";
require_once "app/helpers/DateTimeHelper.php";
require_once "app/helpers/CurrencyHelper.php";
?>

<div class="projects-container">
    <div class="page-header">
        <div style="display: flex; align-items: center">
            <a href="/member/dashboard" class="back-button" title="Retour au tableau de bord">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1>Les projets auxquels vous participez</h1>
        </div>
    </div>
    
    <!-- Outils de gestion: recherche et filtres -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;" class="projects-tools">
        <!-- Barre de recherche -->
        <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" id="search-projects" placeholder="Rechercher un projet..." 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        </div>
        
        <!-- Filtre par statut de projet -->
        <div style="display: flex; flex-direction: column; gap: 5px;" class="project-status-filter type-filter">
            <span style="color: var(--text-muted);">
                Filtrer par statut
            </span>
            <select style="color: var(--text-muted);" id="project-status-filter">
                <option value="all">Tous les statuts</option>
                <option value="In progress">En cours</option>
                <option value="Completed">Terminé</option>
            </select>
        </div>
    </div>
    
    <!-- Card contenant la liste des projets -->
    <div class="card">
        <div class="card-header">
            <h2>Liste de vos projets</h2>
        </div>
        <div class="card-body">
            <!-- Tableau des projets -->
            <div class="table-container">
                <table class="projects-table">
                    <thead>
                        <tr>
                            <th>Nom du projet</th>
                            <th>Description</th>
                            <th>Dates prévues</th>
                          <!--   <th>Dates réelles</th> -->
                            <th class="actions-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="projects-list">
                        <?php if (!empty($data["projects"])): ?>
                            <?php foreach ($data["projects"] as $project): ?>
                                <tr data-project-id="<?php echo $project['id']; ?>">
                                    <td>
                                        <div style="display: flex; flex-direction: column;">
                                            <span class="project-name"><?php echo htmlspecialchars($project['name']); ?></span>
                                            <span style="width: fit-content;" class="badge <?php echo Constants::getProjectStatusClass($project['status']); ?>">
                                                <?php echo htmlspecialchars($project['status']); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="project-description">
                                            <?php echo htmlspecialchars(substr($project['description'], 0, 100)) . (strlen($project['description']) > 100 ? '...' : ''); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="project-dates">
                                            <small>Début:</small><small style="font-size: 15px; font-weight:bold"> <?php echo DateTimeHelper::formatShortDate($project['scheduled_start_date']); ?></small>
                                            <small>Fin:</small><small style="font-size: 15px; font-weight:bold"><?php echo DateTimeHelper::formatShortDate($project['scheduled_end_date']); ?></small> 
                                        </div>
                                    </td>
                                  <!--   <td>
                                        <div class="project-dates">
                                            <div><strong>Début:</strong> <?php echo $project['actual_start_date'] ? DateTimeHelper::formatShortDate($project['actual_start_date']) : 'Non démarré'; ?></div>
                                            <div><strong>Fin:</strong> <?php echo $project['actual_end_date'] ? DateTimeHelper::formatShortDate($project['actual_end_date']) : 'Non terminé'; ?></div>
                                        </div>
                                    </td> -->
                                    <td class="actions-cell">
                                        <a href="/member/project/<?php echo $project['id']; ?>" class="action-btn view-btn" title="Voir les détails">
                                            <i class="fas fa-link"></i>
                                        </a>
                                        <?php if (!empty($project['documents'])): ?>
                                            <div class="dropdown-actions">
                                                <button class="action-btn dropdown-toggle" title="Documents">
                                                    <i class="fas fa-file-download"></i>
                                                </button>
                                                <div class="dropdown-menu-actions">
                                                    <?php 
                                                
                                                    $documents = is_array($project['documents']) ? $project['documents'] : json_decode($project['documents'], true);
                                                    // Correction de l'affichage des documents
                                                    if (json_last_error() !== JSON_ERROR_NONE && is_string($project['documents'])) {
                                                        // Tenter de traiter comme un tableau PostgreSQL si le décodage JSON a échoué
                                                        if (substr($project['documents'], 0, 1) === '{' && substr($project['documents'], -1) === '}') {
                                                            $documents = explode(',', substr($project['documents'], 1, -1));
                                                            $documents = array_map(function($path) {
                                                                return trim($path, '"\'');
                                                            }, $documents);
                                                        }
                                                    }
                                                    
                                                    if (is_array($documents)):
                                                        foreach ($documents as $index => $doc): 
                                                            // Gérer les deux formats possibles (simple chemin ou tableau associatif)
                                                            $filePath = is_array($doc) ? $doc['filepath'] : $doc;                                                          
                                                            $fileName = basename($filePath);
                                                           
                                                    ?>
                                                            <a  href="<?php echo $fileName; ?>" class="dropdown-item-action" download>
                                                                <i class="fas fa-file-pdf"></i> Document <?php echo $index + 1; ?>
                                                            </a>
                                                    <?php 
                                                        endforeach; 
                                                    endif;
                                                    ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 30px;">
                                    <p>Aucun projet trouvé.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if (isset($data["totalPages"]) && $data["totalPages"] > 1): ?>
                <div class="pagination">
                    <a href="?page=1<?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . htmlspecialchars($_GET['status']) : ''; ?>" 
                       class="pagination-btn <?php echo $data["currentPage"] == 1 ? 'disabled' : ''; ?>">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                    
                    <a href="?page=<?php echo max(1, $data["currentPage"] - 1); ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . htmlspecialchars($_GET['status']) : ''; ?>" 
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
                        <a href="?page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . htmlspecialchars($_GET['status']) : ''; ?>" 
                           class="pagination-btn <?php echo $i == $data["currentPage"] ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <a href="?page=<?php echo min($data["totalPages"], $data["currentPage"] + 1); ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . htmlspecialchars($_GET['status']) : ''; ?>" 
                       class="pagination-btn <?php echo $data["currentPage"] == $data["totalPages"] ? 'disabled' : ''; ?>">
                        <i class="fas fa-angle-right"></i>
                    </a>
                    
                    <a href="?page=<?php echo $data["totalPages"]; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['status']) ? '&status=' . htmlspecialchars($_GET['status']) : ''; ?>" 
                       class="pagination-btn <?php echo $data["currentPage"] == $data["totalPages"] ? 'disabled' : ''; ?>">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
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
    // Gestion des menus déroulants pour les documents
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
    
  
    
    // Recherche de projets
    let searchTimeout;
    $('#search-projects').on('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = $(this).val();
        
        searchTimeout = setTimeout(function() {
            searchProjects(searchTerm, $('#project-status-filter').val());
        }, 500);
    });
    
    // Filtre par statut
    $('#project-status-filter').on('change', function() {
        searchProjects($('#search-projects').val(), $(this).val());
    });
    
    // Fonction de recherche avec AJAX
    function searchProjects(searchTerm, status) {
        $.ajax({
            url: '/app/api/projects/search-member-projects.php',
            type: 'GET',
            data: {
                search: searchTerm,
                status: status
            },
            beforeSend: function() {
                // Ajouter un indicateur de chargement si nécessaire
            },
            success: function(response) {
                if (response.success) {
                    updateProjectsTable(response.data.projects);
                    updatePagination(response.data.pagination);
                } else {
                    showToast(response.error || 'Une erreur est survenue lors de la recherche', 'error');
                }
            },
            error: function() {
                showToast('Erreur de connexion au serveur', 'error');
            },
            complete: function() {
                // Supprimer l'indicateur de chargement si nécessaire
            }
        });
    }
    
    // Mettre à jour le tableau de projets
    function updateProjectsTable(projects) {
        const tbody = $('#projects-list');
        tbody.empty();
        
        if (projects.length === 0) {
            tbody.html('<tr><td colspan="5" style="text-align: center; padding: 30px;"><p>Aucun projet trouvé.</p></td></tr>');
            return;
        }
        
        projects.forEach(function(project) {
            let documentsMenu = '';
            
            if (project.documents && project.documents.length > 0) {
                let documentsItems = '';
                const documents = typeof project.documents === 'string' ? JSON.parse(project.documents) : project.documents;
                
                documents.forEach(function(doc, index) {
                    const fileName = doc.split('/').pop();
                    documentsItems += `
                        <a href="${doc}" class="dropdown-item-action" download>
                            <i class="fas fa-file-pdf"></i> Document ${index + 1}
                        </a>
                    `;
                });
                
                documentsMenu = `
                    <div class="dropdown-actions">
                        <button class="action-btn dropdown-toggle" title="Documents">
                            <i class="fas fa-file-download"></i>
                        </button>
                        <div class="dropdown-menu-actions">
                            ${documentsItems}
                        </div>
                    </div>
                `;
            }
            
            const row = `
                <tr data-project-id="${project.id}">
                    <td>
                        <span class="project-name">${project.name}</span>
                        <div class="badge ${getProjectStatusClass(project.status)}">
                            ${project.status}
                        </div>
                    </td>
                    <td>
                        <div class="project-description">
                            ${project.description.length > 100 ? project.description.substring(0, 100) + '...' : project.description}
                        </div>
                    </td>
                    <td>
                        <div class="project-dates">
                            <div><strong>Début:</strong> ${formatDate(project.scheduled_start_date)}</div>
                            <div><strong>Fin:</strong> ${formatDate(project.scheduled_end_date)}</div>
                        </div>
                    </td>
                    <td>
                        <div class="project-dates">
                            <div><strong>Début:</strong> ${project.actual_start_date ? formatDate(project.actual_start_date) : 'Non démarré'}</div>
                            <div><strong>Fin:</strong> ${project.actual_end_date ? formatDate(project.actual_end_date) : 'Non terminé'}</div>
                        </div>
                    </td>
                    <td class="actions-cell">
                        <a href="/member/project/${project.id}" class="action-btn view-btn" title="Voir les détails">
                            <i class="fas fa-link"></i>
                        </a>
                        ${documentsMenu}
                    </td>
                </tr>
            `;
            
            tbody.append(row);
        });
    }
    
    // Mettre à jour la pagination
    function updatePagination(pagination) {
        // Implémentation si nécessaire
    }
    
    // Helper pour obtenir la classe CSS du statut
    function getProjectStatusClass(status) {
        const statusClasses = {
            'In progress': 'in-progress',
            'Completed': 'completed',
            'Cancelled': 'cancelled',
            'Delayed': 'delayed'
        };
        
        return statusClasses[status] || 'bg-secondary';
    }
   
});
</script>