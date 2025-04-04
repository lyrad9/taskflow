<!-- Section de gestion des équipes pour les administrateurs -->
<?php
require_once "app/helpers/Constants.php";
?>
<div class="teams-container">
    <div class="page-header">
        <div style="display: flex; align-items: center">
            <a href="/admin/dashboard" class="back-button" title="Retour au tableau de bord">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1>Gestion des équipes</h1>
        </div>
        
        <!-- Bouton d'ajout d'équipe -->
        <button class="add-record-btn" id="add-team-btn">
            <i class="fas fa-plus"></i>
            Nouvelle équipe
        </button>
    </div>
    
    <!-- Outils de gestion: recherche, filtres -->
    <div style="align-items: end;" class="teams-tools">
        <div style="display: flex; gap: 5px; width: 100%; justify-content: space-between; align-items: end; margin-bottom: 20px;" class="teams-tools-left">
            <!-- Barre de recherche -->
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" id="search" placeholder="Rechercher une équipe..." 
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </div>
              <!-- Filtre par statut d'assignation -->
        <div style="display: flex; flex-direction: column; gap: 5px;" class="team-assignment-filter type-filter">
            <span style="color: var(--text-muted);">
                Filtrer par statut
            </span>
            <select style="color: var(--text-muted);" id="team-assigned-filter">
                <option value="all">Toutes les équipes</option>
                <option value="yes" <?php echo (isset($_GET['assigned']) && $_GET['assigned'] === 'yes') ? 'selected' : ''; ?>>
                    Équipes assignées
                </option>
                <option value="no" <?php echo (isset($_GET['assigned']) && $_GET['assigned'] === 'no') ? 'selected' : ''; ?>>
                    Équipes non assignées
                </option>
            </select>
        </div>
        </div>
        
      
    </div>
    
    <!-- Card contenant la liste des équipes -->
    <div class="card">
        <div class="card-header">
            <h2>Liste des équipes</h2>
        </div>
        <div class="card-body">
            <!-- Tableau des équipes -->
            <div class="table-container">
                <table class="teams-table">
                    <thead>
                        <tr>
                            <th>Nom de l'équipe</th>
                            <th>Description</th>
                            <th>Membres</th>
                            <th class="actions-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="teams-list">
                        <?php if (!empty($data["teams"])): ?>
                            <?php foreach ($data["teams"] as $team): ?>
                                <tr data-team-id="<?php echo $team['id']; ?>">
                                    <td>
                                        <div class="team-name-with-badge">
                                            <span class="team-name"><?php echo htmlspecialchars($team['name']); ?></span>
                                            <?php if (isset($team['has_project']) && $team['has_project']): ?>
                                                <span style="width:fit-content" class="badge in-progress">Assigné</span>
                                            <?php else: ?>
                                                <span style="width:fit-content" class="badge to-do">Non assigné</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="team-description">
                                            <?php echo !empty($team['description']) ? htmlspecialchars($team['description']) : 'Aucune description'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="team-members">
                                            <?php 
                                            if (!empty($team['member_names'])): ?>
                                                <?php echo implode(', ', $team['member_names']); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Aucun membre</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="actions-cell">
                                        <a href="/admin/team/<?php echo $team['id']; ?>" class="action-btn view-btn" title="Voir les détails">
                                            <i class="fas fa-link"></i>
                                        </a>
                                        <div class="dropdown-actions">
                                            <button class="action-btn dropdown-toggle" title="Plus d'actions">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu-actions">
                                                <div class="dropdown-item-action edit-team" data-id="<?php echo $team['id']; ?>">
                                                    <i class="fas fa-edit"></i> Modifier
                                                </div>
                                                <div class="dropdown-item-action delete delete-team" data-id="<?php echo $team['id']; ?>">
                                                    <i class="fas fa-trash"></i> Supprimer
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 30px;">
                                    <p>Aucune équipe trouvée.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if (isset($data["totalPages"]) && $data["totalPages"] > 1): ?>
                <div class="pagination">
                    <a href="?page=1<?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['assigned']) ? '&assigned=' . htmlspecialchars($_GET['assigned']) : ''; ?>" 
                       class="pagination-btn <?php echo $data["currentPage"] == 1 ? 'disabled' : ''; ?>">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                    
                    <a href="?page=<?php echo max(1, $data["currentPage"] - 1); ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['assigned']) ? '&assigned=' . htmlspecialchars($_GET['assigned']) : ''; ?>" 
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
                        <a href="?page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['assigned']) ? '&assigned=' . htmlspecialchars($_GET['assigned']) : ''; ?>" 
                           class="pagination-btn <?php echo $i == $data["currentPage"] ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <a href="?page=<?php echo min($data["totalPages"], $data["currentPage"] + 1); ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['assigned']) ? '&assigned=' . htmlspecialchars($_GET['assigned']) : ''; ?>" 
                       class="pagination-btn <?php echo $data["currentPage"] == $data["totalPages"] ? 'disabled' : ''; ?>">
                        <i class="fas fa-angle-right"></i>
                    </a>
                    
                    <a href="?page=<?php echo $data["totalPages"]; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['assigned']) ? '&assigned=' . htmlspecialchars($_GET['assigned']) : ''; ?>" 
                       class="pagination-btn <?php echo $data["currentPage"] == $data["totalPages"] ? 'disabled' : ''; ?>">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal d'ajout d'une équipe -->
