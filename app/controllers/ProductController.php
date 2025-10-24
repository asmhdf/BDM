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

                // ✅ Ajoute le watermark
                $this->addWatermark($destination);

                // ✅ Ajoute watermark texte
                $this->addTextWatermark($destination, '© BDM Market');


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
    /**
 * 🖼️ Ajoute un watermark texte sur une image
 * $imagePath : chemin de l'image principale
 * $text : texte du watermark (ex: © BDM Market)
 */
private function addTextWatermark($imagePath, $text = '© BDM Market') {
    if (!file_exists($imagePath)) return;

    // Charger l'image originale
    $image = imagecreatefromstring(file_get_contents($imagePath));

    $imgWidth = imagesx($image);
    $imgHeight = imagesy($image);

    // Couleur blanche semi-transparente
    $color = imagecolorallocatealpha($image, 255, 255, 255, 50);

    // Taille et angle du texte
    $fontSize = 20; // en pts
    $angle = 0;

    // Chemin vers une police TTF
    $fontPath = __DIR__ . 'C:/Windows/Fonts/arial.ttf'; // crée le dossier fonts et mets une .ttf

    if (!file_exists($fontPath)) {
        // Si pas de police, fallback à imagestring
        imagestring($image, 5, $imgWidth - 150, $imgHeight - 30, $text, $color);
    } else {
        // Dimensions du texte
        $bbox = imagettfbbox($fontSize, $angle, $fontPath, $text);
        $textWidth = $bbox[2] - $bbox[0];
        $textHeight = $bbox[1] - $bbox[7];

        // Position : bas à droite avec 10px marge
        $x = $imgWidth - $textWidth - 10;
        $y = $imgHeight - 10;

        // Ajouter le texte
        imagettftext($image, $fontSize, $angle, $x, $y, $color, $fontPath, $text);
    }

    // Sauvegarder l'image finale
    imagepng($image, $imagePath);

    // Libérer la mémoire
    imagedestroy($image);
}

    /**
 * 🖼️ Ajoute un watermark (image PNG) sur une image existante
 */
private function addWatermark($imagePath) {
    $watermarkPath = __DIR__ . '/../assets/watermark.png'; 

    // Vérifie que le watermark existe
    if (!file_exists($watermarkPath) || !file_exists($imagePath)) {
        return;
    }

    // Charger l’image principale
    $image = imagecreatefromstring(file_get_contents($imagePath));
    $watermark = imagecreatefrompng($watermarkPath);

    // Dimensions
    $imgWidth = imagesx($image);
    $imgHeight = imagesy($image);
    $wmWidth = imagesx($watermark);
    $wmHeight = imagesy($watermark);

    // Position du watermark (bas à droite avec marge)
    $destX = $imgWidth - $wmWidth - 10;
    $destY = $imgHeight - $wmHeight - 10;

    // Fusionner watermark + image
    imagecopy($image, $watermark, $destX, $destY, 0, 0, $wmWidth, $wmHeight);

    // Sauvegarder (remplace l'image originale)
    imagepng($image, $imagePath);

    // Libère la mémoire
    imagedestroy($image);
    imagedestroy($watermark);
}
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

                // ✅ Ajoute le watermark
                $this->addWatermark($destination);

                // ✅ Ajoute watermark texte
                $this->addTextWatermark($destination, '© BDM Market');

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
        // Récupère les images supplémentaires liées au produit
        $images = $this->productModel->getImages($id);

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
    public function adminDeleteProduct() {
    if (!isset($_SESSION['user']) || $_SESSION['user']['usertype'] !== 'admin') {
        header("Location: index.php?action=login");
        exit;
    }

    $id = $_GET['id'] ?? null;
    if (!$id) {
        header("Location: index.php?action=admin_products");
        exit;
    }

    // Récupère le produit pour supprimer les fichiers
    $product = $this->productModel->getById($id);
    if ($product && !empty($product['image_path'])) {
        $uploadDir = __DIR__ . '/../uploads/products/';
        $baseName = pathinfo($product['image_path'], PATHINFO_FILENAME);

        // Supprime fichier original
        $original = $uploadDir . $product['image_path'];
        if (file_exists($original)) unlink($original);

        // Supprime variantes JPEG et PNG
        $jpeg = $uploadDir . $baseName . '.jpeg';
        $png = $uploadDir . $baseName . '.png';
        if (file_exists($jpeg)) unlink($jpeg);
        if (file_exists($png)) unlink($png);
    }

    // Supprime le produit de la base
    $this->productModel->delete($id);

    // Redirection vers la liste admin
    header("Location: index.php?action=admin_products");
    exit;
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
