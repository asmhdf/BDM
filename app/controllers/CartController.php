<?php
require_once __DIR__ . '/../models/Product.php';

class CartController {
    private $productModel;

    public function __construct($pdo) {
        $this->productModel = new Product($pdo);
    }

    /**
     * Adds a product to the cart.
     *
     * Gets the product ID from the GET request and the quantity from the POST request.
     * If the product ID is not provided, the function returns.
     * If the cart session variable is not set, it initializes it as an empty array.
     * If the product is already in the cart, it increments the quantity.
     * Otherwise, it adds the product to the cart with the specified quantity.
     * Finally, it redirects the user to the cart page.
     */
    public function add() {
        $id = $_GET['id'] ?? null; // Get product id from GET request
        $quantity = max(1, intval($_POST['quantity'] ?? 1)); // Get quantity from POST request, default to 1
        if (!$id) return; // If no id, exit

        // Initialize cart if it doesn't exist
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        // If item exists in cart, increment quantity
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] += $quantity;
        } else {
            // If item doesn't exist in cart, add it
            $_SESSION['cart'][$id] = $quantity;
        }
        header('Location: index.php?action=cart'); // Redirect to cart page
        exit;
    }

    /**
     * Displays the cart contents.
     *
     * Gets the cart data from the session variable.
     * Retrieves the product details from the database for each item in the cart.
     * Calculates the subtotal for each product and the total for the cart.
     * Finally, it includes the cart view to display the cart contents.
     */
    public function show() {
        $cart = $_SESSION['cart'] ?? []; // Get cart from session, default to empty array
        $products = []; // Initialize products array
        $total = 0; // Initialize total
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
        require __DIR__ . '/../views/panier.php'; // Include cart view
    }

    /**
     * Updates the quantities of items in the cart.
     *
     * Gets the quantities from the POST request.
     * If a quantity is less than or equal to 0, the item is removed from the cart.
     * Otherwise, the quantity is updated in the cart.
     * Finally, it redirects the user to the cart page.
     */
    public function update() {
        // Loop through quantities from POST request
        foreach ($_POST['quantities'] as $id => $qty) {
            if ($qty <= 0) {
                unset($_SESSION['cart'][$id]); // Remove item if quantity is 0 or less
            } else {
                $_SESSION['cart'][$id] = $qty; // Update quantity
            }
        }
        header('Location: index.php?action=cart'); // Redirect to cart page
        exit;
    }

    /**
     * Removes an item from the cart.
     *
     * Gets the product ID from the GET request.
     * If the product ID is provided and the item exists in the cart, it removes the item from the cart.
     * Finally, it redirects the user to the cart page.
     */
    public function remove() {
        $id = $_GET['id'] ?? null; // Get product id from GET request
        if ($id && isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]); // Remove item from cart
        }
        header('Location: index.php?action=cart'); // Redirect to cart page
        exit;
    }
}