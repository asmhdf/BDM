<?php
// filepath: e:\xampp\htdocs\ecom\app\controllers\OrderController.php
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Product.php';

class OrderController {
    private $orderModel;
    private $productModel;
    private $pdo;


    public function __construct($pdo) {
        $this->orderModel = new Order($pdo);
        $this->productModel = new Product($pdo);
        $this->pdo = $pdo;
    }

    /**
     * Displays the order form.
     *
     * Checks if the user is logged in, redirects to login page if not.
     * Retrieves user information from the session.
     * Retrieves cart items from the session and calculates the total.
     * Includes the order form view.
     */
    public function form() {
        // Check if the user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $user = $_SESSION['user']; // Get user info from session
        $cart = $_SESSION['cart'] ?? []; // Get cart items from session
        $products = [];
        $total = 0;
        // Loop through cart items
        foreach ($cart as $id => $qty) {
            $product = $this->productModel->getById($id); // Get product details by id
            if ($product) {
                $product['quantity'] = $qty; // Add quantity to product array
                $product['subtotal'] = $product['prix'] * $qty; // Calculate subtotal
                $products[] = $product; // Add product to products array
                $total += $product['subtotal']; // Add subtotal to total
            }
        }
        require __DIR__ . '/../views/commande_form.php'; // Include order form view
    }

    /**
     * Submits the order.
     *
     * Checks if the user is logged in, redirects to login page if not.
     * Retrieves user information from the session.
     * Retrieves order details from the POST request.
     * Retrieves cart items from the session.
     * Creates a new order in the database.
     * Clears the cart session variable.
     * Redirects to the order confirmation page.
     */
    public function submit() {
        // Check if the user is logged in
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit;
        }
        $user = $_SESSION['user']; // Get user info from session
        $nom = $_POST['nom']; // Get name from POST request
        $email = $_POST['email']; // Get email from POST request
        $adresse = $_POST['adresse']; // Get address from POST request
        $paiement = $_POST['paiement']; // Get payment method from POST request
        $cart = $_SESSION['cart'] ?? []; // Get cart items from session
        $products = [];
        // Loop through cart items
        foreach ($cart as $id => $qty) {
            $product = $this->productModel->getById($id); // Get product details by id
            if ($product) {
                $product['quantity'] = $qty; // Add quantity to product array
                $products[] = $product;
            }
        }
        $commande_id = $this->orderModel->create($user['id'], $nom, $email, $adresse, $paiement, $products); // Create order in database
        $_SESSION['cart'] = []; // Clear cart
        header("Location: index.php?action=order_confirm&id=$commande_id"); // Redirect to order confirmation page
        exit;
    }

    /**
     * Displays the order confirmation page.
     *
     * Gets the order ID from the GET request.
     * Retrieves the order details from the database.
     * Retrieves the order items from the database.
     * Includes the order confirmation view.
     */
    public function confirm() {
        $id = $_GET['id'] ?? null; // Get order id from GET request
        if (!$id) {
            http_response_code(404);
            exit('Commande non trouvée');
        }
        $commande = $this->orderModel->getById($id); // Get order details by id
        $items = $this->orderModel->getItems($id); // Get order items by order id
        require __DIR__ . '/../views/commande_confirm.php'; // Include order confirmation view
    }

    /**
     * Displays a list of all orders in the admin panel.
     *
     * Checks if the user is logged in and is an admin, redirects to login page if not.
     * Retrieves all orders from the database.
     * Includes the admin list orders view.
     */
    public function adminListOrders() {
         // Check if the user is logged in and is an admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['usertype'] !== 'admin') {
            header("Location: index.php?action=login"); // Redirect to login if not admin
            exit;
        }
        $orders = $this->orderModel->getAllOrders(); // Get all orders
        require __DIR__ . '/../views/admin_list_orders.php'; // Include admin list orders view
    }
    
    /**
     * Displays the details of a specific order in the admin panel.
     *
     * Checks if the user is logged in and is an admin, redirects to login page if not.
     * Gets the order ID from the GET request.
     * Retrieves the order details from the database.
     * Retrieves the order items from the database.
     * Includes the admin view order view.
     */
    public function adminViewOrder() {
         // Check if the user is logged in and is an admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['usertype'] !== 'admin') {
            header("Location: index.php?action=login"); // Redirect to login if not admin
            exit;
        }
        $id = $_GET['id'] ?? null; // Get order id from GET request
        if (!$id) {
            // Handle missing ID (e.g., redirect to order list)
            header("Location: index.php?action=admin_list_orders");
            exit;
        }

        $order = $this->orderModel->getOrderById($id); // Get order details by id
        if (!$order) {
            // Handle order not found (e.g., redirect to order list)
            header("Location: index.php?action=admin_list_orders");
            exit;
        }
         $orderItems = $this->orderModel->getItems($id); // Get order items by order id

        require __DIR__ . '/../views/admin_view_order.php'; // Include admin view order view
    }
    
    /**
     * Updates the status of an order in the admin panel.
     *
     * Checks if the user is logged in and is an admin, redirects to login page if not.
     * Gets the order ID and status from the POST request.
     * Updates the order status in the database.
     * Redirects back to the view order page with a success or error message.
     */
    public function adminUpdateOrderStatus() {
         // Check if the user is logged in and is an admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['usertype'] !== 'admin') {
            header("Location: index.php?action=login"); // Redirect to login if not admin
            exit;
        }
        $id = $_POST['id'] ?? null; // Get order id from POST request
        $status = $_POST['status'] ?? null; // Get status from POST request

        if (!$id || !$status) {
            // Handle missing data (e.g., redirect back to view order page with an error message)
            header("Location: index.php?action=admin_view_order&id=" . $id . "&error=MissingData");
            exit;
        }

        if ($this->orderModel->updateOrderStatus($id, $status)) { // Update order status
            // Redirect back to the view order page with a success message
            header("Location: index.php?action=admin_view_order&id=" . $id . "&success=StatusUpdated");
            exit;
        } else {
            // Handle update failure (e.g., redirect back to view order page with an error message)
             header("Location: index.php?action=admin_view_order&id=" . $id . "&error=UpdateFailed");
            exit;
        }
    }


    

}