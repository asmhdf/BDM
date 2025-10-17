<?php require __DIR__ . '/header.php'; ?>

<div class="container ">
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center mb-4">
                <i class="fas fa-shopping-cart text-primary me-3" style="font-size: 2rem;"></i>
                <h2 class="mb-0 fw-bold">Votre Panier</h2>
            </div>

            <?php if (empty($products)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart text-muted mb-3" style="font-size: 4rem;"></i>
                    <div class="alert alert-info border-0 shadow-sm">
                        <h5 class="mb-2">Votre panier est vide</h5>
                        <p class="mb-0">Découvrez nos produits et ajoutez-les à votre panier</p>
                    </div>
                    <a href="index.php" class="btn btn-primary btn-lg mt-3">
                        <i class="fas fa-store me-2"></i>Continuer mes achats
                    </a>
                </div>
            <?php else: ?>
                <form method="post" action="index.php?action=update_cart">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #ff6b35, #f7931e);">
                                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Articles dans votre panier</h5>
                                </div>
                                <div class="card-body p-0">
                                    <?php foreach ($products as $index => $p): ?>
                                        <div class="cart-item p-4 <?= $index < count($products) - 1 ? 'border-bottom' : '' ?>">
                                            <div class="row align-items-center">
                                                <div class="col-6 col-md-2 mb-2 mb-md-0 w-100">
                                                    <?php if ($p['image']): ?>
                                                        <img src="data:<?= $p['image_type'] ?>;base64,<?= base64_encode($p['image']) ?>" alt="<?= htmlspecialchars($p['nom']) ?>" class="img-fluid rounded" style="height: 80px; width: auto;">
                                                    <?php else: ?>
                                                        <div class="product-image-placeholder bg-light rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-6 col-md-4 mb-2 mb-md-0 w-100">
                                                    <h6 class="mb-1 fw-bold"><?= htmlspecialchars($p['nom']) ?></h6>
                                                    <p class="text-muted mb-0 small">Produit disponible</p>
                                                </div>
                                                <div class="col-6 col-md-2 mb-2 mb-md-0 w-100">
                                                    <span class="fw-bold text-primary"><?= number_format($p['prix'], 2) ?> €</span>
                                                </div>
                                                <div class="col-6 col-md-2 mb-2 mb-md-0 w-100">
                                                    <div class="input-group input-group-sm">
                                                        <input type="number" name="quantities[<?= $p['id'] ?>]" value="<?= $p['quantity'] ?>" min="1" class="form-control text-center quantity-input">
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-1 mb-2 mb-md-0 w-100">
                                                    <span class="fw-bold"><?= number_format($p['subtotal'], 2) ?> €</span>
                                                </div>
                                                <div class="col-6 col-md-1 w-100">
                                                    <a href="index.php?action=remove_from_cart&id=<?= $p['id'] ?>" class="btn btn-outline-danger btn-sm">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="d-flex gap-2 flex-column flex-md-row">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-sync me-2"></i>Mettre à jour
                                </button>
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Continuer mes achats
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm sticky-top">
                                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #28a745, #20c997);">
                                    <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Résumé de la commande</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-3">
                                        <span>Sous-total:</span>
                                        <span class="fw-bold"><?= number_format($total, 2) ?> €</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span>Livraison:</span>
                                        <span class="text-success">Gratuite</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between mb-4">
                                        <span class="fw-bold fs-5">Total:</span>
                                        <span class="fw-bold fs-5 text-primary"><?= number_format($total, 2) ?> €</span>
                                    </div>
                                    <a href="index.php?action=order_form" class="btn btn-success btn-lg w-100 mb-3">
                                        <i class="fas fa-credit-card me-2"></i>Passer la commande
                                    </a>
                                    <div class="text-center">
                                        <small class="text-muted">
                                            <i class="fas fa-shield-alt me-1"></i>Paiement 100% sécurisé
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.cart-item {
    padding: 1rem;
    border-bottom: 1px solid #eee;
}

.cart-item:last-child {
    border-bottom: none;
}

.cart-item:hover {
    background-color: #f8f9fa;
    transition: background-color 0.3s ease;
}

.product-image-placeholder {
    transition: transform 0.3s ease;
}

.product-image-placeholder:hover {
    transform: scale(1.05);
}

.sticky-top {
    top: 100px;
}

.quantity-input {
    width: 4rem;
}
</style>

<?php require __DIR__ . '/footer.php'; ?>