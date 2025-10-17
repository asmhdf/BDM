<?php require __DIR__ . '/header.php'; ?>

<div class="container-fluid py-5" 
     style="background: linear-gradient(135deg, var(--background-light) 0%, var(--primary-hover) 100%); min-height: 100vh;">
  <div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
      <!-- Added celebration animation and modern success design -->
      <div class="text-center mb-5">
        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-lg mb-4 animate__animated animate__bounceIn" style="width: 100px; height: 100px;">
          <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
        </div>
        <h1 class="text-white mb-2 animate__animated animate__fadeInUp" style="font-weight: 700; font-size: 3rem;">Commande confirmée !</h1>
        <p class="text-white-50 mb-0 animate__animated animate__fadeInUp animate__delay-1s">Merci pour votre confiance</p>
      </div>

      <div class="row g-4">
        <!-- Split into customer info and order details cards -->
        <div class="col-lg-6">
          <div class="card border-0 shadow-lg animate__animated animate__fadeInLeft" style="border-radius: 20px;">
            <div class="card-body p-4">
              <h3 class="mb-4 d-flex align-items-center">
                <i class="fas fa-user-check text-success me-3"></i>
                Informations client
              </h3>
              
              <div class="bg-light rounded-3 p-3 mb-3">
                <div class="d-flex align-items-center mb-3">
                  <i class="fas fa-user text-primary me-3"></i>
                  <div>
                    <small class="text-muted d-block">Nom</small>
                    <strong><?= htmlspecialchars($commande['nom']) ?></strong>
                  </div>
                </div>
                
                <div class="d-flex align-items-center mb-3">
                  <i class="fas fa-envelope text-primary me-3"></i>
                  <div>
                    <small class="text-muted d-block">Email</small>
                    <strong><?= htmlspecialchars($commande['email']) ?></strong>
                  </div>
                </div>
                
                <div class="d-flex align-items-center">
                  <i class="fas fa-map-marker-alt text-primary me-3"></i>
                  <div>
                    <small class="text-muted d-block">Adresse de livraison</small>
                    <strong><?= htmlspecialchars($commande['adresse']) ?></strong>
                  </div>
                </div>
              </div>
              
              <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <small>Un email de confirmation vous a été envoyé</small>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-lg-6">
          <div class="card border-0 shadow-lg animate__animated animate__fadeInRight" style="border-radius: 20px;">
            <div class="card-body p-4">
              <h3 class="mb-4 d-flex align-items-center">
                <i class="fas fa-shopping-bag text-success me-3"></i>
                Articles commandés
              </h3>
              
              <div class="mb-4">
                <?php foreach ($items as $item): ?>
                  <div class="d-flex align-items-center justify-content-between py-3 border-bottom">
                    <div class="d-flex align-items-center">
                      <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-box text-muted"></i>
                      </div>
                      <div>
                        <h6 class="mb-1"><?= htmlspecialchars($item['nom']) ?></h6>
                        <small class="text-muted">Quantité: <?= $item['quantite'] ?></small>
                      </div>
                    </div>
                    <span class="fw-bold text-success"><?= number_format($item['prix_unitaire'], 2) ?> €</span>
                  </div>
                <?php endforeach; ?>
              </div>
              
              <div class="bg-success bg-opacity-10 rounded-3 p-3 mb-4">
                <div class="d-flex align-items-center justify-content-center">
                  <i class="fas fa-truck text-success me-2"></i>
                  <span class="text-success fw-semibold">Livraison estimée: 2-3 jours ouvrés</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Added action buttons with modern styling -->
      <div class="row mt-4">
        <div class="col-12">
          <div class="card border-0 shadow-lg animate__animated animate__fadeInUp animate__delay-2s" style="border-radius: 20px;">
            <div class="card-body p-4 text-center">
              <div class="row g-3">
                <div class="col-md-4">
                  <a href="index.php" class="btn btn-outline-primary btn-lg w-100" style="border-radius: 12px; border-width: 2px;">
                    <i class="fas fa-home me-2"></i>Retour à l'accueil
                  </a>
                </div>
                <div class="col-md-4">
                  <button onclick="window.print()" class="btn btn-primary btn-lg w-100" style="border-radius: 12px;">
                    <i class="fas fa-print me-2"></i>Imprimer la facture
                  </button>
                </div>
                <div class="col-md-4">
                  <a href="mes-commandes.php" class="btn btn-success btn-lg w-100" style="border-radius: 12px;">
                    <i class="fas fa-list me-2"></i>Mes commandes
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Added animate.css for celebration animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

<?php require __DIR__ . '/footer.php'; ?>
