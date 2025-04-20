<?php
#edit_item.php 

// Incluir dependencias necesarias si no están auto-incluidas (ajusta según tu CMS)
// require_once('ruta/a/tus/funciones/settings.inc.php'); // Ejemplo
// require_once('ruta/a/tus/funciones/functions.inc.php'); // Ejemplo
// require_once('ruta/a/tus/clases/Images.php'); // Ejemplo
// require_once('ruta/a/tus/clases/User.php'); // Ejemplo

// --- Función de Compresión de Imagen (sin cambios) ---
function compressImage($filePath, $quality = 90)
{
    //die($filePath);
    $info = getimagesize($filePath);
    $image = null;

    if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($filePath);
        imagejpeg($image, $filePath, $quality);
    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($filePath);
        // La calidad en imagepng va de 0 (sin compresión) a 9 (máxima compresión)
        // Un valor medio como 6 podría ser un buen balance si 9 es muy lento.
        imagepng($image, $filePath, 6);
    } else {
        // return false; // Unsupported file type
    }
    //var_dump($filePath , $image , $info);
    //die();

    if ($image) {
        imagedestroy($image);
    }
    return true;
}

$edited = false;
$DATAJSON = array();
$DATAJSON['max_photos'] = getConfParam('MAX_PHOTOS_AD');

