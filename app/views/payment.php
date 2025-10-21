<?php require __DIR__ . '/header.php'; ?>
<div class="container mt-5">
  <h3>Paiement commande #<?= htmlspecialchars($order['id']) ?></h3>
  <p>Total : <?= htmlspecialchars(number_format($total,2)) ?> EUR</p>

  <?php $ppClient = getenv('AXbuLfkYCEO41t96JXmTjzRdOe5vcKqChXmRke1qYKpNtDbe66lbxdGK-bprCXqOi61wfIJg5gaunO_L') ?: 'AXbuLfkYCEO41t96JXmTjzRdOe5vcKqChXmRke1qYKpNtDbe66lbxdGK-bprCXqOi61wfIJg5gaunO_L'; ?>
  <script src="https://www.paypal.com/sdk/js?client-id=<?= htmlspecialchars($ppClient) ?>&currency=EUR"></script>

  <div id="paypal-button-container"></div>
</div>

<script>
// expose order id to JS
window.LOCAL_ORDER_ID = <?= json_encode($order['id']) ?>;
</script>
<script src="js/payment.js"></script>
<?php require __DIR__ . '/footer.php'; ?>