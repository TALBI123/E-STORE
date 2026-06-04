<div class="row justify-content-center align-items-center" style="min-height: 80vh;">
  <div class="col-md-5">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h2 class="card-title text-center mb-4">Connexion</h2>

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($_GET['registered'])): ?>
          <div class="alert alert-success">Compte créé ! Vous pouvez vous connecter.</div>
        <?php endif; ?>

        <form method="POST" action="<?php echo $BASE_URL; ?>/login">

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required autofocus>
          </div>
          <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Se connecter</button>
        </form>

        <p class="text-center mt-3">
          Pas de compte ? <a href="<?php echo $BASE_URL; ?>/register">S'inscrire</a>
        </p>
      </div>
    </div>
  </div>
</div>