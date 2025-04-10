<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$rootDir = __DIR__; // Define the root directory to start searching
$logFile = __DIR__ . '/compressed.log'; // Path to the log file

// Load the log of already compressed files
$compressedFiles = file_exists($logFile) ? file($logFile, FILE_IGNORE_NEW_LINES) : [];

function compressImage($filePath, $quality = 75) {
    $info = getimagesize($filePath); // Get file information
    $image = null; // Initialize the $image variable
    
    // Check the MIME type and process accordingly
    if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($filePath); // Create JPEG image resource
        imagejpeg($image, $filePath, $quality); // Compress and overwrite file
    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($filePath); // Create PNG image resource
        imagepng($image, $filePath, 9); // Compress and overwrite file
    } else {
        echo "Unsupported file type for compression: $filePath\n"; // Log unsupported file
    }
    
    // Destroy the image resource if it was created
    if ($image) {
        imagedestroy($image);
    }
}

// Recursively find and compress images
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootDir));
foreach ($files as $file) {
    if ($file->isFile()) {
        $filePath = $file->getRealPath();
        if (!in_array($filePath, $compressedFiles) && preg_match('/\.(jpg|jpeg|png)$/i', $filePath)) {
           // compressImage($filePath); // Compress the image
           // file_put_contents($logFile, $filePath . PHP_EOL, FILE_APPEND); // Log the file
           // echo "Compressed: $filePath\n";
        }
    }
}

echo "working";
?>