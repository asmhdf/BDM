<?php
require_once __DIR__ . '/../models/Product.php';

class ProductController {
    private $productModel;
    private $categoryModel;
    private $pdo;

    public function __construct($pdo) {
        // Initialize models for products and categories
        $this->productModel = new Product($pdo);
        $this->categoryModel = new Category($pdo);
        $this->pdo = $pdo;
    }

    // Show a single product page
    public function show() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(404);
            exit('Product not found');
        }
        $product = $this->productModel->getById($id);
        $images = $this->productModel->getImages($id);
        require __DIR__ . '/../views/produit.php';
    }

    // Display product list in admin panel
    public function adminList() {
        // Check if user is logged in and is admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['usertype'] !== 'admin') {
            header("Location: index.php?action=login");
            exit;
        }

        // Apply optional category filter
        $categorie_id = $_GET['categorie'] ?? null;

        // Fetch all products and categories
        $products = $this->productModel->getAll($categorie_id);
        $categories = $this->categoryModel->getAll();

        // Render admin products view
        require __DIR__ . '/../views/admin_products.php';
    }

    // Add new product in admin panel
    public function adminAddProduct() {
        // Verify admin access
        if (!isset($_SESSION['user']) || $_SESSION['user']['usertype'] !== 'admin') {
            header("Location: index.php?action=login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get form data
            $nom = $_POST['nom'];
            $description = $_POST['description'];
            $prix = $_POST['prix'];
            $stock = $_POST['stock'];
            $categorie_id = $_POST['categorie_id'];

            // Handle main image upload
            $image = null;
            $image_type = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image = file_get_contents($_FILES['image']['tmp_name']);
                $image_type = $_FILES['image']['type'];
            }

            // Basic validation
            if (empty($nom) || empty($description) || empty($prix) || empty($stock) || empty($categorie_id)) {
                $error = "All fields are required.";
                $categories = $this->categoryModel->getAll();
                require __DIR__ . '/../views/admin_add_product.php';
                return;
            }

            // Create new product in DB
            if ($this->productModel->create($nom, $description, $prix, $stock, $categorie_id, $image, $image_type)) {
                $product_id = $this->pdo->lastInsertId();

                // Handle multiple images upload
                if (isset($_FILES['images']) && is_array($_FILES['images']['error'])) {
                    $files = $_FILES['images'];
                    $total = count($files['name']);
                    for ($i = 0; $i < $total; $i++) {
                        if ($files['error'][$i] === UPLOAD_ERR_OK) {
                            $image = file_get_contents($files['tmp_name'][$i]);
                            $image_type = $files['type'][$i];
                            $this->productModel->addImage($product_id, $image, $image_type);
                        }
                    }
                }
                header("Location: index.php?action=admin_products");
                exit;
            } else {
                $error = "Error while creating product.";
            }
        }

        // Load categories for dropdown
        $categories = $this->categoryModel->getAll();

        require __DIR__ . '/../views/admin_add_product.php';
    }

    // Edit an existing product
    public function adminEditProduct() {
        // Verify admin access
        if (!isset($_SESSION['user']) || $_SESSION['user']['usertype'] !== 'admin') {
            header("Location: index.php?action=login");
            exit;
        }

        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?action=admin_products");
            exit;
        }

        $product = $this->productModel->getById($id);
        if (!$product) {
            header("Location: index.php?action=admin_products");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get updated data from form
            $nom = $_POST['nom'];
            $description = $_POST['description'];
            $prix = $_POST['prix'];
            $stock = $_POST['stock'];
            $categorie_id = $_POST['categorie_id'];

            // Handle new main image
            $image = null;
            $image_type = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image = file_get_contents($_FILES['image']['tmp_name']);
                $image_type = $_FILES['image']['type'];
            }

            // Validation
            if (empty($nom) || empty($description) || empty($prix) || empty($stock) || empty($categorie_id)) {
                $error = "All fields are required.";
                $categories = $this->categoryModel->getAll();
                require __DIR__ . '/../views/admin_edit_product.php';
                return;
            }

            // Update product in DB
            if ($this->productModel->update($id, $nom, $description, $prix, $stock, $categorie_id, $image, $image_type)) {
                // Handle additional images upload
                if (isset($_FILES['images']) && is_array($_FILES['images']['error'])) {
                    $files = $_FILES['images'];
                    $total = count($files['name']);
                    for ($i = 0; $i < $total; $i++) {
                        if ($files['error'][$i] === UPLOAD_ERR_OK) {
                            $image = file_get_contents($files['tmp_name'][$i]);
                            $image_type = $files['type'][$i];
                            $this->productModel->addImage($id, $image, $image_type);
                        }
                    }
                }
                header("Location: index.php?action=admin_products");
                exit;
            } else {
                $error = "Error while updating product.";
            }
        }

        // Fetch categories and images for the form
        $categories = $this->categoryModel->getAll();
        $images = $this->productModel->getImages($id);

        require __DIR__ . '/../views/admin_edit_product.php';
    }

    // Delete product
    public function adminDeleteProduct() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['usertype'] !== 'admin') {
            header("Location: index.php?action=login");
            exit;
        }

        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->productModel->delete($id);
        }

        header("Location: index.php?action=admin_products");
        exit;
    }

    // Delete a product image
    public function adminDeleteImage() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['usertype'] !== 'admin') {
            header("Location: index.php?action=login");
            exit;
        }

        $id = $_GET['id'] ?? null;
        $product_id = $_GET['product_id'] ?? null;

        if ($id) {
            try {
                $this->pdo->beginTransaction();
                $deletionResult = $this->productModel->deleteImage($id);

                if (!$deletionResult) {
                    throw new Exception("Error deleting image with ID: " . $id);
                }

                $this->pdo->commit();
            } catch (Exception $e) {
                $this->pdo->rollBack();
                echo "Transaction failed: " . $e->getMessage();
            }
        }

        header("Location: index.php?action=admin_edit_product&id=" . $product_id);
        exit;
    }
}
