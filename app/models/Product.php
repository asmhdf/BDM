<?php
class Product {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll($categorie_id = null) {
        if ($categorie_id) {
            $stmt = $this->pdo->prepare("SELECT * FROM produits WHERE categorie_id = ?");
            $stmt->execute([$categorie_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $this->pdo->query("SELECT * FROM produits");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    public function getAllProducts() {
        return $this->getAll();
    }


    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM produits WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nom, $description, $prix, $stock, $categorie_id, $image_path = null) {
        $sql = "INSERT INTO produits (nom, description, prix, stock, categorie_id, image_path)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nom, $description, $prix, $stock, $categorie_id, $image_path]);
    }

    public function update($id, $nom, $description, $prix, $stock, $categorie_id, $image_path = null) {
        $sql = "UPDATE produits SET nom = ?, description = ?, prix = ?, stock = ?, categorie_id = ?";
        $params = [$nom, $description, $prix, $stock, $categorie_id];

        if ($image_path) {
            $sql .= ", image_path = ?";
            $params[] = $image_path;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function getImages($id) {
        $stmt = $this->pdo->prepare("SELECT id, image_type FROM images WHERE produit_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchAll(); // Returns an array of images
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("SELECT image_path FROM produits WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        // Supprime aussi le fichier image
        if ($product && $product['image_path']) {
            $path = __DIR__ . '/../uploads/products/' . $product['image_path'];
            if (file_exists($path)) unlink($path);
        }

        $stmt = $this->pdo->prepare("DELETE FROM produits WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
