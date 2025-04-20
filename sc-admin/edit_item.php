<?php
#edit_item.php

// Activar errores para depuración (recomendado durante el desarrollo)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// --- Funciones (asumiendo que están definidas en otro lugar o incluidas) ---
// require_once('path/to/your/functions.php'); // Asegúrate que todas las funciones estén disponibles

function compressImage($filePath, $quality = 90)
{
    if (!file_exists($filePath) || !is_readable($filePath)) {
        // error_log("CompressImage Error: File not found or not readable - " . $filePath);
        return false;
    }
    $info = getimagesize($filePath);
    if ($info === false) {
         // error_log("CompressImage Error: getimagesize failed for - " . $filePath);
        return false;
    }
    $image = null;

    try {
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($filePath);
            if ($image) imagejpeg($image, $filePath, $quality);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($filePath);
            // La compresión PNG va de 0 (sin compresión) a 9 (máxima compresión)
            if ($image) imagepng($image, $filePath, 9);
        } else {
            // error_log("CompressImage Warning: Unsupported file type - " . $info['mime'] . " for " . $filePath);
             return false; // Unsupported file type
        }
    } catch (Exception $e) {
        // error_log("CompressImage Exception: " . $e->getMessage() . " for " . $filePath);
        return false;
    }


    if ($image) {
        imagedestroy($image);
        return true;
    }
    return false;
}

// Asumir que ImageStatus y otras clases/constantes están definidas
class ImageStatus {
    const Delete = -1; // Ejemplo, ajusta si es diferente
    const Inactive = 0; // Ejemplo
    const Active = 1;   // Ejemplo
}
// Asumir que Images::rotateImage, Images::deleteImage, etc. existen
// Asumir que getConfParam, selectSQL, updateSQL, insertSQL, getDataAd, toAscii, formatName, console_log, loadBlock, etc. existen

$edited = false;
$error_message = ''; // Para mostrar errores específicos
$DATAJSON = array();
$DATAJSON['max_photos'] = getConfParam('MAX_PHOTOS_AD');

