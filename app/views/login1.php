<!doctype html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Connexion - CK-Project</title>
    <link rel="stylesheet" href="/public/assets/css/auth.css" />
    <link rel="stylesheet" href="/public/assets/css/toast-error.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <!--   <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    /> -->
  </head>
  <body>
    <div class="login-container">
      <div class="" style="display: flex; justify-content: center">
        <div class="logo-container">
          <img
            style="width: 54px; height: auto"
            src="/public/static/logo.png"
            alt="Photo de profil"
          />
        </div>
      </div>
      <h2 class="title">Se connecter</h2>

      <form method="POST" action="">
        <div class="input-group">
          <i class="fas fa-user" style="color: #063970"></i>
          <input
            type="text"
            name="username"
            placeholder="Nom d'utilisateur"
           
          />
        </div>

        <div class="input-group">
          <i class="fas fa-lock" style="color: #063970"></i>
          <input
            type="password"
            name="password"
            placeholder="Mot de passe"
            
          />
        </div>

        <button type="submit" class="login-btn">Connexion</button>
      </form>

      <a href="<?= BASE_URL ?>/auth/forgot-password" class="forgot-password"
        >Nom d'utilisateur / Mot de passe oublié ?</a
      >

      <a href="<?= BASE_URL ?>/auth/register" class="create-account">
        Créer un nouveau compte <i class="fas fa-arrow-right"></i>
      </a>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <script>
      function showToast(message) {
        const toastContainer = document.getElementById('toastContainer');
        
        // Créer le toast
        const toast = document.createElement('div');
        toast.className = 'toast';
        
        // Contenu du toast
        toast.innerHTML = `
          
            <div class="toast-content">
              <div class="toast-icon">
                <i class="warning-icon fas fa-exclamation-triangle">
                </i>
              </div>
              <div class="toast-message">${message}
              </div>
            </div>            
            <button class="toast-close" onclick="closeToast(this.parentNode)">
              <i class="fas fa-times"></i>
            </button>          
                           
         
        `;
        
        // Ajouter le toast au conteneur
        toastContainer.appendChild(toast);
        
        // Fermer automatiquement après 5 secondes
        setTimeout(() => {
          closeToast(toast);
        }, 5000);
      }
      
      function closeToast(toast) {
        toast.classList.add('toast-hide');
        setTimeout(() => {
          toast.remove();
        }, 300);
      }

      // Afficher le toast si une erreur existe
      <?php if (!empty($data['error'])): ?>
        document.addEventListener('DOMContentLoaded', function() {
          showToast('<?= $data['error'] ?>');          
        });
      <?php endif; ?>
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
  </body>
</html>

