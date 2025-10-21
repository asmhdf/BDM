// ...existing code...
// Helpers base64url <-> ArrayBuffer
function b64ToBuf(b64) {
    if (typeof b64 !== 'string') throw new TypeError('b64ToBuf expects a base64url string');
    b64 = b64.replace(/-/g, '+').replace(/_/g, '/');
    while (b64.length % 4) b64 += '=';
    const bin = atob(b64);
    const len = bin.length;
    const buf = new Uint8Array(len);
    for (let i = 0; i < len; i++) buf[i] = bin.charCodeAt(i);
    return buf.buffer;
}

function bufToB64(buffer) {
    const bytes = new Uint8Array(buffer);
    let str = '';
    for (let i = 0; i < bytes.byteLength; i++) str += String.fromCharCode(bytes[i]);
    return btoa(str).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
}

document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('biometricLogin');
  if (!btn) return;

  btn.addEventListener('click', async (e) => {
    e.preventDefault();

    if (!window.PublicKeyCredential) {
      alert('Votre navigateur ne supporte pas WebAuthn / biométrie.');
      return;
    }

    try {
      console.log('Starting WebAuthn login flow: requesting options from server...');
      const res = await fetch('index.php?action=webauthn_login_options', { cache: 'no-store', credentials: 'same-origin' });
      if (!res.ok) {
        console.error('Server error retrieving options', res.status);
        alert('Impossible de récupérer les options WebAuthn.');
        return;
      }

      const serverData = await res.json();
      console.log('Options received', serverData);
      const options = serverData.publicKey || serverData;
      if (!options) {
        console.error('No publicKey options in server response', serverData);
        alert('Options WebAuthn manquantes.');
        return;
      }

      // robust challenge extraction (publicKey.challenge OR challenge)
      const rawChallenge = options.challenge ?? serverData.challenge ?? (serverData.publicKey && serverData.publicKey.challenge);
      if (!rawChallenge) {
        console.error('No challenge present in server response', serverData);
        alert('Options WebAuthn invalides (challenge manquant).');
        return;
      }

      // normalize challenge -> ArrayBuffer
      function normalizeChallenge(ch) {
        if (ch instanceof ArrayBuffer) return ch;
        if (ArrayBuffer.isView(ch)) return ch.buffer;
        if (typeof ch === 'string') return b64ToBuf(ch);
        if (ch && Array.isArray(ch.data)) return new Uint8Array(ch.data).buffer;
        throw new TypeError('Unsupported challenge format');
      }

      try {
        options.challenge = normalizeChallenge(rawChallenge);
      } catch (err) {
        console.error('Error converting challenge', err);
        alert('Format du challenge invalide.');
        return;
      }

      // normalize allowCredentials ids (if any)
      if (options.allowCredentials && Array.isArray(options.allowCredentials)) {
        options.allowCredentials = options.allowCredentials.map(c => {
          let id = c.id;
          try {
            if (id instanceof ArrayBuffer) id = id;
            else if (ArrayBuffer.isView(id)) id = id.buffer;
            else if (typeof id === 'string') id = b64ToBuf(id);
            else if (id && Array.isArray(id.data)) id = new Uint8Array(id.data).buffer;
            else throw new TypeError('Unsupported allowCredential id format');
          } catch (err) {
            console.warn('Cannot normalize allowCredential id', err);
          }
          return Object.assign({}, c, { id });
        });
      }

      // call authenticator
      let cred;
      try {
        cred = await navigator.credentials.get({ publicKey: options });
      } catch (err) {
        console.error('navigator.credentials.get error', err);
        alert('Authentification biométrique annulée ou impossible.');
        return;
      }

      // build payload
      const payload = {
        id: cred.id,
        rawId: bufToB64(cred.rawId),
        type: cred.type,
        response: {
          clientDataJSON: bufToB64(cred.response.clientDataJSON),
          authenticatorData: bufToB64(cred.response.authenticatorData),
          signature: bufToB64(cred.response.signature),
          userHandle: cred.response.userHandle ? bufToB64(cred.response.userHandle) : null
        }
      };

      // send to server for verification
      const verify = await fetch('index.php?action=webauthn_verify_assertion', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload),
        credentials: 'same-origin'
      });

      if (!verify.ok) {
        console.error('Verify endpoint returned', verify.status);
        alert('Erreur serveur lors de la vérification.');
        return;
      }

      const result = await verify.json();
      console.log('Verify result', result);
      if (result.success) {
        window.location.href = 'index.php?action=panier';
      } else {
        alert(result.error || 'Échec de la vérification biométrique.');
      }
    } catch (err) {
      console.error('Unexpected error in WebAuthn flow', err);
      alert('Erreur inattendue. Voir console pour détails.');
    }
  });
});
// ...existing code...