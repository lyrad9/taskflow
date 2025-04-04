<!-- Views/layouts/base.php -->
<!DOCTYPE html>
<html>
<head>
<title><?php echo isset($data['title']) ? $data['title'] : 'Dashboard Member'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="/public/assets/css/main-dashboard.admin.css">
  <link rel="stylesheet" href="/public/assets/css/base.css">
  <link rel="stylesheet" href="/public/assets/css/sidebar.css">
  <link rel="stylesheet" href="/public/assets/css/see-project.admin.css">
 
  <link rel="stylesheet" href="/public/assets/css/add-project.admin.css">
  <link rel="stylesheet" href="/public/assets/css/tasks-admin.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>

        
    <div class="dashboard-container">   
    <?php $this->renderPartial('partials/sidebar', $data); ?>
   
        <div class="main-content">

        <?php $this->renderPartial('partials/header-admin', $data); ?>
        <div class="content-area">
        <?php $this->renderPartial("member/{$data["content"]}", $data); ?>
        </div>
        </div>
    </div>
    <script>
    // Toggle Sidebar
    document.getElementById('sidebar-toggle').addEventListener('click', function() {
      document.querySelector('.dashboard-container').classList.toggle('sidebar-collapsed');
    });
    
    // User Dropdown
    document.getElementById('user-dropdown-toggle').addEventListener('click', function() {
      document.getElementById('user-dropdown-menu').classList.toggle('active');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      const dropdown = document.getElementById('user-dropdown-menu');
      const toggle = document.getElementById('user-dropdown-toggle');
      
      if (!toggle.contains(event.target) && !dropdown.contains(event.target)) {
        dropdown.classList.remove('active');
      }
    });
  </script>    


</body>
</html>