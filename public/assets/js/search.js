$(document).ready(function () {
  // Recherche en temps réel
  $("#search").on("input", function () {
    const searchTerm = $(this).val();
    const $this = $(this);
    let searchTimeout = $this.data("searchTimeout");

    // Rediriger avec paramètre de recherche
    const currentUrl = new URL(window.location.href);
    if (searchTerm) {
      currentUrl.searchParams.set("search", searchTerm.toString());
    } else {
      currentUrl.searchParams.delete("search");
    }
    currentUrl.searchParams.set("page", "1"); // Retour à la première page

    // Delay pour éviter trop de requêtes
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      window.location.href = currentUrl.toString();
    }, 500);
    $this.data("searchTimeout", searchTimeout);
  });
});
