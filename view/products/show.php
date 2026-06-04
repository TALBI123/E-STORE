<?php $product = $product ?? []; ?>
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="row g-0">
            <div class="col-md-5">
                <img src="/assets/img/products/<?= htmlspecialchars($product['image'] ?? 'placeholder.jpg') ?>"
                     class="img-fluid rounded-start w-100 h-100"
                     style="object-fit: cover; min-height: 320px;"
                     alt="<?= htmlspecialchars($product['name'] ?? '') ?>">
            </div>
            <div class="col-md-7">
                <div class="card-body">
                    <h1 class="card-title h3"><?= htmlspecialchars($product['name'] ?? '') ?></h1>
                    <p class="text-primary fw-bold fs-4"><?= number_format((float) ($product['price'] ?? 0), 2) ?> MAD</p>
                    <p class="card-text"><?= nl2br(htmlspecialchars($product['description'] ?? '')) ?></p>
                    <p class="mb-1"><strong>Stock :</strong> <?= (int) ($product['stock'] ?? 0) ?></p>
                    <p class="mb-3"><strong>Slug :</strong> <?= htmlspecialchars($product['slug'] ?? '') ?></p>
                    <a href="/products" class="btn btn-outline-primary">Retour aux produits</a>
                </div>
            </div>
        </div>
    </div>
</div>