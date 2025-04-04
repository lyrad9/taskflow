<?php

?>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <div class="logo">
          <h2>GestionPro</h2>
        </div>
      </div>
    
      <div class="sidebar-menu">
        <ul>
        <?php foreach ($data['tabs'] as $tabId => $tab): ?>
          <li   class="<?= $data['activeTab'] === $tabId ? 'active menu-item' : 'menu-item' ?>">
         
            <a href="<?=$tabId?>" >
              <i class="<?= $tab["icon"] ?>"></i>
              <span> <?= $tab["label"] ?></span>
            </a>
          </li>
        
    <?php endforeach; ?>
    </ul>  
        
      </div>
    </div>
    <!-- End of Sidebar -->
   