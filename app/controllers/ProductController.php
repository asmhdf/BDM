<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php'; // si tu l’utilises déjà

class ProductController {
    private $productModel;
    private $categoryModel;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->productModel = new Product($pdo);
        $this->categoryModel = new Category($pdo);
    }

    public function adminAddProduct() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'];
            $description = $_POST['description'];
            $prix = $_POST['prix'];
            $stock = $_POST['stock'];
            $categorie_id = $_POST['categorie_id'];
            $image_path = null;

            // 📁 Gestion de l'image
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/products/';
                $fileTmp = $_FILES['image']['tmp_name'];
                $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                // Génère un nom unique
                $fileName = uniqid('prod_') . '.' . $fileExt;
                $destination = $uploadDir . $fileName;

                // Déplace le fichier original
                move_uploaded_file($fileTmp, $destination);

                // Crée d'autres formats (jpeg et png)
                $this->generateImageVariants($destination, $uploadDir, $fileName);

                $image_path = $fileName;
            }

            if ($this->productModel->create($nom, $description, $prix, $stock, $categorie_id, $image_path)) {
                header("Location: index.php?action=admin_products");
                exit;
            } else {
                $error = "Erreur lors de la création du produit.";
            }
        }

        $categories = $this->categoryModel->getAll();
        require __DIR__ . '/../views/admin_add_product.php';
    }

    public function adminEditProduct() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header("Location: index.php?action=admin_products");
            exit;
        }

        $product = $this->productModel->getById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'];
            $description = $_POST['description'];
            $prix = $_POST['prix'];
            $stock = $_POST['stock'];
            $categorie_id = $_POST['categorie_id'];
            $image_path = null;

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/products/';
                $fileTmp = $_FILES['image']['tmp_name'];
                $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                $fileName = uniqid('prod_') . '.' . $fileExt;
                $destination = $uploadDir . $fileName;
                move_uploaded_file($fileTmp, $destination);

                $this->generateImageVariants($destination, $uploadDir, $fileName);
                $image_path = $fileName;
            }

            if ($this->productModel->update($id, $nom, $description, $prix, $stock, $categorie_id, $image_path)) {
                header("Location: index.php?action=admin_products");
                exit;
            } else {
                $error = "Erreur lors de la mise à jour du produit.";
            }
        }

        $categories = $this->categoryModel->getAll();
        require __DIR__ . '/../views/admin_edit_product.php';
    }

    /** 🔧 Crée automatiquement les variantes PNG / JPEG */
    private function generateImageVariants($sourcePath, $uploadDir, $baseName) {
        $imageInfo = getimagesize($sourcePath);
        $mime = $imageInfo['mime'];

        switch ($mime) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($sourcePath);
                break;
            default:
                return;
        }

        // Sauvegarde au format JPEG
        imagejpeg($image, $uploadDir . pathinfo($baseName, PATHINFO_FILENAME) . ".jpeg", 90);
        // Sauvegarde au format PNG
        imagepng($image, $uploadDir . pathinfo($baseName, PATHINFO_FILENAME) . ".png");

        imagedestroy($image);
    }

    public function adminList() {
    // Vérifie si l’utilisateur est connecté et admin
    if (!isset($_SESSION['user']) || $_SESSION['user']['usertype'] !== 'admin') {
        header("Location: index.php?action=login");
        exit;
    }

    // Filtre facultatif par catégorie
    $categorie_id = $_GET['categorie'] ?? null;

    // Récupère tous les produits et les catégories
    $products = $this->productModel->getAll($categorie_id);
    $categories = $this->categoryModel->getAll();

    // Affiche la vue admin_products.php
    require __DIR__ . '/../views/admin_products.php';
}

}
