<?php require __DIR__ . '/header.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="position-relative">
                    <img src="image.php?file=<?= htmlspecialchars($product['image_path']) ?>" 
                        alt="<?= htmlspecialchars($product['nom']) ?>" class="card-img-top" style="height: 250px; object-fit: contain; background: #f8f9fa;">
                    
                    <div class="position-absolute top-0 end-0 m-3">
                        <span class="badge bg-success">Disponible</span>
                    </div>
                </div>
                
                <?php if (!empty($images)): ?>
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="fas fa-images me-2 text-primary"></i>Images supplémentaires
                        </h6>
                        <div class="row g-2">
                            <?php foreach ($images as $img): ?>
                                <div class="col-3">
                                    <img src="image_supp.php?id=<?= $img['id'] ?>" class="img-thumbnail additional-image" style="height: 250px; object-fit: contain; background: #f8f9fa;">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="product-info">
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Accueil</a></li>
                        <li class="breadcrumb-item active"><?= htmlspecialchars($product['nom']) ?></li>
                    </ol>
                </nav>
                
                <h1 class="display-6 fw-bold mb-3"><?= htmlspecialchars($product['nom']) ?></h1>
                
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-2">
                        <div class="stars text-warning me-2">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                        </div>
                        <small class="text-muted">(4.2/5 - 24 avis)</small>
                    </div>
                </div>
                
                <div class="card border-0 bg-light mb-4">
                    <div class="card-body">
                        <h6 class="card-title">Description</h6>
                        <p class="card-text"><?= htmlspecialchars($product['description']) ?></p>
                    </div>
                </div>
                
                <div class="price-section mb-4">
                    <div class="d-flex align-items-center">
                        <span class="display-5 fw-bold text-primary me-3"><?= number_format($product['prix'], 2) ?> €</span>
                        <div class="text-muted">
                            <small><i class="fas fa-truck me-1"></i>Livraison gratuite</small>
                        </div>
                    </div>
                </div>
                
                <form method="post" action="index.php?action=add_to_cart&id=<?= $product['id'] ?>" class="mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label for="quantity" class="form-label fw-bold">
                                        <i class="fas fa-sort-numeric-up me-2"></i>Quantité
                                    </label>
                                    <div class="input-group">
                                        <button type="button" class="btn btn-outline-secondary" onclick="decreaseQuantity()">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <input type="number" name="quantity" id="quantity" value="1" min="1" class="form-control text-center">
                                        <button type="button" class="btn btn-outline-secondary" onclick="increaseQuantity()">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <button type="submit" class="btn btn-success btn-lg w-100 mb-2">
                                        <i class="fas fa-cart-plus me-2"></i>Ajouter au panier
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                
                <div class="action-buttons d-flex gap-2 mb-4">
                    <a href="index.php?action=cart" class="btn btn-outline-primary flex-fill">
                        <i class="fas fa-shopping-cart me-2"></i>Voir le panier
                    </a>
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-heart"></i>
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-share"></i>
                    </button>
                </div>
                
                <div class="features">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="feature-item p-3">
                                <i class="fas fa-shipping-fast text-primary mb-2" style="font-size: 1.5rem;"></i>
                                <small class="d-block">Livraison rapide</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="feature-item p-3">
                                <i class="fas fa-undo text-success mb-2" style="font-size: 1.5rem;"></i>
                                <small class="d-block">Retour gratuit</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="feature-item p-3">
                                <i class="fas fa-shield-alt text-warning mb-2" style="font-size: 1.5rem;"></i>
                                <small class="d-block">Garantie 30 jour</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function increaseQuantity() {
    const input = document.getElementById('quantity');
    input.value = parseInt(input.value) + 1;
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

// Image gallery functionality
document.querySelectorAll('.additional-image').forEach(img => {
    img.addEventListener('click', function() {
        const mainImage = document.querySelector('.product-main-image');
        const tempSrc = mainImage.src;
        mainImage.src = this.src;
        this.src = tempSrc;
    });
});
</script>

<style>
.product-main-image {
    transition: transform 0.3s ease;
}

.product-main-image:hover {
    transform: scale(1.02);
}

.additional-image {
    transition: all 0.3s ease;
}

.additional-image:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.feature-item {
    transition: transform 0.3s ease;
}

.feature-item:hover {
    transform: translateY(-5px);
}

.stars i {
    font-size: 0.9rem;
}
</style>

<?php require __DIR__ . '/footer.php'; ?>
