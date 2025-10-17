<?php

class AdminHomeController {
    private $pdo; // PDO database connection object
    private $productModel; // Instance of the Product model
    private $orderModel; // Instance of the Order model

    /**
     * Constructor for the AdminHomeController class.
     * Initializes the database connection and model instances.
     *
     * @param PDO $pdo Database connection object.
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
        // Include the Product model
        require_once __DIR__ . '/../models/Product.php';
        $this->productModel = new Product($pdo);
        // Include the Order model
        require_once __DIR__ . '/../models/Order.php';
        $this->orderModel = new Order($pdo);
    }

    /**
     * Index method to handle the admin dashboard or redirect to the home page.
     * Checks if the user is an admin and displays the admin dashboard with order statistics.
     * Otherwise, redirects to the regular home page.
     */
    public function index() {
        // Check if a user is logged in and has admin privileges
        if (!empty($_SESSION['user']) && $_SESSION['user']['usertype'] === 'admin') {
            // Admin Dashboard Logic
            // Count the number of orders with 'Pending' status
            $totalPendingOrders = count($this->orderModel->getOrdersByStatus('Pending'));
            // Count the number of orders with 'Processing' status
            $totalProcessingOrders = count($this->orderModel->getOrdersByStatus('Processing'));
            // Count the number of orders with 'Shipped' status
            $totalShippedOrders = count($this->orderModel->getOrdersByStatus('Shipped'));
            // Count the number of orders with 'Delivered' status
            $totalDeliveredOrders = count($this->orderModel->getOrdersByStatus('Delivered'));
            // Count the number of orders with 'Cancelled' status
            $totalCancelledOrders = count($this->orderModel->getOrdersByStatus('Cancelled'));
            // Count the total number of products
            $totalProducts = count($this->productModel->getAllProducts());

            // Include the admin dashboard view
            require __DIR__ . '/../views/admin_dashboard.php';
        } else {
            // Regular Home Page Logic
            // Include the home page view
            require __DIR__ . '/../views/home.php';
        }
    }
}