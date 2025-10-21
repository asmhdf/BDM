<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user']['id'])) {
    header('Location: index.php?action=login');
    exit;
}
require __DIR__ . '/header.php';
?>
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <h3>Configurer l’authentification biométrique</h3>
      <p class="text-muted">Cliquez pour enregistrer une clé sur cet appareil (réservé à votre compte).</p>

      <div class="mb-3">
        <button id="webauthnRegisterBtn" type="button" class="btn btn-primary"
                onclick="startWebAuthnRegister()">
          <i class="bi bi-fingerprint"></i> Enregistrer une clé (biométrie)
        </button>
      </div>

      <div id="webauthnStatus" class="mt-3 text-muted small"></div>
    </div>
  </div>
</div>

<script src="js/webauthn_register.js"></script>

<?php require __DIR__ . '/footer.php'; ?>