<div class="modal-overlay" id="add-team-modal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">Ajouter une équipe</div>
            <div class="modal-description">Créez une nouvelle équipe et assignez-lui des membres (3 maximum).</div>
        </div>
        <div class="modal-body">
            <form id="add-team-form">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="team-name">Nom de l'équipe</label>
                    <input type="text" id="team-name" name="name" required>
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="team-description">Description</label>
                    <textarea id="team-description" name="description" rows="3"></textarea>
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="search-members">Ajouter des membres (3 maximum)</label>
                    
                    <!-- Datalist pour sélectionner les membres -->
                    <div class="member-search-container">
                        <input type="text" id="search-members" list="members-list" placeholder="Rechercher un membre...">
                        <datalist id="members-list">
                            <?php foreach ($data["members"] as $member): ?>
                                <option value="<?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>" 
                                        data-id="<?php echo $member['id']; ?>" 
                                        data-fonction="<?php echo htmlspecialchars($member['role'] ?? 'Non spécifié'); ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    
                    <!-- Container pour les tags des membres sélectionnés -->
                    <div id="selected-members-container" style="margin-top: 15px;"></div>
                    <input type="hidden" id="selected-members" name="members">
                    
                    <div class="members-limit-counter">
                        <span id="members-count">0</span>/3 membres sélectionnés
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancel-add-team">Annuler</button>
            <button class="btn btn-primary" id="confirm-add-team">Créer l'équipe</button>
        </div>
        <button class="modal-close" id="close-add-team-modal">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<!-- Modal de modification d'une équipe -->
<div class="modal-overlay" id="edit-team-modal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">Modifier l'équipe</div>
            <div class="modal-description">Modifiez les détails de l'équipe et ses membres (3 maximum).</div>
        </div>
        <div class="modal-body">
            <form id="edit-team-form">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="team_id" id="edit-team-id">
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="edit-team-name">Nom de l'équipe</label>
                    <input type="text" id="edit-team-name" name="name" required>
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="edit-team-description">Description</label>
                    <textarea id="edit-team-description" name="description" rows="3"></textarea>
                </div>
                
                <div class="form-group" style="margin-bottom: 20px;">
                    <label for="edit-search-members">Membres de l'équipe (3 maximum)</label>
                    
                    <!-- Datalist pour sélectionner les membres -->
                    <div class="member-search-container">
                        <input type="text" id="edit-search-members" list="edit-members-list" placeholder="Rechercher un membre...">
                        <datalist id="edit-members-list">
                            <?php foreach ($data["members"] as $member): ?>
                                <option value="<?php echo htmlspecialchars($member['first_name'] . ' ' . $member['last_name']); ?>" 
                                        data-id="<?php echo $member['id']; ?>" 
                                        data-fonction="<?php echo htmlspecialchars($member['role'] ?? 'Non spécifié'); ?>">
                            <?php endforeach; ?>
                        </datalist>
                    </div>
                    
                    <!-- Container pour les tags des membres sélectionnés -->
                    <div id="edit-selected-members-container" style="margin-top: 15px;"></div>
                    <input type="hidden" id="edit-selected-members" name="members">
                    
                    <div class="members-limit-counter">
                        <span id="edit-members-count">0</span>/3 membres sélectionnés
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancel-edit-team">Annuler</button>
            <button class="btn btn-primary" id="confirm-edit-team">Enregistrer</button>
        </div>
        <button class="modal-close" id="close-edit-team-modal">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal-overlay" id="delete-team-modal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">Supprimer l'équipe</div>
            <div class="modal-description">Êtes-vous sûr de vouloir supprimer cette équipe ? Cette action est irréversible.</div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancel-delete-team">Annuler</button>
            <button class="btn btn-danger" id="confirm-delete-team">Supprimer</button>
        </div>
        <button class="modal-close" id="close-delete-team-modal">
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

