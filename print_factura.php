<?php
define('ABSPATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

// Ruta del archivo en el servidor
if (isset($_GET['file'])) {
    $archivo = ABSPATH . "src/facturas/" . $_GET['file'];

    if (file_exists($archivo)) {
        // Configuración de las cabeceras para mostrar el PDF en el navegador
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($archivo) . '"');
        header('Content-Length: ' . filesize($archivo));

        // Limpia el búfer de salida y lee el archivo
        ob_clean();
        flush();
        readfile($archivo);
        exit;
    } else {
        // Si el archivo no existe, muestra un error
        echo "El archivo no se encontró.";
    }
} else {
    echo "No se especificó ningún archivo.";
}
