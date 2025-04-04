<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $data['title']; ?></title> 
  <?php echo $data['css']; ?>
  <style>
    .login-form {
      max-width: 400px;
      width: 90%;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      background-color: white;
    }
    .bg-custom {
      background-color: #f8f9fa;
    }
    .logo {
      max-width: 150px;
      margin-bottom: 1.5rem;
    }
  </style>
</head>
<body class="min-vh-100 d-flex justify-content-center align-items-center bg-custom">
  <div class="login-form">
    <div class="text-center mb-4">
    <p class="text-muted">LOGO</p>
      <h1 class="h3 mb-3 fw-normal">Se connecter</h1>
     
    </div>
    
    <?php if (!empty($data['error'])): ?>
      <div class="alert alert-danger" role="alert">
        <?php echo $data['error']; ?>
      </div>
    <?php endif; ?>
    
    <form method="POST" action="">
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" required autocomplete="username">
      </div>
      
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
      </div>
      
      <div class="d-grid gap-2 mt-4">
        <button type="submit" class="btn btn-primary btn-lg">Se connecter</button>
      </div>
    </form>
  </div>
  
  <?php echo $data['js']; ?>
  <?php echo $data['jquery']; ?>
</body>
</html>
