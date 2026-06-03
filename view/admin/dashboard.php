<h2 class="mb-4">📊 Tableau de bord Admin</h2>

<!-- Cartes statistiques -->
<div class="row g-4 mb-5">
  <div class="col-md-3">
    <div class="card text-white bg-primary shadow">
      <div class="card-body">
        <h5>Commandes</h5>
        <h2><?= $stats['total_orders'] ?? 0 ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-white bg-success shadow">
      <div class="card-body">
        <h5>CA du mois</h5>
        <h2><?= number_format($stats['revenue_month'] ?? 0, 2) ?> MAD</h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-white bg-info shadow">
      <div class="card-body">
        <h5>Clients</h5>
        <h2><?= $stats['total_users'] ?? 0 ?></h2>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card text-white bg-warning shadow">
      <div class="card-body">
        <h5>Produits</h5>
        <h2><?= $stats['total_products'] ?? 0 ?></h2>
      </div>
    </div>
  </div>
</div>

<!-- Dernières commandes -->
<h4>Dernières commandes</h4>
<table class="table table-hover">
  <thead class="table-dark">
    <tr>
      <th>#</th><th>Client</th><th>Montant</th><th>Statut</th><th>Date</th><th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($stats['recent_orders'])): ?>
      <tr><td colspan="6" class="text-center text-muted">Aucune commande récente</td></tr>
    <?php endif; ?>
    <?php foreach ($stats['recent_orders'] as $order): ?>
      <tr>
        <td><?= $order['id'] ?></td>
        <td><?= htmlspecialchars($order['client_name']) ?></td>
        <td><?= number_format($order['total_amount'] ?? 0, 2) ?> MAD</td>
        <td>
          <span class="badge bg-<?= match($order['status']) {
              'delivered' => 'success', 'cancelled' => 'danger',
              'shipped'   => 'info',    default      => 'warning'
          } ?>"><?= $order['status'] ?></span>
        </td>
        <td><?= date('d/m/Y', strtotime($order['ordered_at'])) ?></td>
        <td><a href="/orders/<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">Voir</a></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>