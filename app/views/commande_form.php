<?php require __DIR__ . '/header.php'; ?>

<div class="container-fluid py-5" style="background: linear-gradient(135deg, var(--background-light) 0%, white 100%); border-radius: 1rem; margin-bottom: 2rem;">
  <div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
      <!-- Added modern header with gradient background and icon -->
      <div class="text-center mb-5">
        <div class="d-inline-flex align-items-center justify-content-center bg-white rounded-circle shadow-lg mb-3" style="width: 80px; height: 80px;">
          <i class="fas fa-shopping-cart text-primary" style="font-size: 2rem;"></i>
        </div>
<h1 class="text-white mb-2" 
    style="font-weight: 700; font-size: 2.5rem; color: var(--primary-color) !important;">
  Finaliser votre commande
</h1>
        <p class="text-white-50 mb-0">Dernière étape avant de recevoir vos produits</p>
      </div>

      <div class="row g-4">
        <!-- Split into two columns: form and order summary -->
        <div class="col-lg-7">
          <div class="card border-0 shadow-lg" style="border-radius: 20px;">
            <div class="card-body p-4">
              <h3 class="mb-4 d-flex align-items-center">
                <i class="fas fa-user-edit text-primary me-3"></i>
                Informations de livraison
              </h3>
              
              <form method="post" action="index.php?action=order_submit">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="nom" class="form-label fw-semibold">
                      <i class="fas fa-user text-muted me-2"></i>Nom complet
                    </label>
                    <input type="text" name="nom" id="nom" class="form-control form-control-lg" 
                           value="<?= htmlspecialchars($user['nom']) ?>" 
                           style="border-radius: 12px; border: 2px solid #e9ecef;" required>
                  </div>
                  
                  <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold">
                      <i class="fas fa-envelope text-muted me-2"></i>Email
                    </label>
                    <input type="email" name="email" id="email" class="form-control form-control-lg" 
                           value="<?= htmlspecialchars($user['email']) ?>" 
                           style="border-radius: 12px; border: 2px solid #e9ecef;" required>
                  </div>
                  
                  <div class="col-12">
                    <label for="adresse" class="form-label fw-semibold">
                      <i class="fas fa-map-marker-alt text-muted me-2"></i>Adresse de livraison
                    </label>
                    <input type="text" name="adresse" id="adresse" class="form-control form-control-lg" 
                           placeholder="123 Rue de la Paix, 75001 Paris"
                           style="border-radius: 12px; border: 2px solid #e9ecef;" required>
                  </div>
                  
                  <div class="col-12">
                    <label for="paiement" class="form-label fw-semibold">
                      <i class="fas fa-credit-card text-muted me-2"></i>Méthode de paiement
                    </label>
                    <select name="paiement" id="paiement" class="form-select form-select-lg" 
                            style="border-radius: 12px; border: 2px solid #e9ecef;" required>
                      <option value="">Choisir une méthode</option>
                      <option value="carte">Carte bancaire</option>
                      <option value="paypal">PayPal</option>
                      <option value="virement">Virement bancaire</option>
                    </select>
                  </div>
                </div>
                
                <button type="submit" class="btn btn-lg w-100 mt-4 py-3" 
                        style="background: linear-gradient(135deg, #ff6b6b, #ee5a24); border: none; border-radius: 12px; color: white; font-weight: 600; font-size: 1.1rem;">
                  <i class="fas fa-lock me-2"></i>Valider la commande
                </button>
              </form>
            </div>
          </div>
        </div>
        
        <div class="col-lg-5">
          <div class="card border-0 shadow-lg" style="border-radius: 20px;">
            <div class="card-body p-4">
              <h3 class="mb-4 d-flex align-items-center">
                <i class="fas fa-receipt text-success me-3"></i>
                Récapitulatif
              </h3>
              
              <div class="mb-4">
                <?php foreach ($products as $p): ?>
                  <div class="d-flex align-items-center justify-content-between py-3 border-bottom">
                    <div class="d-flex align-items-center">
                      <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-box text-muted"></i>
                      </div>
                      <div>
                        <h6 class="mb-1"><?= htmlspecialchars($p['nom']) ?></h6>
                        <small class="text-muted">Quantité: <?= $p['quantity'] ?></small>
                      </div>
                    </div>
                    <span class="fw-bold text-primary"><?= number_format($p['subtotal'], 2) ?> €</span>
                  </div>
                <?php endforeach; ?>
              </div>
              
              <div class="bg-light rounded-3 p-3 mb-3">
                <div class="d-flex justify-content-between mb-2">
                  <span>Sous-total:</span>
                  <span><?= number_format($total, 2) ?> €</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                  <span>Livraison:</span>
                  <span class="text-success">Gratuite</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold fs-5">
                  <span>Total:</span>
                  <span class="text-primary"><?= number_format($total, 2) ?> €</span>
                </div>
              </div>
              
              <div class="d-flex align-items-center text-muted">
                <i class="fas fa-shield-alt me-2"></i>
                <small>Paiement sécurisé SSL</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/footer.php'; ?>
