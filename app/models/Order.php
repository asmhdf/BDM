<?php
class Order {
    private $pdo;

    // Constructor: initialize with database connection
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Create a new order with its items.
     * - Calculates the total price from all items
     * - Inserts the order into the 'commandes' table
     * - Inserts each product into 'commande_items'
     * - Uses transactions to ensure data consistency
     */
    public function create($user_id, $nom, $email, $adresse, $paiement, $items) {
        $total = 0;

        // Calculate total price
        foreach ($items as $item) {
            $total += $item['prix'] * $item['quantity'];
        }

        // Start transaction to avoid partial data insertion
        $this->pdo->beginTransaction();

        // Insert order details into 'commandes'
        $stmt = $this->pdo->prepare("
            INSERT INTO commandes (user_id, nom, email, adresse, paiement, total, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$user_id, $nom, $email, $adresse, $paiement, $total]);

        // Get the ID of the newly created order
        $commande_id = $this->pdo->lastInsertId();

        // Insert each product into 'commande_items'
        $itemStmt = $this->pdo->prepare("
            INSERT INTO commande_items (commande_id, produit_id, quantite, prix_unitaire) 
            VALUES (?, ?, ?, ?)
        ");
        foreach ($items as $item) {
            $itemStmt->execute([$commande_id, $item['id'], $item['quantity'], $item['prix']]);
        }

        // Commit the transaction
        $this->pdo->commit();

        return $commande_id; // Return new order ID
    }

    // Fetch an order by its ID
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM commandes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Fetch all items belonging to a specific order
    public function getItems($commande_id) {
        $stmt = $this->pdo->prepare("
            SELECT ci.*, p.nom, p.image, p.image_type
            FROM commande_items ci
            JOIN produits p ON ci.produit_id = p.id
            WHERE ci.commande_id = ?
        ");
        $stmt->execute([$commande_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch all orders
    public function getAllOrders() {
        $stmt = $this->pdo->query("SELECT * FROM commandes"); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch a single order by ID
    public function getOrderById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM commandes WHERE id = ?"); 
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update the status of an order
    public function updateOrderStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE commandes SET status = ? WHERE id = ?"); 
        return $stmt->execute([$status, $id]);
    }

    // Fetch all orders with a specific status (e.g., 'Pending', 'Shipped')
    public function getOrdersByStatus($status) {
        $stmt = $this->pdo->prepare("SELECT * FROM commandes WHERE status = ?");
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