<style>
/* Styles spécifiques pour les équipes */
.team-name-with-badge {
    display: flex;
    flex-direction: column;
    
    gap: 10px;
}

.team-members {
    max-width: 300px;
    white-space: normal;
}

/* Styles pour les tags de membres sélectionnés */
.member-tag {
    display: inline-flex;
    align-items: center;
    background-color: #4361ee;
    color: white;
    padding: 5px 10px;
    border-radius: 50px;
    margin-right: 8px;
    margin-bottom: 8px;
    font-size: 0.9rem;
   /*  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); */
   /*  transition: transform 0.2s, box-shadow 0.2s; */
}

/* .member-tag:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
} */

.member-tag i {
    margin-left: 8px;
    cursor: pointer;
    color: rgba(255, 255, 255, 0.8);
    transition: color 0.2s;
}

.member-tag i:hover {
    color: #fff;
}

.member-function {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.85rem;
    margin-left: 5px;
}

/* Container pour les membres sélectionnés */
#selected-members-container,
#edit-selected-members-container {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    min-height: 40px;
    margin-bottom: 10px;
}

/* Compteur de membres */
.members-limit-counter {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-top: 8px;
    text-align: right;
}

/* Conteneur de recherche de membres */
.member-search-container {
    position: relative;
}

.member-search-container input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid var(--border-color);
    border-radius: 5px;
    font-size: 0.95rem;
    transition: border-color 0.3s;
}

