<?php require __DIR__ . '/header.php'; ?>

<!-- Added modern admin dashboard with enhanced styling and icons -->
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="dashboard-title">
                <i class="fas fa-tachometer-alt me-3"></i>
                Tableau de Bord Admin
            </h2>
            <p class="text-muted">Vue d'ensemble de votre boutique en ligne</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-5">
        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stats-card pending">
                <div class="stats-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stats-content">
                    <h3><?= htmlspecialchars($totalPendingOrders) ?></h3>
                    <p>Commandes en Attente</p>
                </div>
                <div class="stats-trend">
                    <i class="fas fa-arrow-up"></i>
                    <span>+12%</span>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stats-card processing">
                <div class="stats-icon">
                    <i class="fas fa-cog fa-spin"></i>
                </div>
                <div class="stats-content">
                    <h3><?= htmlspecialchars($totalProcessingOrders) ?></h3>
                    <p>En Traitement</p>
                </div>
                <div class="stats-trend">
                    <i class="fas fa-arrow-up"></i>
                    <span>+8%</span>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stats-card shipped">
                <div class="stats-icon">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <div class="stats-content">
                    <h3><?= htmlspecialchars($totalShippedOrders) ?></h3>
                    <p>Expédiées</p>
                </div>
                <div class="stats-trend">
                    <i class="fas fa-arrow-up"></i>
                    <span>+15%</span>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stats-card delivered">
                <div class="stats-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-content">
                    <h3><?= htmlspecialchars($totalDeliveredOrders) ?></h3>
                    <p>Livrées</p>
                </div>
                <div class="stats-trend">
                    <i class="fas fa-arrow-up"></i>
                    <span>+22%</span>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stats-card cancelled">
                <div class="stats-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stats-content">
                    <h3><?= htmlspecialchars($totalCancelledOrders) ?></h3>
                    <p>Annulées</p>
                </div>
                <div class="stats-trend">
                    <i class="fas fa-arrow-down"></i>
                    <span>-5%</span>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-sm-6">
            <div class="stats-card products">
                <div class="stats-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stats-content">
                    <h3><?= htmlspecialchars($totalProducts) ?></h3>
                    <p>Total Produits</p>
                </div>
                <div class="stats-trend">
                    <i class="fas fa-arrow-up"></i>
                    <span>+3%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4 mb-5">
        <div class="col-12">
            <h4 class="mb-4">
                <i class="fas fa-bolt me-2"></i>
                Actions Rapides
            </h4>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-plus"></i>
                </div>
                <h5>Ajouter Produit</h5>
                <p>Créer un nouveau produit</p>
                <a href="#" class="btn btn-primary btn-sm">Ajouter</a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-list"></i>
                </div>
                <h5>Gérer Commandes</h5>
                <p>Voir toutes les commandes</p>
                <a href="#" class="btn btn-primary btn-sm">Gérer</a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h5>Clients</h5>
                <p>Gérer les utilisateurs</p>
                <a href="#" class="btn btn-primary btn-sm">Voir</a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="action-card">
                <div class="action-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h5>Rapports</h5>
                <p>Analyses et statistiques</p>
                <a href="#" class="btn btn-primary btn-sm">Analyser</a>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="activity-card">
                <h4 class="mb-4">
                    <i class="fas fa-history me-2"></i>
                    Activité Récente
                </h4>
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-icon success">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="activity-content">
                            <h6>Nouvelle commande #1234</h6>
                            <p>Client: Jean Dupont - 150€</p>
                            <small>Il y a 5 minutes</small>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon info">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="activity-content">
                            <h6>Produit ajouté</h6>
                            <p>Nouveau produit: Smartphone XYZ</p>
                            <small>Il y a 1 heure</small>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="activity-content">
                            <h6>Stock faible</h6>
                            <p>Produit: Casque Audio (5 restants)</p>
                            <small>Il y a 2 heures</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Added modern dashboard styles */
.dashboard-title {
    color: #2d3748;
    font-weight: 700;
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
}

.stats-card {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border: none;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 180px;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stats-card.pending::before { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.stats-card.processing::before { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stats-card.shipped::before { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.stats-card.delivered::before { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.stats-card.cancelled::before { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); }
.stats-card.products::before { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    margin-bottom: 1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stats-card.pending .stats-icon { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.stats-card.processing .stats-icon { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.stats-card.shipped .stats-icon { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.stats-card.delivered .stats-icon { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
.stats-card.cancelled .stats-icon { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); }
.stats-card.products .stats-icon { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }

.stats-content h3 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.stats-content p {
    color: #718096;
    font-weight: 500;
    margin: 0;
    font-size: 0.9rem;
}

.stats-trend {
    position: absolute;
    top: 1rem;
    right: 1rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.8rem;
    font-weight: 600;
}

.stats-trend i {
    color: #48bb78;
}

.stats-trend .fa-arrow-down {
    color: #f56565;
}

.action-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    height: 100%;
}

.action-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.action-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    color: white;
    font-size: 1.5rem;
}

.action-card h5 {
    color: #2d3748;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.action-card p {
    color: #718096;
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
}

.activity-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

.activity-card h4 {
    color: #2d3748;
    font-weight: 600;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.activity-icon.success { background: #48bb78; }
.activity-icon.info { background: #4299e1; }
.activity-icon.warning { background: #ed8936; }

.activity-content h6 {
    color: #2d3748;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.activity-content p {
    color: #4a5568;
    margin-bottom: 0.25rem;
    font-size: 0.9rem;
}

.activity-content small {
    color: #a0aec0;
    font-size: 0.8rem;
}

@media (max-width: 768px) {
    .stats-card {
        height: auto;
        padding: 1.5rem;
    }
    
    .dashboard-title {
        font-size: 2rem;
    }
}
</style>

<?php require __DIR__ . '/footer.php'; ?>
