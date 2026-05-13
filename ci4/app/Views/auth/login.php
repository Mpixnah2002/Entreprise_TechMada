<?php /** Simple login form view */ ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Login</title>
</head>
<body>
  <h1>Connexion</h1>
  <?php if(session()->getFlashdata('error')): ?>
    <div style="color:#b00"><?=esc(session()->getFlashdata('error'))?></div>
  <?php endif; ?>
  <form method="post" action="<?=site_url('auth/attempt')?>">
    <label>Email<br><input type="email" name="email" required></label><br>
    <label>Mot de passe<br><input type="password" name="password" required></label><br>
    <button type="submit">Se connecter</button>
  </form>
</body>
</html>
