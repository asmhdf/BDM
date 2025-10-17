<?php require __DIR__ . '/header.php'; ?>

<div class="container-fluid py-4">
    <!-- Modern header with gradient background and improved typography -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1 fw-bold"><i class="fas fa-box me-2"></i>Gestion des produits</h2>
                            <p class="mb-0 opacity-75">Gérez votre catalogue de produits</p>
                        </div>
                        <div class="text-end">
                            <div class="fs-3 fw-bold"><?= count($products) ?></div>
                            <small class="opacity-75">Produits total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern filter and add button section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="get" action="index.php?action=admin_products" class="d-flex align-items-center">
                        <i class="fas fa-filter text-muted me-2"></i>
                        <label class="form-label mb-0 me-3 fw-semibold">Filtrer par catégorie :</label>
                        <select name="categorie" class="form-select form-select-sm" style="max-width: 200px;" onchange="this.form.submit()">
                            <option value="">Toutes les catégories</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= (isset($_GET['categorie']) && $_GET['categorie'] == $c['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="action" value="admin_products">
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-end">
            <a href="index.php?action=admin_add_product" class="btn btn-success btn-lg shadow-sm">
                <i class="fas fa-plus me-2"></i>Ajouter un produit
            </a>
        </div>
    </div>

    <!-- Modern product cards instead of basic table -->
    <div class="row">
        <?php foreach ($products as $p): ?>
        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100 product-card">
                <div class="position-relative">
                    <img src="image.php?id=<?= $p['id'] ?>" alt="<?= htmlspecialchars($p['nom']) ?>" 
                         class="card-img-top" style="height: 250px; object-fit: contain; background: #f8f9fa;">
                    <div class="position-absolute top-0 end-0 m-2">
                        <span class="badge bg-primary">#<?= $p['id'] ?></span>
                    </div>
                    <?php if ($p['stock'] <= 5): ?>
                    <div class="position-absolute top-0 start-0 m-2">
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-exclamation-triangle me-1"></i>Stock faible
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold text-truncate"><?= htmlspecialchars($p['nom']) ?></h5>
                    <p class="card-text text-muted small flex-grow-1"><?= htmlspecialchars(substr($p['description'], 0, 100)) ?>...</p>
                    
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <div class="fw-bold text-success fs-5"><?= number_format($p['prix'], 2) ?> €</div>
                                <small class="text-muted">Prix</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2 bg-light rounded">
                                <div class="fw-bold <?= $p['stock'] <= 5 ? 'text-warning' : 'text-info' ?> fs-5"><?= $p['stock'] ?></div>
                                <small class="text-muted">Stock</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-tag me-1"></i>Catégorie: <?= $p['categorie_id'] ?>
                        </small>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="index.php?action=admin_edit_product&id=<?= $p['id'] ?>" 
                           class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Modifier
                        </a>
                        <a href="index.php?action=admin_delete_product&id=<?= $p['id'] ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                            <i class="fas fa-trash me-1"></i>Supprimer
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.product-card {
    transition: all 0.3s ease;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}
</style>

<?php require __DIR__ . '/footer.php'; ?>
