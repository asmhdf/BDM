<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;

    /**
     * AuthController constructor.
     * @param PDO $pdo
     */
    public function __construct($pdo) {
        $this->userModel = new User($pdo);
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