if (isset($_GET['a'])) {
    $check = selectSQL("sc_ad", $w = array('ID_ad' => $_GET['a']));
    if (count($check) != 0) {
        // Obtener datos iniciales antes de procesar POST para tenerlos disponibles en caso de error
        $ad = getDataAd($check[0]['ID_ad']); // Asume que esta función trae TODOS los campos necesarios

        $error_insert = false; // Renombrado para claridad, aunque es update

        // --- Procesamiento del Formulario Enviado (POST) ---
        if (isset($_POST['tit'])) { // Usamos 'tit' como indicador de que el form se envió

            $datos_ad = array(); // Array para guardar los datos a actualizar

            // --- Campos Existentes ---
            $datos_ad['ID_cat'] = $_POST['category'];

            // Obtener parent_cat (asegúrate que selectSQL funciona correctamente)
            $cat = selectSQL('sc_category', $w = array('ID_cat' => $_POST['category']));
            if ($cat && isset($cat[0]['parent_cat'])) {
                $datos_ad['parent_cat'] = $cat[0]['parent_cat'];
            } else {
                // Intentar obtenerlo del anuncio existente si falla la consulta
                $datos_ad['parent_cat'] = $ad['ad']['parent_cat'] ?? 0;
            }

            $datos_ad['ID_region'] = $_POST['region'];
            $datos_ad['location'] = $_POST['city']; // El campo se llama 'city' pero guarda la localización como texto
            $datos_ad['ad_type'] = $_POST['ad_type'];
            $datos_ad['title'] = trim($_POST['tit']); // Quitar espacios extra
            $datos_ad['title_seo'] = toAscii($datos_ad['title']);
            $datos_ad['texto'] = htmlspecialchars(trim($_POST['text']), ENT_QUOTES, 'UTF-8'); // Limpiar y asegurar codificación

            // Campos específicos de categoría (mantener por si se usan en algún lado)
            $datos_ad['mileage'] = isset($_POST['km_car']) ? $_POST['km_car'] : null;
            $datos_ad['fuel'] = isset($_POST['fuel_car']) ? $_POST['fuel_car'] : null;
            $datos_ad['date_car'] = isset($_POST['date_car']) ? $_POST['date_car'] : null;
            $datos_ad['area'] = isset($_POST['area']) ? $_POST['area'] : null;
            $datos_ad['room'] = isset($_POST['room']) ? $_POST['room'] : null;
            $datos_ad['broom'] = isset($_POST['bathroom']) ? $_POST['bathroom'] : null; // 'bathroom' en el form, 'broom' en BD? verificar

            // Contacto
            $datos_ad['name'] = isset($_POST['name']) ? formatName($_POST['name']) : '';
            $datos_ad['phone'] = isset($_POST['phone']) ? trim($_POST['phone']) : '';
            $datos_ad['whatsapp'] = isset($_POST['whatsapp']) ? 1 : 0;
            $datos_ad['seller_type'] = isset($_POST['seller_type']) ? $_POST['seller_type'] : 0;

            // Precio (mantener, podría ser usado) - Asegurarse que sea numérico
            $datos_ad['price'] = isset($_POST['precio']) && is_numeric($_POST['precio']) ? $_POST['precio'] : 0;

            // --- NUEVOS CAMPOS (basado en newForm.php) ---

            // 1. Salidas a domicilio/hotel (out)
            $datos_ad['out'] = isset($_POST['out']) ? (int)$_POST['out'] : 0; // Asume 0 o 1

            // 2. Servicios (servicios) - Guardar como JSON
            $lista_servicios = [];
            if (isset($_POST['servicios']) && is_array($_POST['servicios'])) {
                // Filtra valores vacíos que podrían venir de checkboxes no marcados si se envían incorrectamente
                $lista_servicios = array_filter($_POST['servicios'], 'strlen');
            }
            // Siempre guardar un JSON válido, incluso si está vacío
            $datos_ad['servicios'] = json_encode(array_values($lista_servicios)); // array_values para asegurar array simple

            // 3. Horario Detallado (horario) - Guardar como JSON
            $horario_completo = [];
            $dias_semana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
            if (isset($_POST['horario_dia']) && is_array($_POST['horario_dia'])) {
                $formato_hora = '/^([01]\d|2[0-3]):([0-5]\d)$/'; // HH:MM
                foreach ($dias_semana as $dia) {
                    // Verifica si existe la entrada para ese día en el POST
                    if (isset($_POST['horario_dia'][$dia])) {
                        $dia_data = $_POST['horario_dia'][$dia];
                        $activo = isset($dia_data['activo']) && $dia_data['activo'] == '1';
                        // Usar horas por defecto si no vienen o son inválidas
                        $inicio = (isset($dia_data['inicio']) && preg_match($formato_hora, $dia_data['inicio'])) ? $dia_data['inicio'] : '09:00';
                        $fin = (isset($dia_data['fin']) && preg_match($formato_hora, $dia_data['fin'])) ? $dia_data['fin'] : '18:00';

                        $horario_completo[$dia] = [
                            'activo' => $activo ? 1 : 0,
                            'inicio' => $inicio,
                            'fin'    => $fin
                        ];
                    } else {
                        // Si no viene nada para un día, asumir 'no disponible'
                        $horario_completo[$dia] = [
                            'activo' => 0,
                            'inicio' => '09:00', // O la hora por defecto que prefieras
                            'fin'    => '18:00'
                        ];
                    }
                }
            } else {
                // Si no viene el array 'horario_dia', guardar un JSON vacío o estructura por defecto
                // Podrías inicializar todos los días como no activos si prefieres
                foreach ($dias_semana as $dia) {
                    $horario_completo[$dia] = ['activo' => 0, 'inicio' => '09:00', 'fin' => '18:00'];
                }
            }
            $datos_ad['horario'] = json_encode($horario_completo);


            // 4. Idiomas (idiomas) - Guardar como JSON
            $lista_idiomas = [];
            $i = 1;
            // Asume que los campos se llaman idioma_1, nivel_idioma_1, idioma_2, nivel_idioma_2, etc.
            while (isset($_POST['idioma_' . $i])) {
                $idioma_code = trim($_POST['idioma_' . $i]);
                // Solo añadir si se seleccionó un idioma
                if (!empty($idioma_code)) {
                    // Nivel por defecto si no se selecciona
                    $nivel = isset($_POST['nivel_idioma_' . $i]) && !empty($_POST['nivel_idioma_' . $i]) ? trim($_POST['nivel_idioma_' . $i]) : 'basico';
                    $lista_idiomas[] = ['idioma' => $idioma_code, 'nivel' => $nivel];
                }
                $i++;
                // Poner un límite por si acaso, ej. máximo 5 idiomas
                if ($i > 5) break;
            }
            $datos_ad['idiomas'] = json_encode($lista_idiomas); // Guardar siempre JSON válido


            // --- Rotación de Imágenes (ANTES de actualizar BD) ---
            if (isset($_POST['photo_name']) && is_array($_POST['photo_name']) && isset($_POST['optImgage']) && is_array($_POST['optImgage'])) {
                foreach ($_POST['photo_name'] as $photo_index => $name) {
                    // Asegurarse que el índice de rotación existe para esta foto
                    if (isset($_POST['optImgage'][$photo_index]['rotation'])) {
                        $rotation_value = (int)$_POST['optImgage'][$photo_index]['rotation'];
                        // Solo rotar si el valor no es 0 o si la función maneja 0 como 'no rotar'
                        if ($rotation_value != 0) {
                            // Construir path absoluto de forma segura
                            $imagePath = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . ltrim(IMG_ADS, '/') . $name;
                            if (file_exists($imagePath)) {
                                Images::rotateImage($imagePath, $rotation_value); // Asume que rotateImage usa el path completo
                            }
                        }
                    }
                }
            }

            // --- Validación (simplificada, puedes añadir más checks si es necesario) ---
            // Añadir checks para campos obligatorios nuevos si aplica (ej: 'out')
            $validation_ok = (
                !empty($datos_ad['ID_cat']) && $datos_ad['ID_cat'] != 0 &&
                !empty($datos_ad['ID_region']) && $datos_ad['ID_region'] != 0 &&
                //!empty($datos_ad['location']) && // Ciudad no es obligatoria?
                !empty($datos_ad['ad_type']) && $datos_ad['ad_type'] != 0 &&
                !empty($datos_ad['seller_type']) && $datos_ad['seller_type'] != 0 &&
                !empty($datos_ad['title']) && // strlen($datos_ad['title']) > 9 && // Ajusta longitud mínima si es necesario
                !empty($datos_ad['texto']) && // strlen($datos_ad['texto']) > 9 && // Ajusta longitud mínima
                isset($datos_ad['price']) && is_numeric($datos_ad['price']) && // Precio debe ser numérico (0 es válido)
                isset($datos_ad['out']) && ($datos_ad['out'] == 0 || $datos_ad['out'] == 1) && // Out debe ser 0 o 1
                !empty($datos_ad['name']) &&
                !empty($datos_ad['phone'])
                // Añadir más validaciones aquí si hacen falta
            );

            if ($validation_ok) {

                // --- Actualizar Anuncio en BD ---
                $update = updateSQL("sc_ad", $datos_ad, $w = array('ID_ad' => $ad['ad']['ID_ad']));

                if ($update) {
                    // --- Procesamiento de Imágenes (Después de actualizar el anuncio principal) ---

                    // 1. Nuevas Imágenes Subidas (si existen)
                    if (isset($_FILES['userImage']) && !empty($_FILES['userImage']['name'][0])) { // Check si se subió algo
                        $tot = count($_FILES['userImage']['name']);
                        for ($i = 0; $i < $tot; $i++) {
                            // Verificar si hubo error en la subida de este archivo específico
                            if ($_FILES['userImage']['error'][$i] == UPLOAD_ERR_OK) {
                                // Pasar el índice $i a uploadImage
                                $resultado = uploadImage($_FILES['userImage'], IMG_ADS, $i, true);
                                if ($resultado !== false) {
                                    // Construir path absoluto seguro para la compresión
                                    $imagePath = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . ltrim(IMG_ADS, '/') . $resultado;
                                    if (file_exists($imagePath)) {
                                        compressImage($imagePath); // Comprimir inmediatamente
                                    }
                                    // Insertar en BD la nueva imagen asociada al anuncio
                                    // Determinar la posición (después de las existentes)
                                    $current_images_count = selectSQL("sc_images", ['ID_ad' => $ad['ad']['ID_ad']], '', 'COUNT(*) as count')[0]['count'] ?? 0;
                                    insertSQL("sc_images", $data = array(
                                        'ID_ad' => $ad['ad']['ID_ad'],
                                        'name_image' => $resultado,
                                        'date_upload' => time(),
                                        'position' => $current_images_count + $i, // Posición secuencial
                                        'status' => 1 // Asumir status activo (ajusta si es necesario)
                                    ));
                                }
                            }
                        }
                    }

                    // 2. Actualizar Posición y Comprimir Imágenes Existentes (si se reordenaron/rotaron)
                    if (isset($_POST['photo_name']) && is_array($_POST['photo_name'])) {
                        foreach ($_POST['photo_name'] as $photo_index => $name) {
                            // Actualizar solo la posición de las imágenes existentes
                            updateSQL("sc_images", $data = array('position' => $photo_index), $wa = array('name_image' => $name, 'ID_ad' => $ad['ad']['ID_ad']));

                            // Comprimir imagen existente (por si se rotó o solo por si acaso)
                            $imagePath = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . ltrim(IMG_ADS, '/') . $name;
                            if (file_exists($imagePath)) {
                                compressImage($imagePath);
                            }
                        }
                    }

                    // 3. Eliminar Imágenes Marcadas (asumiendo que el JS pone un status especial o un campo oculto)
                    if (isset($_POST['deleted_images']) && is_array($_POST['deleted_images'])) {
                        foreach ($_POST['deleted_images'] as $image_name_to_delete) {
                            // Buscar el ID de la imagen por nombre y ID de anuncio para seguridad
                            $img_data = selectSQL("sc_images", ['ID_ad' => $ad['ad']['ID_ad'], 'name_image' => $image_name_to_delete]);
                            if ($img_data && count($img_data) > 0) {
                                Images::deleteImage($img_data[0]['ID_image']); // Asume que deleteImage borra de BD y archivo
                            }
                        }
                    }
                    // Alternativa si se usa status: Comentar lo anterior y descomentar/adaptar esto:
                    /*
                     $images_check = selectSQL("sc_images", $w = array('ID_ad' => $ad['ad']['ID_ad']));
                     foreach ($images_check as $img) {
                         // Si tu JS actualiza un campo 'status' en el POST para cada imagen
                         // if (isset($_POST['image_status'][$img['name_image']]) && $_POST['image_status'][$img['name_image']] == ImageStatus::Delete) { // Ajusta ImageStatus::Delete
                         //     Images::deleteImage($img['ID_image']);
                         // }
                     }
                     */

                    $edited = true; // Marcar como editado exitosamente

                    // Recargar los datos del anuncio actualizados para mostrar el mensaje
                    $ad = getDataAd($check[0]['ID_ad'], false);
                    // $ad = parseChanges($ad); // Descomentar si esta función es necesaria

                } else {
                    $error_insert = true; // Hubo un error al actualizar en BD
                    // Considera guardar el error de la BD si tu función lo permite: $db_error = mysqli_error($Connection);
                }
            } else {
                // La validación falló
                $error_insert = true;
                // Puedes añadir mensajes de error específicos a sesión si lo necesitas
                // $_SESSION['edit_error_message'] = "Faltan campos obligatorios o son inválidos.";
            }
        } // Fin del procesamiento POST

        // --- Cargar Datos del Anuncio para Mostrar el Formulario ---
        // (Se cargó al principio, pero lo volvemos a cargar si hubo edición exitosa)
        if (!$edited && !$error_insert) { // Si no se procesó POST o hubo error, recargar por si acaso
            $ad = getDataAd($check[0]['ID_ad'], false);
            // $ad = parseChanges($ad); // Descomentar si esta función es necesaria
        }
        // Decodificar campos JSON para usarlos en el formulario
        $ad_servicios = isset($ad['ad']['servicios']) ? json_decode($ad['ad']['servicios'], true) : [];
        if (!is_array($ad_servicios)) $ad_servicios = []; // Asegurar que sea array

        $ad_horario = isset($ad['ad']['horario']) ? json_decode($ad['ad']['horario'], true) : [];
        if (!is_array($ad_horario)) $ad_horario = []; // Asegurar que sea array

        $ad_idiomas = isset($ad['ad']['idiomas']) ? json_decode($ad['ad']['idiomas'], true) : [];
        if (!is_array($ad_idiomas)) $ad_idiomas = []; // Asegurar que sea array


?>

        <div class="col_single formularioEditNew">
            <h2><?= $language['edit.title_h1'] ?? 'Editar Anuncio' ?></h2>

            <?php if ($edited): ?>
                <?php /* <script> window.close(); </script> // Comentado, quizá prefieras no cerrar la ventana */ ?>
                <div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i> Anuncio modificado correctamente!</div>
                <div class="text-center">
                    <a class="btn btn-primary" href="javascript:void(0);" onclick="window.opener?.location?.reload(); window.close();"><i class="fa fa-check" aria-hidden="true"></i> Cerrar y Recargar Panel</a>
                    <?php /* O un enlace para volver al panel principal si no es una ventana emergente */ ?>
                    <?php /* <a class="btn btn-secondary" href="/admin/dashboard.php"><i class="fa fa-arrow-left"></i> Volver al Panel</a> */ ?>
                </div>
            <?php else: ?>
                <?php if ($error_insert): ?>
                    <div class="error_msg" style="display: block; border: 1px solid red; padding: 10px; margin-bottom: 15px; background-color: #ffebeb;">
                        <strong>Error:</strong> No se pudo guardar el anuncio. Revisa los campos marcados.
                        <?php // Si tienes un mensaje de error más específico: echo htmlspecialchars($_SESSION['edit_error_message']); 
                        ?>
                    </div>
                <?php endif; ?>

                <form id="new_item_post" class="fm" method="post" action="<?= htmlspecialchars($_SERVER['REQUEST_URI']); // Usa REQUEST_URI para mantener el ?a=ID 
                                                                            ?>" enctype="multipart/form-data">
                    <fieldset>
                        <legend>Información Básica</legend>
                        <div class="row">
                            <div class="col_lft"><label for="category"><?= $language['post.label_category'] ?? 'Categoría' ?> *</label></div>
                            <div class="col_rgt"><select name="category" id="category" required> <?php // Añadido required 
                                                                                                    ?>
                                    <option value=""><?= $language['post.select_category'] ?? 'Selecciona categoría...' ?></option>
                                    <?php
                                    // Lógica para cargar categorías (asumiendo que funciona)
                                    $parent_categories = selectSQL("sc_category", $where = array('parent_cat' => -1), "ord ASC");
                                    foreach ($parent_categories as $parent_cat) {
                                        $child_categories = selectSQL("sc_category", $where = array('parent_cat' => $parent_cat['ID_cat']), "name ASC");
                                        // Crear un optgroup para la categoría padre
                                        echo '<optgroup label="' . htmlspecialchars($parent_cat['name']) . '">';
                                        if (count($child_categories) > 0) {
                                            foreach ($child_categories as $child) {
                                                // Usar ID_cat del hijo como valor
                                                $selected = ($child['ID_cat'] == $ad['ad']['ID_cat']) ? ' selected' : '';
                                                echo '<option value="' . $child['ID_cat'] . '"' . $selected . '>' . htmlspecialchars($child['name']) . '</option>';
                                            }
                                        } else {
                                            // Si no hay hijos, la categoría padre es seleccionable (si tu lógica lo permite)
                                            // $selected = ($parent_cat['ID_cat'] == $ad['ad']['ID_cat']) ? ' selected' : '';
                                            // echo '<option value="' . $parent_cat['ID_cat'] . '"' . $selected . '>'. htmlspecialchars($parent_cat['name']) . '</option>';
                                            // O mostrar un mensaje o deshabilitar el optgroup si las padres no son seleccionables
                                            echo '<option value="" disabled>-- No hay subcategorías --</option>';
                                        }
                                        echo '</optgroup>';
                                    }
                                    ?>
                                </select>
                                <div class="error_msg" id="error_category"><?= $language['post.error_category'] ?? 'Selecciona una categoría válida' ?></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col_lft">
                                <label for="region"><?= $language['edit.label_region'] ?? 'Provincia' ?> *</label>
                            </div>
                            <div class="col_rgt">
                                <select name="region" size="1" id="region" required> <?php // Añadido required 
                                                                                        ?>
                                    <option value=""><?= $language['edit.select_region'] ?? 'Selecciona provincia...' ?></option>
                                    <?php
                                    $regions = selectSQL("sc_region", $arr = array(), "name ASC");
                                    foreach ($regions as $region) {
                                        $selected = ($region['ID_region'] == $ad['ad']['ID_region']) ? ' selected' : '';
                                    ?>
                                        <option value="<?= htmlspecialchars($region['ID_region']); ?>" <?= $selected ?>>
                                            <?= htmlspecialchars($region['name']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <div class="error_msg" id="error_region"><?= $language['edit.error_region'] ?? 'Selecciona una provincia' ?></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col_lft">
                                <label for="city"><?= $language['edit.label_city'] ?? 'Ciudad/Zona' ?> </label>
                            </div>
                            <div class="col_rgt">
                                <input name="city" type="text" id="city" maxlength="250" value="<?= htmlspecialchars(stripslashes($ad['ad']['location'] ?? '')); ?>" placeholder="Ej: Centro, Barrio Gótico" />
                                <div class="error_msg" id="error_city"><?= $language['edit.error_city'] ?? 'Indica la ciudad o zona' ?></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col_lft">
                                <label for="ad_type"><?= $language['edit.label_ad_type'] ?? 'Tipo Anuncio' ?> *</label>
                            </div>
                            <div class="col_rgt">
                                <select name="ad_type" size="1" id="ad_type" required> <?php // Añadido required 
                                                                                        ?>
                                    <option value="" <?= ($ad['ad']['ad_type'] ?? 0) == 0 ? 'selected' : '' ?>><?= $language['edit.select_ad_type'] ?? 'Selecciona tipo...' ?></option>
                                    <option value="1" <?= ($ad['ad']['ad_type'] ?? 0) == 1 ? 'selected' : '' ?>><?= $language['edit.ad_type_option_1'] ?? 'Oferta' ?></option>
                                    <option value="2" <?= ($ad['ad']['ad_type'] ?? 0) == 2 ? 'selected' : '' ?>><?= $language['edit.ad_type_option_2'] ?? 'Demanda' ?></option>
                                </select>
                                <div class="error_msg" id="error_ad_type"><?= $language['edit.error_ad_type'] ?? 'Selecciona el tipo de anuncio' ?></div>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Detalles del Anuncio</legend>
                        <div class="row">
                            <div class="col_lft"><label for="tit"><?= $language['edit.label_title'] ?? 'Título' ?> *</label></div>
                            <div class="col_rgt">
                                <input name="tit" type="text" id="tit" value="<?= htmlspecialchars(stripslashes($ad['ad']['title'] ?? '')); ?>" required minlength="10" maxlength="50" /> <?php // Añadido required y min/max length 
                                                                                                                                                                                            ?>
                                <div class="input_count">Caracteres <span id="nro-car-tit"><?= strlen($ad['ad']['title'] ?? '') ?></span> (min 10/máx 50)</div>
                                <div class="error_msg" id="error_tit"><?= $language['edit.error_title'] ?? 'Título inválido (10-50 caracteres)' ?></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col_lft"><label for="text"><?= $language['edit.label_description'] ?? 'Descripción' ?> *</label></div>
                            <div class="col_rgt">
                                <textarea name="text" rows="8" id="text" maxlength="1200" required minlength="30"><?= htmlspecialchars(stripslashes($ad['ad']['texto'] ?? '')); ?></textarea> <?php // Aumentado rows, añadido required y minlength 
                                                                                                                                                                                                ?>
                                <div class="input_count">Caracteres <span id="nro-car-text"><?= strlen($ad['ad']['texto'] ?? '') ?></span> (min 30/máx 1200)</div> <?php // Ajustado min/max 
                                                                                                                                                                    ?>
                                <div class="error_msg" id="error_text"><?= $language['edit.error_description'] ?? 'Descripción inválida (30-1200 caracteres)' ?></div>
                            </div>
                        </div>

                        <?php // --- Sección Servicios --- 
                        ?>
                        <div class="row">
                            <div class="col_lft"><label>Servicios Ofrecidos</label></div>
                            <div class="col_rgt">
                                <div class="grupo-checkboxes" id="servicios-container">
                                    <?php
                                    // Lista de servicios posibles (debería venir de la BD o config)
                                    $possible_services = ["Masaje relajante", "Masaje deportivo", "Masaje podal", "Masaje antiestrés", "Masaje linfático", "Masaje shiatsu", "Masaje descontracturante", "Masaje ayurvédico", "Masaje circulatorio", "Masaje tailandés"]; // Ejemplo
                                    foreach ($possible_services as $service_name) {
                                        $service_value = strtolower(str_replace(' ', '_', $service_name)); // Generar valor consistente
                                        $checked = (in_array($service_value, $ad_servicios)) ? 'checked' : '';
                                        echo '<label class="frm-checkbox">';
                                        echo '<input type="checkbox" name="servicios[]" value="' . htmlspecialchars($service_value) . '" ' . $checked . '> ';
                                        echo htmlspecialchars($service_name);
                                        echo '</label>';
                                    }
                                    ?>
                                </div>
                                <div class="error-msg oculto" id="error-servicios">Selecciona al menos un servicio si aplica.</div>
                            </div>
                        </div>

                        <?php // --- Campos Extra Condicionales (Mantener si siguen siendo relevantes) --- 
                        ?>
                        <?php
                        // Verificar si $ad['category'] existe antes de acceder a sus índices
                        $show_car_fields = isset($ad['category']['field_0']) && $ad['category']['field_0'] == 1;
                        $show_realestate_fields_1 = isset($ad['category']['field_3']) && $ad['category']['field_3'] == 1;
                        $show_realestate_fields_2 = isset($ad['category']['field_2']) && $ad['category']['field_2'] == 1;
                        ?>
                        <?php if ($show_car_fields || $show_realestate_fields_1 || $show_realestate_fields_2): ?>
                            <div id="extra_fields"> <?php // No ocultar por defecto, mostrar si aplica 
                                                    ?>
                                <hr>
                                <p><strong>Campos Adicionales (Categoría):</strong></p>
                                <?php if ($show_car_fields): ?>
                                    <div class="row">
                                        <div class="col_lft"><label for="km_car">Kilómetros</label></div>
                                        <div class="col_rgt"><input name="km_car" type="tel" id="km_car" size="10" maxlength="10" value="<?= htmlspecialchars($ad['ad']['mileage'] ?? '') ?>" /></div>
                                    </div>
                                    <div class="row">
                                        <div class="col_lft"><label for="date_car">Año</label></div>
                                        <div class="col_rgt"><select name="date_car" id="date_car">
                                                <option value="">Año</option>
                                                <?php for ($i = date("Y"); $i > 1970; $i--) {
                                                    $selected = ($i == ($ad['ad']['date_car'] ?? '')) ? 'selected' : ''; ?>
                                                    <option value="<?= $i ?>" <?= $selected ?>><?= $i ?></option>
                                                <?php } ?>
                                            </select></div>
                                    </div>
                                    <div class="row">
                                        <div class="col_lft"><label for="fuel_car">Combustible</label></div>
                                        <div class="col_rgt"><select name="fuel_car" id="fuel_car">
                                                <option value="">Combustible</option>
                                                <?php
                                                $type_fuel = selectSQL("sc_type_fuel", $w = array(), 'ID_fuel ASC');
                                                if ($type_fuel) {
                                                    foreach ($type_fuel as $fuel) {
                                                        $selected = (($ad['ad']['fuel'] ?? '') == $fuel['ID_fuel']) ? 'selected' : ''; ?>
                                                        <option value="<?= $fuel['ID_fuel'] ?>" <?= $selected ?>><?= htmlspecialchars($fuel['name']) ?></option>
                                                <?php }
                                                } ?>
                                            </select></div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($show_realestate_fields_1): ?>
                                    <div class="row">
                                        <div class="col_lft"><label for="room">Habitaciones</label></div>
                                        <div class="col_rgt"><input type="tel" id="room" name="room" maxlength="4" value="<?= htmlspecialchars($ad['ad']['room'] ?? '') ?>"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col_lft"><label for="bathroom">Baños</label></div>
                                        <div class="col_rgt"><input type="tel" id="bathroom" name="bathroom" maxlength="4" value="<?= htmlspecialchars($ad['ad']['broom'] ?? '') ?>"></div> <?php // Usar 'bathroom' consistentemente 
                                                                                                                                                                                            ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($show_realestate_fields_2): ?>
                                    <div class="row">
                                        <div class="col_lft"><label for="area">Superficie (m<sup>2</sup>)</label></div>
                                        <div class="col_rgt"><input type="tel" id="area" name="area" maxlength="10" value="<?= htmlspecialchars($ad['ad']['area'] ?? '') ?>"><span class="decimal_price">.00 <b>m<sup>2</sup></b></span></div>
                                    </div>
                                <?php endif; ?>
                                <hr>
                            </div>
                        <?php endif; ?>


                        <div class="row"> <?php // Mantener precio si sigue siendo relevante 
                                            ?>
                            <div class="col_lft"><label for="precio"><?= $language['edit.label_price'] ?? 'Precio' ?></label></div>
                            <div class="col_rgt"><input class="numeric" name="precio" type="number" id="precio" size="8" maxlength="9" value="<?= htmlspecialchars($ad['ad']['price'] ?? '0'); ?>" step="0.01" min="0" /><span class="decimal_price"><b><?= COUNTRY_CURRENCY_CODE ?? 'EUR'; ?></b></span> <?php // Añadido tipo number y min=0 
                                                                                                                                                                                                                                                                                                            ?>
                                <div class="error_msg" id="error_price">Indica un precio numérico válido (0 si es gratis o a consultar)</div>
                            </div>
                        </div>

                        <?php // --- Sección Horario Detallado --- 
                        ?>
                        <div class="row">
                            <div class="col_lft"><label>Horario Semanal</label></div>
                            <div class="col_rgt">
                                <div class="horario-semanal-editor" id="horario-container">
                                    <?php
                                    $dias_es = ['lunes' => 'Lunes', 'martes' => 'Martes', 'miercoles' => 'Miércoles', 'jueves' => 'Jueves', 'viernes' => 'Viernes', 'sabado' => 'Sábado', 'domingo' => 'Domingo'];
                                    foreach ($dias_es as $key => $nombre_dia) {
                                        $dia_actual_data = $ad_horario[$key] ?? ['activo' => 0, 'inicio' => '09:00', 'fin' => '18:00'];
                                        $activo = $dia_actual_data['activo'] == 1;
                                        $inicio = $dia_actual_data['inicio'];
                                        $fin = $dia_actual_data['fin'];
                                    ?>
                                        <div class="dia-horario-edit" style="margin-bottom: 10px; padding: 5px; border: 1px solid #eee;">
                                            <label style="display:inline-block; width: 100px;">
                                                <input type="checkbox" name="horario_dia[<?= $key ?>][activo]" value="1" <?= $activo ? 'checked' : '' ?>>
                                                <strong><?= $nombre_dia ?></strong>
                                            </label>
                                            <span class="horas-dia-edit" style="margin-left: 10px; <?= $activo ? '' : 'opacity:0.5;' // Atenuar si no está activo 
                                                                                                    ?>">
                                                De: <select name="horario_dia[<?= $key ?>][inicio]" class="frm-select corto" <?= $activo ? '' : 'disabled' ?>>
                                                    <?php for ($h = 0; $h < 24; $h++) {
                                                        $hora00 = sprintf('%02d:00', $h);
                                                        $sel00 = ($hora00 == $inicio) ? 'selected' : '';
                                                        $hora30 = sprintf('%02d:30', $h);
                                                        $sel30 = ($hora30 == $inicio) ? 'selected' : '';
                                                        echo "<option value='$hora00' $sel00>$hora00</option><option value='$hora30' $sel30>$hora30</option>";
                                                    } ?>
                                                </select>
                                                A: <select name="horario_dia[<?= $key ?>][fin]" class="frm-select corto" <?= $activo ? '' : 'disabled' ?>>
                                                    <?php for ($h = 0; $h < 24; $h++) {
                                                        $hora00 = sprintf('%02d:00', $h);
                                                        $sel00 = ($hora00 == $fin) ? 'selected' : '';
                                                        $hora30 = sprintf('%02d:30', $h);
                                                        $sel30 = ($hora30 == $fin) ? 'selected' : '';
                                                        echo "<option value='$hora00' $sel00>$hora00</option><option value='$hora30' $sel30>$hora30</option>";
                                                    } ?>
                                                </select>
                                            </span>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="error-msg oculto" id="error-horario">Configura el horario para los días disponibles.</div>
                                <script>
                                    // Script simple para habilitar/deshabilitar selects de hora al marcar/desmarcar el día
                                    document.addEventListener('DOMContentLoaded', function() {
                                        document.querySelectorAll('.dia-horario-edit input[type="checkbox"]').forEach(function(checkbox) {
                                            checkbox.addEventListener('change', function() {
                                                var selects = this.closest('.dia-horario-edit').querySelectorAll('select');
                                                var spanHoras = this.closest('.dia-horario-edit').querySelector('.horas-dia-edit');
                                                if (this.checked) {
                                                    selects.forEach(function(select) {
                                                        select.disabled = false;
                                                    });
                                                    spanHoras.style.opacity = '1';
                                                } else {
                                                    selects.forEach(function(select) {
                                                        select.disabled = true;
                                                    });
                                                    spanHoras.style.opacity = '0.5';
                                                }
                                            });
                                            // Disparar el evento al cargar para establecer estado inicial
                                            checkbox.dispatchEvent(new Event('change'));
                                        });
                                    });
                                </script>
                            </div>
                        </div>

                        <?php // --- Sección Idiomas --- 
                        ?>
                        <div class="row">
                            <div class="col_lft"><label>Idiomas Hablados</label></div>
                            <div class="col_rgt">
                                <div id="idiomas-container">
                                    <?php
                                    // Cargar lista completa de idiomas (ejemplo, obtener de BD/config)
                                    $possible_languages = ['es' => 'Español', 'en' => 'Inglés', 'fr' => 'Francés', 'de' => 'Alemán', 'pt' => 'Portugués', 'it' => 'Italiano', 'ru' => 'Ruso', 'zh' => 'Chino'];
                                    $levels = ['basico' => 'Básico', 'intermedio' => 'Intermedio', 'avanzado' => 'Avanzado', 'nativo' => 'Nativo'];

                                    // Mostrar campos para idiomas guardados + 1 vacío
                                    $idioma_count = 0;
                                    foreach ($ad_idiomas as $index => $lang_data) {
                                        $idioma_count++;
                                        echo '<div class="par-idioma" style="margin-bottom: 5px;">';
                                        echo '<select name="idioma_' . $idioma_count . '" class="frm-campo frm-select" style="width: auto; margin-right: 5px;">';
                                        echo '<option value="">-- Idioma ' . $idioma_count . ' --</option>';
                                        foreach ($possible_languages as $code => $name) {
                                            $selected = ($lang_data['idioma'] == $code) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($code) . '" ' . $selected . '>' . htmlspecialchars($name) . '</option>';
                                        }
                                        echo '</select>';
                                        echo '<select name="nivel_idioma_' . $idioma_count . '" class="frm-campo frm-select" style="width: auto;">';
                                        echo '<option value="">-- Nivel --</option>';
                                        foreach ($levels as $level_code => $level_name) {
                                            $selected = (isset($lang_data['nivel']) && $lang_data['nivel'] == $level_code) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($level_code) . '" ' . $selected . '>' . htmlspecialchars($level_name) . '</option>';
                                        }
                                        echo '</select>';
                                        echo '</div>';
                                    }

                                    // Añadir un par de campos vacíos para agregar más (hasta un límite)
                                    $max_langs = 3; // Define cuántos idiomas puede añadir como máximo
                                    for ($i = $idioma_count + 1; $i <= $max_langs; $i++) {
                                        echo '<div class="par-idioma" style="margin-bottom: 5px;">';
                                        echo '<select name="idioma_' . $i . '" class="frm-campo frm-select" style="width: auto; margin-right: 5px;">';
                                        echo '<option value="">-- Idioma ' . $i . ' (Opcional) --</option>';
                                        foreach ($possible_languages as $code => $name) {
                                            echo '<option value="' . htmlspecialchars($code) . '">' . htmlspecialchars($name) . '</option>';
                                        }
                                        echo '</select>';
                                        echo '<select name="nivel_idioma_' . $i . '" class="frm-campo frm-select" style="width: auto;">';
                                        echo '<option value="">-- Nivel --</option>';
                                        foreach ($levels as $level_code => $level_name) {
                                            echo '<option value="' . htmlspecialchars($level_code) . '">' . htmlspecialchars($level_name) . '</option>';
                                        }
                                        echo '</select>';
                                        echo '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <?php // --- Sección Salidas a Domicilio --- 
                        ?>
                        <div class="row">
                            <div class="col_lft"><label for="realiza_salidas">¿Realiza salidas? *</label></div>
                            <div class="col_rgt">
                                <select name="out" id="realiza_salidas" class="frm-campo frm-select" required>
                                    <?php $selected_out = $ad['ad']['out'] ?? '0'; ?>
                                    <option value="0" <?= ($selected_out == 0) ? 'selected' : ''; ?>>No</option>
                                    <option value="1" <?= ($selected_out == 1) ? 'selected' : ''; ?>>Sí, a domicilio/hotel</option>
                                </select>
                                <div class="error_msg oculto" id="error_out">Debes indicar si realizas salidas.</div>
                            </div>
                        </div>


                    </fieldset>
                    <fieldset>
                        <legend>Fotos del Anuncio</legend>
                        <div class="title_photos_list" id="title_photos_list">¡Sube o modifica las fotos de tu anuncio! (Máx: <?= $DATAJSON['max_photos'] ?>)</div>
                        <div class="error_msg" id="error_photo" style="display:none;">Sube al menos una foto.</div>
                        <div class="photos_list sortable">
                            <?php
                            // Imágenes Actuales
                            $current_photos = isset($ad['images']) && is_array($ad['images']) ? count($ad['images']) : 0;
                            // Ordenar imágenes por posición si existe el campo
                            if ($current_photos > 0 && isset($ad['images'][0]['position'])) {
                                usort($ad['images'], function ($a, $b) {
                                    return ($a['position'] ?? 99) <=> ($b['position'] ?? 99);
                                });
                            }
                            $max_photos = $DATAJSON['max_photos'];
                            $upload_slots = $max_photos - $current_photos;
                            $photo_id_counter = 1; // Para IDs únicos en JS

                            // Mostrar imágenes existentes
                            if ($current_photos > 0) {
                                foreach ($ad['images'] as $img) {
                                    $image_url = getConfParam('SITE_URL') . ltrim(IMG_ADS, '/') . $img['name_image'];
                            ?>
                                    <div class="photo_box" data-photo-id="<?= $photo_id_counter ?>">
                                        <div id="photo_container-<?= $photo_id_counter; ?>" class="photo_list">
                                            <?php // Botón para marcar para eliminar (requiere JS) 
                                            ?>
                                            <div class="removeImg" title="Marcar para eliminar" onclick="markImageForDeletion(this, '<?= htmlspecialchars($img['name_image']) ?>')"><i class="fa fa-times" aria-hidden="true"></i></div>
                                            <?php /* <a href="javascript:void(0);" class="edit-photo-icon" onclick="editImage(<?= $photo_id_counter ?>)">...</a> // Si tienes editor */ ?>
                                            <span class="helper"></span>
                                            <img class="<?= getImgOrientation($img['name_image']) ?>" src="<?= $image_url ?>" alt="Foto <?= $photo_id_counter ?>" />
                                            <input type="hidden" name="photo_name[]" value="<?= htmlspecialchars($img['name_image']); ?>"> <?php // Nombre de imagen existente 
                                                                                                                                            ?>
                                            <input type="hidden" name="optImgage[<?= $photo_id_counter - 1 ?>][rotation]" id="rotation-<?= $photo_id_counter; ?>" value="0"> <?php // Rotación para esta imagen 
                                                                                                                                                                                ?>
                                        </div>
                                        <div class="photos_options">
                                            <?php /* Botones de rotación, etc. (si tu JS los maneja) */ ?>
                                            <a href="javascript:void(0);" onclick="rotateRight(<?= $photo_id_counter ?>)" title="Rotar Derecha">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="20px" viewBox="0 -960 960 960" width="20px">
                                                    <path d="M522-80v-82q34-5 66.5-18t61.5-34l56 58q-42 32-88 51.5T522-80Zm-80 0Q304-98 213-199.5T122-438q0-75 28.5-140.5t77-114q48.5-48.5 114-77T482-798h6l-62-62 56-58 160 160-160 160-56-56 64-64h-8q-117 0-198.5 81.5T202-438q0 104 68 182.5T442-162v82Zm322-134-58-56q21-29 34-61.5t18-66.5h82q-5 50-24.5 96T764-214Zm76-264h-82q-5-34-18-66.5T706-606l58-56q32 39 51 86t25 98Z" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                <?php
                                    $photo_id_counter++;
                                } // Fin foreach imágenes existentes
                            } // Fin if current_photos > 0

                            // Mostrar slots vacíos para subir nuevas fotos
                            for ($i = 0; $i < $upload_slots; $i++) {
                                $current_slot_id = $photo_id_counter + $i;
                                ?>
                                <div class="photo_box empty_slot" data-photo-id="<?= $current_slot_id ?>">
                                    <div id="photo_container-<?= $current_slot_id; ?>" class="photo_list free">
                                        <input name="userImage[]" id="photo-<?= $current_slot_id; ?>" type="file" class="photoFile" accept="image/jpeg, image/png" onchange="previewImage(this, <?= $current_slot_id ?>)" />
                                        <div class="upload-placeholder">
                                            <i class="fa fa-plus-circle" style="font-size: 24px; color: #ccc;"></i><br>Añadir Foto
                                        </div>
                                        <?php // Placeholder para la preview 
                                        ?>
                                        <img src="#" alt="Previsualización" class="img-preview" style="display:none; max-width: 100%; max-height: 100%; position: absolute; top: 0; left: 0;" />
                                    </div>
                                    <div class="photos_options" style="visibility: hidden;"> <?php // Opciones ocultas para slots vacíos 
                                                                                                ?>
                                    </div>
                                    <?php // No necesitamos input de rotación para slots vacíos inicialmente 
                                    ?>
                                </div>
                            <?php
                            } // Fin for slots vacíos
                            ?>
                            <?php // Input oculto para guardar nombres de imágenes a borrar 
                            ?>
                            <div id="deleted-images-container"></div>
                        </div>
                        <div class="error_msg" id="error_photos"></div>
                        <script>
                            // JS Básico para preview y marcar para borrar (requiere jQuery si usas sortable)

                            function previewImage(input, id) {
                                if (input.files && input.files[0]) {
                                    var reader = new FileReader();
                                    reader.onload = function(e) {
                                        var container = document.getElementById('photo_container-' + id);
                                        var preview = container.querySelector('.img-preview');
                                        var placeholder = container.querySelector('.upload-placeholder');
                                        if (preview) {
                                            preview.src = e.target.result;
                                            preview.style.display = 'block';
                                        }
                                        if (placeholder) placeholder.style.display = 'none';
                                        container.classList.remove('free'); // Ya no está libre
                                    }
                                    reader.readAsDataURL(input.files[0]);
                                }
                            }

                            function markImageForDeletion(button, imageName) {
                                var photoBox = button.closest('.photo_box');
                                if (photoBox.classList.contains('marked-for-deletion')) {
                                    // Desmarcar
                                    photoBox.classList.remove('marked-for-deletion');
                                    photoBox.style.opacity = '1';
                                    // Quitar input oculto de borrado
                                    var hiddenInput = document.getElementById('delete_' + imageName.replace(/\./g, '_')); // Crear ID único
                                    if (hiddenInput) hiddenInput.remove();
                                } else {
                                    // Marcar
                                    photoBox.classList.add('marked-for-deletion');
                                    photoBox.style.opacity = '0.5'; // Indicar visualmente
                                    // Añadir input oculto para enviar al POST
                                    var input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = 'deleted_images[]';
                                    input.value = imageName;
                                    input.id = 'delete_' + imageName.replace(/\./g, '_');
                                    document.getElementById('deleted-images-container').appendChild(input);
                                }
                            }

                            // Rotación (si tu JS lo maneja)
                            function rotateRight(id) {
                                var rotationInput = document.getElementById('rotation-' + id);
                                if (rotationInput) {
                                    var currentRotation = parseInt(rotationInput.value) || 0;
                                    var newRotation = (currentRotation - 90) % 360; // -90 para rotar a la derecha en GD
                                    if (newRotation < 0) newRotation += 360; // Asegurar positivo
                                    rotationInput.value = newRotation;

                                    // Opcional: Rotar visualmente la imagen con CSS (no afecta al servidor)
                                    var imgElement = document.querySelector('#photo_container-' + id + ' img');
                                    if (imgElement) imgElement.style.transform = 'rotate(' + newRotation + 'deg)';

                                    alert('Imagen marcada para rotar ' + newRotation + ' grados al guardar.'); // Feedback visual
                                }
                            }

                            // Inicializar Sortable si usas jQuery UI (ejemplo)
                            if (typeof $ !== 'undefined' && $.ui && $.ui.sortable) {
                                $(document).ready(function() {
                                    $('.sortable').sortable({
                                        placeholder: "ui-state-highlight", // Clase para el placeholder
                                        forcePlaceholderSize: true,
                                        update: function(event, ui) {
                                            // Actualizar los índices de los inputs si es necesario tras reordenar
                                            $('.sortable .photo_box').each(function(index) {
                                                var rotationInput = $(this).find('input[name^="optImgage"]');
                                                if (rotationInput.length) {
                                                    rotationInput.attr('name', 'optImgage[' + index + '][rotation]');
                                                }
                                                // Si necesitas actualizar position en BD dinámicamente, aquí iría una llamada AJAX
                                            });
                                        }
                                    }).disableSelection();
                                });
                            }
                        </script>
                    </fieldset>
                    <fieldset>
                        <legend>Datos de Contacto</legend>
                        <div class="row">
                            <div class="col_lft"><label for="name"><?= $language['post.label_name'] ?? 'Tu Nombre/Alias' ?> *</label></div>
                            <div class="col_rgt"><input name="name" type="text" id="name" size="30" maxlength="50" value="<?= htmlspecialchars($ad['ad']['name'] ?? ''); ?>" required /> <?php // Maxlength aumentado, añadido required 
                                                                                                                                                                                            ?>
                                <div class="error_msg" id="error_name"><?= $language['post.error_name'] ?? 'Indica tu nombre o alias' ?></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col_lft">
                                <label for="sellerType"><?= $language['edit.label_seller_type'] ?? 'Tipo Vendedor' ?> *</label>
                            </div>
                            <div class="col_rgt">
                                <select id="sellerType" name="seller_type" size="1" required> <?php // Añadido required 
                                                                                                ?>
                                    <option value="" <?= ($ad['ad']['seller_type'] ?? 0) == 0 ? 'selected' : '' ?>><?= $language['edit.select_seller_type'] ?? 'Selecciona tipo...' ?></option>
                                    <?php // Asume que los valores 1, 2, 3 corresponden a los tipos definidos 
                                    ?>
                                    <option value="1" <?= ($ad['ad']['seller_type'] ?? 0) == 1 ? 'selected' : '' ?>><?= $language['edit.seller_type_option_1'] ?? 'Particular' ?></option>
                                    <option value="2" <?= ($ad['ad']['seller_type'] ?? 0) == 2 ? 'selected' : '' ?>><?= $language['edit.seller_type_option_2'] ?? 'Profesional/Centro' ?></option>
                                    <?php // Añadir opción 3 si existe (Publicista?)
                                    // <option value="3" <?= ($ad['ad']['seller_type'] ?? 0) == 3 ? 'selected' : '' 
                                    ?>>Publicista</option>
                                    ?>
                                </select>
                                <div class="error_msg" id="error_sellerType"><?= $language['edit.error_seller_type'] ?? 'Selecciona tu tipo de vendedor' ?></div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col_lft"><label for="phone"><?= $language['post.label_phone'] ?? 'Teléfono' ?> *</label></div>
                            <div class="col_rgt">
                                <div class="phone_container">
                                    <?php // Usar tipo tel y pattern para validación básica HTML5 
                                    ?>
                                    <input name="phone" type="tel" id="phone" size="20" maxlength="20" value="<?= htmlspecialchars($ad['ad']['phone'] ?? ''); ?>" required pattern="[0-9\s\+\-]{7,20}" />
                                    <label style="margin-left: 10px;"><input type="checkbox" value="1" name="whatsapp" <?= ($ad['ad']['whatsapp'] ?? 0) == 1 ? 'checked' : '' ?>> Whatsapp </label>
                                </div>
                                <div class="error_msg" id="error_phone"><?= $language['post.error_phone'] ?? 'Introduce un teléfono válido' ?></div>
                            </div>
                        </div>

                        <?php // Email no suele ser editable directamente si está ligado a la cuenta 
                        ?>
                        <div class="row">
                            <div class="col_lft"><label>Email (Cuenta)</label></div>
                            <div class="col_rgt">
                                <input type="email" value="<?= htmlspecialchars($ad['user']['mail'] ?? 'No disponible'); ?>" disabled style="background-color: #eee;" />
                                <small>El email está asociado a la cuenta del usuario.</small>
                            </div>
                        </div>

                    </fieldset>

                    <?php /* Términos y condiciones no suelen mostrarse en la edición */ ?>

                    <div class="row submit-row" style="margin-top: 20px; text-align: center;">
                        <input type="submit" class="button btn btn-success" id="editPub" value="<?= $language['edit.button_update'] ?? 'Guardar Cambios' ?>" />
                        <a href="javascript:void(0);" onclick="window.close();" class="btn btn-secondary" style="margin-left: 15px;">Cancelar</a>
                    </div>
                </form>

            <?php endif // Fin else ($edited) 
            ?>

            <?php // loadBlock("editor"); // ¿Sigue siendo necesario si textarea no usa editor WYSIWYG? Comentar si no. 
            ?>
        </div> <?php // Fin col_single 
                ?>

        <?php // Cargar JS necesario (asegúrate que las rutas sean correctas) 
        ?>
        <?php // Cargar jQuery si Select2 o Sortable lo necesitan 
        ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <?php // Cargar jQuery UI si usas Sortable 
        ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">

        <?php // Cargar Select2 
        ?>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <style>

        </style>

        <?php // Cargar tus JS personalizados (asegúrate que filter.js y post.js no interfieran negativamente) 
        ?>
        <script src="<?= getConfParam('SITE_URL') ?>src/js/filter.js?v=<?= time() // Cache busting 
                                                                        ?>"></script> <?php // ¿Necesario en edit? 
                                                                                        ?>
        <script src="<?= getConfParam('SITE_URL') ?>src/js/post.js?v=<?= time() // Cache busting 
                                                                        ?>"></script> <?php // ¿Necesario en edit? O renombrar a item_form.js? 
                                                                                        ?>

        <script type="text/javascript">
            // Inicializar Select2 y Sortable (Ejemplo usando jQuery)
            $(document).ready(function() {
                // Aplicar Select2 a los selects relevantes
                $('#category, #region, #ad_type, #sellerType, #realiza_salidas').select2({
                    minimumResultsForSearch: 10, // Ocultar búsqueda si hay pocas opciones
                    width: '100%' // Ajustar ancho
                });
                // Selects de hora y año podrían no necesitar Select2 si son simples
                // $('select[name^="horario_dia"], #date_car, #fuel_car').select2({ width: 'style' }); // Ejemplo si quieres aplicarlo

                // Selects de idioma
                $('select[name^="idioma_"], select[name^="nivel_idioma_"]').select2({
                    minimumResultsForSearch: 5,
                    width: 'resolve' // Ajusta ancho automáticamente
                });


                // Inicializar Sortable (si jQuery UI está cargado)
                if ($.ui && $.ui.sortable) {
                    $('.sortable').sortable({
                        placeholder: "ui-state-highlight",
                        forcePlaceholderSize: true,
                        items: '> .photo_box:not(.empty_slot)', // Solo permitir ordenar las imágenes existentes
                        update: function(event, ui) {
                            // Actualizar los índices de los inputs de rotación tras reordenar
                            $('.sortable .photo_box').each(function(index) {
                                var rotationInput = $(this).find('input[id^="rotation-"]');
                                if (rotationInput.length) {
                                    // Necesitamos extraer el ID original del data-attribute para mantener la correspondencia
                                    var originalId = $(this).data('photo-id');
                                    if (originalId) {
                                        rotationInput.attr('name', 'optImgage[' + (originalId - 1) + '][rotation]');
                                        // Actualizar el input de nombre también si su índice depende del orden
                                        // var nameInput = $(this).find('input[name="photo_name[]"]');
                                        // nameInput.attr('name', 'photo_name['+index+']'); // Esto podría no ser necesario si el backend usa el valor
                                    }
                                }
                            });
                        }
                    }).disableSelection();
                }

                // --- Contador de Caracteres ---
                function updateCounter(inputId, counterId, min, max) {
                    var input = document.getElementById(inputId);
                    var counter = document.getElementById(counterId);
                    if (input && counter) {
                        var len = input.value.length;
                        counter.textContent = len;
                        // Opcional: Cambiar color si está fuera de rango
                        if (len < min || len > max) {
                            input.style.borderColor = 'red';
                        } else {
                            input.style.borderColor = ''; // O color original
                        }
                    }
                }

                $('#tit').on('input', function() {
                    updateCounter('tit', 'nro-car-tit', 10, 50);
                });
                $('#text').on('input', function() {
                    updateCounter('text', 'nro-car-text', 30, 1200);
                });
                // Llamar una vez al cargar
                updateCounter('tit', 'nro-car-tit', 10, 50);
                updateCounter('text', 'nro-car-text', 30, 1200);

                // --- Validación simple en submit (opcional, complementa HTML5) ---
                $('#new_item_post').on('submit', function(e) {
                    var isValid = true;
                    $('.error_msg').hide(); // Ocultar errores previos

                    // Check Título
                    var titulo = $('#tit').val();
                    if (titulo.length < 10 || titulo.length > 50) {
                        $('#error_tit').show();
                        isValid = false;
                    }
                    // Check Descripción
                    var desc = $('#text').val();
                    if (desc.length < 30 || desc.length > 1200) {
                        $('#error_text').show();
                        isValid = false;
                    }
                    // Check Teléfono (simple)
                    var telefono = $('#phone').val();
                    if (!/^[0-9\s\+\-]{7,20}$/.test(telefono)) {
                        $('#error_phone').show();
                        isValid = false;
                    }
                    // Check si hay al menos una foto (contando existentes no borradas y nuevas)
                    var existingPhotos = $('.sortable .photo_box:not(.empty_slot):not(.marked-for-deletion)').length;
                    var newPhotos = $('.sortable .photo_box.empty_slot input[type="file"]').filter(function() {
                        return this.files.length > 0;
                    }).length;
                    if (existingPhotos + newPhotos === 0) {
                        $('#error_photo').text('Debes tener al menos una foto en el anuncio.').show();
                        isValid = false;
                    }


                    if (!isValid) {
                        e.preventDefault(); // Detener envío si no es válido
                        alert('Por favor, corrige los errores marcados en el formulario.');
                    }
                });

            }); // Fin document ready
        </script>

        <style>
            /* Estilos básicos para elementos añadidos */
            .grupo-checkboxes label {
                margin-right: 15px;
            }

            .horario-semanal-editor .dia-horario-edit {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }

            .horario-semanal-editor .dia-horario-edit label {
                flex-shrink: 0;
            }

            .horario-semanal-editor .horas-dia-edit {
                display: inline-flex;
                gap: 5px;
                align-items: center;
            }

            .horario-semanal-editor .horas-dia-edit select {
                padding: 2px 5px;
                font-size: 0.9em;
            }

            .idiomas-container .par-idioma {
                margin-bottom: 8px;
            }

            .idiomas-container .par-idioma select {
                margin-right: 5px;
                padding: 4px 6px;
            }

            .photo_box.marked-for-deletion {
                border: 2px dashed red;
            }

            .photo_box .removeImg {
                position: absolute;
                top: 2px;
                right: 2px;
                background: rgba(255, 0, 0, 0.7);
                color: white;
                border-radius: 50%;
                width: 20px;
                height: 20px;
                text-align: center;
                line-height: 20px;
                cursor: pointer;
                z-index: 10;
            }

            .photo_box .removeImg:hover {
                background: red;
            }

            .photo_box.empty_slot .upload-placeholder {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                text-align: center;
                color: #aaa;
                cursor: pointer;
            }

            .photo_box.empty_slot .photoFile {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                opacity: 0;
                cursor: pointer;
                z-index: 5;
            }

            .photo_box .img-preview {
                object-fit: cover;
                width: 100%;
                height: 100%;
            }

            .ui-state-highlight {
                height: 100px;
                width: 100px;
                background-color: #f0f0f0;
                border: 1px dashed #ccc;
                float: left;
                margin: 5px;
            }

            .formularioEditNew .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 26px;
                position: absolute;
                top: 17px;
                right: 1px;
                width: 20px;
            }

            .formularioEditNew span.select2 {
                max-width: 600px;
            }

            /* Placeholder para sortable */
        </style>

<?php
    } else {
        // No se encontró el anuncio con ese ID
        echo "<div class='error_msg'>Error: Anuncio no encontrado o ID inválido.</div>";
        echo "<script>window.history.go(-1);</script>"; // Volver atrás
    }
} else {
    // No se proporcionó 'a' (ID del anuncio) en la URL
    echo "<div class='error_msg'>Error: No se especificó el anuncio a editar.</div>";
    echo "<script>window.history.go(-1);</script>"; // Volver atrás
}
?>