.member-search-container input:focus {
    border-color: #4361ee;
    outline: none;
}
</style>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<script>
$(document).ready(function() {
    // Gestion des menus déroulants pour les actions
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown-toggle, .dropdown-menu-actions').length) {
            $('.dropdown-menu-actions').removeClass('active');
        }
    });

    $(document).on('click', '.dropdown-toggle', function(e) {
        e.stopPropagation();
        let menu = $(this).next('.dropdown-menu-actions');
        $('.dropdown-menu-actions').not(menu).removeClass('active');
        menu.toggleClass('active');
    });
    
    // Recherche d'équipes
    $('#search').on('input', function() {
        const searchTerm = $(this).val();
        const assignedFilter = $('#team-assigned-filter').val();
        
        // Rediriger avec les paramètres de recherche
        window.location.href = `/admin/teams?search=${encodeURIComponent(searchTerm)}&assigned=${assignedFilter}`;
    });
    
    // Filtre par assignation
    $('#team-assigned-filter').on('change', function() {
        const searchTerm = $('#search').val();
        const assignedFilter = $(this).val();
        
        // Rediriger avec le filtre d'assignation
        window.location.href = `/admin/teams?search=${encodeURIComponent(searchTerm)}&assigned=${assignedFilter}`;
    });
    
    // Ouverture de la modal d'ajout d'équipe
    $('#add-team-btn').on('click', function() {
        $('#add-team-modal').addClass('active');
        resetAddTeamForm();
    });
    
    // Fermeture des modals
    $('#close-add-team-modal, #cancel-add-team').on('click', function() {
        $('#add-team-modal').removeClass('active');
        resetAddTeamForm();
    });
    
    $('#close-edit-team-modal, #cancel-edit-team').on('click', function() {
        $('#edit-team-modal').removeClass('active');
    });
    
    $('#close-delete-team-modal, #cancel-delete-team').on('click', function() {
        $('#delete-team-modal').removeClass('active');
    });
    
    // Sélection d'un membre dans le datalist
    $('#search-members').on('change', function() {
        const selectedValue = $(this).val();
        const selectedOption = $('#members-list option').filter(function() {
            return $(this).val() === selectedValue;
        });
        
        if (selectedOption.length > 0) {
            const memberId = selectedOption.data('id');
            const memberFonction = selectedOption.data('fonction');
            
            // Vérifier si le membre est déjà dans la liste
            if ($('#selected-members-container .member-tag[data-id="' + memberId + '"]').length > 0) {
                showToast('Ce membre est déjà dans l\'équipe', 'error');
                $(this).val('');
                return;
            }
            
            // Vérifier la limite de membres
            if ($('#selected-members-container .member-tag').length >= 3) {
                showToast('Une équipe ne peut pas avoir plus de 3 membres', 'error');
                $(this).val('');
                return;
            }
            
            // Ajouter le tag du membre
            addMemberTag(selectedValue, memberId, memberFonction, false);
            
            // Vider le champ de recherche
            $(this).val('');
        }
    });
    
    // Sélection d'un membre dans le datalist pour modification
    $('#edit-search-members').on('change', function() {
        const selectedValue = $(this).val();
        const selectedOption = $('#edit-members-list option').filter(function() {
            return $(this).val() === selectedValue;
        });
        
        if (selectedOption.length > 0) {
            const memberId = selectedOption.data('id');
            const memberFonction = selectedOption.data('fonction');
            
            // Vérifier si le membre est déjà dans la liste
            if ($('#edit-selected-members-container .member-tag[data-id="' + memberId + '"]').length > 0) {
                showToast('Ce membre est déjà dans l\'équipe', 'error');
                $(this).val('');
                return;
            }
            
            // Vérifier la limite de membres
            if ($('#edit-selected-members-container .member-tag').length >= 3) {
                showToast('Une équipe ne peut pas avoir plus de 3 membres', 'error');
                $(this).val('');
                return;
            }
            
            // Ajouter le tag du membre
            addMemberTag(selectedValue, memberId, memberFonction, true);
            
            // Vider le champ de recherche
            $(this).val('');
        }
    });
    
    // Supprimer un membre de l'équipe
    $(document).on('click', '.remove-member', function() {
        const memberTag = $(this).closest('.member-tag');
        const memberId = memberTag.data('id');
        const isEdit = memberTag.closest('#edit-selected-members-container').length > 0;
        
        // Supprimer le tag
        memberTag.remove();
        
        // Mettre à jour l'input caché et le compteur
        updateMembersData(isEdit);
    });
    
    // Ajout d'une équipe
    $('#confirm-add-team').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        $btn.prop('disabled', true);
        $btn.html('<i class="fa-spin fa-solid fa-circle-notch"></i>');
        
        const formData = new FormData($('#add-team-form')[0]);
        console.log(formData);
        
        $.ajax({
            url: '/app/api/teams/add-team.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    showToast(data.message, 'success');
                    $('#add-team-modal').removeClass('active');
                    resetAddTeamForm();
                    
                    // Recharger la page pour afficher la nouvelle équipe
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    showToast(data.error || 'Une erreur est survenue', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Une erreur est survenue';
                
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMessage = response.error || response.errors?.name || errorMessage;
                } catch (e) {
                    console.error('Erreur lors de la création de l\'équipe', xhr);
                }
                
                showToast(errorMessage, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false);
                $btn.html(originalText);
            }
        });
    });
    
    // Ouverture de la modal de modification
    $(document).on('click', '.edit-team', function() {
        const teamId = $(this).data('id');
        
        // Réinitialiser le formulaire
        $('#edit-team-form')[0].reset();
        $('#edit-selected-members-container').empty();
        $('#edit-selected-members').val('');
        
        // Charger les données de l'équipe
        $.ajax({
            url: `/app/api/teams/get-team.php?id=${teamId}`,
            type: 'GET',
            success: function(response) {
                const data = JSON.parse(response);
                if (data.team) {
                    $('#edit-team-id').val(data.team.id);
                    $('#edit-team-name').val(data.team.name);
                    $('#edit-team-description').val(data.team.description);
                    
                    // Ajouter les membres actuels
                    if (data.members && data.members.length > 0) {
                        data.members.forEach(function(member) {
                            addMemberTag(`${member.first_name} ${member.last_name}`, member.id, member.fonction || 'Non spécifié', true);
                        });
                    }
                    
                    // Mettre à jour le compteur de membres
                    updateMembersData(true);
                    
                    // Afficher la modal
                    $('#edit-team-modal').addClass('active');
                } else {
                    showToast('Équipe non trouvée', 'error');
                }
            },
            error: function() {
                showToast('Erreur lors du chargement de l\'équipe', 'error');
            }
        });
    });
    
    // Modification d'une équipe
    $('#confirm-edit-team').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        $btn.prop('disabled', true);
        $btn.html('<i class="fa-spin fa-solid fa-circle-notch"></i>');
        
        const formData = new FormData($('#edit-team-form')[0]);
        
        $.ajax({
            url: '/app/api/teams/update-team.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = JSON.parse(response);
                if (data.success) {
                    showToast(data.message, 'success');
                    $('#edit-team-modal').removeClass('active');
                    
                    // Recharger la page pour afficher les modifications
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    showToast(data.error || 'Une erreur est survenue', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Une erreur est survenue';
                
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMessage = response.error || response.errors?.name || errorMessage;
                } catch (e) {
                    console.error('Erreur lors de la modification de l\'équipe', xhr);
                }
                
                showToast(errorMessage, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false);
                $btn.html(originalText);
            }
        });
    });
    
    // Ouverture de la modal de suppression
    $(document).on('click', '.delete-team', function() {
        const teamId = $(this).data('id');
        sessionStorage.setItem('teamIdToDelete', teamId);
        $('#delete-team-modal').addClass('active');
    });
    
    // Suppression d'une équipe
    $('#confirm-delete-team').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        $btn.prop('disabled', true);
        $btn.html('<i class="fa-spin fa-solid fa-circle-notch"></i>');
        
        const teamId = sessionStorage.getItem('teamIdToDelete');
        
        if (teamId) {
            $.ajax({
                url: '/app/api/teams/delete-team.php',
                type: 'POST',
                data: {
                    csrf_token: '<?php echo $_SESSION['csrf_token']; ?>',
                    team_id: teamId
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        showToast(data.message, 'success');
                        $('#delete-team-modal').removeClass('active');
                        
                        // Supprimer la ligne du tableau
                        $(`tr[data-team-id="${teamId}"]`).fadeOut(300, function() {
                            $(this).remove();
                            
                            // Si le tableau est vide, ajouter un message
                            if ($('#teams-list tr').length === 0) {
                                $('#teams-list').html('<tr><td colspan="4" style="text-align: center; padding: 30px;"><p>Aucune équipe trouvée.</p></td></tr>');
                            }
                        });
                        
                        sessionStorage.removeItem('teamIdToDelete');
                    } else {
                        showToast(data.error || 'Une erreur est survenue', 'error');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Une erreur est survenue';
                    
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.error || errorMessage;
                    } catch (e) {
                        console.error('Erreur lors de la suppression de l\'équipe', xhr);
                    }
                    
                    showToast(errorMessage, 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false);
                    $btn.html(originalText);
                    $('#delete-team-modal').removeClass('active');
                }
            });
        }
    });
    
    // Fonction pour ajouter un tag de membre
    function addMemberTag(memberName, memberId, memberFonction, isEdit) {
        const container = isEdit ? '#edit-selected-members-container' : '#selected-members-container';
        
        const memberTag = $(`
            <div class="member-tag" data-id="${memberId}">
                ${memberName}
              
                <i class="fas fa-times remove-member"></i>
            </div>
        `);
        
        $(container).append(memberTag);
        
        // Mettre à jour l'input caché et le compteur
        updateMembersData(isEdit);
    }
    
    // Fonction pour mettre à jour les données des membres
    function updateMembersData(isEdit) {
        const container = isEdit ? '#edit-selected-members-container' : '#selected-members-container';
        const input = isEdit ? '#edit-selected-members' : '#selected-members';
        const counter = isEdit ? '#edit-members-count' : '#members-count';
        
        const memberIds = [];
        $(container + ' .member-tag').each(function() {
            memberIds.push($(this).data('id'));
        });
        
        $(input).val(memberIds.join(','));
        $(counter).text(memberIds.length);
    }
    
    // Fonction pour réinitialiser le formulaire d'ajout
    function resetAddTeamForm() {
        $('#add-team-form')[0].reset();
        $('#selected-members-container').empty();
        $('#selected-members').val('');
        $('#members-count').text('0');
    }
    
   
});
</script>
