<?php require __DIR__ . '/header.php'; ?>

<div class="container-fluid py-4">
    <!-- Added modern header with gradient background and icon -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
">
                <div class="card-body text-white py-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-shopping-cart fa-2x me-3"></i>
                        <div>
                            <h2 class="mb-1 fw-bold">Gestion des Commandes</h2>
                            <p class="mb-0 opacity-75">Gérez et suivez toutes les commandes de votre boutique</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Replaced basic table with modern cards layout -->
    <div class="row">
        <?php foreach ($orders as $order): ?>
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card border-0 shadow-sm h-100 order-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1 fw-bold text-dark">Commande #<?= htmlspecialchars($order['id']) ?></h5>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>
                                </small>
                            </div>
                            <!-- Added status badges with colors -->
                            <?php 
                            $statusColors = [
                                'Pending' => 'warning',
                                'Processing' => 'info', 
                                'Shipped' => 'primary',
                                'Delivered' => 'success',
                                'Cancelled' => 'danger'
                            ];
                            $statusColor = $statusColors[$order['status']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $statusColor ?> px-3 py-2">
                                <?= htmlspecialchars($order['status']) ?>
                            </span>
                        </div>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user text-primary me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">Client</small>
                                        <span class="fw-semibold">ID: <?= htmlspecialchars($order['user_id']) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-euro-sign text-success me-2"></i>
                                    <div>
                                        <small class="text-muted d-block">Total</small>
                                        <span class="fw-bold text-success"><?= number_format($order['total'], 2) ?>€</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Modern action button with gradient -->
                        <a href="index.php?action=admin_view_order&id=<?= $order['id'] ?>" 
                           class="btn btn-primary w-100 fw-semibold" 
                           style="background: linear-gradient(45deg, #667eea, #764ba2); border: none;">
                            <i class="fas fa-eye me-2"></i>Voir les détails
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.order-card {
    transition: all 0.3s ease;
    border-radius: 15px !important;
}

.order-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
}

.badge {
    font-size: 0.75rem;
    border-radius: 20px;
}
</style>

<?php require __DIR__ . '/footer.php'; ?>
