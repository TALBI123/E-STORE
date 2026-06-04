<?php ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'E-STORE') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <?php $BASE_URL = "/tps_php/projet"; ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?php echo $BASE_URL; ?>">🛍 E-STORE</a>
        <div class="d-flex align-items-center gap-3">
            <a href="<?php echo $BASE_URL; ?>/search" class="text-white">🔍</a>
            <a href="<?php echo $BASE_URL; ?>/cart" class="text-white position-relative">
                🛒
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <span class="badge bg-danger position-absolute" id="cart-count">0</span>
                <?php endif; ?>
            </a>
            <?php echo $_SESSION['user_id']; ?>
            <?php if (!empty($_SESSION['user_id'])): ?>

                <span class="text-white">👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <a href="<?php echo $BASE_URL; ?>/admin/dashboard" class="btn btn-sm btn-warning">Admin</a>
                <?php endif; ?>
                <a href="<?php echo $BASE_URL; ?>/logout" class="btn btn-sm btn-outline-light">Déconnexion</a>
            <?php else: ?>
                <a href="<?php echo $BASE_URL; ?>/login" class="btn btn-sm btn-outline-light">Connexion</a>
                <a href="<?php echo $BASE_URL; ?>/register" class="btn btn-sm btn-light">Inscription</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<main class="container my-4"></main>