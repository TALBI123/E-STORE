<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Tests - E-Store</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        
        .card-custom {
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            overflow: hidden;
            background: white;
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1.5rem;
            color: white;
        }
        
        .table-custom {
            border-radius: 15px;
            overflow: hidden;
        }
        
        .table-custom thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .table-custom thead th {
            font-weight: 600;
            padding: 1rem;
            border: none;
        }
        
        .table-custom tbody tr {
            transition: all 0.3s ease;
        }
        
        .table-custom tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .table-custom tbody td {
            padding: 1rem;
            vertical-align: middle;
        }
        
        .badge-id {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            color: white;
            display: inline-block;
        }
        
        .btn-action {
            border-radius: 10px;
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            transition: all 0.3s ease;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .search-form {
            margin-bottom: 2rem;
        }
        
        .search-input {
            border-radius: 50px;
            border: 2px solid #e0e0e0;
            padding: 0.75rem 1.5rem;
        }
        
        .search-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
        
        .pagination {
            justify-content: center;
            margin-top: 2rem;
        }
        
        .page-link {
            color: #667eea;
            border-radius: 10px;
            margin: 0 0.25rem;
        }
        
        .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
        }
        
        .filter-card {
            background: white;
            border-radius: 15px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .stats-number {
                font-size: 1.8rem;
            }
            
            .btn-action {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card-custom">
                    <div class="card-header-custom">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <h2 class="mb-0">
                                    <i class="fas fa-flask me-2"></i>
                                    Gestion des Tests
                                </h2>
                                <p class="mb-0 mt-2 opacity-75">
                                    <i class="fas fa-database me-1"></i>
                                    Base de données - Table des tests
                                </p>
                            </div>
                            <div class="mt-3 mt-md-0">
                                <a href="?refresh=1" class="btn btn-light btn-action">
                                    <i class="fas fa-sync-alt me-2"></i>
                                    Rafraîchir
                                </a>
                                <a href="?action=add" class="btn btn-outline-light btn-action">
                                    <i class="fas fa-plus me-2"></i>
                                    Nouveau test
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Cartes statistiques -->
        <div class="row mb-4 g-3">
            <div class="col-md-4">
                <div class="stats-card">
                    <i class="fas fa-chart-line fa-2x mb-2" style="color: #667eea;"></i>
                    <h6 class="text-muted mb-2">Total Tests</h6>
                    <div class="stats-number"><?= number_format(count($tests ?? [])) ?></div>
                    <small class="text-muted">Enregistrements</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <i class="fas fa-plus-circle fa-2x mb-2" style="color: #28a745;"></i>
                    <h6 class="text-muted mb-2">Ajoutés cette semaine</h6>
                    <div class="stats-number">
                        <?php 
                            $weekCount = 0;
                            if (!empty($tests)) {
                                foreach ($tests as $test) {
                                    if (isset($test['created_at']) && strtotime($test['created_at']) > strtotime('-7 days')) {
                                        $weekCount++;
                                    }
                                }
                            }
                            echo $weekCount;
                        ?>
                    </div>
                    <small class="text-muted">7 derniers jours</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <i class="fas fa-check-circle fa-2x mb-2" style="color: #17a2b8;"></i>
                    <h6 class="text-muted mb-2">Statut général</h6>
                    <div class="stats-number">
                        <span class="badge bg-success fs-6">Actif</span>
                    </div>
                    <small class="text-muted">Système opérationnel</small>
                </div>
            </div>
        </div>
        
        <!-- Formulaire d'ajout (affiché quand action=add) -->
        <?php if (isset($_GET['action']) && $_GET['action'] === 'add'): ?>
        <div class="row mb-4">
            <div class="col-md-8 mx-auto">
                <div class="card-custom">
                    <div class="card-body p-4">
                        <h4 class="mb-3">
                            <i class="fas fa-plus-circle me-2" style="color: #667eea;"></i>
                            Ajouter un nouveau test
                        </h4>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-tag me-1" style="color: #667eea;"></i>
                                    Nom du test
                                </label>
                                <input type="text" class="form-control" name="name" required 
                                       placeholder="Entrez le nom du test...">
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" name="submit_add" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    Enregistrer
                                </button>
                                <a href="?cancel=1" class="btn btn-secondary">
                                    <i class="fas fa-times me-2"></i>
                                    Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Barre de recherche et filtres -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="filter-card">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control search-input" 
                                       placeholder="Rechercher par ID ou nom..." 
                                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select name="sort" class="form-select">
                                <option value="id_asc" <?= ($_GET['sort'] ?? '') === 'id_asc' ? 'selected' : '' ?>>
                                    <i class="fas fa-sort-numeric-down-alt"></i> ID (croissant)
                                </option>
                                <option value="id_desc" <?= ($_GET['sort'] ?? '') === 'id_desc' ? 'selected' : '' ?>>
                                    ID (décroissant)
                                </option>
                                <option value="name_asc" <?= ($_GET['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>>
                                    Nom (A-Z)
                                </option>
                                <option value="name_desc" <?= ($_GET['sort'] ?? '') === 'name_desc' ? 'selected' : '' ?>>
                                    Nom (Z-A)
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i>
                                Filtrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Tableau des tests -->
        <div class="row">
            <div class="col-12">
                <div class="card-custom">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-custom mb-0">
                                <thead>
                                    <tr>
                                        <th width="100">
                                            <i class="fas fa-hashtag me-1"></i> ID
                                            <br>
                                            <small class="fw-normal">
                                                <a href="?sort=id_asc&search=<?= urlencode($_GET['search'] ?? '') ?>" class="text-white me-2">↑</a>
                                                <a href="?sort=id_desc&search=<?= urlencode($_GET['search'] ?? '') ?>" class="text-white">↓</a>
                                            </small>
                                        </th>
                                        <th>
                                            <i class="fas fa-tag me-1"></i> Nom
                                            <br>
                                            <small class="fw-normal">
                                                <a href="?sort=name_asc&search=<?= urlencode($_GET['search'] ?? '') ?>" class="text-white me-2">↑</a>
                                                <a href="?sort=name_desc&search=<?= urlencode($_GET['search'] ?? '') ?>" class="text-white">↓</a>
                                            </small>
                                        </th>
                                        <th width="180"><i class="fas fa-calendar me-1"></i> Date création</th>
                                        <th width="180"><i class="fas fa-cog me-1"></i> Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                        // Filtrage et tri
                                        $filteredTests = $tests ?? [];
                                        
                                        // Recherche
                                        if (!empty($_GET['search'])) {
                                            $search = strtolower($_GET['search']);
                                            $filteredTests = array_filter($filteredTests, function($test) use ($search) {
                                                return strpos(strtolower((string)$test['id']), $search) !== false || 
                                                       strpos(strtolower((string)$test['name']), $search) !== false;
                                            });
                                        }
                                        
                                        // Tri
                                        if (!empty($_GET['sort'])) {
                                            switch($_GET['sort']) {
                                                case 'id_asc':
                                                    usort($filteredTests, fn($a, $b) => $a['id'] <=> $b['id']);
                                                    break;
                                                case 'id_desc':
                                                    usort($filteredTests, fn($a, $b) => $b['id'] <=> $a['id']);
                                                    break;
                                                case 'name_asc':
                                                    usort($filteredTests, fn($a, $b) => strcmp($a['name'], $b['name']));
                                                    break;
                                                case 'name_desc':
                                                    usort($filteredTests, fn($a, $b) => strcmp($b['name'], $a['name']));
                                                    break;
                                            }
                                        }
                                        
                                        // Pagination
                                        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                                        $perPage = 10;
                                        $total = count($filteredTests);
                                        $totalPages = ceil($total / $perPage);
                                        $offset = ($page - 1) * $perPage;
                                        $paginatedTests = array_slice($filteredTests, $offset, $perPage);
                                    ?>
                                    
                                    <?php if (empty($paginatedTests)): ?>
                                        <tr>
                                            <td colspan="4">
                                                <div class="empty-state">
                                                    <i class="fas fa-inbox"></i>
                                                    <h5 class="mt-3">Aucun test trouvé</h5>
                                                    <p class="text-muted">La base de données est vide pour le moment.</p>
                                                    <a href="?action=add" class="btn btn-primary btn-action">
                                                        <i class="fas fa-plus me-2"></i>
                                                        Ajouter un test
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($paginatedTests as $test): ?>
                                            <tr>
                                                <td>
                                                    <span class="badge-id">
                                                        <i class="fas fa-hashtag"></i> <?= htmlspecialchars((string) $test['id']) ?>
                                                    </span>
                                                 </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            <div class="rounded-circle bg-light p-2" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-file-alt" style="color: #667eea;"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <strong><?= htmlspecialchars((string) $test['name']) ?></strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                <i class="fas fa-id-card"></i> ID: <?= $test['id'] ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                 </td>
                                                <td>
                                                    <i class="far fa-calendar-alt text-muted me-2"></i>
                                                    <?= isset($test['created_at']) ? date('d/m/Y H:i', strtotime($test['created_at'])) : date('d/m/Y H:i') ?>
                                                 </td>
                                                <td>
                                                    <a href="?action=edit&id=<?= $test['id'] ?>" class="btn btn-sm btn-warning btn-action">
                                                        <i class="fas fa-edit"></i>
                                                        Modifier
                                                    </a>
                                                    <a href="?action=delete&id=<?= $test['id'] ?>" 
                                                       class="btn btn-sm btn-danger btn-action"
                                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce test ?')">
                                                        <i class="fas fa-trash"></i>
                                                        Supprimer
                                                    </a>
                                                 </td>
                                             </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="row">
            <div class="col-12">
                <nav>
                    <ul class="pagination">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&sort=<?= urlencode($_GET['sort'] ?? '') ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        
                        <?php for($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&sort=<?= urlencode($_GET['sort'] ?? '') ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&sort=<?= urlencode($_GET['sort'] ?? '') ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Informations de debug (optionnel) -->
        <div class="row mt-4">
            <div class="col-12 text-center">
                <small class="text-white-50">
                    <i class="fas fa-info-circle me-1"></i>
                    Affichage de <?= count($paginatedTests) ?> sur <?= $total ?> enregistrements
                </small>
            </div>
        </div>
    </div>
</body>
</html>