<?php require __DIR__ . '/header.php'; ?>

<div class="container py-4">
    <!-- Modern header with gradient background -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, var(--background-light) 0%, var(--primary-hover) 100%);">
                <div class="card-body text-white">
                    <h2 class="mb-1 fw-bold"><i class="fas fa-plus-circle me-2"></i>Ajouter un nouveau produit</h2>
                    <p class="mb-0 opacity-75">Créez un nouveau produit pour votre catalogue</p>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($error)): ?>
        <!-- Modern error alert -->
        <div class="alert alert-danger border-0 shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Modern form with improved styling and icons -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="post" action="index.php?action=admin_add_product" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label fw-semibold">
                                    <i class="fas fa-tag text-primary me-2"></i>Nom du produit
                                </label>
                                <input type="text" class="form-control form-control-lg" id="nom" name="nom" 
                                       placeholder="Entrez le nom du produit" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="prix" class="form-label fw-semibold">
                                    <i class="fas fa-euro-sign text-success me-2"></i>Prix
                                </label>
                                <div class="input-group input-group-lg">
                                    <input type="number" class="form-control" id="prix" name="prix" 
                                           step="0.01" placeholder="0.00" required>
                                    <span class="input-group-text">€</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">
                                <i class="fas fa-align-left text-info me-2"></i>Description
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="4" 
                                      placeholder="Décrivez votre produit en détail..." required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="stock" class="form-label fw-semibold">
                                    <i class="fas fa-boxes text-warning me-2"></i>Stock
                                </label>
                                <input type="number" class="form-control form-control-lg" id="stock" name="stock" 
                                       placeholder="Quantité en stock" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="categorie_id" class="form-label fw-semibold">
                                    <i class="fas fa-list text-secondary me-2"></i>Catégorie
                                </label>
                                <select class="form-select form-select-lg" id="categorie_id" name="categorie_id" required>
                                    <option value="">Sélectionner une catégorie</option>
                                    <?php foreach ($categories as $c): ?>
                                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Modern file upload sections -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="image" class="form-label fw-semibold">
                                    <i class="fas fa-image text-primary me-2"></i>Image principale
                                </label>
                                <input type="file" class="form-control form-control-lg" id="image" name="image" 
                                       accept="image/*">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>Formats acceptés: JPG, PNG, GIF
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="images" class="form-label fw-semibold">
                                    <i class="fas fa-images text-secondary me-2"></i>Images supplémentaires
                                </label>
                                <input type="file" class="form-control form-control-lg" id="images" name="images[]" 
                                       multiple accept="image/*">
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>Vous pouvez sélectionner plusieurs images
                                </div>
                            </div>
                        </div>

                        <!-- Modern action buttons -->
                        <div class="d-flex gap-3 justify-content-end mt-4 pt-3 border-top">
                            <a href="index.php?action=admin_products" class="btn btn-light btn-lg px-4">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-success btn-lg px-4 shadow-sm">
                                <i class="fas fa-plus me-2"></i>Ajouter le produit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/footer.php'; ?>
