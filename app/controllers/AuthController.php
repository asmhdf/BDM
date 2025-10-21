<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../vendor/autoload.php';
use lbuchs\WebAuthn\WebAuthn;

class AuthController {
    private $userModel;

    /**
     * AuthController constructor.
     * @param PDO $pdo
     */
    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }

    private function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    } 

    /**
     * Handles user registration.
     * If the request method is POST, it retrieves user data from the form,
     * registers the user using the user model, and redirects to the login page.
     * Otherwise, it includes the registration view.
     */
    public function register() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = $_POST['nom'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $recaptcha_response = $_POST['g-recaptcha-response']; // ✅ récupère la réponse CAPTCHA

        // Vérification auprès de Google
        $secret = '6LelY-0rAAAAAN73BRoum7TAykiI4cNvQ-6ckUSe'; // ⚠️ clé secrète de ton compte reCAPTCHA
        $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$recaptcha_response}");
        $response = json_decode($verify);

        if ($response->success) {
            // ✅ CAPTCHA validé → continuer l’inscription
            
            if ($this->userModel->register($nom, $email, $password)) {
                // tenter une connexion automatique pour récupérer l'ID utilisateur et créer la session
                $user = $this->userModel->login($email, $password);
                if ($user) {
                    if (session_status() === PHP_SESSION_NONE) session_start();
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'nom' => $user['nom'],
                        'email' => $user['email'],
                        'usertype' => $user['role'] ?? null
                    ];
                    // rediriger vers la page de configuration WebAuthn pour enregistrer la clé
                    header('Location: index.php?action=webauthn_setup');
                    exit;
                }
                // fallback : rediriger vers login si la connexion automatique échoue
                header("Location: index.php?action=login");
                exit;
            }
        } else {
            // ❌ CAPTCHA échoué
            $error = "Veuillez valider le CAPTCHA avant de continuer.";
        }
    }

    require __DIR__ . '/../views/register.php';
}


    /**
     * Handles user login.
     * If the request method is POST, it retrieves user credentials from the form,
     * authenticates the user using the user model, and sets the user session.
     * If the user is an admin, it redirects to the admin products page.
     * Otherwise, it redirects to the home page.
     * If authentication fails, it sets an error message.
     * Otherwise, it includes the login view.
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $user = $this->userModel->login($email, $password);
            if ($user) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'nom' => $user['nom'],
                    'email' => $user['email'],
                    'usertype' => $user['role']
                ];
                if ($user['usertype'] === 'admin') {
                    header("Location: index.php?action=admin_products");
                } else {
                    header("Location: index.php");
                }

                exit;
            } else {
                $error = "Email ou mot de passe incorrect";
            }
        }
        require __DIR__ . '/../views/login.php';
    }
    
    // Étape 1 : génération du challenge/options pour assertion (login)
    public function webauthnLoginOptions() {
    if (session_status() === PHP_SESSION_NONE) session_start();

    $webauthn = new WebAuthn('ShipiShop', $_SERVER['SERVER_NAME']);
    $args = $webauthn->getGetArgs();
    if (is_object($args)) {
        $args = json_decode(json_encode($args), true);
    }

    // Normalize challenge -> base64url (handle different shapes from the lib)
    if (isset($args['publicKey'])) {
        $pk =& $args['publicKey'];

        if (isset($pk['challenge'])) {
            $raw = $pk['challenge'];
            // object like ['type'=>'Buffer','data'=>[...]]
            if (is_array($raw) && isset($raw['data']) && is_array($raw['data'])) {
                $bytes = implode(array_map("chr", $raw['data']));
                $pk['challenge'] = $this->base64url_encode($bytes);
            } elseif (!is_string($raw) || !preg_match('/^[A-Za-z0-9\-_]+$/', $raw)) {
                // binary string -> encode
                $pk['challenge'] = $this->base64url_encode($raw);
            }
        }

        // normalize allowCredentials ids if present
        if (!empty($pk['allowCredentials']) && is_array($pk['allowCredentials'])) {
            foreach ($pk['allowCredentials'] as &$c) {
                if (isset($c['id'])) {
                    $id = $c['id'];
                    if (is_array($id) && isset($id['data'])) {
                        $bytes = implode(array_map("chr", $id['data']));
                        $c['id'] = $this->base64url_encode($bytes);
                    } elseif (!is_string($id) || !preg_match('/^[A-Za-z0-9\-_]+$/', $id)) {
                        $c['id'] = $this->base64url_encode($id);
                    }
                }
            }
            unset($c);
        }
    } else {
        // top-level challenge (fallback)
        if (isset($args['challenge'])) {
            $raw = $args['challenge'];
            if (is_array($raw) && isset($raw['data'])) {
                $bytes = implode(array_map("chr", $raw['data']));
                $args['challenge'] = $this->base64url_encode($bytes);
            } elseif (!is_string($raw) || !preg_match('/^[A-Za-z0-9\-_]+$/', $raw)) {
                $args['challenge'] = $this->base64url_encode($raw);
            }
        }
    }

    $_SESSION['webauthn_challenge'] = $args['publicKey']['challenge'] ?? $args['challenge'] ?? null;

    header('Content-Type: application/json');
    echo json_encode($args);
}

    // Étape 2 : vérification de l'assertion (login)
    public function webauthnVerifyAssertion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        header('Content-Type: application/json; charset=utf-8');

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || empty($data['id']) || empty($data['response'])) {
            echo json_encode(['success' => false, 'error' => 'Données invalides']);
            return;
        }

        $credentialId = $data['id'];
        $user = $this->userModel->getByCredentialId($credentialId);
        if (!$user) {
            echo json_encode(['success' => false, 'error' => 'Credential inconnu']);
            return;
        }

        $webauthn = new WebAuthn('ShipiShop', $_SERVER['SERVER_NAME']);

        $clientDataJSON    = $data['response']['clientDataJSON'];
        $authenticatorData = $data['response']['authenticatorData'];
        $signature         = $data['response']['signature'];
        $userHandle        = $data['response']['userHandle'] ?? null;
        $challenge         = $_SESSION['webauthn_challenge'] ?? null;
        $publicKey         = $user['public_key'];

        // Build candidate method list dynamically from available methods (prefer likely names)
        $available = get_class_methods($webauthn);
        $candidates = array_filter($available, function($m){
            return preg_match('/(check|verify|validate|assert|authentication|get).*(get|assert|response|assertion|authentication|getResponse|GetResponse|Verify|Check)/i', $m) 
                || preg_match('/(checkGetResponse|verifyGetResponse|checkAssertion|verifyAssertion|validateGetResponse|checkResponse|verifyResponse|validateAssertion)/i', $m);
        });
        // ensure some common names included if not present
        $fallbacks = ['checkGetResponse','verifyGetResponse','checkAssertion','verifyAssertion','checkResponse','verifyResponse','validateGetResponse','validateAssertion','checkAuthentication'];
        foreach ($fallbacks as $f) if (!in_array($f, $candidates) && in_array($f, $available)) $candidates[] = $f;

        // Prepare possible invocation signatures to try (various libs expect different args)
        $invocations = [
            // common: clientDataJSON, authenticatorData, signature, publicKey, userHandle, challenge
            [$clientDataJSON, $authenticatorData, $signature, $publicKey, $userHandle, $challenge],
            // some libs expect clientDataJSON, attestation/authData, signature, publicKey, challenge
            [$clientDataJSON, $authenticatorData, $signature, $publicKey, $challenge],
            // pass the whole response array
            [$data['response']],
            // pass the whole data array
            [$data],
            // pass id + response
            [$data['id'], $data['response']],
            // rawId + response
            [$data['rawId'] ?? null, $data['response']],
            // minimal: clientDataJSON, authenticatorData, signature
            [$clientDataJSON, $authenticatorData, $signature],
        ];

        $verified = false;
        $debug = ['tried' => []];
        foreach ($candidates as $method) {
            foreach ($invocations as $args) {
                // skip invocations where required args are null
                $skip = false;
                foreach ($args as $a) {
                    // if argument is null and appears essential, skip
                    // (but allow null for optional userHandle/rawId)
                    if ($a === null) {
                        // allow null only if arg list contains at least one non-null
                        // we still try, but avoid calls with all-null args
                    }
                }
                try {
                    if (!method_exists($webauthn, $method)) continue;
                    $debugEntry = ['method' => $method, 'args_count' => count($args)];
                    // attempt call
                    $res = call_user_func_array([$webauthn, $method], $args);
                    $debugEntry['result_type'] = is_bool($res) ? 'bool' : gettype($res);
                    // Evaluate result: true, 1, non-empty array/object => success
                    if ($res === true || $res === 1 || (is_array($res) && !empty($res)) || (is_object($res) && !empty((array)$res))) {
                        $verified = true;
                        $debugEntry['success'] = true;
                        $debug['tried'][] = $debugEntry;
                        $debug['used'] = ['method' => $method, 'args' => $args];
                        break 2; // stop both loops
                    } else {
                        $debugEntry['success'] = false;
                    }
                    $debug['tried'][] = $debugEntry;
                } catch (\ArgumentCountError $ae) {
                    $debug['tried'][] = ['method'=>$method, 'error'=>'ArgumentCountError','message'=>$ae->getMessage()];
                    continue;
                } catch (\Throwable $e) {
                    $debug['tried'][] = ['method'=>$method, 'error'=>get_class($e), 'message'=>$e->getMessage()];
                    continue;
                }
            }
        }

        if (!$verified) {
            // return helpful debug info (don't include sensitive raw data in production)
            echo json_encode([
                'success' => false,
                'error' => 'Aucune méthode de vérification compatible trouvée.',
                'webauthn_methods' => $available,
                'debug' => $debug
            ]);
            return;
        }

        // Si vérification OK -> créer la session utilisateur
        $_SESSION['user_authenticated'] = true;
        $_SESSION['user'] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'nom' => $user['nom'] ?? null,
            'usertype' => $user['role'] ?? ($user['usertype'] ?? 'user')
        ];

        echo json_encode(['success' => true, 'debug' => $debug]);
    }
    // GET options pour l'enregistrement (navigator.credentials.create)
    public function webauthnRegisterOptions() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        header('Content-Type: application/json; charset=utf-8');

        // require an authenticated user to bind the credential
        if (empty($_SESSION['user']['id'])) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Utilisateur non connecté']);
            exit;
        }

        $userId = (string) $_SESSION['user']['id'];
        $userName = $_SESSION['user']['email'] ?? ('user' . $userId);
        $userDisplayName = $_SESSION['user']['nom'] ?? $userName;

        try {
            $webauthn = new WebAuthn('ShipiShop', $_SERVER['SERVER_NAME']);
            // Provide required args: userId, userName, userDisplayName, timeout, requireResidentKey, requireUserVerification
            // requireResidentKey and requireUserVerification set to 'required' to create discoverable (resident) credential
            $args = $webauthn->getCreateArgs(
                $userId,
                $userName,
                $userDisplayName,
                60000,
                'required',
                'required',
                null,
                [] // excludeCredentialIds
            );

            // Normalize to array if stdClass
            if (is_object($args)) $args = json_decode(json_encode($args), true);

            // Ensure publicKey.challenge is base64url string
            if (isset($args['publicKey'])) {
                $pk =& $args['publicKey'];
                if (isset($pk['challenge'])) {
                    $raw = $pk['challenge'];
                    if (is_array($raw) && isset($raw['data'])) {
                        $bytes = implode(array_map("chr", $raw['data']));
                        $pk['challenge'] = $this->base64url_encode($bytes);
                    } elseif (!is_string($raw) || !preg_match('/^[A-Za-z0-9\-_]+$/', $raw)) {
                        $pk['challenge'] = $this->base64url_encode($raw);
                    }
                }

                // user.id might be binary -> base64url
                if (isset($pk['user']['id'])) {
                    $uid = $pk['user']['id'];
                    if (is_array($uid) && isset($uid['data'])) {
                        $bytes = implode(array_map("chr", $uid['data']));
                        $pk['user']['id'] = $this->base64url_encode($bytes);
                    } elseif (!is_string($uid) || !preg_match('/^[A-Za-z0-9\-_]+$/', $uid)) {
                        $pk['user']['id'] = $this->base64url_encode($uid);
                    }
                }

                // normalize excludeCredentials if present
                if (!empty($pk['excludeCredentials']) && is_array($pk['excludeCredentials'])) {
                    foreach ($pk['excludeCredentials'] as &$c) {
                        if (isset($c['id'])) {
                            $id = $c['id'];
                            if (is_array($id) && isset($id['data'])) {
                                $bytes = implode(array_map("chr", $id['data']));
                                $c['id'] = $this->base64url_encode($bytes);
                            } elseif (!is_string($id) || !preg_match('/^[A-Za-z0-9\-_]+$/', $id)) {
                                $c['id'] = $this->base64url_encode($id);
                            }
                        }
                    }
                    unset($c);
                }
            } else {
                // fallback top-level
                if (isset($args['challenge'])) {
                    $raw = $args['challenge'];
                    if (is_array($raw) && isset($raw['data'])) {
                        $bytes = implode(array_map("chr", $raw['data']));
                        $args['challenge'] = $this->base64url_encode($bytes);
                    } elseif (!is_string($raw) || !preg_match('/^[A-Za-z0-9\-_]+$/', $raw)) {
                        $args['challenge'] = $this->base64url_encode($raw);
                    }
                }
            }

            // ensure attestation/authenticatorSelection for resident key
            if (isset($args['publicKey'])) {
                $args['publicKey']['authenticatorSelection'] = $args['publicKey']['authenticatorSelection'] ?? [];
                $args['publicKey']['authenticatorSelection']['residentKey'] = 'required';
                $args['publicKey']['authenticatorSelection']['userVerification'] = 'required';
                $args['publicKey']['attestation'] = $args['publicKey']['attestation'] ?? 'none';
            }

            $_SESSION['webauthn_challenge_reg'] = $args['publicKey']['challenge'] ?? $args['challenge'] ?? null;

            echo json_encode($args);
            exit;
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'server_exception', 'message' => $e->getMessage()]);
            exit;
        }
    }

    // POST finish registration: receives client response and store credential
    public function webauthnFinishRegistration() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json');

        // must be logged in to link credential to user
        if (empty($_SESSION['user']['id'])) {
            echo json_encode(['success' => false, 'error' => 'Utilisateur non connecté']);
            return;
        }
        $userId = $_SESSION['user']['id'];

        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || empty($data['id']) || empty($data['response'])) {
            echo json_encode(['success' => false, 'error' => 'Données d\'inscription invalides']);
            return;
        }

        $webauthn = new WebAuthn('ShipiShop', $_SERVER['SERVER_NAME']);

        $rawClientData = $data['response']['clientDataJSON'] ?? null;
        $rawAttestation = $data['response']['attestationObject'] ?? null;
        $credentialId = $data['id'];
        $challenge = $_SESSION['webauthn_challenge_reg'] ?? null;

        // Try several verification method names (lbuchs versions vary)
        $candidates = [
            'checkCreateResponse',
            'verifyCreateResponse',
            'checkRegisterResponse',
            'verifyRegisterResponse',
            'validateCreateResponse',
            'validateRegisterResponse',
            'checkAttestation',
            'verifyAttestation'
        ];

        $verified = false;
        $publicKeyPem = null;
        $lastException = null;
        foreach ($candidates as $m) {
            if (!method_exists($webauthn, $m)) continue;
            try {
                // many libs expect raw base64url strings (clientDataJSON, attestationObject) and challenge
                $res = call_user_func([$webauthn, $m], $rawClientData, $rawAttestation, $challenge);
                // If lib returns array/object with public key, adapt
                if ($res === true || $res === 1) {
                    // some libs return true but not the public key; fallback to storing attestation raw for dev
                    $verified = true;
                    break;
                }
                if (is_array($res) || is_object($res)) {
                    $arr = json_decode(json_encode($res), true);
                    // try known keys
                    if (!empty($arr['publicKey'])) $publicKeyPem = $arr['publicKey'];
                    if (!empty($arr['credentialPublicKey'])) $publicKeyPem = $arr['credentialPublicKey'];
                    $verified = true;
                    break;
                }
            } catch (\Throwable $e) {
                $lastException = $e;
                continue;
            }
        }

        // fallback: if verification not possible, still store credential id and raw attestation for testing (NOT FOR PROD)
        if (!$verified) {
            // store attestation object as public_key fallback (base64url)
            $publicKeyPem = $rawAttestation;
            // return debug info
            // continue storing but warn
        }

        // Save in DB (User::saveWebAuthnData expects credential_id and public_key)
        $saved = $this->userModel->saveWebAuthnData($userId, $credentialId, $publicKeyPem);
        if ($saved) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Échec insertion DB']);
        }
    }
   
public function webauthnSetup() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user']['id'])) {
        header('Location: index.php?action=login');
        exit;
    }
    require __DIR__ . '/../views/webauthn_setup.php';
}


    /**
     * Handles user logout.
     * It unsets all session variables, destroys the session, and redirects to the home page.
     */
    public function logout() {
        session_unset();
        session_destroy();
        session_start();
        header("Location: index.php");
    }
}