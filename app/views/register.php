<?php require __DIR__ . '/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-lg-5 col-md-7 col-sm-9">
        <!-- <CHANGE> Modern registration form with improved styling -->
        <div class="form-modern mt-5">
            <div class="text-center mb-4">
                <div class="mb-3">
                    <i class="fas fa-user-plus fa-3x text-primary"></i>
                </div>
                <h2 class="fw-bold mb-2" style="color: var(--text-dark);">Inscription</h2>
                <p class="text-muted">Rejoignez la communauté ShopModerne</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="post">
                <div class="mb-4">
                    <label for="nom" class="form-label fw-semibold">
                        <i class="fas fa-user me-2 text-primary"></i>Nom complet
                    </label>
                    <input type="text" 
                           name="nom" 
                           id="nom" 
                           class="form-control form-control-modern" 
                           placeholder="Votre nom complet"
                           required>
                </div>
                
                <div class="mb-4">
                    <label for="email" class="form-label fw-semibold">
                        <i class="fas fa-envelope me-2 text-primary"></i>Adresse email
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           class="form-control form-control-modern" 
                           placeholder="votre@email.com"
                           required>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold">
                        <i class="fas fa-lock me-2 text-primary"></i>Mot de passe
                    </label>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           class="form-control form-control-modern" 
                           placeholder="••••••••"
                           required>
                    <div class="form-text">
                        <i class="fas fa-info-circle me-1"></i>
                        Minimum 8 caractères recommandés
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="terms" required>
                        <label class="form-check-label text-muted" for="terms">
                            J'accepte les <a href="#" class="text-primary">conditions d'utilisation</a>
                        </label>
                    </div>
                </div>
                <div class="g-recaptcha mb-4" data-sitekey="6LelY-0rAAAAAMOD1DgOXWdVdov7X3TSMexLqbzs"></div>
                
                <button type="submit" class="btn btn-modern-primary w-100 mb-4">
                    <i class="fas fa-user-plus me-2"></i>Créer mon compte
                </button>
                 <?php
                 if (session_status() === PHP_SESSION_NONE) session_start();
                 if (!empty($_SESSION['user']['id'])): ?>
                     <button id="webauthnRegisterBtn" type="button" class="btn btn-secondary mt-2" onclick="startWebAuthnRegister()">Enregistrer une clé (biométrie)</button>
                     <script src="js/webauthn_register.js"></script>
                 <?php else: ?>
                     <div class="mt-2 text-muted small">
                         Après création du compte vous pourrez ajouter une clé biométrique depuis votre espace personnel.
                     </div>
                 <?php endif; ?>
            </form>
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            

            
            <div class="text-center">
                <p class="text-muted mb-3">Déjà inscrit ?</p>
                <a href="index.php?action=login" class="btn btn-outline-primary">
                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                </a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/footer.php'; ?>
