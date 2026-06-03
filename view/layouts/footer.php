</main>
<footer class="bg-dark text-white text-center py-4 mt-5">
    <p class="mb-0">&copy; <?= date('Y') ?> E-STORE. Tous droits réservés.</p>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Met à jour le compteur du panier via AJAX au chargement
document.addEventListener('DOMContentLoaded', () => {
    fetch('/cart/count')
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('cart-count');
            if (badge && data.count > 0) badge.textContent = data.count;
        })
        .catch(() => {}); // Silencieux si non connecté
});
</script>
</body>
</html