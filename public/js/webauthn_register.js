// ...existing code...
// Helpers base64url <-> ArrayBuffer (same as biometric.js)
function b64ToBuf(b64) {
  b64 = b64.replace(/-/g, '+').replace(/_/g, '/');
  while (b64.length % 4) b64 += '=';
  return Uint8Array.from(atob(b64), c => c.charCodeAt(0)).buffer;
}
function bufToB64(buf) {
  const bytes = new Uint8Array(buf);
  let str = '';
  for (let i=0;i<bytes.length;i++) str+=String.fromCharCode(bytes[i]);
  return btoa(str).replace(/\+/g,'-').replace(/\//g,'_').replace(/=+$/,'');
}

// ...existing code...
async function startRegister() {
  try {
    const res = await fetch('index.php?action=webauthn_register_options', { credentials: 'same-origin' });
    if (res.status === 403) {
      alert('Vous devez être connecté pour enregistrer une clé biométrique. Veuillez vous connecter.');
      window.location.href = 'index.php?action=login';
      return;
    }

    const text = await res.text();
    let server;
    try {
      server = JSON.parse(text);
    } catch (e) {
      console.error('Server returned non-JSON response for register options:', text);
      alert('Erreur serveur (voir console).');
      return;
    }

    const options = server.publicKey || server;
    if (!options) {
      console.error('No publicKey options in server response', server);
      alert('Options WebAuthn manquantes.');
      return;
    }

    // normalize challenge and user.id -> ArrayBuffer
    if (typeof options.challenge === 'string') options.challenge = b64ToBuf(options.challenge);
    if (options.user && typeof options.user.id === 'string') options.user.id = b64ToBuf(options.user.id);

    // Make authenticator selection less strict (increase chance of success)
    options.authenticatorSelection = options.authenticatorSelection || {};
    // prefer resident key if available, but don't require it
    options.authenticatorSelection.residentKey = options.authenticatorSelection.residentKey || 'preferred';
    options.authenticatorSelection.userVerification = options.authenticatorSelection.userVerification || 'preferred';
    options.userVerification = options.userVerification || 'preferred';
    options.attestation = options.attestation || 'none';

    console.log('WebAuthn create options (after normalization):', options);

    // create credential
    let cred;
    try {
      cred = await navigator.credentials.create({ publicKey: options });
    } catch (err) {
      console.error('navigator.credentials.create error', err);
      if (err.name === 'NotAllowedError') {
        alert('Opération WebAuthn annulée ou non autorisée. Vérifiez : authenticator disponible, site en HTTPS (ou localhost), et que vous interagissez directement (pas dans un iframe). Voir console pour détails.');
      } else if (err.name === 'InvalidStateError') {
        alert('Credential déjà existant ou état invalide. Essayez de supprimer les anciennes clés ou changez excludeCredentials.');
      } else {
        alert('Création du credential annulée / échouée. Voir console pour détails.');
      }
      return;
    }

    // ... existing payload + finish registration fetch ...
    const payload = {
      id: cred.id,
      rawId: bufToB64(cred.rawId),
      type: cred.type,
      response: {
        clientDataJSON: bufToB64(cred.response.clientDataJSON),
        attestationObject: bufToB64(cred.response.attestationObject)
      }
    };

    const finish = await fetch('index.php?action=webauthn_finish_registration', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      credentials: 'same-origin',
      body: JSON.stringify(payload)
    });

    const finishText = await finish.text();
    let json;
    try { json = JSON.parse(finishText); } catch (e) {
      console.error('Finish registration returned non-JSON:', finishText);
      alert('Erreur serveur lors de l\'enregistrement (voir console).');
      return;
    }

    if (json.success) {
      alert('Clé enregistrée avec succès');
      window.location.href = 'index.php?action=login';
    } else {
      alert('Erreur enregistrement: ' + (json.error || 'unknown'));
      console.log(json);
    }
  } catch (err) {
    console.error('Register flow error', err);
    alert('Erreur réseau ou inattendue. Voir console.');
  }
}
// ...existing code...

// Expose function to be called from register page button
window.startWebAuthnRegister = startRegister;
// ...existing code...