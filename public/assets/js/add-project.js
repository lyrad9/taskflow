/**
 * JavaScript pour la page d'ajout de projet
 */
$(document).ready(function () {
  // Gestion des onglets client
  $(".client-select-tab").on("click", function () {
    // Supprimer la classe active de tous les onglets
    $(".client-select-tab").removeClass("active");

    // Ajouter la classe active à l'onglet cliqué
    $(this).addClass("active");

    // Masquer tous les contenus d'onglet
    $(".tab-pane").hide();

    // Afficher le contenu correspondant à l'onglet
    const tabId = $(this).data("tab");
    if (tabId === "existing") {
      $("#existingClient").show();
      // Ajouter l'attribut required au select de client
      $("#clientId").prop("required", true);
      // Supprimer l'attribut required des champs du nouveau client
      $(
        "#clientFirstName, #clientLastName, #clientCity, #clientPhoneNumber"
      ).prop("required", false);
    } else {
      $("#newClient").show();
      // Supprimer l'attribut required du select de client
      $("#clientId").prop("required", false);
      // Ajouter l'attribut required aux champs du nouveau client
      $(
        "#clientFirstName, #clientLastName, #clientCity, #clientPhoneNumber"
      ).prop("required", true);
    }

    // Mettre à jour le champ caché indiquant l'onglet actif
    $("#clientTab").val(tabId);
  });

  // Validation des dates
  function validateDateRange() {
    const startDate = new Date($("#scheduledStartDate").val());
    const endDate = new Date($("#scheduledEndDate").val());

    if (startDate && endDate && endDate < startDate) {
      $("#scheduledEndDate").addClass("is-invalid");
      $("#dateRangeError").show();
      return false;
    } else {
      $("#scheduledEndDate").removeClass("is-invalid");
      $("#dateRangeError").hide();
      return true;
    }
  }

  $("#scheduledStartDate, #scheduledEndDate").on("change", validateDateRange);

  // Gestion de la sélection d'équipe
  $("#teamId").on("change", function () {
    const teamId = $(this).val();
    if (teamId) {
      // Charger les membres de l'équipe via AJAX
      $.ajax({
        url: "/app/api/teams/get-members.php",
        type: "POST",
        data: {
          team_id: teamId,
          csrf_token: '<?php echo $_SESSION["csrf_token"]?>',
        },
        dataType: "json",
        success: function (response) {
          if (response.success) {
            const members = response.data;
            $("#teamMembersContainer").show();

            // Vider et remplir la liste des membres
            const membersList = $("#teamMembersList");
            membersList.empty();

            if (members.length === 0) {
              membersList.append(
                '<div class="text-center">Aucun membre dans cette équipe</div>'
              );
            } else {
              members.forEach(function (member) {
                const memberHtml = `
                                    <div class="team-member-item">
                                        <div class="team-member-avatar">
                                            <img src="${member.profile_picture || "/public/static/defaultUser.jpg"}" alt="${member.first_name} ${member.last_name}">
                                        </div>
                                        <div class="team-member-info">
                                            <div class="team-member-name">${member.first_name} ${member.last_name}</div>
                                            <div class="team-member-role">${member.email}</div>
                                        </div>
                                    </div>
                                `;
                membersList.append(memberHtml);
              });
            }
          } else {
            showToast(response.error, "error");
          }
        },
        error: function () {
          showToast(
            "Erreur lors du chargement des membres de l'équipe",
            "error"
          );
        },
      });
    } else {
      $("#teamMembersContainer").hide();
    }
  });

  // Search dans le select des clients
  $("#clientSearch").on("input", function () {
    const searchTerm = $(this).val().toLowerCase();
    const clientList = $("#clientList");

    if (searchTerm.length > 0) {
      // Afficher la liste des clients
      clientList.addClass("active");

      // Charger les clients via AJAX
      $.ajax({
        url: "/app/api/clients/search.php",
        type: "POST",
        data: {
          search: searchTerm,
          csrf_token: '<?php echo $_SESSION["csrf_token"]?>',
        },
        dataType: "json",
        success: function (response) {
          if (response.success) {
            const clients = response.data;
            clientList.empty();

            if (clients.length === 0) {
              clientList.append(
                '<div class="client-datalist-option">Aucun client trouvé</div>'
              );
            } else {
              clients.forEach(function (client) {
                const clientHtml = `
                                    <div class="client-datalist-option" data-id="${client.id}">
                                        <span class="client-option-name">${client.first_name} ${client.last_name}</span>
                                        <span class="client-option-phone">${client.phone_number}</span>
                                    </div>
                                `;
                clientList.append(clientHtml);
              });

              // Gérer le clic sur une option
              $(".client-datalist-option").on("click", function () {
                const clientId = $(this).data("id");
                const clientName = $(this).find(".client-option-name").text();
                const clientPhone = $(this).find(".client-option-phone").text();

                $("#clientId").val(clientId);
                $("#clientSearch").val(clientName + " - " + clientPhone);
                clientList.removeClass("active");
              });
            }
          } else {
            showToast(response.error, "error");
          }
        },
        error: function () {
          showToast("Erreur lors de la recherche de clients", "error");
        },
      });
    } else {
      // Masquer la liste si le champ est vide
      clientList.removeClass("active");
    }
  });

  // Masquer la liste des clients quand on clique ailleurs
  $(document).on("click", function (e) {
    if (!$(e.target).closest(".client-search-container").length) {
      $("#clientList").removeClass("active");
    }
  });

  // Gestion du chargement et prévisualisation des fichiers
  $("#projectDocuments").on("change", function () {
    const files = Array.from(this.files);
    const fileCount = files.length;
    const fileUploadText = $("#fileUploadText");
    const uploadedFiles = $("#uploadedFiles");

    // Mettre à jour le texte du bouton
    fileUploadText.text(
      fileCount > 0
        ? `${fileCount} fichier(s) sélectionné(s)`
        : "Choisir des fichiers"
    );

    // Vider la zone de prévisualisation
    uploadedFiles.empty();

    if (fileCount > 0) {
      // Valider les extensions de fichier (PDF uniquement)
      const invalidFiles = files.filter((file) => {
        const extension = file.name.split(".").pop().toLowerCase();
        return extension !== "pdf";
      });

      if (invalidFiles.length > 0) {
        showToast("Seuls les fichiers PDF sont autorisés", "error");
        this.value = ""; // Vider la sélection
        fileUploadText.text("Choisir des fichiers");
        return;
      }

      // Calculer la taille totale
      const totalSize = files.reduce((acc, file) => acc + file.size, 0);
      const maxSize = 7 * 1024 * 1024; // 7 Mo

      if (totalSize > maxSize) {
        showToast(
          "La taille totale des fichiers ne doit pas dépasser 7 Mo",
          "error"
        );
        this.value = ""; // Vider la sélection
        fileUploadText.text("Choisir des fichiers");
        return;
      }

      // Afficher les fichiers
      files.forEach((file, index) => {
        const fileSize = (file.size / 1024).toFixed(2);
        const fileItem = `
                    <div class="uploaded-file">
                        <div class="uploaded-file-icon">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <div class="uploaded-file-info">
                            <div class="uploaded-file-name">${file.name}</div>
                            <div class="uploaded-file-size">${fileSize} KB</div>
                        </div>
                        <div class="uploaded-file-remove" data-index="${index}">
                            <i class="fas fa-times"></i>
                        </div>
                    </div>
                `;
        uploadedFiles.append(fileItem);
      });

      // Gestion de la suppression d'un fichier
      $(".uploaded-file-remove").on("click", function () {
        const index = parseInt($(this).data("index"));
        removeFile(index);
      });
    }
  });

  // Fonction pour supprimer un fichier de la liste
  function removeFile(index) {
    const input = document.getElementById("projectDocuments");
    const dt = new DataTransfer();

    // Recréer la liste de fichiers sans celui à supprimer
    Array.from(input.files).forEach((file, i) => {
      if (i !== index) dt.items.add(file);
    });

    // Mettre à jour l'input
    input.files = dt.files;

    // Déclencher l'événement change pour mettre à jour la prévisualisation
    const event = new Event("change");
    input.dispatchEvent(event);
  }

  // Gérer la soumission du formulaire via AJAX
  $("#projectForm").on("submit", function (e) {
    e.preventDefault();

    // Valider les dates avant de soumettre
    if (!validateDateRange()) {
      return false;
    }

    // Récupérer le bouton de soumission et sauvegarder son texte original
    const $submitBtn = $("#submitButton");
    const originalText = $submitBtn.html();

    // Désactiver le bouton et afficher le loader
    $submitBtn.prop("disabled", true);
    $submitBtn.html(
      '<i class="fa-spin fa-solid fa-circle-notch"></i> Création en cours...'
    );

    // Créer un objet FormData pour gérer les fichiers
    const formData = new FormData(this);
    formData.append("client_tab", $(".client-select-tab.active").data("tab"));

    // Soumettre le formulaire via AJAX
    $.ajax({
      url: "/app/api/projects/add-project.php",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showToast(response.message, "success");
          // Rediriger vers la liste des projets après 2 secondes
          setTimeout(function () {
            window.location.href = "/admin/projects";
          }, 2000);
        } else {
          showToast(response.error, "error");
        }
      },
      error: function () {
        showToast("Erreur lors de la création du projet", "error");
      },
      complete: function () {
        // Réactiver le bouton
        $submitBtn.prop("disabled", false);
        $submitBtn.html(originalText);
      },
    });
  });

  // Gérer le bouton d'annulation
  $("#cancelButton").on("click", function (e) {
    e.preventDefault();
    // Vider le formulaire
    $("#projectForm")[0].reset();
    // Vider la prévisualisation des fichiers
    $("#uploadedFiles").empty();
    $("#fileUploadText").text("Choisir des fichiers");
    // Masquer la liste des membres
    $("#teamMembersContainer").hide();
  });

  // Ajouter une icône de calendrier aux inputs de date
  /*   $(".date-input").each(function () {
    $(this).wrap('<div class="date-input-container"></div>');
  });
 */
  // Pour les selects avec recherche (équipes)
  $("#teamSearch").on("input", function () {
    const searchTerm = $(this).val().toLowerCase();
    const teamDropdown = $("#teamDropdown");
    const teamMembersContainer = $("#teamMembersContainer");

    if (searchTerm.length > 0) {
      // Afficher la liste des équipes
      teamDropdown.addClass("active");

      // Charger les équipes via AJAX
      $.ajax({
        url: "/app/api/teams/search.php",
        type: "POST",
        data: {
          search: searchTerm,
        },
        dataType: "json",
        success: function (response) {
          if (response.success) {
            const teams = response.data;
            teamDropdown.empty();

            if (teams.length === 0) {
              teamDropdown.append(
                '<div class="select-search-option">Aucune équipe trouvée</div>'
              );
            } else {
              teams.forEach(function (team) {
                const teamHtml = `
                                    <div class="select-search-option" data-id="${team.id}">
                                        <span class="select-search-option-highlight">${team.name}</span>
                                        <span class="text-muted"> (${team.members_count} membres)</span>
                                    </div>
                                `;
                teamDropdown.append(teamHtml);
              });

              // Gérer le clic sur une option
              $(".select-search-option").on("click", function () {
                const teamId = $(this).data("id");
                const teamName = $(this)
                  .find(".select-search-option-highlight")
                  .text();

                $("#teamId").val(teamId);
                $("#teamSearch").val(teamName);
                teamDropdown.removeClass("active");

                // Déclencher l'événement change pour charger les membres
                $("#teamId").trigger("change");
              });
            }
          } else {
            showToast(response.error, "error");
          }
        },
        error: function () {
          showToast("Erreur lors de la recherche d'équipes", "error");
        },
      });
    } else {
      // Masquer la liste si le champ est vide
      teamDropdown.removeClass("active");
      teamMembersContainer.hide();
    }
  });

  // Masquer la liste des équipes quand on clique ailleurs
  $(document).on("click", function (e) {
    if (!$(e.target).closest(".select-search-container").length) {
      $("#teamDropdown").removeClass("active");
    }
  });
});
