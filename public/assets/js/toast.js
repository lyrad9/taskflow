 // Fonction pour afficher une notification toast
function showToast(message, type) {
  const toast = $('#toast');
  toast.removeClass('success error').addClass(type);
  
  if (type === 'success') {
      toast.find('.toast-icon i').attr('class', 'fas fa-check-circle');
  } else {
      toast.find('.toast-icon i').attr('class', 'fas fa-exclamation-circle');
  }
  
  toast.find('.toast-message').text(message);
  toast.addClass('active');
  
  setTimeout(() => {
      toast.removeClass('active');
  }, 3000);
}