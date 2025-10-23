<?php
$filename = $_GET['file'] ?? '';
$path = __DIR__ . '/../app/uploads/products/' . basename($filename);

if (file_exists($path)) {
    $mime = mime_content_type($path);
    header("Content-Type: $mime");
    readfile($path);
    exit;
} else {
    http_response_code(404);
    echo "Image non trouvée.";
}
