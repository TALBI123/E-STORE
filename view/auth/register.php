<div class="row justify-content-center align-items-center" style="min-height: 80vh;">
  <div class="col-md-5">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h2 class="card-title text-center mb-4">Inscription</h2>

        <?php if (!empty($errors)): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo $BASE_URL ?? '/tps_php/projet'; ?>/register">

          <div class="mb-3">
            <label class="form-label">Nom complet</label>
            <input type="text" name="name" class="form-control" 
                   value="<?= htmlspecialchars($old['name'] ?? '') ?>" 
                   required autofocus>
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" 
                   value="<?= htmlspecialchars($old['email'] ?? '') ?>" 
                   required>
          </div>

          <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control" required>
            <small class="text-muted">Minimum 8 caractères</small>
          </div>

          <div class="mb-3">
            <label class="form-label">Confirmer le mot de passe</label>
            <input type="password" name="password_confirm" class="form-control" required>
          </div>

          <button type="submit" class="btn btn-primary w-100">S'inscrire</button>
        </form>

        <p class="text-center mt-3">
          Déjà un compte ? <a href="<?php echo $BASE_URL ?? '/tps_php/projet'; ?>/login">Se connecter</a>
        </p>
      </div>
    </div>
  </div>
</div>