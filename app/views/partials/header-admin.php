<?php require_once 'app/helpers/AuthHelper.php';

$user = AuthHelper::getUser();

?>
<style>
 /* Style pour les badges de rôle */
.role-badge {
  display: inline-block;
  font-size: 10px;
  padding: 2px 6px;
  margin-left: 6px;
  border-radius: 4px;
  color: white;
  font-weight: bold;
  vertical-align: middle;
}

.role-badge.admin {
  background-color: #2c6fdb;
}

.role-badge.super_admin {
  background-color: #b92020;
}

.role-separator {
  margin: 0 5px;
  color: #8c8c8c;
  font-weight: 300;
  font-size: 14px;
  opacity: 0.7;
  vertical-align: middle;
}
</style>
<!-- Header -->
<header class="main-header">
        <div class="toggle-sidebar">
          <i class="fas fa-bars" id="sidebar-toggle"></i>
        </div>
        <div style="display: flex; align-items: center; justify-content: center; gap: 2px;" class="user-dropdown">
          
        <span class="user-name">
         
          <?php 
                if (empty($user['lastname'])) {
                    echo htmlspecialchars(ucfirst($user['firstname']));
                } else echo htmlspecialchars(ucfirst($user['firstname']) . ' ' . ucfirst($user['lastname']));
                
               
            ?>
            <?php if (isset($user['role']) && ($user['role'] === 'ADMIN' || $user['role'] === 'SUPER_ADMIN')): ?>
              <span class="role-separator">|</span>
              <span class="role-badge <?php echo strtolower($user['role']); ?>"><?php echo $user['role'] === 'SUPER_ADMIN' ? 'Super Admin' : 'Admin'; ?></span>
            <?php endif; ?>
        </span>
          <div class="user-info" id="user-dropdown-toggle">
            <div class="user-avatar">
              <img src="<?php echo isset($data['user']['profile_picture']) ? $data['user']['profile_picture'] : '/public/static/defaultUser.jpg'; ?>" alt="Avatar">
            </div>
            <svg style="color: var(--text-muted);" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
</svg>
           
          </div>
          <div class="dropdown-menu" id="user-dropdown-menu">
            <div style="display: flex; align-items: center; gap: 10px;" class="user-email">
            <i style="font-size:16px;" class="bi bi-envelope-at-fill"></i>
            <span>
              <?php echo $user['email']; ?>
            </span>
            </div>
           
            <a href="/profile" class="dropdown-item">
              <i class="fas fa-user"></i>
              <span>Profil</span>
            </a>
            <a href="/settings" class="dropdown-item">
              <i class="fas fa-cog"></i>
              <span>Paramètres</span>
            </a>
            <div class="dropdown-divider"></div>
            <a href="/auth/logout" class="dropdown-item">
              <i class="fas fa-sign-out-alt"></i>
              <span>Déconnexion</span>
            </a>
          </div>
        </div>
      </header>