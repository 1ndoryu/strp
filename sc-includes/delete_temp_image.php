<?php
# sc-includes/delete_temp_image.php
// TODO: Incluir aquí la inicialización básica del CMS si es necesaria para usar funciones
// como deleteSQL, selectSQL, user::getSession(), etc.
// session_start(); // Si necesitas verificar la sesión del usuario

define('TEMP_UPLOAD_DIR', __DIR__ . '/uploads/temp/'); // Misma ruta que en upload_temp_image.php

header('Content-Type: application/json');

// --- Validación Básica ---

if (!isset($_POST['identifier']) || empty($_POST['identifier'])) {
     echo json_encode(['success' => false, 'error' => 'Identificador no proporcionado.']);
    exit;
}

$identifier = basename(filter_var($_POST['identifier'], FILTER_SANITIZE_STRING)); // Limpiar el identificador

// --- Seguridad Adicional (Opcional pero Recomendado) ---
// $id_user = isset($_SESSION['data']['ID_user']) ? $_SESSION['data']['ID_user'] : null;
// if (!$id_user) {
//     echo json_encode(['success' => false, 'error' => 'No autorizado.']);
//     exit;
// }
// Añadir condición a deleteSQL: 'ID_user_uploader' => $id_user (si implementaste esa columna)

// --- Borrar Registro de la Base de Datos ---
// TODO: Asegúrate que deleteSQL está disponible aquí.
// Borramos solo si ID_ad es NULL (o 0), para no borrar imágenes ya asociadas
$delete_success = deleteSQL("sc_images", $where = [
    'name_image' => $identifier,
    'ID_ad' => null // O 'ID_ad' => 0 si usas 0 en lugar de NULL
    // Añadir condición de usuario si aplica: , 'ID_user_uploader' => $id_user
]);

if ($delete_success) {
    // --- Borrar Archivo Físico ---
    $file_path = TEMP_UPLOAD_DIR . $identifier;
    if (file_exists($file_path)) {
        if (unlink($file_path)) {
            echo json_encode(['success' => true]);
        } else {
             // El registro se borró pero el archivo no, posible problema de permisos
             echo json_encode(['success' => false, 'error' => 'Error al borrar el archivo físico.']);
             error_log("Error borrando archivo temporal (unlink): " . $file_path); // Log para depuración
        }
    } else {
         // El registro se borró, pero el archivo ya no existía (puede ser normal si hubo error previo)
        echo json_encode(['success' => true, 'warning' => 'Archivo no encontrado, registro borrado.']);
    }
} else {
    // No se encontró el registro o hubo un error SQL
    echo json_encode(['success' => false, 'error' => 'No se pudo borrar la referencia de la imagen o ya estaba asociada.']);
    error_log("Error SQL borrando imagen temporal: " . $identifier); // Log para depuración
}

exit;
?>