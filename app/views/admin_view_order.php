<?php require __DIR__ . '/header.php'; ?>

<div class="container-fluid py-4">
    <!-- Added modern header with gradient background -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
">
                <div class="card-body text-white py-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-invoice fa-2x me-3"></i>
                            <div>
                                <h2 class="mb-1 fw-bold">Détails de la Commande #<?= htmlspecialchars($order['id']) ?></h2>
                                <p class="mb-0 opacity-75">Informations complètes et gestion du statut</p>
                            </div>
                        </div>
                        <a href="index.php?action=admin_list_orders" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Added success/error alerts with modern styling -->
    <?php if (isset($_GET['success']) && $_GET['success'] === 'StatusUpdated'): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <i class="fas fa-check-circle me-2"></i>Statut mis à jour avec succès!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 15px;">
            <i class="fas fa-exclamation-triangle me-2"></i>Erreur: <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Order information in modern card layout -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-header bg-primary text-white" style="border-radius: 15px 15px 0 0;">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations de la Commande</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">ID de la commande</small>
                        <p class="fw-bold mb-0">#<?= htmlspecialchars($order['id']) ?></p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">ID du client</small>
                        <p class="fw-bold mb-0"><?= htmlspecialchars($order['user_id']) ?></p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Date de commande</small>
                        <p class="fw-bold mb-0"><?= date('d/m/Y à H:i', strtotime($order['created_at'])) ?></p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted">Montant total</small>
                        <p class="fw-bold text-success mb-0 fs-4"><?= number_format($order['total'], 2) ?>€</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status update form in modern card -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-warning text-dark" style="border-radius: 15px 15px 0 0;">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Mise à jour du Statut</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="index.php?action=admin_update_order_status">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($order['id']) ?>">
                        <div class="row align-items-end">
                            <div class="col-md-8 mb-3">
                                <label for="status" class="form-label fw-semibold">
                                    <i class="fas fa-flag me-2"></i>Statut de la commande
                                </label>
                                <select class="form-select form-select-lg" id="status" name="status" style="border-radius: 10px;">
                                    <option value="Pending" <?= ($order['status'] === 'Pending') ? 'selected' : '' ?>>
                                        🟡 En attente
                                    </option>
                                    <option value="Processing" <?= ($order['status'] === 'Processing') ? 'selected' : '' ?>>
                                        🔵 En traitement
                                    </option>
                                    <option value="Shipped" <?= ($order['status'] === 'Shipped') ? 'selected' : '' ?>>
                                        🚚 Expédiée
                                    </option>
                                    <option value="Delivered" <?= ($order['status'] === 'Delivered') ? 'selected' : '' ?>>
                                        ✅ Livrée
                                    </option>
                                    <option value="Cancelled" <?= ($order['status'] === 'Cancelled') ? 'selected' : '' ?>>
                                        ❌ Annulée
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <button type="submit" class="btn btn-success btn-lg w-100 fw-semibold" style="border-radius: 10px;">
                                    <i class="fas fa-save me-2"></i>Mettre à jour
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Order items in modern card layout with better styling -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-info text-white" style="border-radius: 15px 15px 0 0;">
                    <h5 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Articles de la Commande</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 py-3">Image</th>
                                    <th class="border-0 py-3">Produit</th>
                                    <th class="border-0 py-3 text-center">Quantité</th>
                                    <th class="border-0 py-3 text-end">Prix unitaire</th>
                                    <th class="border-0 py-3 text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderItems as $item): ?>
                                    <tr>
                                        <td class="py-3">
                                            <?php if ($item['image_path']): ?>
                                                 <img src="image.php?file=<?= htmlspecialchars($item['image_path']) ?>" 
                                                        alt="<?= htmlspecialchars($item['nom']) ?>" class="card-img-top" style="height: 250px; object-fit: contain; background: #f8f9fa;">
                                            <?php else: ?>
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 60px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-3">
                                            <h6 class="mb-0 fw-semibold"><?= htmlspecialchars($item['nom']) ?></h6>
                                        </td>
                                        <td class="py-3 text-center">
                                            <span class="badge bg-primary px-3 py-2">
                                                <?= htmlspecialchars($item['quantite']) ?>
                                            </span>
                                        </td>
                                        <td class="py-3 text-end fw-semibold">
                                            <?= number_format($item['prix_unitaire'], 2) ?>€
                                        </td>
                                        <td class="py-3 text-end fw-bold text-success">
                                            <?= number_format($item['prix_unitaire'] * $item['quantite'], 2) ?>€
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    transition: all 0.3s ease;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

.form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
}
</style>

<?php require __DIR__ . '/footer.php'; ?>
