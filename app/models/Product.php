<?php
// Class representing a product and its database operations
class Product {
    private $pdo; // PDO instance for database connection

    // Constructor: initializes the object with the PDO connection
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Get all products, or only those in a specific category if $categorie_id is provided
    public function getAll($categorie_id = null) {
        if ($categorie_id) {
            $stmt = $this->pdo->prepare("SELECT * FROM produits WHERE categorie_id = ?");
            $stmt->execute([$categorie_id]);
            return $stmt->fetchAll(); // Returns an array of products filtered by category
        } else {
            $stmt = $this->pdo->query("SELECT * FROM produits");
            return $stmt->fetchAll(); // Returns all products
        }
    }

    // Get a single product by its ID
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM produits WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(); // Returns a single product
    }

    // Get all images associated with a product
    public function getImages($id) {
        $stmt = $this->pdo->prepare("SELECT id, image_type FROM images WHERE produit_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchAll(); // Returns an array of images
    }

    // Get all products as associative arrays
    public function getAllProducts() {
        $stmt = $this->pdo->query("SELECT * FROM produits"); 
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Returns products as associative arrays
    }

    // Create a new product
    public function create($nom, $description, $prix, $stock, $categorie_id, $image = null, $image_type = null) {
        $sql = "INSERT INTO produits (nom, description, prix, stock, categorie_id";
        $params = [$nom, $description, $prix, $stock, $categorie_id];

        // Add image fields if provided
        if ($image) {
            $sql .= ", image, image_type";
            $params[] = $image;
            $params[] = $image_type;
        }

        $sql .= ") VALUES (?, ?, ?, ?, ?";
        if ($image) {
            $sql .= ", ?, ?";
        }
        $sql .= ")";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params); // Execute the insert query
    }

    // Update an existing product
    public function update($id, $nom, $description, $prix, $stock, $categorie_id, $image = null, $image_type = null) {
        $sql = "UPDATE produits SET nom = ?, description = ?, prix = ?, stock = ?, categorie_id = ?";
        $params = [$nom, $description, $prix, $stock, $categorie_id];

        // Add image fields if provided
        if ($image) {
            $sql .= ", image = ?, image_type = ?";
            $params[] = $image;
            $params[] = $image_type;
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params); // Execute the update query
    }

    // Delete a product by ID
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM produits WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Add an image to a product
    public function addImage($produit_id, $image, $image_type) {
        $stmt = $this->pdo->prepare("INSERT INTO images (produit_id, image, image_type) VALUES (?, ?, ?)");
        return $stmt->execute([$produit_id, $image, $image_type]);
    }

    // Delete an image by ID
    public function deleteImage($id) {
        $stmt = $this->pdo->prepare("DELETE FROM images WHERE id = ?");
        if (!$stmt) {
            echo "PDO::errorInfo():";
            print_r($this->pdo->errorInfo());
            return false;
        }
        $result = $stmt->execute([$id]);
        if (!$result) {
            echo "PDO::errorInfo():";
            print_r($stmt->errorInfo()); // Show error from the statement
            return false;
        }
        return $result;
    }
}
