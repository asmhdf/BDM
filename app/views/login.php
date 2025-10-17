<?php require __DIR__ . '/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-lg-5 col-md-7 col-sm-9">
        <div class="form-modern mt-5">
            <div class="text-center mb-4">
                <div class="mb-3">
                    <i class="fas fa-user-circle fa-3x text-primary"></i>
                </div>
                <h2 class="fw-bold mb-2" style="color: var(--text-dark);">Connexion</h2>
                <p class="text-muted">Accédez à votre compte ShopModerne</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="post">
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
                </div>
                
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember">
                        <label class="form-check-label text-muted" for="remember">
                            Se souvenir de moi
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-modern-primary w-100 mb-4">
                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                </button>
            </form>
            
            <div class="text-center">
                <p class="text-muted mb-3">Pas encore de compte ?</p>
                <a href="index.php?action=register" class="btn btn-outline-primary">
                    <i class="fas fa-user-plus me-2"></i>Créer un compte
                </a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/footer.php'; ?>
