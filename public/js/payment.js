document.addEventListener('DOMContentLoaded', () => {
  if (window.paypal) {
    paypal.Buttons({
      createOrder: async function() {
        const res = await fetch('index.php?action=create_paypal_order', {
          method: 'POST',
          headers: {'Content-Type':'application/json'},
          credentials: 'same-origin',
          body: JSON.stringify({ order_id: window.LOCAL_ORDER_ID })
        });
        const j = await res.json();
        if (j.id) return j.id;
        throw new Error(j.error || 'create order failed');
      },
      onApprove: async function(data) {
        const res = await fetch('index.php?action=capture_paypal_order', {
          method: 'POST',
          headers: {'Content-Type':'application/json'},
          credentials: 'same-origin',
          body: JSON.stringify({ orderID: data.orderID, local_order_id: window.LOCAL_ORDER_ID })
        });
        const json = await res.json();
        if (json.success) {
          window.location.href = 'index.php?action=commande_confirm&order_id=' + encodeURIComponent(window.LOCAL_ORDER_ID);
        } else {
          alert('Erreur capture paiement: ' + (json.error || 'unknown'));
          console.error(json);
        }
      },
      onError: function(err) {
        console.error('PayPal error', err);
        alert('Erreur PayPal. Voir console.');
      }
    }).render('#paypal-button-container');
  }
});