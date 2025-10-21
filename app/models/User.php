<?php
// Class representing a user and handling authentication
class User {
    private $pdo; // PDO instance for database connection
    public $id;
    public $email;
    public $password;
    public $credential_id;
    public $public_key;


    // Constructor: initializes the object with the PDO connection
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

   public function saveWebAuthnData($id, $credential_id, $public_key) {
        $sql = "UPDATE users SET credential_id = :cid, public_key = :pk WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':cid' => $credential_id,
            ':pk'  => $public_key,
            ':id'  => $id
        ]);
    }

    public function getByCredentialId($credential_id) {
        $sql = "SELECT * FROM users WHERE credential_id = :cid LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':cid' => $credential_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

    // Register a new user
    public function register($nom, $email, $password) {
        // Hash the password securely using BCRYPT
        $hash = password_hash($password, PASSWORD_BCRYPT);

        // Prepare the SQL statement to insert a new user
        $stmt = $this->pdo->prepare("INSERT INTO users (nom, email, password) VALUES (?, ?, ?)");

        // Execute the statement with the provided parameters
        return $stmt->execute([$nom, $email, $hash]);
    }

    // Login a user
    public function login($email, $password) {
        // Prepare the SQL statement to get user data by email
        $stmt = $this->pdo->prepare("SELECT id, nom, email, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(); // Fetch user data

        // Verify password
        if ($user && password_verify($password, $user['password'])) {
            return $user; // Return user data if login is successful
        }

        return false; // Return false if login fails
    }
}
