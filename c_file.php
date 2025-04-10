<?php
$sourceFolder = __DIR__; 


function manageImage($folder) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $file) {
        if ($file->isFile() && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file->getFilename())) {

			unlink($file->getPathname());
        }
    }
}

manageImage($sourceFolder);
?>