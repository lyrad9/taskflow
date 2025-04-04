$(document).ready(function () {
  // Ouvrir la modal de confirmation de suppression
  $(document).on("click", ".delete-project", function () {
    console.log("delete activated");
    sessionStorage.setItem("projectId", $(this).data("id"));
    console.log(sessionStorage.getItem("projectId"));
    $("#delete-modal").addClass("active");
  });

  // Fermer la modal de suppression
  $("#close-delete-modal, #cancel-delete").on("click", function () {
    $("#delete-modal").removeClass("active");
    sessionStorage.removeItem("projectId");
  });
});
