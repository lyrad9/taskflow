function getSelectedProjectIds() {
  const selectedIds = [];
  $(".d-checkbox:checked").each(function () {
    selectedIds.push($(this).data("id"));
  });
  return selectedIds;
}
