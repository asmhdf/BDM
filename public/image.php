<?php
require_once __DIR__ . '/../config/config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    http_response_code(404);
    exit('Image non trouvée');
}

$stmt = $pdo->prepare("SELECT image, image_type FROM produits WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();

if ($row && $row['image']) {
    header('Content-Type: ' . ($row['image_type'] ?? 'image/jpeg'));
    echo $row['image'];
} else {
    http_response_code(404);
    exit('Image non trouvée');
}