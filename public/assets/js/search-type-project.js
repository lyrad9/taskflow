// Filtre par type de projet
$("#project-type-filter").on("change", function () {
  const projectType = $(this).val();

  // Rediriger avec paramètre de filtre
  const currentUrl = new URL(window.location.href);
  if (projectType && projectType !== "all") {
    currentUrl.searchParams.set("project_type", projectType.toString());
  } else {
    currentUrl.searchParams.delete("project_type");
  }
  currentUrl.searchParams.set("page", "1"); // Retour à la première page

  window.location.href = currentUrl.toString();
});
