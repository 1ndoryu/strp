<?php
# sc-includes/upload_temp_image.php
# WANDORIUS 
# 10/04/25

// TODO: Incluir aquí la inicialización básica del CMS si es necesaria para usar funciones
// como insertSQL, getConfParam, user::getSession(), etc.
// session_start(); // Si necesitas verificar la sesión del usuario

define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2 MB - ¡AJUSTAR SI ES NECESARIO!
define('TEMP_UPLOAD_DIR', __DIR__ . '/uploads/temp/'); // Asegúrate que esta ruta sea correcta y tenga permisos 755 o 777
define('ALLOWED_TYPES', ['image/jpeg', 'image/png']);
define('TEMP_UPLOAD_URL', '/uploads/temp/'); // URL base para acceder a las imágenes temporales desde el navegador

header('Content-Type: application/json');

// --- Validación Básica ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método no permitido.']);
    exit;
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $error_message = 'Error desconocido al subir.';
    if (isset($_FILES['file']['error'])) {
        switch ($_FILES['file']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $error_message = 'El archivo excede el límite de tamaño.';
                break;
            case UPLOAD_ERR_PARTIAL:
                $error_message = 'El archivo se subió parcialmente.';
                break;
            case UPLOAD_ERR_NO_FILE:
                $error_message = 'No se subió ningún archivo.';
                break;
            default:
                $error_message = 'Error interno del servidor al subir.';
        }
    }
    echo json_encode(['success' => false, 'error' => $error_message]);
    exit;
}

$file = $_FILES['file'];

// --- Validaciones Adicionales ---
if ($file['size'] > MAX_FILE_SIZE) {
    echo json_encode(['success' => false, 'error' => 'El archivo es demasiado grande (Máx ' . (MAX_FILE_SIZE / 1024 / 1024) . ' MB).']);
    exit;
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime_type, ALLOWED_TYPES)) {
    echo json_encode(['success' => false, 'error' => 'Tipo de archivo no permitido (Solo JPG, PNG).']);
    exit;
}

// --- Crear Directorio Temporal si no existe ---
if (!is_dir(TEMP_UPLOAD_DIR)) {
    if (!mkdir(TEMP_UPLOAD_DIR, 0755, true)) { // Intentar crear recursivamente con permisos
         echo json_encode(['success' => false, 'error' => 'Error interno: No se pudo crear el directorio temporal.']);
         error_log("Error creando directorio temporal: " . TEMP_UPLOAD_DIR); // Log para depuración
         exit;
    }
}
if (!is_writable(TEMP_UPLOAD_DIR)) {
     echo json_encode(['success' => false, 'error' => 'Error interno: El directorio temporal no tiene permisos de escritura.']);
     error_log("Error de permisos en directorio temporal: " . TEMP_UPLOAD_DIR); // Log para depuración
     exit;
}


// --- Generar Nombre Único y Mover Archivo ---
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$unique_name = uniqid('img_', true) . '.' . strtolower($extension);
$destination_path = TEMP_UPLOAD_DIR . $unique_name;

if (move_uploaded_file($file['tmp_name'], $destination_path)) {
    // --- Calcular Hash ---
    $file_hash = md5_file($destination_path);

    // --- Insertar en Base de Datos (sc_images) ---
    // TODO: Asegúrate que la función insertSQL está disponible aquí.
     $id_user_temp = isset($_SESSION['data']['ID_user']) ? $_SESSION['data']['ID_user'] : null; // Opcional: guardar quién subió

    $image_data = [
        'position' => 0, // Posición inicial, se ajustará al final
        'ID_ad' => null, // Importante: Aún no asociado a ningún anuncio
        'name_image' => $unique_name,
        'date_upload' => (string)time(), // Guardar timestamp como string
        'hash' => $file_hash,
        'score' => 0,
        'status' => 0, // Marcar como temporal/pendiente (1 sería activo)
        'edit' => 0 // O el valor por defecto que corresponda
        // 'ID_user_uploader' => $id_user_temp, // Si añades una columna para esto
    ];

    $insert_success = insertSQL("sc_images", $image_data);

    if ($insert_success) {
        $preview_url = TEMP_UPLOAD_URL . $unique_name;
        echo json_encode([
            'success' => true,
            'identifier' => $unique_name, // Usamos el nombre de archivo como identificador
            'previewUrl' => $preview_url
        ]);
    } else {
        // Error insertando en BD, borrar archivo físico para no dejar basura
        unlink($destination_path);
        echo json_encode(['success' => false, 'error' => 'Error al guardar la información de la imagen.']);
         error_log("Error SQL insertando imagen temporal: " . $unique_name); // Log para depuración
    }

} else {
    echo json_encode(['success' => false, 'error' => 'Error al mover el archivo subido.']);
    error_log("Error moviendo archivo subido: " . $file['tmp_name'] . " a " . $destination_path); // Log para depuración
}

exit;
?>