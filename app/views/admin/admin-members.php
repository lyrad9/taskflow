<?php
require_once "app/helpers/Constants.php";
?>
<div class="members-container">
    <div class="page-header">
        <div style="display: flex; align-items: center">
            <a href="/admin/dashboard" class="back-button" title="Retour au tableau de bord">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1>Gestion des membres</h1>
        </div>
        
        <!-- Bouton d'ajout de membre -->
        <button class="add-record-btn" id="add-member-btn">
            <i class="fas fa-plus"></i>
            Nouveau membre
        </button>  
    </div>
    
    <!-- Outils de gestion: recherche, filtres, ajout -->
    <div style="margin-bottom: 20px;" class="members-tools">
        <div style="display: flex; gap: 5px;" class="members-tools-left">
            <!-- Barre de recherche -->
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" id="search" placeholder="Rechercher un membre..." 
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </div>
            
            <!-- Bouton d'actions groupées (visible seulement quand des membres sont sélectionnés) -->
             
            <div  class="bulk-actions" id="bulk-actions">
                <!-- Bouton de suppression en masse -->
                <button style="color: var(--text-muted);" class="bulk-actions-btn" id="bulk-delete-btn">
                    <i class="fas fa-trash"></i>
                    Supprimer
                </button>
                
                <!-- Bouton de modification du rôle -->
                <div class="dropdown-actions">
                    <button style="color: var(--text-muted);" class="bulk-actions-btn" id="bulk-role-btn">
                        <i class="fas fa-user-shield"></i>
                        Modifier rôle
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="bulk-actions-menu" id="bulk-role-menu">
                        <div class="dropdown-item-action bulk-role-option" data-role="ADMIN">
                            <i class="fas fa-user-tie"></i>
                            Administrateur
                        </div>
                        <div class="dropdown-item-action bulk-role-option" data-role="SUPER_ADMIN">
                            <i class="fas fa-user-shield"></i>
                            Super Administrateur
                        </div>
                    </div>
                </div>
            </div>
        </div>
     
        <!-- Filtre par rôle -->
      <!--   <div style="display: flex; flex-direction: column; gap: 5px;" class="role-filter type-filter">
            <span style="color: var(--text-muted);">
                Sélectionner le rôle
            </span>
            <select style="color: var(--text-muted);" id="role-filter">
                <option value="all">Tous les rôles</option>
                <option value="USER" <?php echo (isset($_GET['role']) && $_GET['role'] === 'USER') ? 'selected' : ''; ?>>Membre</option>
                <option value="ADMIN" <?php echo (isset($_GET['role']) && $_GET['role'] === 'ADMIN') ? 'selected' : ''; ?>>Administrateur</option>
                <option value="SUPER_ADMIN" <?php echo (isset($_GET['role']) && $_GET['role'] === 'SUPER_ADMIN') ? 'selected' : ''; ?>>Super Administrateur</option>
            </select>
        </div> -->
    </div>
    
    <!-- Card contenant la liste des membres -->
    <div class="card">
        <div class="card-header">
            <h2>Liste des membres</h2>
        </div>
        <div class="card-body">
            <!-- Tableau des membres -->
            <div class="table-container">
                <table class="members-table">
                    <thead>
                        <tr>
                            <th class="checkbox-cell">
                                <div class="checkbox-container">
                                    <input type="checkbox" id="select-all-records">
                                </div>
                            </th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Fonction</th>
                            <th class="actions-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="members-list">
                        <?php if (!empty($data["members"])): ?>
                            <?php foreach ($data["members"] as $member): ?>
                                <tr data-member-id="<?php echo $member['id']; ?>">
                                    <td class="checkbox-cell">
                                        <div class="checkbox-container">
                                            <input type="checkbox" class="d-checkbox" data-id="<?php echo $member['id']; ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="member-info">
                                            <span class="member-name"><?php echo htmlspecialchars($member['first_name']); ?></span>
                                            <span class="member-lastname"><?php echo htmlspecialchars($member['last_name'] ?? ''); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="member-email"><?php echo htmlspecialchars($member['email']); ?></span>
                                    </td>
                                    <td>
                                        <span class="member-fonction"><?php echo htmlspecialchars($member['fonction'] ?? 'Non renseigné'); ?></span>
                                    </td>
                                    <td class="actions-cell">
                                        <a href="/admin/member/<?php echo $member['id']; ?>" class="action-btn view-btn" title="Voir les détails">
                                            <i class="fas fa-link"></i>
                                        </a>
                                        <div class="dropdown-actions">
                                            <button class="action-btn dropdown-toggle" title="Plus d'actions">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu-actions">
                                                <div class="dropdown-item-action delete delete-member" data-id="<?php echo $member['id']; ?>">
                                                    <i class="fas fa-trash"></i> Supprimer
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 30px;">
                                    <p>Aucun membre trouvé.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if (isset($data["totalPages"]) && $data["totalPages"] > 1): ?>
                <div class="pagination">
                    <a href="?page=1<?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['role']) ? '&role=' . htmlspecialchars($_GET['role']) : ''; ?>" 
                       class="pagination-btn <?php echo $data["currentPage"] == 1 ? 'disabled' : ''; ?>">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                    
                    <a href="?page=<?php echo max(1, $data["currentPage"] - 1); ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['role']) ? '&role=' . htmlspecialchars($_GET['role']) : ''; ?>" 
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
                        <a href="?page=<?php echo $i; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['role']) ? '&role=' . htmlspecialchars($_GET['role']) : ''; ?>" 
                           class="pagination-btn <?php echo $i == $data["currentPage"] ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <a href="?page=<?php echo min($data["totalPages"], $data["currentPage"] + 1); ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['role']) ? '&role=' . htmlspecialchars($_GET['role']) : ''; ?>" 
                       class="pagination-btn <?php echo $data["currentPage"] == $data["totalPages"] ? 'disabled' : ''; ?>">
                        <i class="fas fa-angle-right"></i>
                    </a>
                    
                    <a href="?page=<?php echo $data["totalPages"]; ?><?php echo isset($_GET['search']) ? '&search=' . htmlspecialchars($_GET['search']) : ''; ?><?php echo isset($_GET['role']) ? '&role=' . htmlspecialchars($_GET['role']) : ''; ?>" 
                       class="pagination-btn <?php echo $data["currentPage"] == $data["totalPages"] ? 'disabled' : ''; ?>">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal d'ajout de membre -->