if (isset($_GET['a'])) {
    $id_ad_to_edit = (int)$_GET['a']; // Sanitize input
    $check = selectSQL("sc_ad", $w = array('ID_ad' => $id_ad_to_edit));

    if ($check !== false && count($check) != 0) {
        // Carga inicial de datos del anuncio
        $ad = getDataAd($check[0]['ID_ad'], false); // false = no parsear cambios? revisar función
        if (!$ad || !isset($ad['ad'])) {
             die("Error: No se pudieron cargar los datos del anuncio.");
        }
        // Decodificar campos JSON para usarlos en el formulario
        // Usar ?? '[]' o '{}' para evitar errores si el campo es NULL o no existe
        $ad_servicios = json_decode($ad['ad']['servicios'] ?? '[]', true);
        if (json_last_error() !== JSON_ERROR_NONE) $ad_servicios = []; // Fallback si JSON inválido

        $ad_horario = json_decode($ad['ad']['horario'] ?? '{}', true);
         if (json_last_error() !== JSON_ERROR_NONE) $ad_horario = []; // Fallback si JSON inválido

        $ad_idiomas = json_decode($ad['ad']['idiomas'] ?? '[]', true);
         if (json_last_error() !== JSON_ERROR_NONE) $ad_idiomas = []; // Fallback si JSON inválido


        // --- Procesamiento del Formulario POST ---
        if (isset($_POST['tit'])) { // Usar un campo clave como 'tit' para detectar submit
            $datos_ad = array();
            $error_insert = false; // Flag para errores lógicos

            // --- Mapeo de Campos desde POST ---
            $datos_ad['ID_cat'] = (int)($_POST['category'] ?? 0);

            // Obtener parent_cat (asegurarse que selectSQL funciona)
            if ($datos_ad['ID_cat'] > 0) {
                $cat = selectSQL('sc_category', $w = array('ID_cat' => $datos_ad['ID_cat']));
                $datos_ad['parent_cat'] = ($cat && isset($cat[0]['parent_cat'])) ? $cat[0]['parent_cat'] : 0;
            } else {
                $datos_ad['parent_cat'] = 0;
                 $error_insert = true;
                 $error_message = 'Categoría inválida.';
            }

            $datos_ad['ID_region'] = (int)($_POST['region'] ?? 0);
            $datos_ad['location'] = trim($_POST['city'] ?? ''); // Campo de texto libre para ciudad

            $datos_ad['ad_type'] = (int)($_POST['ad_type'] ?? 0); // Asegurar que sea int
            $datos_ad['seller_type'] = (int)($_POST['seller_type'] ?? 0); // Asegurar que sea int

            $datos_ad['title'] = trim($_POST['tit'] ?? '');
            $datos_ad['title_seo'] = !empty($datos_ad['title']) ? toAscii($datos_ad['title']) : '';
            $datos_ad['texto'] = htmlspecialchars(trim($_POST['text'] ?? ''), ENT_QUOTES, 'UTF-8');

            $datos_ad['name'] = isset($_POST['name']) ? formatName($_POST['name']) : ''; // Usar formatName si existe
            $datos_ad['phone'] = trim($_POST['phone'] ?? '');
            $datos_ad['whatsapp'] = isset($_POST['whatsapp']) && $_POST['whatsapp'] == '1' ? 1 : 0;

            // $datos_ad['phone1'] = trim($_POST['phone1'] ?? ''); // Mantener si aún se usa
            // $datos_ad['whatsapp1'] = isset($_POST['whatsapp1']) ? 1 : 0; // Mantener si aún se usa

            // Campos Condicionales (Coche/Inmobiliaria) - Mantener si la lógica de categorías sigue activa
            $datos_ad['price'] = isset($_POST['precio']) ? preg_replace('/[^\d.]/', '', $_POST['precio']) : 0; // Limpiar precio, permitir decimales
            $datos_ad['price'] = is_numeric($datos_ad['price']) ? (float)$datos_ad['price'] : 0;

            $datos_ad['mileage'] = isset($_POST['km_car']) ? (int)$_POST['km_car'] : null;
            $datos_ad['fuel'] = isset($_POST['fuel_car']) ? (int)$_POST['fuel_car'] : null;
            $datos_ad['date_car'] = isset($_POST['date_car']) ? $_POST['date_car'] : null; // Podría ser INT o VARCHAR
            $datos_ad['area'] = isset($_POST['area']) ? (int)$_POST['area'] : null;
            $datos_ad['room'] = isset($_POST['room']) ? (int)$_POST['room'] : null;
            $datos_ad['broom'] = isset($_POST['bathroom']) ? (int)$_POST['bathroom'] : null; // Nombre de campo 'bathroom' en form

            // --- Nuevos Campos ---
            // Servicios (JSON Array)
            $lista_servicios = [];
            if (isset($_POST['servicios']) && is_array($_POST['servicios'])) {
                // Filtrar valores vacíos y limpiar (ejemplo: trim)
                $lista_servicios = array_map('trim', array_filter($_POST['servicios'], 'strlen'));
            }
            $datos_ad['servicios'] = json_encode(array_values($lista_servicios)); // Reindexar y codificar

            // Horario Detallado (JSON Object)
            $horario_completo = [];
            $dias_semana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
             $formato_hora = '/^([01]\d|2[0-3]):([0-5]\d)$/';
            if (isset($_POST['horario_dia']) && is_array($_POST['horario_dia'])) {
                 foreach ($dias_semana as $dia) {
                    // Asume que si el día está activo, se envía 'activo' = 1 (mediante JS o hidden input)
                     $activo = isset($_POST['horario_dia'][$dia]['activo']) && $_POST['horario_dia'][$dia]['activo'] == '1' ? 1 : 0;
                     $inicio = '00:00';
                     $fin = '23:30';
                     // Solo guardar horas si está activo
                     if ($activo) {
                        $inicio_raw = isset($_POST['horario_dia'][$dia]['inicio']) ? $_POST['horario_dia'][$dia]['inicio'] : '00:00';
                        $fin_raw = isset($_POST['horario_dia'][$dia]['fin']) ? $_POST['horario_dia'][$dia]['fin'] : '23:30';
                        // Validar formato HH:MM
                         $inicio = preg_match($formato_hora, $inicio_raw) ? $inicio_raw : '00:00';
                         $fin = preg_match($formato_hora, $fin_raw) ? $fin_raw : '23:30';
                     }

                     $horario_completo[$dia] = [
                         'activo' => $activo,
                         'inicio' => $inicio,
                         'fin' => $fin
                     ];
                 }
             }
             $datos_ad['horario'] = json_encode($horario_completo);


            // Idiomas (JSON Array of Objects) + lang1/lang2 mapping
             $lista_idiomas = [];
             $i = 1;
             // Asume que el formulario de edición tendrá inputs como idioma_1, nivel_idioma_1, etc.
             while (isset($_POST['idioma_' . $i])) {
                 $idioma_code = trim($_POST['idioma_' . $i]);
                 if (!empty($idioma_code)) {
                     $nivel = isset($_POST['nivel_idioma_' . $i]) ? trim($_POST['nivel_idioma_' . $i]) : 'desconocido';
                     $lista_idiomas[] = ['idioma' => $idioma_code, 'nivel' => $nivel];
                 }
                 $i++;
             }
             $datos_ad['idiomas'] = json_encode($lista_idiomas);

            // Mapear los dos primeros a lang1/lang2 si esas columnas existen y se usan
            // Asumiendo que lang1/lang2 guardan el código del idioma ('es', 'en', etc.) o un ID numérico.
            // Ajusta el '0' por defecto si es necesario (e.g., null, '')
             $datos_ad['lang1'] = $lista_idiomas[0]['idioma'] ?? 0;
             $datos_ad['lang2'] = $lista_idiomas[1]['idioma'] ?? 0;

            // Salidas a domicilio/hotel
            $datos_ad['out'] = isset($_POST['out']) && in_array($_POST['out'], [0, 1]) ? (int)$_POST['out'] : 0;

            // Notificaciones por email
            $datos_ad['notifications'] = isset($_POST['notifications']) && $_POST['notifications'] == '1' ? 1 : 0;

            // Campos Antiguos Eliminados (Asegurarse que no se intentan guardar si se quitaron de la tabla/lógica):
            // unset($datos_ad['dis']); // Ya no se usa
            // unset($datos_ad['hor_start']); // Ya no se usa
            // unset($datos_ad['hor_end']); // Ya no se usa

            // --- Rotación de Imágenes (Antes de guardar, si se hace) ---
             if (isset($_POST['photo_name']) && is_array($_POST['photo_name']) && isset($_POST['optImgage']) && is_array($_POST['optImgage'])) {
                foreach ($_POST['photo_name'] as $photo_index => $name) {
                    // Asegurarse que el índice existe en optImgage y tiene rotation
                    if (isset($_POST['optImgage'][$photo_index]['rotation'])) {
                        $rotation_value = (int)$_POST['optImgage'][$photo_index]['rotation'];
                         if ($rotation_value != 0) { // Solo rotar si es necesario
                             // Construir la ruta completa de forma segura
                             // ¡CUIDADO! Evitar hardcodear rutas absolutas si es posible. Usar constantes o funciones del CMS.
                             $imagePath = '/var/www/vhosts/41121521.servicio-online.net/httpdocs/' . IMG_ADS . $name; // Ruta original, revisar si es correcta/segura
                              if (file_exists($imagePath) && is_writable($imagePath)) {
                                 Images::rotateImage($imagePath, $rotation_value); // Asume que esta función existe y maneja la ruta
                              } else {
                                  // error_log("Rotate Error: File not found or not writable - " . $imagePath);
                              }
                         }
                    }
                }
             }


            // --- Validación Final (Adaptada del original y de newForm) ---
            // Mantener las validaciones esenciales originales y añadir nuevas si es crítico
             $condicion_region = ($datos_ad['ID_region'] != 0);
             $condicion_ad_type = ($datos_ad['ad_type'] != 0); // ¿Sigue siendo obligatorio?
             $condicion_seller_type = ($datos_ad['seller_type'] != 0); // ¿Sigue siendo obligatorio?
             $condicion_cat = ($datos_ad['ID_cat'] != 0);
             // $condicion_price = (is_numeric($datos_ad['price'])); // Precio 0 es válido
             $condicion_titulo = (mb_strlen($datos_ad['title'], 'UTF-8') >= 10 && mb_strlen($datos_ad['title'], 'UTF-8') <= 50); // Límites de newForm
             $condicion_texto = (mb_strlen($datos_ad['texto'], 'UTF-8') >= 100 && mb_strlen($datos_ad['texto'], 'UTF-8') <= 500); // Límites de newForm
             $condicion_telefono = (!empty($datos_ad['phone']) && preg_match('/^[0-9]{9,15}$/', $datos_ad['phone'])); // Validación básica teléfono

             // Comprobar si alguna de las condiciones falla
             if (!$condicion_region) { $error_insert = true; $error_message = 'Debes seleccionar una provincia.'; }
             elseif (!$condicion_cat) { $error_insert = true; $error_message = 'Debes seleccionar una categoría.'; }
             elseif (!$condicion_ad_type) { $error_insert = true; $error_message = 'Debes seleccionar un tipo de anuncio.'; }
             elseif (!$condicion_seller_type) { $error_insert = true; $error_message = 'Debes seleccionar un tipo de vendedor.'; }
             elseif (!$condicion_titulo) { $error_insert = true; $error_message = 'El título debe tener entre 10 y 50 caracteres.'; }
             elseif (!$condicion_texto) { $error_insert = true; $error_message = 'La descripción debe tener entre 100 y 500 caracteres.'; }
             elseif (!$condicion_telefono) { $error_insert = true; $error_message = 'El teléfono no es válido (9-15 dígitos).'; }
             // Añadir más validaciones si son necesarias (ej: servicios obligatorios?)


            // --- Guardar en Base de Datos ---
            if (!$error_insert) {
                $insert = updateSQL("sc_ad", $datos_ad, $w = array('ID_ad' => $id_ad_to_edit));

                if ($insert === false) {
                     $error_insert = true;
                     $error_message = 'Error al actualizar los datos del anuncio en la base de datos.';
                     // Podrías intentar obtener el error de la BD aquí si tus funciones lo permiten
                     // global $Connection; $error_message .= ' DB Error: ' . mysqli_error($Connection);
                } else {
                     // --- Manejo de Imágenes (Después de actualizar el anuncio) ---

                    // 1. Subida de Nuevas Imágenes (si se añaden en la edición)
                     if (isset($_FILES['userImage']) && is_array($_FILES['userImage']['name'])) {
                        $tot = count($_FILES['userImage']['name']);
                        for ($i = 0; $i < $tot; $i++) {
                             // Solo procesar si se subió un archivo sin error
                             if (isset($_FILES['userImage']['error'][$i]) && $_FILES['userImage']['error'][$i] == UPLOAD_ERR_OK && !empty($_FILES['userImage']['name'][$i])) {
                                // Pasar el índice correcto a uploadImage
                                $resultado = uploadImage($_FILES['userImage'], IMG_ADS, $i, true); // true = unique name?
                                 if ($resultado !== false) {
                                    // Ruta de la imagen subida - ¡Revisar esta ruta!
                                     $imagePath = '/var/www/vhosts/41121521.servicio-online.net/httpdocs/' . IMG_ADS . $resultado;
                                     compressImage($imagePath); // Comprimir inmediatamente
                                     // Insertar en sc_images
                                     insertSQL("sc_images", $data = array('ID_ad' => $id_ad_to_edit, 'name_image' => $resultado, 'date_upload' => time(), 'status' => ImageStatus::Active)); // Asegurar status activo
                                 } else {
                                      // Registrar error de subida si es necesario
                                      // error_log("Upload Error: uploadImage failed for file index " . $i);
                                 }
                             }
                         }
                     }

                    // 2. Actualizar Posición/Estado de Imágenes Existentes y Comprimir
                     if (isset($_POST['photo_name']) && is_array($_POST['photo_name'])) {
                        $current_positions = [];
                        foreach ($_POST['photo_name'] as $photo_index => $name) {
                            // Validar que el nombre no esté vacío
                             if (!empty($name)) {
                                // Actualiza la posición de la imagen
                                updateSQL("sc_images", $data = array('position' => $photo_index), $wa = array('name_image' => $name, 'ID_ad' => $id_ad_to_edit)); // Añadir ID_ad a la condición por seguridad

                                // Comprimir la imagen existente (cuidado con la ruta)
                                $imagePath = '/var/www/vhosts/41121521.servicio-online.net/httpdocs/' . IMG_ADS . $name;
                                 if (file_exists($imagePath) && is_readable($imagePath)) {
                                     compressImage($imagePath);
                                 }
                                 $current_positions[$name] = $photo_index; // Guardar las imágenes que permanecen
                             }
                        }

                         // 3. Eliminar Imágenes Marcadas para Borrar (o las que ya no están en photo_name)
                        // Cargar todas las imágenes actuales de la BD para este anuncio
                         $images_in_db = selectSQL("sc_images", $w = array('ID_ad' => $id_ad_to_edit), 'ID_image ASC');
                         if ($images_in_db) {
                             foreach ($images_in_db as $img_db) {
                                 // Si la imagen de la BD no está en el array $_POST['photo_name'] que se envió, significa que se eliminó en el formulario.
                                 if (!isset($current_positions[$img_db['name_image']])) {
                                     // Marcar como eliminada o borrar directamente
                                      // Opción 1: Marcar (si tienes un estado 'eliminado')
                                      // updateSQL("sc_images", ['status' => ImageStatus::Delete], ['ID_image' => $img_db['ID_image']]);
                                      // Opción 2: Eliminar registro y archivo físico (¡CUIDADO!)
                                     Images::deleteImage($img_db['ID_image']); // Asume que esta función borra registro Y archivo
                                 }
                             }
                         }
                     } else {
                         // Si no se envió photo_name[], quizás todas las imágenes fueron eliminadas.
                         // Habría que cargar las de la BD y borrarlas.
                         $images_in_db = selectSQL("sc_images", $w = array('ID_ad' => $id_ad_to_edit));
                         if ($images_in_db) {
                             foreach ($images_in_db as $img_db) {
                                 Images::deleteImage($img_db['ID_image']);
                             }
                         }
                     }

                     // 4. (Opcional) Re-activar imágenes si tenías lógica de status Inactive/Active
                     // Esta parte del código original parecía genérica, podría no ser necesaria si manejas status en la subida/actualización
                     /*
                     $images = selectSQL("sc_images", $w = array('ID_ad' => $id_ad_to_edit), 'position ASC, ID_image ASC');
                     foreach ($images as $key => $value) {
                         //if ($value['status'] == ImageStatus::Delete) // Ya manejado arriba
                         //    Images::deleteImage($value['ID_image']);
                         if ($value['status'] == ImageStatus::Inactive)
                             updateSQL("sc_images", $d = array('status' => ImageStatus::Active), $w = array('ID_image' => $value['ID_image']));
                     }
                     */

                    $edited = true; // Marcar como editado exitosamente
                } // Fin else $insert
            } // Fin if !$error_insert

             // Si hubo éxito, recargar los datos actualizados para mostrarlos (o cerrar ventana)
             if ($edited) {
                // Opcional: Recargar datos frescos para asegurar que se ven los cambios
                // $ad = getDataAd($id_ad_to_edit, false);
                // $ad_servicios = json_decode($ad['ad']['servicios'] ?? '[]', true);
                // $ad_horario = json_decode($ad['ad']['horario'] ?? '{}', true);
                // $ad_idiomas = json_decode($ad['ad']['idiomas'] ?? '[]', true);

                // Opcional: Forzar parseChanges si es necesario para reflejar cambios
                // $ad = parseChanges($ad);
             }
             // Si hubo error ($error_insert), $edited será false y se mostrará el formulario con el mensaje de error.

        } // Fin if (isset($_POST['tit']))

        // --- Preparación Final de Datos para el Formulario (después de posible POST) ---
         // Si no hubo POST o si hubo error, $ad ya tiene los datos (originales o recargados si hubo error y se recargó)
         // Si hubo éxito en POST ($edited = true), $ad podría tener los datos actualizados si se recargaron arriba.

         // Asegurar que $ad['ad'] y $ad['category'] existen antes de usarlos en el HTML
         if (!isset($ad['ad'])) $ad['ad'] = [];
         if (!isset($ad['category'])) $ad['category'] = []; // Para los campos condicionales field_x

         // Variables de idioma (asegurar que $language existe)
         global $language;
         if (!isset($language)) $language = []; // Evitar errores si no está cargado


?>

        <div class="col_single">
            <h2><?= $language['edit.title_h1'] ?? 'Editar Anuncio' ?></h2>

            <?php if ($edited): ?>
                <?php /* Decidir si cerrar la ventana o mostrar mensaje de éxito */ ?>
                <div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i> Anuncio modificado correctamente!</div>
                <div class="text-center">
                    <a class="btn btn-primary" href="javascript:void(0);" onclick="window.close(); return false;"><i class="fa fa-times" aria-hidden="true"></i> Cerrar</a>
                    <?php /* O redirigir a la lista de anuncios del admin?
                    <a class="btn btn-primary" href="/admin/list_items.php"> Volver a la lista</a>
                    */ ?>
                </div>
                 <script>
                     // Opcional: Cerrar automáticamente después de un delay
                     // setTimeout(function() { window.close(); }, 2000);
                 </script>
            <?php else: ?>
                <?php // Mostrar error si lo hubo durante el POST
                 if (!empty($error_message)): ?>
                    <div class="error_msg" style="display: block; border: 1px solid red; padding: 10px; margin-bottom: 15px; background-color: #ffebeb;">
                        <strong>Error:</strong> <?= htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <form id="edit_item_post" class="fm" method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']); // Usar REQUEST_URI para mantener el ?a=ID ?>" enctype="multipart/form-data">
                    <?php /* Considerar añadir un token CSRF si el CMS lo soporta */ ?>
                    <?php // echo generateFormToken('editAdToken'); // Ejemplo ?>
                    <?php // <input type="hidden" name="token" value="..."> ?>

                    <fieldset>
                        <legend>Información Principal</legend>
                        <div class="row">
                            <div class="col_lft"><label><?= $language['post.label_category'] ?? 'Categoría' ?> *</label></div>
                            <div class="col_rgt"><select name="category" id="category" required>
                                    <option value=""><?= $language['post.select_category'] ?? 'Seleccionar Categoría' ?></option>
                                    <?php
                                    // Lógica de categorías original - Revisar si sigue siendo válida
                                    $parent_cat_actual = $ad['ad']['parent_cat'] ?? 0;
                                    $cat_actual = $ad['ad']['ID_cat'] ?? 0;

                                    $parent_cats = selectSQL("sc_category", $where = array('parent_cat' => -1), "ord ASC");
                                    if ($parent_cats) {
                                        foreach ($parent_cats as $parent) {
                                             echo '<optgroup label="' . htmlspecialchars($parent['name']) . '">';
                                             $child_cats = selectSQL("sc_category", $where = array('parent_cat' => $parent['ID_cat']), "name ASC");
                                             if ($child_cats) {
                                                foreach ($child_cats as $child) {
                                                    $selected = ($child['ID_cat'] == $cat_actual) ? ' selected' : '';
                                                    echo '<option value="' . $child['ID_cat'] . '"' . $selected . '>  ' . htmlspecialchars($child['name']) . '</option>';
                                                }
                                             } else {
                                                 // Si no hay hijos, la categoría padre podría ser seleccionable? Originalmente no lo parecía.
                                                 // $selected = ($parent['ID_cat'] == $cat_actual) ? ' selected' : '';
                                                 // echo '<option value="' . $parent['ID_cat'] . '"' . $selected . '>' . htmlspecialchars($parent['name']) . '</option>';
                                             }
                                              echo '</optgroup>';
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="error_msg" id="error_category"><?= $language['post.error_category'] ?? 'Selecciona una categoría válida' ?></div>
                            </div>
                        </div>

                         <div class="row">
                            <div class="col_lft">
                                <label><?= $language['edit.label_region'] ?? 'Provincia' ?> *</label>
                            </div>
                            <div class="col_rgt">
                                <select name="region" size="1" id="region" required>
                                    <option value=""><?= $language['edit.select_region'] ?? 'Seleccionar Provincia' ?></option>
                                    <?php $regions = selectSQL("sc_region", $arr = array(), "name ASC");
                                    if ($regions) {
                                        foreach ($regions as $region) {
                                            $selected = ($region['ID_region'] == ($ad['ad']['ID_region'] ?? 0)) ? ' selected' : '';
                                            echo '<option value="' . $region['ID_region'] . '"' . $selected . '>' . htmlspecialchars($region['name']) . '</option>';
                                        }
                                     } ?>
                                </select>
                                <div class="error_msg" id="error_region"><?= $language['edit.error_region'] ?? 'Selecciona una provincia' ?></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col_lft">
                                <label><?= $language['edit.label_city'] ?? 'Localidad/Zona' ?> </label>
                            </div>
                            <div class="col_rgt">
                                <?php /* El select de ciudad se eliminó en favor de un input de texto */ ?>
                                <input name="city" type="text" id="city" maxlength="250" value="<?= htmlspecialchars(stripslashes($ad['ad']['location'] ?? '')); ?>" />
                                <div class="error_msg" id="error_city"><?= $language['edit.error_city'] ?? 'Introduce la localidad o zona' ?></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col_lft">
                                <label><?= $language['edit.label_ad_type'] ?? 'Tipo Anuncio' ?> *</label>
                            </div>
                            <div class="col_rgt">
                                <select name="ad_type" size="1" id="ad_type" required>
                                    <option value="" <?= ($ad['ad']['ad_type'] ?? '') == '' ? 'selected' : '' ?>><?= $language['edit.select_ad_type'] ?? 'Seleccionar Tipo' ?></option>
                                    <option value="1" <?= ($ad['ad']['ad_type'] ?? '') == '1' ? 'selected' : '' ?>><?= $language['edit.ad_type_option_1'] ?? 'Oferta' ?></option> <?php /* Ajusta textos */ ?>
                                    <option value="2" <?= ($ad['ad']['ad_type'] ?? '') == '2' ? 'selected' : '' ?>><?= $language['edit.ad_type_option_2'] ?? 'Demanda' ?></option> <?php /* Ajusta textos */ ?>
                                </select>
                                <div class="error_msg" id="error_ad_type"><?= $language['edit.error_ad_type'] ?? 'Selecciona el tipo de anuncio' ?></div>
                            </div>
                        </div>
                    </fieldset>

                     <fieldset>
                        <legend>Detalles del Anuncio</legend>
                        <div class="row">
                            <div class="col_lft"><label><?= $language['edit.label_title'] ?? 'Título' ?> *</label></div>
                            <div class="col_rgt">
                                <input name="tit" type="text" id="tit" value="<?= htmlspecialchars(stripslashes($ad['ad']['title'] ?? '')); ?>" required minlength="10" maxlength="50" />
                                <div class="input_count">Caracteres <span id="nro-car-tit"><?= mb_strlen($ad['ad']['title'] ?? '', 'UTF-8') ?></span> (min 10 / máx 50)</div>
                                <div class="error_msg" id="error_tit"><?= $language['edit.error_title'] ?? 'Título obligatorio (10-50 caracteres)' ?></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col_lft"><label><?= $language['edit.label_description'] ?? 'Descripción' ?> *</label></div>
                            <div class="col_rgt">
                                <?php // Usar htmlspecialchars_decode si el texto se guarda ya codificado, o stripslashes si solo tiene slashes
                                $text_value = $ad['ad']['texto'] ?? '';
                                // Decidir si decodificar o no. Si se usa htmlspecialchars al guardar, se debe mostrar el texto plano aquí.
                                $text_value = htmlspecialchars_decode($text_value, ENT_QUOTES);
                                ?>
                                <textarea name="text" rows="8" id="text" required minlength="100" maxlength="500"><?= htmlspecialchars($text_value); // Volver a codificar para el atributo value/contenido ?></textarea>
                                <div class="input_count">Caracteres <span id="nro-car-text"><?= mb_strlen($text_value, 'UTF-8') ?></span> (min 100 / máx 500)</div>
                                <div class="error_msg" id="error_text"><?= $language['edit.error_description'] ?? 'Descripción obligatoria (100-500 caracteres)' ?></div>
                            </div>
                        </div>

                        <?php // --- NUEVO: Sección de Servicios --- ?>
                        <div class="row">
                            <div class="col_lft"><label>Servicios *</label></div>
                            <div class="col_rgt">
                                <div class="grupo-checkboxes" style="max-height: 150px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
                                    <?php
                                    // *** IMPORTANTE: Define aquí la lista COMPLETA de servicios posibles ***
                                    $all_servicios = [
                                        "Masaje relajante", "Masaje deportivo", "Masaje podal", "Masaje antiestrés",
                                        "Masaje linfático", "Masaje shiatsu", "Masaje descontracturante", "Masaje ayurvédico",
                                        "Masaje circulatorio", "Masaje tailandés", "Otros masajes" // Añade todos los que tengas
                                    ];
                                    // $ad_servicios ya está decodificado más arriba

                                    foreach ($all_servicios as $servicio) {
                                        $valor = strtolower(str_replace(' ', '_', $servicio)); // Generar valor consistente
                                        $checked = ($ad_servicios && in_array($valor, $ad_servicios)) ? 'checked' : '';
                                        echo '<label style="display: block; margin-bottom: 5px;">';
                                        echo '<input type="checkbox" name="servicios[]" value="' . htmlspecialchars($valor) . '" ' . $checked . '> ';
                                        echo htmlspecialchars($servicio);
                                        echo '</label>';
                                    }
                                    ?>
                                </div>
                                <div class="error_msg" id="error_servicios">Selecciona al menos un servicio.</div>
                            </div>
                        </div>

                        <?php // --- Campos Condicionales (Coche/Inmobiliaria) --- ?>
                        <?php // Mantener esta lógica si sigue siendo relevante según la categoría ?>
                        <div id="extra_fields" style="<?= ($ad['category']['field_0'] ?? 0) == 1 || ($ad['category']['field_2'] ?? 0) == 1 || ($ad['category']['field_3'] ?? 0) == 1 ? 'display: block;' : 'display: none;' ?>">
                            <legend>Detalles Adicionales (Según Categoría)</legend>
                             <?php if (($ad['category']['field_0'] ?? 0) == 1): // Coche ?>
                                <div class="row">
                                    <div class="col_lft"><label>Kilómetros</label></div>
                                    <div class="col_rgt"><input name="km_car" type="tel" id="km_car" size="10" maxlength="10" value="<?= htmlspecialchars($ad['ad']['mileage'] ?? '') ?>" /></div>
                                </div>
                                <div class="row">
                                    <div class="col_lft"><label>Año</label></div>
                                    <div class="col_rgt"><select name="date_car" id="date_car">
                                            <option value="">Año</option>
                                            <?php for ($i = date("Y"); $i > 1970; $i--): ?>
                                                <option value="<?= $i ?>" <?= (($ad['ad']['date_car'] ?? '') == $i) ? 'selected' : ''; ?>><?= $i ?></option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col_lft"><label>Combustible</label></div>
                                    <div class="col_rgt"><select name="fuel_car" id="fuel_car">
                                            <option value="">Combustible</option>
                                            <?php $type_fuel = selectSQL("sc_type_fuel", $w = array(), 'ID_fuel ASC');
                                            if ($type_fuel) {
                                                foreach ($type_fuel as $fuel) {
                                                    $selected = (($ad['ad']['fuel'] ?? '') == $fuel['ID_fuel']) ? 'selected' : '';
                                                    echo '<option value="' . $fuel['ID_fuel'] . '" ' . $selected . '>' . htmlspecialchars($fuel['name']) . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if (($ad['category']['field_3'] ?? 0) == 1): // Habitaciones/Baños ?>
                                <div class="row">
                                    <div class="col_lft"><label>Habitaciones</label></div>
                                    <div class="col_rgt"><input type="tel" id="room" name="room" maxlength="4" value="<?= htmlspecialchars($ad['ad']['room'] ?? '') ?>"></div>
                                </div>
                                <div class="row">
                                    <div class="col_lft"><label>Baños</label></div>
                                    <div class="col_rgt"><input type="tel" id="bathroom" name="bathroom" maxlength="4" value="<?= htmlspecialchars($ad['ad']['broom'] ?? '') ?>"></div>
                                </div>
                            <?php endif; ?>
                            <?php if (($ad['category']['field_2'] ?? 0) == 1): // Superficie ?>
                                <div class="row">
                                    <div class="col_lft"><label>Superficie (m<sup>2</sup>)</label></div>
                                    <div class="col_rgt"><input type="tel" id="area" name="area" maxlength="10" value="<?= htmlspecialchars($ad['ad']['area'] ?? '') ?>"><span class="decimal_price">.00 <b>m<sup>2</sup></b></span></div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="row"> <?php // Mantener precio si se puede editar ?>
                            <div class="col_lft"><label><?= $language['edit.label_price'] ?? 'Precio' ?></label></div>
                            <div class="col_rgt"><input class="numeric" name="precio" type="text" id="precio" size="10" maxlength="12" value="<?= htmlspecialchars($ad['ad']['price'] ?? '0'); ?>" /><span class="decimal_price"><b><?= COUNTRY_CURRENCY_CODE ?? 'EUR'; ?></b></span>
                                <div class="error_msg" id="error_price">Indica un precio válido (puede ser 0).</div>
                            </div>
                        </div>

                         <?php // --- NUEVO: Horario Detallado --- ?>
                        <div class="row">
                             <div class="col_lft"><label>Horario Detallado *</label></div>
                             <div class="col_rgt">
                                 <div class="horario-semanal" id="contenedor-horario-edit" style="border: 1px solid #ccc; padding: 10px;">
                                     <?php
                                     $dias = ['lunes' => 'Lunes', 'martes' => 'Martes', 'miercoles' => 'Miércoles', 'jueves' => 'Jueves', 'viernes' => 'Viernes', 'sabado' => 'Sábado', 'domingo' => 'Domingo'];
                                     // $ad_horario ya está decodificado más arriba
                                     foreach ($dias as $key => $nombre) {
                                         $dia_data = $ad_horario[$key] ?? ['activo' => 0, 'inicio' => '09:00', 'fin' => '18:00']; // Defaults razonables
                                         $is_active = $dia_data['activo'] ?? 0;
                                         $start_time = $dia_data['inicio'] ?? '09:00';
                                         $end_time = $dia_data['fin'] ?? '18:00';
                                         // Clases y estado inicial para HTML y JS
                                         $active_class = $is_active ? 'disponible' : 'no-disponible';
                                         $hours_hidden_class = !$is_active ? 'oculto' : ''; // Ocultar si no está activo
                                     ?>
                                         <div class="dia-horario" id="horario-edit-<?= $key ?>" data-dia="<?= $key ?>" style="margin-bottom: 8px; padding-bottom: 5px; border-bottom: 1px dashed #eee; display: flex; align-items: center; flex-wrap: wrap;">
                                             <span class="nombre-dia" style="width: 80px; font-weight: bold;"><?= $nombre ?>:</span>
                                             <button type="button" class="btn-dia-estado <?= $active_class ?>" data-dia="<?= $key ?>" style="margin: 0 10px; padding: 2px 8px; cursor: pointer;"><?= $is_active ? 'Disponible' : 'No disponible' ?></button>
                                             <?php /* Input oculto para enviar estado activo (JS lo maneja) */ ?>
                                             <?php if ($is_active): ?>
                                                <input type="hidden" name="horario_dia[<?= $key ?>][activo]" value="1" class="activo-hidden-input">
                                             <?php endif; ?>
                                             <div class="horas-dia <?= $hours_hidden_class ?>" style="display: inline-flex; align-items: center;">
                                                <label style="margin-right: 5px;">De:</label>
                                                 <select name="horario_dia[<?= $key ?>][inicio]" class="frm-select corto" <?= !$is_active ? 'disabled' : '' ?> style="width: 80px; margin-right: 10px;">
                                                     <?php for ($h = 0; $h < 24; $h++) {
                                                         $hora = sprintf('%02d', $h);
                                                         $sel00 = ($start_time == "{$hora}:00") ? 'selected' : '';
                                                         $sel30 = ($start_time == "{$hora}:30") ? 'selected' : '';
                                                         echo "<option value='{$hora}:00' $sel00>{$hora}:00</option>";
                                                         if ($h < 24) echo "<option value='{$hora}:30' $sel30>{$hora}:30</option>";
                                                     } ?>
                                                 </select>
                                                 <label style="margin-right: 5px;">A:</label>
                                                 <select name="horario_dia[<?= $key ?>][fin]" class="frm-select corto" <?= !$is_active ? 'disabled' : '' ?> style="width: 80px;">
                                                     <?php for ($h = 0; $h < 24; $h++) {
                                                         $hora = sprintf('%02d', $h);
                                                         $sel00 = ($end_time == "{$hora}:00") ? 'selected' : '';
                                                         $sel30 = ($end_time == "{$hora}:30") ? 'selected' : '';
                                                         echo "<option value='{$hora}:00' $sel00>{$hora}:00</option>";
                                                          if ($h < 24) echo "<option value='{$hora}:30' $sel30>{$hora}:30</option>";
                                                     } ?>
                                                 </select>
                                             </div>
                                         </div>
                                     <?php } // Fin foreach dias ?>
                                 </div>
                                 <div class="error_msg" id="error_horario">Configura el horario para los días disponibles.</div>
                             </div>
                        </div>
                        <?php // Script JS básico para manejar los botones de disponibilidad del horario ?>
                        <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            document.querySelectorAll('#contenedor-horario-edit .btn-dia-estado').forEach(button => {
                                button.addEventListener('click', function(e) {
                                    e.preventDefault(); // Prevenir submit si estuviera dentro de form
                                    const diaDiv = this.closest('.dia-horario');
                                    const horasDiv = diaDiv.querySelector('.horas-dia');
                                    const selects = horasDiv.querySelectorAll('select');
                                    let activoInput = diaDiv.querySelector('.activo-hidden-input'); // Buscar existente

                                    if (this.classList.contains('no-disponible')) {
                                        // Cambiar a Disponible
                                        this.classList.remove('no-disponible');
                                        this.classList.add('disponible');
                                        this.textContent = 'Disponible';
                                        horasDiv.classList.remove('oculto');
                                        selects.forEach(s => s.disabled = false);
                                        // Añadir o asegurar input oculto activo=1
                                        if (!activoInput) {
                                             const hidden = document.createElement('input');
                                             hidden.type = 'hidden';
                                             hidden.name = `horario_dia[${this.dataset.dia}][activo]`;
                                             hidden.value = '1';
                                             hidden.classList.add('activo-hidden-input');
                                             // Insertar después del botón
                                             this.parentNode.insertBefore(hidden, this.nextSibling);
                                        } else {
                                            activoInput.value = '1'; // Asegurar valor
                                        }
                                    } else {
                                        // Cambiar a No Disponible
                                        this.classList.remove('disponible');
                                        this.classList.add('no-disponible');
                                        this.textContent = 'No disponible';
                                        horasDiv.classList.add('oculto');
                                        selects.forEach(s => s.disabled = true);
                                        // Eliminar input oculto si existe
                                         if (activoInput) {
                                             activoInput.remove();
                                         }
                                    }
                                });
                            });
                            // Opcional: inicializar selects basados en data-initial-active si es necesario
                        });
                        </script>

                         <?php // --- NUEVO: Idiomas --- ?>
                        <div class="row">
                            <div class="col_lft"><label>Idiomas Hablados</label></div>
                            <div class="col_rgt">
                                 <?php
                                 // $ad_idiomas ya está decodificado
                                 $lang1_data = $ad_idiomas[0] ?? null;
                                 $lang2_data = $ad_idiomas[1] ?? null;
                                 // *** IMPORTANTE: Carga aquí la lista COMPLETA de idiomas y niveles ***
                                 $idiomas_lista = ['es' => 'Español', 'en' => 'Inglés', 'fr' => 'Francés', 'de' => 'Alemán', 'pt' => 'Portugués', 'it' => 'Italiano', /* ... añadir más */ ];
                                 $niveles = ['basico' => 'Básico', 'intermedio' => 'Intermedio', 'avanzado' => 'Avanzado', 'nativo' => 'Nativo', 'desconocido' => 'Desconocido'];
                                 ?>
                                 <div style="margin-bottom: 10px; display: flex; gap: 10px;">
                                     <select name="idioma_1" class="frm-select" style="flex: 1;">
                                         <option value="">-- Idioma 1 --</option>
                                         <?php foreach ($idiomas_lista as $code => $name): ?>
                                             <option value="<?= htmlspecialchars($code) ?>" <?= ($lang1_data && isset($lang1_data['idioma']) && $lang1_data['idioma'] == $code) ? 'selected' : '' ?>><?= htmlspecialchars($name) ?></option>
                                         <?php endforeach; ?>
                                     </select>
                                     <select name="nivel_idioma_1" class="frm-select" style="flex: 1;">
                                         <option value="">-- Nivel 1 --</option>
                                          <?php foreach ($niveles as $code => $name): ?>
                                             <option value="<?= htmlspecialchars($code) ?>" <?= ($lang1_data && isset($lang1_data['nivel']) && $lang1_data['nivel'] == $code) ? 'selected' : '' ?>><?= htmlspecialchars($name) ?></option>
                                         <?php endforeach; ?>
                                     </select>
                                 </div>
                                  <div style="display: flex; gap: 10px;">
                                     <select name="idioma_2" class="frm-select" style="flex: 1;">
                                         <option value="">-- Idioma 2 --</option>
                                          <?php foreach ($idiomas_lista as $code => $name): ?>
                                             <option value="<?= htmlspecialchars($code) ?>" <?= ($lang2_data && isset($lang2_data['idioma']) && $lang2_data['idioma'] == $code) ? 'selected' : '' ?>><?= htmlspecialchars($name) ?></option>
                                         <?php endforeach; ?>
                                     </select>
                                     <select name="nivel_idioma_2" class="frm-select" style="flex: 1;">
                                         <option value="">-- Nivel 2 --</option>
                                          <?php foreach ($niveles as $code => $name): ?>
                                             <option value="<?= htmlspecialchars($code) ?>" <?= ($lang2_data && isset($lang2_data['nivel']) && $lang2_data['nivel'] == $code) ? 'selected' : '' ?>><?= htmlspecialchars($name) ?></option>
                                         <?php endforeach; ?>
                                     </select>
                                 </div>
                            </div>
                        </div>

                        <?php // --- NUEVO: Salidas --- ?>
                        <div class="row">
                            <div class="col_lft"><label>¿Realizas salidas? *</label></div>
                            <div class="col_rgt">
                                <select name="out" id="realiza_salidas" class="frm-select" required>
                                    <?php // Usar ?? '0' por si el campo es NULL en la BD ?>
                                    <option value="0" <?= (($ad['ad']['out'] ?? '0') == '0') ? 'selected' : '' ?>>No</option>
                                    <option value="1" <?= (($ad['ad']['out'] ?? '0') == '1') ? 'selected' : '' ?>>Sí</option>
                                </select>
                                 <div class="error_msg" id="error_out">Indica si realizas salidas.</div>
                            </div>
                        </div>

                    </fieldset>

                    <fieldset>
                        <legend>Fotos del Anuncio</legend>
                        <div class="title_photos_list" id="title_photos_list">Gestionar fotos (Máx: <?= $DATAJSON['max_photos'] ?? 3 ?>)</div>
                        <div class="error_msg" id="error_photo" style="display: none;">Sube al menos una foto para tu anuncio.</div>
                        <div class="photos_list sortable">
                            <?php
                            // Cargar imágenes existentes
                            $ad_images = $ad['images'] ?? []; // Usar array vacío si no hay imágenes
                            $current_photos = count($ad_images);
                             $upload_photos = ($DATAJSON['max_photos'] ?? 3) - $current_photos; // Espacios libres
                             $photo_id = 1; // Contador para IDs de elementos HTML/JS

                            // Mostrar imágenes existentes
                            foreach ($ad_images as $image_data) {
                                 if (!isset($image_data['name_image']) || empty($image_data['name_image'])) continue; // Saltar si no hay nombre
                                $image_name = $image_data['name_image'];
                                // ¡Revisar la URL base de las imágenes! ¿Es correcta para el admin?
                                $image_url = (getConfParam('SITE_URL') ?? '/') . 'src/photos/' . $image_name; // Asumiendo estructura original
                                ?>
                                <div class="photo_box" id="photo_box_<?= $photo_id ?>">
                                    <div id="photo_container-<?= $photo_id; ?>" class="photo_list">
                                         <div class="removeImg" title="Eliminar esta imagen"><i class="fa fa-times" aria-hidden="true"></i></div>
                                         <?php /* El icono de editar/rotar del original necesita JS (editImage, rotateRight, etc) */ ?>
                                         <a href="javascript:void(0);" class="edit-photo-icon" onclick="editImage(<?= $photo_id ?>); return false;" title="Rotar/Editar">
                                            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#333"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h357l-80 80H200v560h560v-278l80-80v358q0 33-23.5 56.5T760-120H200Zm280-360ZM360-360v-170l367-367q12-12 27-18t30-6q16 0 30.5 6t26.5 18l56 57q11 12 17 26.5t6 29.5q0 15-5.5 29.5T897-728L530-360H360Zm481-424-56-56 56 56ZM440-440h56l232-232-28-28-29-28-231 231v57Zm260-260-29-28 29 28 28 28-28-28Z"/></svg>
                                         </a>
                                         <span class="helper"></span>
                                         <?php // Comprobar si la imagen existe antes de mostrarla
                                         // La ruta física real podría ser diferente a la URL
                                         $physical_path = '/var/www/vhosts/41121521.servicio-online.net/httpdocs/' . IMG_ADS . $image_name; // Revisar ruta
                                          if (file_exists($physical_path)): ?>
                                            <img class="<?= getImgOrientation($physical_path) // Necesita ruta física? Revisar función ?>" src="<?= htmlspecialchars($image_url) ?>" alt="Foto <?= $photo_id ?>" />
                                         <?php else: ?>
                                             <span style="color: red; font-size: 0.8em;">Imagen no encontrada</span>
                                         <?php endif; ?>
                                         <input type="hidden" name="photo_name[]" value="<?= htmlspecialchars($image_name); ?>">
                                    </div>
                                     <div class="photos_options">
                                         <?php /* Los controles de mover/rotar necesitan JS (transferPhoto, rotateRight) */ ?>
                                        <?php /* Icono Rotar Derecha */ ?>
                                         <a href="javascript:void(0);" onclick="rotateRight(<?= $photo_id ?>); return false;" title="Rotar Derecha">
                                             <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#333"><path d="M522-80v-82q34-5 66.5-18t61.5-34l56 58q-42 32-88 51.5T522-80Zm-80 0Q304-98 213-199.5T122-438q0-75 28.5-140.5t77-114q48.5-48.5 114-77T482-798h6l-62-62 56-58 160 160-160 160-56-56 64-64h-8q-117 0-198.5 81.5T202-438q0 104 68 182.5T442-162v82Zm322-134-58-56q21-29 34-61.5t18-66.5h82q-5 50-24.5 96T764-214Zm76-264h-82q-5-34-18-66.5T706-606l58-56q32 39 51 86t25 98Z"/></svg>
                                         </a>
                                         <?php /* Inputs ocultos para opciones de imagen (rotación) */ ?>
                                         <input type="hidden" name="optImgage[<?= $photo_id-1 ?>][rotation]" id="rotation-<?= $photo_id; ?>" value="0"> <?php // Asegura el índice correcto ?>
                                    </div>
                                </div>
                            <?php $photo_id++;
                             } // Fin foreach imágenes existentes ?>

                            <?php // Mostrar placeholders para subir nuevas fotos
                            for ($i = 0; $i < $upload_photos; $i++) { ?>
                                <div class="photo_box">
                                    <div id="photo_container-<?= $photo_id; ?>" class="photo_list free">
                                         <?php /* Cambiar name a userImage[] para coincidir con POST handling */ ?>
                                        <input name="userImage[]" id="photo-<?= $photo_id; ?>" type="file" class="photoFile" accept="image/jpeg, image/png" />
                                        <span class="upload-placeholder-icon">+</span> <?php // Placeholder visual ?>
                                    </div>
                                    <div class="photos_options">
                                         <?php /* Opciones vacías o deshabilitadas para placeholders */ ?>
                                    </div>
                                     <?php /* No necesita input de rotación hasta que se suba */ ?>
                                     <?php /* <input type="hidden" name="optImgage[<?= $photo_id-1 ?>][rotation]" id="rotation-<?= $photo_id; ?>" value="0"> */ ?>
                                </div>
                            <?php $photo_id++;
                            } // Fin for placeholders ?>
                            <div class="error_msg" id="error_photos_upload" style="clear: both; padding-top: 10px;"></div> <?php // Mensajes de error JS ?>
                        </div>
                        <?php // Script básico para eliminar imagen (necesitarás adaptarlo/integrarlo con tu JS existente) ?>
                         <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                document.querySelectorAll('.photos_list .removeImg').forEach(button => {
                                    button.addEventListener('click', function(e) {
                                        e.preventDefault();
                                        if (confirm('¿Seguro que quieres eliminar esta imagen? No se podrá recuperar.')) {
                                            const photoBox = this.closest('.photo_box');
                                            const photoInput = photoBox.querySelector('input[name="photo_name[]"]');
                                            if (photoInput) {
                                                // Opción 1: Vaciar el valor para que la lógica PHP lo detecte como eliminado
                                                // photoInput.value = "";
                                                // photoBox.style.display = 'none'; // Ocultarlo visualmente

                                                // Opción 2: Eliminar completamente el photo_box (preferido si es seguro)
                                                 photoBox.remove();

                                                // Aquí podrías necesitar lógica adicional, como:
                                                // - Contar cuántos quedan y mostrar/ocultar el error de "mínimo una foto".
                                                // - Añadir un nuevo placeholder si se elimina uno y hay espacio libre.
                                            } else {
                                                 console.error("No se encontró el input oculto de la imagen para eliminar.");
                                            }
                                        }
                                    });
                                });
                                // Añadir aquí la inicialización de Sortable y otros JS necesarios para las fotos
                                // $('.sortable').sortable({ ... });
                            });
                         </script>

                    </fieldset>

                    <fieldset>
                        <legend>Datos de Contacto</legend>

                        <div class="row">
                            <div class="col_lft"><label><?= $language['post.label_name'] ?? 'Nombre Contacto' ?> *</label></div>
                            <div class="col_rgt"><input name="name" type="text" id="name" size="30" maxlength="50" required value="<?= htmlspecialchars($ad['ad']['name'] ?? ''); ?>" />
                                <div class="error_msg" id="error_name"><?= $language['post.error_name'] ?? 'Introduce tu nombre' ?></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col_lft">
                                <label><?= $language['edit.label_seller_type'] ?? 'Tipo Vendedor' ?> *</label>
                            </div>
                            <div class="col_rgt">
                                <select id="sellerType" name="seller_type" size="1" required>
                                     <option value="" <?= (($ad['ad']['seller_type'] ?? '') == '') ? 'selected' : '' ?>><?= $language['edit.select_seller_type'] ?? 'Seleccionar Tipo' ?></option>
                                     <?php // Asegúrate que los valores (1, 2) coinciden con tu lógica/BD ?>
                                     <option value="1" <?= (($ad['ad']['seller_type'] ?? '') == '1') ? 'selected' : '' ?>><?= $language['edit.seller_type_option_1'] ?? 'Particular' ?></option> <?php /* Ajusta texto */ ?>
                                     <option value="2" <?= (($ad['ad']['seller_type'] ?? '') == '2') ? 'selected' : '' ?>><?= $language['edit.seller_type_option_2'] ?? 'Profesional/Centro' ?></option> <?php /* Ajusta texto */ ?>
                                     <?php /* <option value="3" ...>Publicista</option> si aplica */ ?>
                                </select>
                                <div class="error_msg" id="error_sellerType"><?= $language['edit.error_seller_type'] ?? 'Selecciona el tipo de vendedor' ?></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col_lft"><label><?= $language['post.label_phone'] ?? 'Teléfono' ?> *</label></div>
                            <div class="col_rgt">
                                <div class="phone_container" style="display: flex; align-items: center; gap: 15px; margin-bottom: 5px;">
                                    <input name="phone" type="tel" id="phone" size="20" maxlength="15" required pattern="[0-9]{9,15}" value="<?= htmlspecialchars($ad['ad']['phone'] ?? ''); ?>" />
                                    <label style="white-space: nowrap;">
                                        <input type="checkbox" value="1" name="whatsapp" <?= (($ad['ad']['whatsapp'] ?? 0) == 1) ? 'checked' : '' ?>> WhatsApp
                                    </label>
                                </div>
                                <?php /* Teléfono secundario - Mantener si aún es relevante
                                <div class="phone_container" style="display: flex; align-items: center; gap: 15px;">
                                    <input name="phone1" type="tel" id="phone1" size="20" maxlength="15" value="<?= htmlspecialchars($ad['ad']['phone1'] ?? ''); ?>"/>
                                    <label style="white-space: nowrap;">
                                        <input type="checkbox" value="1" name="whatsapp1" <?= (($ad['ad']['whatsapp1'] ?? 0) == 1) ? 'checked' : '' ?> > WhatsApp Sec.
                                    </label>
                                </div>
                                */ ?>
                                <div class="error_msg" id="error_phone"><?= $language['post.error_phone'] ?? 'Introduce un teléfono válido (9-15 dígitos)' ?></div>
                            </div>
                        </div>

                         <?php // --- NUEVO: Notifications --- ?>
                         <div class="row">
                            <div class="col_lft"><label>Notificaciones</label></div>
                            <div class="col_rgt">
                                 <label>
                                    <?php // Default a 1 (checked) si no existe el campo? Decide según tu lógica.
                                    $notifications_checked = ($ad['ad']['notifications'] ?? 1) == 1;
                                    ?>
                                    <input type="checkbox" value="1" name="notifications" <?= $notifications_checked ? 'checked' : '' ?>>
                                    Recibir emails cuando contacten por el anuncio
                                 </label>
                             </div>
                         </div>

                    </fieldset>

                     <?php /* Términos y condiciones no suelen ser necesarios en edición */ ?>
                     <?php /*
                    <div class="row">
                        <label class="radio">
                            <input name="terminos" type="checkbox" id="terminos" value="1"/>
                            <?=$language['edit.label_terms']?></label><div class="error_msg" id="error_terminos"><?=$language['edit.error_terms']?></div>
                    </div>
                    */ ?>

                    <div class="row text-center" style="margin-top: 20px;">
                        <?php // Cambiar ID del botón si causa conflictos con JS ?>
                        <input type="submit" class="button btn btn-success" id="submitEditAd" value="<?= $language['edit.button_update'] ?? 'Guardar Cambios' ?>" />
                         <a href="javascript:void(0);" onclick="window.close(); return false;" class="button btn btn-danger" style="margin-left: 15px;">Cancelar</a>
                    </div>
                </form>
            <?php endif; // Fin else ($edited) ?>

            <?php
            // Cargar bloques adicionales si es necesario (editor WYSIWYG, etc.)
            // loadBlock("editor"); // ¿Sigue siendo necesario si usas textarea simple?
            ?>
        </div> <?php // Fin col_single ?>

        <?php // Cargar JS necesario para esta página de edición ?>
        <?php /* Asegúrate que las rutas son correctas para el admin */ ?>
        <?php $admin_base_url = getConfParam('SITE_URL') ?? '/'; // O la URL base del admin ?>
        <script src="<?= $admin_base_url ?>src/js/jquery.min.js"></script> <?php // Asumiendo jQuery necesario ?>
        <script src="<?= $admin_base_url ?>src/js/select2.min.js"></script> <?php // Si usas Select2 ?>
        <script src="<?= $admin_base_url ?>src/js/jquery-ui.min.js"></script> <?php // Si usas Sortable de jQuery UI ?>
        <?php /* Cargar tus JS específicos de admin/post si los tienes */ ?>
         <script src="<?= $admin_base_url ?>src/js/filter.js"></script> <?php // Original ?>
         <script src="<?= $admin_base_url ?>src/js/post.js"></script> <?php // Original - ¡Revisar y adaptar! ?>

        <script type="text/javascript">
            $(document).ready(function() {
                // Inicializar Select2 (si se usa)
                 // Asegúrate que los IDs de los selects son correctos
                $('#category, #region, #ad_type, #sellerType, #date_car, #fuel_car, #realiza_salidas, select[name^="idioma_"], select[name^="nivel_idioma_"]').select2({
                     minimumResultsForSearch: Infinity, // Ocultar búsqueda si no es necesaria
                     width: 'resolve' // Ajustar ancho
                 });

                 // Inicializar Sortable para las fotos (asegúrate que el selector es correcto)
                 $('.photos_list.sortable').sortable({
                     helper: "clone",
                     items: ".photo_box", // Elementos que se pueden ordenar
                     placeholder: "photo_box_placeholder", // Clase para el placeholder
                     forcePlaceholderSize: true,
                     // forceHelperSize: true, // Puede causar problemas visuales a veces
                     // grid: [10, 10], // Rejilla opcional
                     tolerance: "pointer",
                     update: function(event, ui) {
                         // Opcional: Puedes hacer algo cuando se reordena, como re-numerar IDs si es necesario
                     }
                 }).disableSelection(); // Evitar selección de texto al arrastrar

                // Añadir validación JS si es necesario (ej: longitud de título/descripción)
                 $('#tit').on('input', function() {
                     $('#nro-car-tit').text($(this).val().length);
                 });
                 $('#text').on('input', function() {
                     $('#nro-car-text').text($(this).val().length);
                 });

                 // Re-inicializar cualquier otra funcionalidad JS del script 'post.js' original
                 // que sea relevante aquí, adaptándola si es necesario.
                 // Por ejemplo, la lógica para cargar ciudades basada en región (si se vuelve a usar select de ciudad).

                 // Validación antes de enviar (ejemplo básico)
                 $('#edit_item_post').on('submit', function(e) {
                     let isValid = true;
                     let errorMsg = '';

                    // Validar categoría
                    if ($('#category').val() == '' || $('#category').val() == '0') {
                        isValid = false;
                        $('#error_category').show();
                    } else {
                        $('#error_category').hide();
                    }
                    // Validar región
                     if ($('#region').val() == '' || $('#region').val() == '0') {
                         isValid = false;
                         $('#error_region').show();
                     } else {
                        $('#error_region').hide();
                     }

                     // Validar título
                     const titleLen = $('#tit').val().trim().length;
                     if (titleLen < 10 || titleLen > 50) {
                         isValid = false;
                         $('#error_tit').show();
                     } else {
                         $('#error_tit').hide();
                     }
                    // Validar descripción
                     const textLen = $('#text').val().trim().length;
                     if (textLen < 100 || textLen > 500) {
                          isValid = false;
                          $('#error_text').show();
                     } else {
                          $('#error_text').hide();
                     }
                    // Validar teléfono
                     const phone = $('#phone').val().trim();
                      if (!/^[0-9]{9,15}$/.test(phone)) {
                          isValid = false;
                          $('#error_phone').show();
                      } else {
                          $('#error_phone').hide();
                      }

                     // Validar servicios (al menos uno)
                     if ($('input[name="servicios[]"]:checked').length === 0) {
                         isValid = false;
                         $('#error_servicios').show().text('Debes seleccionar al menos un servicio.');
                     } else {
                         $('#error_servicios').hide();
                     }

                    // Validar horario (al menos un día disponible?) - Más complejo, requiere revisar estado de botones/selects
                     let horarioOk = false;
                     $('.dia-horario').each(function() {
                         if ($(this).find('.btn-dia-estado').hasClass('disponible')) {
                             horarioOk = true;
                             return false; // Salir del bucle each
                         }
                     });
                     if (!horarioOk) {
                          isValid = false;
                          $('#error_horario').show().text('Debes marcar al menos un día como disponible.');
                     } else {
                          $('#error_horario').hide();
                     }

                     // Validar fotos (al menos una foto subida o existente)
                    // Contar inputs photo_name[] con valor no vacío + inputs userImage[] con archivo seleccionado
                     let photoCount = $('input[name="photo_name[]"][value!=""]').length;
                     $('input[name="userImage[]"]').each(function() {
                         if (this.files && this.files.length > 0) {
                             photoCount++;
                         }
                     });
                     if (photoCount === 0) {
                         isValid = false;
                         $('#error_photo').show().text('Debes tener al menos una foto.');
                     } else {
                         $('#error_photo').hide();
                     }


                     if (!isValid) {
                         e.preventDefault(); // Detener envío
                         alert('Por favor, corrige los errores marcados en el formulario.');
                         // Opcional: scroll al primer error
                         // $('html, body').animate({ scrollTop: $('.error_msg:visible').first().offset().top - 100 }, 500);
                     }
                     // Si es válido, el formulario se enviará.
                 });

            });
        </script>

<?php
    } else {
        // No se encontró el anuncio con ese ID
        echo "<div class='error_msg'>Error: Anuncio no encontrado.</div>";
        echo "<script>setTimeout(function() { window.history.go(-1); }, 2000);</script>"; // Volver atrás
    }
} else {
    // No se proporcionó el parámetro 'a' (ID del anuncio)
    echo "<div class='error_msg'>Error: ID de anuncio no especificado.</div>";
     echo "<script>setTimeout(function() { window.history.go(-1); }, 2000);</script>"; // Volver atrás
}
?>