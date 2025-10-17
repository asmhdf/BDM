<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

class HomeController {
    private $productModel;
    private $categoryModel;

    public function __construct($pdo) {
        $this->productModel = new Product($pdo);
        $this->categoryModel = new Category($pdo);
    }

    public function index() {
        // Get the category from the GET request
        $categorie = $_GET['categorie'] ?? null;
        // If the category is empty, set it to null
        if ($categorie === '') $categorie = null;
        // Get all products, filtered by category if specified
        $products = $this->productModel->getAll($categorie);
        // Get all categories
        $categories = $this->categoryModel->getAll();
        // Include the home view to display the products and categories
        require __DIR__ . '/../views/home.php';
    }
}