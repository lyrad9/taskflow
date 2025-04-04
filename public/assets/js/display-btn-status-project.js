$(document).ready(function () {
  // Sélectionner/Désélectionner tous les projets
  $("#select-all-records").on("change", function () {
    const isChecked = $(this).prop("checked");
    $(".d-checkbox").prop("checked", isChecked);
    updateBulkActionsVisibility();
  });

  // Mise à jour de la visibilité des actions groupées(change the status of the projects)
  $(".d-checkbox").on("change", function () {
    updateBulkActionsVisibility();
  });
});

// Fonctions utilitaires

// Mettre à jour la visibilité du bouton d'actions groupées
function updateBulkActionsVisibility() {
  const hasCheckedProjects = $(".d-checkbox:checked").length > 0;
  if (hasCheckedProjects) {
    $("#bulk-actions").addClass("active");
  } else {
    $("#bulk-actions").removeClass("active");
  }
}
