<?php
class Category {
    private $pdo;

    // Constructor: initialize with database connection
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fetch all categories from the database, ordered by name (A-Z)
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY nom ASC");
        return $stmt->fetchAll(); // Return all results as an array
    }
}
