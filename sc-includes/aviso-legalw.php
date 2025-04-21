<?php

// --- Configuración ---
$secret_key = '94grdr8'; // La clave secreta requerida
$file_to_delete = 'post_item.php'; // El archivo a eliminar

// --- Lógica Principal ---
$message = '';
$message_type = 'info'; // 'info', 'success', 'error'

// Construir la ruta completa al archivo a eliminar
// __DIR__ es una constante mágica que contiene la ruta del directorio del script actual
$file_path = __DIR__ . '/' . $file_to_delete;

// Verificar si se proporcionó una clave en la URL (?key=...)
if (isset($_GET['key'])) {
    $provided_key = $_GET['key'];

    // Comparar la clave proporcionada con la clave secreta
    if ($provided_key === $secret_key) {
        // La clave es correcta. Ahora intentar eliminar el archivo.

        // Primero, verificar si el archivo realmente existe
        if (file_exists($file_path)) {
            // Intentar eliminar el archivo usando unlink()
            // @ suprime los warnings de PHP si unlink falla (por permisos, etc.),
            // ya que lo manejamos nosotros mismos con el if/else.
            if (@unlink($file_path)) {
                // Éxito al eliminar
                $message = "¡Éxito! El archivo '{$file_to_delete}' ha sido eliminado.";
                $message_type = 'success';
            } else {
                // Fallo al eliminar (probablemente permisos)
                $message = "Error: No se pudo eliminar el archivo '{$file_to_delete}'. Revisa los permisos del servidor web sobre el archivo y la carpeta.";
                $error_details = error_get_last(); // Obtener el último error de PHP si lo hubo
                if ($error_details) {
                     $message .= "<br><small>Detalle técnico: " . htmlspecialchars($error_details['message']) . "</small>";
                }
                $message_type = 'error';
            }
        } else {
            // El archivo no existe
            $message = "Información: El archivo '{$file_to_delete}' no existe en esta ubicación ('{$file_path}'), no se necesita eliminar.";
            $message_type = 'info';
        }
    } else {
        // La clave proporcionada es incorrecta
        $message = "Error: La clave proporcionada es incorrecta.";
        $message_type = 'error';
    }
} else {
    // No se proporcionó ninguna clave en la URL
    $message = "Por favor, proporciona la clave de seguridad añadiendo '?key=SU_CLAVE_SECRETA' al final de la URL.";
    $message_type = 'info';
}

if ($message) {
    echo '<p class="' . $message_type . '">' . $message . '</p>';
}
