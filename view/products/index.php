<? echo $totalPages; ?>
<div class="row">
  <!-- Filtres latéraux -->
  <aside class="col-md-3">
    <form method="GET" action="/search">
      <h5>Filtrer</h5>

      <div class="mb-3">
        <label class="form-label">Recherche</label>
        <input type="text" name="q" class="form-control"
          value="<?= htmlspecialchars($query ?? '') ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Catégorie</label>
        <select name="category" class="form-select">
          <option value="">Toutes</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>"
              <?= ($filters['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="row mb-3">
        <div class="col">
          <input type="number" name="min_price" class="form-control"
            placeholder="Prix min" value="<?= $filters['min_price'] ?? '' ?>">
        </div>
        <div class="col">
          <input type="number" name="max_price" class="form-control"
            placeholder="Prix max" value="<?= $filters['max_price'] ?? '' ?>">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Trier par</label>
        <select name="sort" class="form-select">
          <option value="">Popularité</option>
          <option value="price_asc">Prix croissant</option>
          <option value="price_desc">Prix décroissant</option>
          <option value="newest">Nouveautés</option>
        </select>
      </div>

      <button type="submit" class="btn btn-primary w-100">Filtrer</button>
    </form>
  </aside>

  <!-- Grille de produits -->
  <section class="col-md-9">
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?php if (empty($products)): ?>
        <p class="text-muted">Aucun produit trouvé.</p>
      <?php else: ?>
        <?php foreach ($products as $product): ?>
          <div class="col">
            <div class="card h-100 shadow-sm">
              <img src="/assets/img/products/<?= htmlspecialchars($product['image'] ?: 'placeholder.jpg') ?>"
                class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>"
                style="height: 200px; object-fit: cover;">
              <div class="card-body">
                <h6 class="card-title"><?= htmlspecialchars($product['name']) ?></h6>
                <p class="text-primary fw-bold"><?= number_format($product['price'], 2) ?> MAD</p>
                <?php if ($product['stock'] == 0): ?>
                  <span class="badge bg-danger">Rupture de stock</span>
                <?php endif; ?>
              </div>
              <div class="card-footer d-flex gap-2">
                <a href="<?php echo BASE_URL; ?>/products/<?= htmlspecialchars($product['slug']) ?>"
                  class="btn btn-sm btn-outline-primary flex-grow-1">Voir</a>
                <?php if ($product['stock'] > 0): ?>
                  <button class="btn btn-sm btn-primary btn-add-cart"
                    data-product-id="<?= $product['id'] ?>">🛒</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
    <?php echo "Total pages is : " . $totalPages; ?>
    <!-- Pagination -->
    <?php if (($totalPages ?? 1) > 1): ?>
      <nav class="mt-4">
        <ul class="pagination justify-content-center">
          <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <li class="page-item <?= $p === $currentPage ? 'active' : '' ?>">
              <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    <?php endif; ?>
  </section>
</div>

<script>
  // Ajout au panier en AJAX — pas de rechargement de page
  document.querySelectorAll('.btn-add-cart').forEach(btn => {
    btn.addEventListener('click', async () => {
      const productId = btn.dataset.productId;
      const res = await fetch('/cart/add', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `product_id=${productId}&quantity=1`
      });
      const data = await res.json();
      alert(data.message);
      if (data.success) {
        document.getElementById('cart-count').textContent =
          Math.round(data.total / 1); // simplifié
      }
    });
  });
</script>