<div class="modal-overlay" id="add-member-modal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">Ajouter un nouveau membre</div>
            <div class="modal-description">Remplissez les informations du nouveau membre.</div>
        </div>
        <div class="modal-body">
            <form id="add-member-form">
                <div style="margin-bottom: 15px;">
                    <label for="first_name">Prénom</label>
                    <input type="text" id="first_name" name="first_name" placeholder="Prénom" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="last_name">Nom</label>
                    <input type="text" id="last_name" name="last_name" placeholder="Nom" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Adresse email" required>
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="fonction">Fonction</label>
                    <input type="text" id="fonction" name="fonction" placeholder="Fonction (ex: Dev Laravel)">
                </div>
                <input type="hidden" name="role" value="USER">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancel-add">Annuler</button>
            <button class="btn btn-primary" id="confirm-add">Enregistrer</button>
        </div>
        <button class="modal-close" id="close-add-modal">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal-overlay" id="delete-modal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">Supprimer le membre</div>
            <div class="modal-description">Êtes-vous sûr de vouloir supprimer ce membre ?</div>
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

<!-- Modal de confirmation de suppression en masse -->
<div class="modal-overlay" id="bulk-delete-modal">
    <div class="modal">
        <div class="modal-header">
            <div class="modal-title">Supprimer plusieurs membres</div>
            <div class="modal-description">Êtes-vous sûr de vouloir supprimer les membres sélectionnés ?</div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" id="cancel-bulk-delete">Annuler</button>
            <button class="btn btn-danger" id="confirm-bulk-delete">Supprimer</button>
        </div>
        <button class="modal-close" id="close-bulk-delete-modal">
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
    // Gestion des menus déroulants
    $(document).on('click', function(e) {
        // Fermer tous les menus si on clique en dehors
        if (!$(e.target).closest('.dropdown-toggle, .dropdown-menu-actions').length) {
            $('.dropdown-menu-actions').removeClass('active');
        }
        
        if (!$(e.target).closest('#bulk-role-btn, #bulk-role-menu').length) {
            $('#bulk-role-menu').removeClass('active');
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
    
    $('#bulk-role-btn').on('click', function(e) {
        e.stopPropagation();
        $('#bulk-role-menu').toggleClass('active');
    });
    
    // Gestion des modales
    $('#add-member-btn').on('click', function() {
        $('#add-member-modal').addClass('active');
    });
    
    $('#close-add-modal, #cancel-add').on('click', function() {
        $('#add-member-modal').removeClass('active');
    });
    
    $('#close-delete-modal, #cancel-delete').on('click', function() {
        $('#delete-modal').removeClass('active');
    });
    
    $('#close-bulk-delete-modal, #cancel-bulk-delete').on('click', function() {
        $('#bulk-delete-modal').removeClass('active');
    });
    
    // Affichage de la modale de suppression pour un membre
    $(document).on('click', '.delete-member', function() {
        const memberId = $(this).data('id');
        sessionStorage.setItem("memberId", memberId);
        $('#delete-modal').addClass('active');
    });
    
    // Bulk delete button
    $('#bulk-delete-btn').on('click', function() {
        const selectedCount = $('.d-checkbox:checked').length;
        if (selectedCount > 0) {
            $('#bulk-delete-modal').addClass('active');
        } else {
            showToast('Veuillez sélectionner au moins un membre', 'error');
        }
    });
    
    // Modifier le rôle de plusieurs membres
    $('.bulk-role-option').on('click', function() {
        const newRole = $(this).data('role');
        const selectedMembers = getSelectedMemberIds();
        
        if (selectedMembers.length > 0) {
            $.ajax({
                url: '/app/api/members/update-role.php',
                type: 'POST',
                data: {
                    csrf_token: '<?php echo $_SESSION['csrf_token']; ?>',
                    member_ids: selectedMembers,
                    role: newRole
                },
                success: function(response) {
                    // Fermer le menu
                    $('#bulk-role-menu').removeClass('active');
                    
                    // Retirer les membres de la liste (car ils ne sont plus USER)
                    selectedMembers.forEach(function(id) {
                        $(`tr[data-member-id="${id}"]`).fadeOut(300, function() {
                            $(this).remove();
                        });
                    });
                    
                    // Décocher toutes les cases
                    $('.d-checkbox, #select-all-records').prop('checked', false);
                    
                    // Cacher le bouton d'actions
                    updateBulkActionsVisibility();
                    
                    // Afficher une notification de succès
                    showToast('Rôle mis à jour pour ' + selectedMembers.length + ' membre(s)', 'success');
                    
                    // Vérifier si le tableau est vide
                    if ($('#members-list tr').length === 0) {
                        $('#members-list').html('<tr><td colspan="5" style="text-align: center; padding: 30px;"><p>Aucun membre trouvé.</p></td></tr>');
                    }
                },
                error: function(xhr) {
                    // Afficher une notification d'erreur
                    const response = JSON.parse(xhr.responseText);
                    showToast(response.error || 'Une erreur est survenue', 'error');
                }
            });
        }
    });
    
    // Confirmer l'ajout d'un membre
    $('#confirm-add').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        
        // Vérifier les champs obligatoires
        const first_name = $('#first_name').val().trim();
        const last_name = $('#last_name').val().trim();
        const email = $('#email').val().trim();
        
        if (!first_name || !last_name || !email) {
            showToast('Veuillez remplir tous les champs obligatoires', 'error');
            return;
        }
        
        // Désactiver le bouton pour prévenir les doubles clics
        $btn.prop('disabled', true);
        $btn.html('<i class="fa-spin fa-solid fa-circle-notch"></i>');
        
        // Envoyer la requête AJAX pour ajouter le membre
        $.ajax({
            url: '/app/api/members/add-member.php',
            type: 'POST',
            data: $('#add-member-form').serialize(),
            success: function(response) {
                // Fermer la modal
                $('#add-member-modal').removeClass('active');
                
                // Réinitialiser le formulaire
                $('#add-member-form')[0].reset();
                
                // Afficher une notification de succès
                showToast('Membre ajouté avec succès', 'success');
                
                // Recharger la liste des membres
                window.location.reload();
            },
            error: function(xhr) {
                // Afficher une notification d'erreur
                const response = JSON.parse(xhr.responseText);
                showToast(response.error || 'Une erreur est survenue', 'error');
            },
            complete: function() {
                // Réactiver le bouton
                $btn.prop('disabled', false);
                $btn.html(originalText);
            }
        });
    });
    
    // Confirmer la suppression d'un membre
    $('#confirm-delete').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        
        const memberId = sessionStorage.getItem("memberId");
        
        if (memberId) {
            // Désactiver le bouton pour prévenir les doubles clics
            $btn.prop('disabled', true);
            $btn.html('<i class="fa-spin fa-solid fa-circle-notch"></i>');
            
            // Envoyer la requête AJAX pour supprimer le membre
            $.ajax({
                url: '/app/api/members/delete-member.php',
                type: 'POST',
                data: {
                    csrf_token: '<?php echo $_SESSION['csrf_token']; ?>',
                    memberId: memberId,
                    userRole: '<?php echo $data['user']['role']; ?>'
                },
                success: function(xhr) {
                  
                    // Fermer la modal
                    $('#delete-modal').removeClass('active');
                    
                    // Supprimer la ligne du tableau
                    $(`tr[data-member-id="${memberId}"]`)
                    .fadeOut(300, function() {
                        $(this).remove();
                        
                        // Afficher une notification de succès
                      /*   showToast('Membre supprimé avec succès', 'success'); */
                      
                        showToast("membre ajouté avec succès", 'success');
                     /*    showToast(response.email_sent, 'success'); */
                        
                        // Si le tableau est vide, ajouter un message
                        if ($('#members-list tr').length === 0) {
                            $('#members-list').html('<tr><td colspan="5" style="text-align: center; padding: 30px;"><p>Aucun membre trouvé.</p></td></tr>');
                        }
                    });
                    
                    sessionStorage.removeItem("memberId");
                },
                error: function(xhr) {
                    const response = JSON.parse(xhr.responseText);
                    // Afficher une notification d'erreur
                    showToast(response.error || 'Une erreur est survenue', 'error');
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
    
    // Confirmer la suppression en masse
    $('#confirm-bulk-delete').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        
        const selectedMembers = getSelectedMemberIds();
        
        if (selectedMembers.length > 0) {
            // Désactiver le bouton pour prévenir les doubles clics
            $btn.prop('disabled', true);
            $btn.html('<i class="fa-spin fa-solid fa-circle-notch"></i>');
            
            // Envoyer la requête AJAX pour supprimer les membres
            $.ajax({
                url: '/app/api/members/delete-members.php',
                type: 'POST',
                data: {
                    csrf_token: '<?php echo $_SESSION['csrf_token']; ?>',
                    member_ids: selectedMembers,
                    userRole: '<?php echo $data['user']['role']; ?>'
                },
                success: function(response) {
                    // Fermer la modal
                    $('#bulk-delete-modal').removeClass('active');
                    
                    // Supprimer les lignes du tableau
                    selectedMembers.forEach(function(id) {
                        $(`tr[data-member-id="${id}"]`).fadeOut(300, function() {
                            $(this).remove();
                        });
                    });
                    
                    // Décocher toutes les cases
                    $('.d-checkbox, #select-all-records').prop('checked', false);
                    
                    // Cacher le bouton d'actions
                    updateBulkActionsVisibility();
                    
                    // Afficher une notification de succès
                    showToast('Membre(s) supprimé(s) avec succès', 'success');
                    
                    // Si le tableau est vide, ajouter un message
                    if ($('#members-list tr').length === 0) {
                        $('#members-list').html('<tr><td colspan="5" style="text-align: center; padding: 30px;"><p>Aucun membre trouvé.</p></td></tr>');
                    }
                },
                error: function(xhr) {
                    const response = JSON.parse(xhr.responseText);
                    // Afficher une notification d'erreur
                    showToast(response.error || 'Une erreur est survenue', 'error');
                    $('#bulk-delete-modal').removeClass('active');
                },
                complete: function() {
                    // Réactiver le bouton
                    $btn.prop('disabled', false);
                    $btn.html(originalText);
                }
            });
        }
    });
    
    // Recherche en temps réel
    $('#search').on('input', function() {
        
        const searchValue = $(this).val().trim();
        console.log("searchValue", searchValue);
        
        if (searchValue.length > 2 || searchValue.length === 0) {
            $.ajax({
                url: '/app/api/members/search.php',
                method: 'GET',
                data: {
                    search: searchValue,
                    role: $('#role-filter').val()
                },
                success: function(response) {
                    console.log("response.members", response.members);
                    updateMembersTable(response.members);
                }
            });
        }
    });
    
    // Filtre par rôle
    $('#role-filter').on('change', function() {
        const roleValue = $(this).val();
        
        $.ajax({
            url: '/app/api/members/search.php',
            method: 'GET',
            data: {
                search: $('#search').val().trim(),
                role: roleValue
            },
            success: function(response) {
                updateMembersTable(response.members);
            }
        });
    });
    
    // Fonction pour mettre à jour le tableau des membres
    function updateMembersTable(members) {
        const tableBody = $('#members-list');
        tableBody.empty();
        
        if (members.length > 0) {
            members.forEach(function(member) {
                const row = `
                <tr data-member-id="${member.id}">
                    <td class="checkbox-cell">
                        <div class="checkbox-container">
                            <input type="checkbox" class="d-checkbox" data-id="${member.id}">
                        </div>
                    </td>
                    <td>
                        <div class="member-info">
                            <span class="member-name">${member.first_name}</span>
                            <span class="member-lastname">${member.last_name || ''}</span>
                        </div>
                    </td>
                    <td>
                        <span class="member-email">${member.email}</span>
                    </td>
                    <td>
                        <span class="member-fonction">${member.fonction || 'Non renseigné'}</span>
                    </td>
                    <td class="actions-cell">
                        <a href="/admin/member/${member.id}" class="action-btn view-btn" title="Voir les détails">
                            <i class="fas fa-link"></i>
                        </a>
                        <div class="dropdown-actions">
                            <button class="action-btn dropdown-toggle" title="Plus d'actions">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu-actions">
                                <div class="dropdown-item-action delete delete-member" data-id="${member.id}">
                                    <i class="fas fa-trash"></i> Supprimer
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                `;
                tableBody.append(row);
            });
        } else {
            tableBody.html('<tr><td colspan="5" style="text-align: center; padding: 30px;"><p>Aucun membre trouvé.</p></td></tr>');
        }
        
        // Réinitialiser les actions groupées
        $('.d-checkbox, #select-all-records').prop('checked', false);
        updateBulkActionsVisibility();
    }
    
    // Gestion des cases à cocher
    $('#select-all-records').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.d-checkbox').prop('checked', isChecked);
        updateBulkActionsVisibility();
    });
    
    $(document).on('change', '.d-checkbox', function() {
        updateBulkActionsVisibility();
        
        // Si tous les checkboxes sont cochés, cocher aussi "select-all"
        if ($('.d-checkbox:checked').length === $('.d-checkbox').length) {
            $('#select-all-records').prop('checked', true);
        } else {
            $('#select-all-records').prop('checked', false);
        }
    });
    
    // Récupérer IDs des membres sélectionnés
    function getSelectedMemberIds() {
        const selectedIds = [];
        $('.d-checkbox:checked').each(function() {
            selectedIds.push($(this).data('id'));
        });
        return selectedIds;
    }
    
    // Afficher/masquer les boutons d'actions groupées
    function updateBulkActionsVisibility() {
        if ($('.d-checkbox:checked').length > 0) {
            $('#bulk-actions').addClass('active');
        } else {
            $('#bulk-actions').removeClass('active');
        }
    }
});
</script>

<style>
.btn-primary {
    background-color: var(--accent-color);
    border: 1px solid var(--accent-color);
    color: white;
}

.btn-primary:hover {
    background-color: #3050d8;
}

.member-info {
    display: flex;
    flex-direction: column;
}

.member-name {
    font-weight: 500;
}

.member-lastname {
    color: var(--text-muted);
    font-size: 0.9rem;
}

.member-email {
    color: var(--text-color);
}

.member-fonction {
    color: var(--text-muted);
    font-size: 0.9rem;
}
</style>
