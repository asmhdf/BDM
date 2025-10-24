<?php require __DIR__ . '/header.php'; ?>

<div class="row ">
    <div class="col-12">
        <div class="text-center" style="background: linear-gradient(135deg, var(--background-light) 0%, white 100%); border-radius: 1rem; margin-bottom: 2rem;">
            <h1 class="display-4 fw-bold mb-3" style="color: var(--primary-color);">
                Découvrez nos produits exceptionnels
            </h1>
            <p class="lead text-muted">
                Une sélection soignée de produits de qualité pour tous vos besoins
            </p>
        </div>
    </div>
</div>

<div class="filter-section">
    <form method="get" class="d-flex align-items-center gap-3 flex-wrap">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-filter text-primary"></i>
            <label class="form-label mb-0 fw-semibold">Filtrer par catégorie :</label>
        </div>
        <div class="flex-grow-1" style="min-width: 200px;">
            <select name="categorie" class="form-select form-control-modern" onchange="this.form.submit()">
                <option value="">🏷️ Toutes les catégories</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= (isset($_GET['categorie']) && $_GET['categorie'] == $c['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</div>

<div class="row g-4">
    <?php foreach ($products as $p): ?>
        <div class="col-lg-4 col-md-6 col-sm-12">
            <div class="card card-modern h-100">
                <div class="position-relative overflow-hidden">
                   <img src="image.php?file=<?= htmlspecialchars($p['image_path']) ?>" 
                        alt="<?= htmlspecialchars($p['nom']) ?>" class="card-img-top" style="height: 250px; object-fit: contain; background: #f8f9fa;">
                    
                    <!-- <CHANGE> Added overlay with quick actions -->
                    <div class="position-absolute top-0 end-0 p-3">
                        <span class="badge bg-primary rounded-pill">Nouveau</span>
                    </div>
                </div>
                
                <div class="card-body d-flex flex-column p-4">
                    <h5 class="card-title fw-bold mb-3" style="color: var(--text-dark);">
                        <?= htmlspecialchars($p['nom']) ?>
                    </h5>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="price-tag">
                            <?= number_format($p['prix'], 2) ?> €
                        </span>
                        <div class="text-warning">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    
                    <div class="mt-auto">
                        <a href="index.php?action=product&id=<?= $p['id'] ?>" 
                           class="btn btn-modern-primary w-100">
                            <i class="fas fa-eye me-2"></i>Voir le détail
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row mt-5">
    <div class="col-12">
        <div class="text-center py-5" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%); border-radius: 1rem; color: white;">
            <h3 class="fw-bold mb-3">Besoin d'aide pour choisir ?</h3>
            <p class="mb-4">Notre équipe d'experts est là pour vous conseiller</p>
            <button class="btn btn-light btn-lg px-4">
                <i class="fas fa-phone me-2"></i>Nous contacter
            </button>
        </div>
    </div>
</div>

<?php require __DIR__ . '/footer.php'; ?>
