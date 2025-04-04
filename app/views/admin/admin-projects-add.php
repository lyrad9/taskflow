


<div class="form-project-container">
  <!-- Page Header -->
<div class="page-header">
        

     
        <div style="display: flex; align-items: center;">
        <div style="display: flex; align-items: center">
    <a href="/admin/projects" class="back-button" title="Retour au tableau de projets">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1>Ajouter un projet</h1>
    </div>
      
      <!--      
          
            <h1 style="color: var(--primary-color);">Ajouter un projet</h1> -->
           <!--  <p>Créez un nouveau projet en remplissant le formulaire ci-dessous. </p> -->
           
           
         
          </div>
       
         
     
    
</div>  

      <!-- Project Form -->
      <form id="projectForm" action="/api/projects/add-project.php" method="post" enctype="multipart/form-data">
        <input type="hidden" id="clientTab" name="client_tab" value="existing">
        
        <div class="project-form-grid">
          <!-- Left Column -->
          <div class="form-column">
            <!-- Project Information Card -->
            <div class="card form-card shadow mb-4">
              <div class="card-header">
                Informations du projet
              </div>
              <div class="card-body">
                <div class="form-group">
                  <label for="name" class="form-label required">Nom du projet</label>
                  <input type="text" id="name" name="name" class="form-control">
                </div>
                
                <div class="form-group">
                  <label for="description" class="form-label required">Description</label>
                  <textarea id="description" name="description" class="form-control" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                  <label for="project_type" class="form-label required">Type de projet</label>
                  <select id="project_type" name="project_type" class="form-select">
                    <option value="" selected disabled>Sélectionnez un type...</option>
                    <?php foreach (Constants::PROJECT_TYPES as $key => $value): ?>
                      <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                
                <div class="form-group">
                  <label for="budget" class="form-label required">Budget (FCFA)</label>
                  <input type="text" id="budget" name="budget" class="form-control">
                <!--   <small class="form-text">Entrez le montant sans séparateurs de milliers</small> -->
                </div>
              </div>
            </div>
            
            <!-- Client Card -->
            <div class="card form-card shadow mb-4">
              <div class="card-header">
                Client associé
              </div>
              <div class="card-body">
                <div class="client-select-container">
                  <div class="client-select-tabs">
                    <div class="client-select-tab active" data-tab="existing">Client existant</div>
                    <div class="client-select-tab" data-tab="new">Nouveau client</div>
                  </div>
                  
                  <div class="client-select-content">
                    <!-- Existing Client -->
                    <div id="existingClient" class="tab-pane active">
                      <div class="client-search-container">
                        <span class="client-search-icon">
                          <i class="fas fa-search"></i>
                        </span>
                        <input type="text" id="clientSearch" class="form-control" placeholder="Rechercher un client par nom ou numéro...">
                        <input type="hidden" id="clientId" name="client_id" required>
                      </div>
                      
                      <div id="clientList" class="client-datalist"></div>
                    </div>
                    
                    <!-- New Client -->
                    <div id="newClient" class="tab-pane" style="display: none;">
                      <div class="client-form-inline">
                        <div class="form-group">
                          <label for="clientFirstName" class="form-label required">Prénom</label>
                          <input type="text" id="clientFirstName" name="client_first_name" class="form-control">
                        </div>
                        
                        <div class="form-group">
                          <label for="clientLastName" class="form-label required">Nom</label>
                          <input type="text" id="clientLastName" name="client_last_name" class="form-control">
                        </div>
                      </div>
                      
                      <div class="client-form-inline">
                        <div class="form-group">
                          <label for="clientCity" class="form-label required">Ville</label>
                          <input type="text" id="clientCity" name="client_city" class="form-control">
                        </div>
                        
                        <div class="form-group">
                          <label for="clientResidence" class="form-label">Résidence</label>
                          <input type="text" id="clientResidence" name="client_residence" class="form-control">
                        </div>
                      </div>
                      
                      <div class="form-group">
                        <label for="clientPhoneNumber" class="form-label required">Numéro de téléphone</label>
                        <input type="tel" id="clientPhoneNumber" name="client_phone_number" class="form-control">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Right Column -->
          <div class="form-column">
            <!-- Project Dates Card -->
            <div class="card form-card shadow mb-4">
              <div class="card-header">
                Dates du projet
              </div>
              <div class="card-body">
                <div class="date-range-container">
                  <div class="form-group">
                    <label for="scheduledStartDate" class="form-label">Date de début prévue</label>
                    <input type="date" id="scheduledStartDate" name="scheduled_start_date" class="form-control date-input">
                  </div>
                  
                  <div class="form-group">
                    <label for="scheduledEndDate" class="form-label">Date de fin prévue</label>
                    <input type="date" id="scheduledEndDate" name="scheduled_end_date" class="form-control date-input">
                    <div id="dateRangeError" class="invalid-feedback" style="display: none;">La date de fin doit être postérieure à la date de début</div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Documents Card -->
            <div class="card form-card shadow mb-4">
              <div class="card-header">
                Documents relatifs au projet
              </div>
              <div class="card-body">
                <div class="file-upload">
                  <input type="file" id="projectDocuments" name="documents[]" class="file-upload-input" multiple accept=".pdf">
                  <label for="projectDocuments" class="file-upload-label">
                    <i style="margin-right: 5px;" class="fas fa-cloud-upload-alt mr-2"></i>
                    <span id="fileUploadText">Choisir des fichiers</span>
                  </label>
                  <div class="file-upload-info">
                    Formats acceptés: PDF uniquement (max. 7 Mo au total)
                  </div>
                </div>
                
                <div id="uploadedFiles" class="uploaded-files"></div>
              </div>
            </div>
            
            <!-- Team Card -->
            <div class="card form-card shadow mb-4">
              <div class="card-header">
                Équipe assignée
              </div>
              <div class="card-body">
                <div class="select-search-container">
                  <input type="text" id="teamSearch" class="select-search-input" placeholder="Rechercher une équipe...">
                  <input type="hidden" id="teamId" name="team_id" required>
                  <div id="teamDropdown" class="select-search-dropdown"></div>
                </div>
                
                <div id="teamMembersContainer" class="team-members-container" style="display: none;">
                  <label class="form-label">Membres de l'équipe</label>
                  <div id="teamMembersList" class="team-member-list"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Form Actions -->
        <div class="form-actions">
          <button type="button" id="cancelButton" class="btn btn-secondary">Annuler</button>
          <button id="submitButton" type="submit" class="btn btn-primary btn-icon">
            <i class="fas fa-save"></i> Ajouter le projet
          </button>
        </div>
      </form>
</div>      
                  
        <!-- Notification Toast -->
        <div id="toast" class="toast">
            <div class="toast-icon">
              <i class=""></i>
            </div>
            <div class="toast-message"></div>
          </div>
          
  
  <!-- JavaScript Libraries -->
  <script src="/public/assets/js/jquery.min.js"></script>

  <script src="/public/assets/js/toast.js"></script>
  <script src="/public/assets/js/add-project.js"></script>
