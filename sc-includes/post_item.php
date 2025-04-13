<?php

require '/html/newForm.php';
require '/html/iconos.php';

// Activar TODOS los errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- Inicialización (Código original) ---
user::updateSesion();
$limite = check_item_limit(); // Se recalcula después si el usuario se crea/identifica
$ANUNCIO_NUEVO_PREMIUM = false;
$user_registered = false;
$DATAJSON = array();
$DATAJSON['max_photos'] = getConfParam('MAX_PHOTOS_AD');
$DATAJSON['edit'] = 0;

// Requisito de login (código original)
if (getConfParam('POST_ITEM_REG') == 1) {
    check_login(); // Asegúrate que esta función no haga un exit/redirect inesperado si no está logueado
}


// --- Inicio Procesamiento POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ----- Checkpoint 1: POST Recibido (Ya confirmado, podemos comentarlo) -----
    // echo '<pre>¡AHORA SÍ ESTOY PROCESANDO EL POST EN EL SCRIPT CORRECTO!</pre>';
    // echo '<pre>Datos POST iniciales:</pre>';
    // var_dump($_POST);
    // // exit; // COMENTADO - Ya sabemos que llega aquí

    $en_revision = false;

    // ----- Checkpoint 2: Verificación de Token y Categoría -----
    if (isset($_POST['category']) && !empty($_POST['category'])) { // Añadida comprobación !empty
        echo "<pre>Checkpoint 2: Verificación de Token y Categoría.</pre>";

        $tokenValido = false; // Inicializar
        if (isset($_POST['token'])) {
            // Asume que DEBUG está definido en algún lugar o quítalo si no lo usas
            $tokenValido = verifyFormToken('postAdToken', $_POST['token']) || (defined('DEBUG') && DEBUG);
            echo "<pre>Token Recibido: " . htmlspecialchars($_POST['token']) . "</pre>";
            echo "<pre>¿Token Válido?: " . ($tokenValido ? 'SÍ' : 'NO') . "</pre>";
        } else {
            echo "<pre>Advertencia: No se recibió el campo 'token'.</pre>";
            // Decide si continuar sin token si DEBUG está activo, o fallar.
            // $tokenValido = (defined('DEBUG') && DEBUG); // Ejemplo: Solo permite sin token en DEBUG
        }


        if ($tokenValido) {
            echo "<pre>Checkpoint 2.1: Token VÁLIDO. Continuando...</pre>";

            // >>>>> PON EL EXIT AQUÍ (Intento 1) <<<<<
            // exit;

            // ----- Checkpoint 3: Construcción de $datos_ad -----
            echo "<pre>Checkpoint 3: Construyendo array \$datos_ad...</pre>";
            $datos_ad = array();
            // --- Mapeo de Campos ---
            $datos_ad['ID_cat'] = $_POST['category'];
            // OBTENER parent_cat (requiere acceso a BD)
            // Asumiendo que $Connection está disponible globalmente (desde settings.inc.php?)
            // ¡IMPORTANTE! Asegúrate de que selectSQL maneje errores o usa mysqli directamente
            global $Connection; // O el método que use tu CMS para acceder a la conexión
            $category_ad = selectSQL('sc_category', array('ID_cat' => $datos_ad['ID_cat']));
            if ($category_ad && isset($category_ad[0]['parent_cat'])) {
                $datos_ad['parent_cat'] = $category_ad[0]['parent_cat'];
            } else {
                echo "<pre>ERROR CRÍTICO: No se pudo obtener parent_cat para ID_cat=" . htmlspecialchars($datos_ad['ID_cat']) . "</pre>";
                $datos_ad['parent_cat'] = 0; // O manejar el error como prefieras
                // exit; // Podrías detener aquí si parent_cat es esencial
            }
            $datos_ad['ID_region'] = isset($_POST['region']) ? $_POST['region'] : 0; // Default a 0 si no existe
            $datos_ad['location'] = isset($_POST['city']) ? $_POST['city'] : '';
            $datos_ad['ad_type'] = isset($_POST['ad_type']) ? $_POST['ad_type'] : 1; // Valor por defecto 1
            $datos_ad['title'] = isset($_POST['tit']) ? $_POST['tit'] : '';
            $datos_ad['title_seo'] = isset($_POST['tit']) ? toAscii($_POST['tit']) : '';
            $datos_ad['texto'] = isset($_POST['text']) ? htmlspecialchars($_POST['text'], ENT_QUOTES, 'UTF-8') : ''; // Especificar encoding
            $datos_ad['price'] = 0; // Asignado 0
            $datos_ad['mileage'] = null;
            $datos_ad['fuel'] = null;
            $datos_ad['date_car'] = null;
            $datos_ad['area'] = null;
            $datos_ad['room'] = null;
            $datos_ad['broom'] = null;
            $datos_ad['address'] = $datos_ad['location'];
            $datos_ad['name'] = isset($_POST['name']) ? formatName($_POST['name']) : '';
            $datos_ad['phone'] = isset($_POST['phone']) ? $_POST['phone'] : '';
            $datos_ad['whatsapp'] = isset($_POST['whatsapp']) && $_POST['whatsapp'] == '1' ? 1 : 0; // Asegurar que sea 0 o 1
            $datos_ad['phone1'] = '';
            $datos_ad['whatsapp1'] = 0;
            $datos_ad['seller_type'] = isset($_POST['seller_type']) ? $_POST['seller_type'] : 0; // Default a 0 si no existe
            $datos_ad['notifications'] = isset($_POST['notifications']) && $_POST['notifications'] == '1' ? 1 : 0; // Asegurar que sea 0 o 1
            $datos_ad['dis'] = isset($_POST['dis']) ? $_POST['dis'] : null;
            $datos_ad['hor_start'] = isset($_POST['horario-inicio']) && !empty($_POST['horario-inicio']) ? $_POST['horario-inicio'] : '';
            $datos_ad['hor_end'] = isset($_POST['horario-final']) && !empty($_POST['horario-final']) ? $_POST['horario-final'] : '';
            $datos_ad['lang1'] = isset($_POST['lang-1']) && !empty($_POST['lang-1']) ? (int)$_POST['lang-1'] : 0;
            $datos_ad['lang2'] = isset($_POST['lang-2']) && !empty($_POST['lang-2']) ? (int)$_POST['lang-2'] : 0;
            $datos_ad['out'] = isset($_POST['out']) ? $_POST['out'] : 0; // Default a 0 si no existe
            $datos_ad['ID_order'] = isset($_POST['order']) ? $_POST['order'] : 0; // Default a 0 si no existe
            $datos_ad['payment'] = "[]"; // Asignado


            // --- Procesamiento de NUEVOS datos ---
            // 1. Servicios
            $lista_servicios = [];
            if (isset($_POST['servicios']) && is_array($_POST['servicios'])) {
                $lista_servicios = array_filter($_POST['servicios'], 'strlen'); // Filtra vacíos
            }
            $datos_ad['servicios'] = json_encode(array_values($lista_servicios));

            // 2. Horario Detallado
            $horario_completo = [];
            $dias_semana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
            if (isset($_POST['horario_dia']) && is_array($_POST['horario_dia'])) {
                $formato_hora = '/^([01]\d|2[0-3]):([0-5]\d)$/';
                foreach ($dias_semana as $dia) {
                    $activo = isset($_POST['horario_dia'][$dia]['activo']) && $_POST['horario_dia'][$dia]['activo'] == '1';
                    $inicio = isset($_POST['horario_dia'][$dia]['inicio']) ? $_POST['horario_dia'][$dia]['inicio'] : '00:00';
                    $fin = isset($_POST['horario_dia'][$dia]['fin']) ? $_POST['horario_dia'][$dia]['fin'] : '23:30';
                    // Validación simple de formato HH:MM
                    if (!preg_match($formato_hora, $inicio)) $inicio = '00:00';
                    if (!preg_match($formato_hora, $fin)) $fin = '23:30';

                    $horario_completo[$dia] = [
                        'activo' => $activo ? 1 : 0,
                        'inicio' => $inicio,
                        'fin' => $fin
                    ];
                }
            }
            $datos_ad['horario'] = json_encode($horario_completo);

            // 3. Idiomas y Niveles
            $lista_idiomas = [];
            $i = 1;
            while (isset($_POST['idioma_' . $i])) {
                $idioma_code = trim($_POST['idioma_' . $i]);
                if (!empty($idioma_code)) {
                    $nivel = isset($_POST['nivel_idioma_' . $i]) ? trim($_POST['nivel_idioma_' . $i]) : 'desconocido';
                    $lista_idiomas[] = ['idioma' => $idioma_code, 'nivel' => $nivel];
                }
                $i++;
            }
            $datos_ad['idiomas'] = json_encode($lista_idiomas);
            // --- Fin Procesamiento NUEVOS datos ---

            echo "<pre>Checkpoint 3.1: Array \$datos_ad construido.</pre>";
            echo '<pre>';
            var_dump($datos_ad);
            echo '</pre>';

            // >>>>> PON EL EXIT AQUÍ (Intento 2) <<<<<
            // exit;

            // ----- Checkpoint 4: Lógica de Usuario -----
            echo "<pre>Checkpoint 4: Iniciando lógica de usuario...</pre>";
            $id_user = 0; // Inicializar
            if (!isset($_SESSION['data']['ID_user'])) {
                echo "<pre>Usuario NO logueado. Verificando/Creando...</pre>";
                if (isset($_POST['email']) && !empty($_POST['email'])) {
                    $checkUser = selectSQL("sc_user", array('mail' => $_POST['email']));
                    $ip = get_client_ip();

                    if ($checkUser !== false && count($checkUser) == 0) {
                        echo "<pre>Usuario no encontrado por email. Verificando registro por IP/Teléfono...</pre>";
                        // Asume que check_registered devuelve true si ya existe un registro reciente con esa IP/Tlf
                        if (!User::check_registered($ip, $datos_ad['phone'])) {
                            echo "<pre>Usuario nuevo detectado. Creando...</pre>";
                            // --- Foto Perfil ---
                            $banner_img = "";
                            if (isset($_POST['photo_name'][0])) {
                                echo "<pre>Intentando copiar imagen de perfil: " . $_POST['photo_name'][0] . "</pre>";
                                // Asegúrate que Images::copyImage y las constantes IMG_USER/IMG_ADS funcionen
                                $banner_img = Images::copyImage($_POST['photo_name'][0], IMG_USER, IMG_ADS, true);
                                echo "<pre>Resultado copyImage: " . ($banner_img ? $banner_img : 'Fallo') . "</pre>";
                            }
                            // --- Datos Usuario ---
                            $pass = randomString(6);
                            $limit_u = user::getLimitsByRol($datos_ad['seller_type']);
                            $datos_u = array(
                                'name' => $datos_ad['name'],
                                'mail' => $_POST['email'],
                                'phone' => $datos_ad['phone'],
                                'banner_img' => $banner_img,
                                'pass' => $pass, // Considera hashear la contraseña: password_hash($pass, PASSWORD_DEFAULT)
                                'date_reg' => time(),
                                'active' => 1,
                                'rol' => $datos_ad['seller_type'],
                                'date_credits' => "0",
                                'credits' => "0",
                                'IP_user' => $ip,
                                'anun_limit' => $limit_u
                            );
                            echo "<pre>Datos para insertar usuario:</pre><pre>" . print_r($datos_u, true) . "</pre>";
                            $result_u = insertSQL("sc_user", $datos_u); // Intenta insertar
                            if ($result_u) {
                                $id_user = lastIdSQL();
                                echo "<pre>Usuario CREADO con éxito. ID: " . $id_user . "</pre>";
                                // mailWelcome($datos_u['name'], $datos_u['mail'], $pass);
                            } else {
                                echo "<pre>ERROR: Falló la inserción del nuevo usuario (insertSQL devolvió false).</pre>";
                                // Intenta obtener error MySQL si es posible
                                if (isset($Connection) && mysqli_error($Connection)) {
                                    echo "<pre>ERROR MYSQL (Usuario): " . htmlspecialchars(mysqli_error($Connection)) . "</pre>";
                                }
                                $id_user = 0; // Asegura que sea 0 si falla
                                $_SESSION['form_error_message'] = "Error al crear la cuenta de usuario.";
                                // Considera detener aquí si la creación de usuario es obligatoria
                                // exit;
                            }
                        } else {
                            echo "<pre>ERROR: Usuario ya registrado recientemente (IP/Teléfono). Asignando ID 0.</pre>";
                            $user_registered = true; // Flag para posible mensaje
                            $id_user = 0;
                            $_SESSION['form_error_message'] = "Ya existe una cuenta reciente asociada a este teléfono o conexión.";
                            // Considera detener aquí
                            // exit;
                        }
                    } elseif ($checkUser !== false) {
                        echo "<pre>Usuario encontrado por email. Obteniendo ID.</pre>";
                        $id_user = $checkUser[0]['ID_user'];
                    } else {
                        echo "<pre>ERROR: Falló la consulta selectSQL para buscar usuario por email.</pre>";
                        $id_user = 0;
                        $_SESSION['form_error_message'] = "Error al verificar la cuenta de usuario.";
                        // exit;
                    }
                } else {
                    echo "<pre>ERROR: Email no proporcionado para usuario no logueado.</pre>";
                    $id_user = 0;
                    $_SESSION['form_error_message'] = "Se requiere un email para publicar.";
                    // exit;
                }
            } else {
                $id_user = $_SESSION['data']['ID_user'];
                echo "<pre>Usuario YA logueado. ID: " . $id_user . "</pre>";
            }

            echo "<pre>Checkpoint 4.1: Lógica de usuario completada.</pre>";
            echo "<pre>ID Usuario final (\$id_user): " . $id_user . "</pre>";
            // echo "<pre>Datos de Sesión Actuales:</pre>"; // Descomenta si necesitas ver la sesión completa
            // echo '<pre>'; var_dump($_SESSION); echo '</pre>';

            // >>>>> PON EL EXIT AQUÍ (Intento 3) <<<<<
            // exit;

            // ----- Checkpoint 5: Verificación de Límite y Preparación Final -----
            echo "<pre>Checkpoint 5: Verificando límite de anuncios...</pre>";
            // Si el id_user es 0, check_item_limit podría fallar o dar resultado incorrecto.
            if ($id_user > 0) {
                $limite = check_item_limit($id_user); // Llama a la función con el ID correcto
                echo "<pre>Resultado check_item_limit(\$limite) para ID $id_user: " . $limite . "</pre>";
            } else {
                echo "<pre>Advertencia: No se puede verificar límite porque ID Usuario es 0.</pre>";
                // Decide cómo manejar esto. ¿Permitir publicar si falla la creación/identificación de usuario? Probablemente no.
                // Si $id_user debe ser > 0, la comprobación más adelante lo detectará.
                $limite = 99; // Poner un valor que impida pasar el if de abajo si no hay orden paga
            }
            echo "<pre>Valor \$_POST['order']: " . htmlspecialchars($_POST['order']) . "</pre>";


            // El límite (0=ok, 1=premium_requerido?, 2=limite_alcanzado) y si es una orden paga
            if (($limite == 0 || $limite == 1) || $_POST['order'] != 0) { // Modificado: Asume que 1 también puede publicar (quizás pagando)
                echo "<pre>Checkpoint 5.1: Límite OK o es Orden Paga o requiere pago. Continuando...</pre>";

                // --- Lógica de Extras y Renovación (Código Original) ---
                // list($extras, $extra_limit) = User::updateExtras($id_user);
                // if ($extras) {
                //     $datos_ad['renovable'] = renovationType::Diario;
                //     $datos_ad['renovable_limit'] = $extra_limit;
                // }
                // --- Rotación Imágenes (Código Original - Comentado) ---
                // ...

                // --- Añadir datos finales al array $datos_ad ---
                $datos_ad['ID_user'] = $id_user; // Asegurar que el ID correcto esté aquí
                $datos_ad['date_ad'] = time();
                if (getConfParam('REVIEW_ITEM') == 1) {
                    $datos_ad['review'] = 1;
                    echo "<pre>Anuncio marcado para revisión (review=1).</pre>";
                } else {
                    $datos_ad['review'] = 0; // O el valor por defecto de tu BD si no es 1
                }

                echo "<pre>Checkpoint 5.2: Datos del anuncio listos para la comprobación final.</pre>";
                echo '<pre>';
                var_dump($datos_ad); // Muestra los datos completos justo antes de la comprobación
                echo '</pre>';

                // >>>>> PON EL EXIT AQUÍ (Intento 4) <<<<<
                // exit;

                // ----- Checkpoint 6: Comprobación Final de Datos -----
                echo "<pre>Checkpoint 6: Realizando comprobación final de datos...</pre>";
                // Adaptada a los campos disponibles y posibles valores 0 válidos
                $condicion_region = ($datos_ad['ID_region'] != 0);
                $condicion_seller_type = ($datos_ad['seller_type'] != 0); // Asumiendo que 0 no es un tipo válido
                $condicion_cat = ($datos_ad['ID_cat'] != 0);
                $condicion_price = (isset($datos_ad['price']) && ($datos_ad['price'] == 0 || is_numeric($datos_ad['price']))); // Price 0 es válido
                $condicion_user = ($datos_ad['ID_user'] > 0); // ID de usuario debe ser válido
                // Añadir otras condiciones si son cruciales (ej: título, texto)
                $condicion_titulo = (!empty($datos_ad['title']));
                $condicion_texto = (!empty($datos_ad['texto']));

                echo "<pre>Resultados: region=" . ($condicion_region ? 'OK' : 'Fallo') .
                    ", seller_type=" . ($condicion_seller_type ? 'OK' : 'Fallo') .
                    ", cat=" . ($condicion_cat ? 'OK' : 'Fallo') .
                    ", price=" . ($condicion_price ? 'OK' : 'Fallo') .
                    ", user=" . ($condicion_user ? 'OK' : 'Fallo') .
                    ", titulo=" . ($condicion_titulo ? 'OK' : 'Fallo') .
                    ", texto=" . ($condicion_texto ? 'OK' : 'Fallo') .
                    "</pre>";

                if ($condicion_region && $condicion_seller_type && $condicion_cat && $condicion_price && $condicion_user && $condicion_titulo && $condicion_texto) {
                    echo "<pre>Checkpoint 6.1: ¡Comprobación SUPERADA! Preparando para insertar...</pre>";
                    echo '<pre>Datos a insertar:';
                    var_dump($datos_ad); // Muestra exactamente lo que se intentará insertar
                    echo '</pre>';

                    // >>>>> PON EL EXIT AQUÍ (Intento 5) <<<<<
                    // exit;

                    // ----- Checkpoint 7: Intento de Inserción -----
                    echo "<pre>Checkpoint 7: Intentando insertar/actualizar en BD...</pre>";
                    $insert = false; // Inicializar resultado
                    $last_ad = 0;   // Inicializar ID
                    $error_insert = false; // Flag de error explícito en la lógica

                    if ($_POST['order'] != 0) {
                        echo "<pre>Procesando como Orden Paga (ID: " . htmlspecialchars($_POST['order']) . ")...</pre>";
                        $order = Orders::getOrderByID($_POST['order']);
                        if ($order && $order['ID_ad'] == 0) {
                            echo "<pre>Orden encontrada y sin anuncio asociado. Intentando insertSQL...</pre>";
                            $insert = insertSQL("sc_ad", $datos_ad); // INTENTO DE INSERCIÓN
                            $last_ad = lastIdSQL();
                            echo "<pre>insertSQL ejecutado. Resultado \$insert: " . ($insert ? 'TRUE' : 'FALSE') . ", \$last_ad: " . $last_ad . "</pre>";
                            if ($insert && $last_ad > 0) {
                                echo "<pre>Inserción exitosa. Actualizando orden...</pre>";
                                // ¡CUIDADO! updateSQL también podría fallar silenciosamente
                                $update_order_result = updateSQL("sc_orders", array("ID_ad" => $last_ad), array("ID_order" => $_POST['order']));
                                echo "<pre>updateSQL para orden ejecutado. Resultado: " . ($update_order_result ? 'TRUE' : 'FALSE') . "</pre>";
                                if (!$update_order_result && isset($Connection) && mysqli_error($Connection)) {
                                    echo "<pre>ERROR MYSQL (Update Order): " . htmlspecialchars(mysqli_error($Connection)) . "</pre>";
                                }
                                // Considerar $insert = false si el update de la orden falla? Depende de la lógica de negocio.
                            }
                            Statistic::addAnuncioNuevoPremium();
                        } else {
                            echo "<pre>ERROR: Orden no encontrada O ya tiene un anuncio asociado (ID_ad: " . ($order ? $order['ID_ad'] : 'N/A') . ").</pre>";
                            $error_insert = true; // Error lógico, no de inserción directa
                            $insert = false; // No se intentó/logró la inserción principal
                            $last_ad = ($order ? $order['ID_ad'] : 0); // ID del anuncio existente si lo hay
                            $_SESSION['form_error_message'] = "Error: La orden de pago no es válida o ya está usada.";
                        }
                    } else {
                        // Anuncio normal
                        echo "<pre>Intentando insertSQL para anuncio normal...</pre>";
                        $insert = insertSQL("sc_ad", $datos_ad); // <<<--- ¡¡INTENTO DE INSERCIÓN!!
                        $last_ad = lastIdSQL(); // Obtener ID si la inserción tuvo éxito
                        echo "<pre>insertSQL ejecutado. Resultado \$insert: " . ($insert ? 'TRUE' : 'FALSE') . ", \$last_ad: " . $last_ad . "</pre>";
                        if ($insert) {
                            Statistic::addAnuncioNuevo();
                        }
                    }

                    echo "<pre>Checkpoint 7.1: Resultado final de la operación de inserción/actualización.</pre>";
                    echo "<pre>Valor final \$insert: " . ($insert ? 'TRUE (Éxito)' : 'FALSE (Fallo o no realizado)') . "</pre>";
                    echo "<pre>Valor final \$last_ad: " . $last_ad . "</pre>";

                    // Si la inserción falló (explícitamente $insert === false) Y NO fue un error lógico ($error_insert === false)
                    if ($insert === false && $error_insert === false && isset($Connection)) {
                        $db_error = mysqli_error($Connection); // Obtener error DESPUÉS de la operación fallida
                        if (!empty($db_error)) {
                            echo "<pre>ERROR MYSQL: " . htmlspecialchars($db_error) . "</pre>";
                        } else {
                            echo "<pre>Fallo en insertSQL/updateSQL, pero no hay error explícito de MySQL disponible (revisar funciones SQL o conexión: \$Connection).</pre>";
                        }
                        // Establecer mensaje de error genérico si falló la BD
                        if (!isset($_SESSION['form_error_message'])) { // Evita sobreescribir mensajes más específicos
                            $_SESSION['form_error_message'] = "Error al guardar los datos en la base de datos.";
                        }
                    } elseif ($error_insert === true && !isset($_SESSION['form_error_message'])) {
                        // Si hubo error lógico (orden usada, etc.) y no se puso mensaje antes
                        $_SESSION['form_error_message'] = "Error al procesar la solicitud (lógica interna).";
                    }

                    // ----- Checkpoint 8: Procesamiento Post-Inserción -----
                    echo "<pre>Checkpoint 8: Iniciando procesamiento post-inserción...</pre>";

                    // ----- Asociar Imágenes -----
                    if ($insert && $last_ad > 0 && isset($_POST['photo_name']) && is_array($_POST['photo_name'])) {
                        echo "<pre>Asociando imágenes al anuncio ID: " . $last_ad . "</pre>";
                        $fotos_asociadas = 0;
                        foreach ($_POST['photo_name'] as $photo => $name) {
                            echo "<pre>Intentando updateSQL para imagen: " . htmlspecialchars($name) . " con posición: " . $photo . "</pre>";
                            // ¡CUIDADO! updateSQL también puede fallar
                            $update_img_result = updateSQL("sc_images", array('ID_ad' => $last_ad, 'position' => $photo, "status" => 1), array('name_image' => $name));
                            echo "<pre>Resultado updateSQL imagen: " . ($update_img_result ? 'TRUE' : 'FALSE') . "</pre>";
                            if ($update_img_result) {
                                $fotos_asociadas++;
                            } elseif (isset($Connection) && mysqli_error($Connection)) {
                                echo "<pre>ERROR MYSQL (Update Imagen): " . htmlspecialchars(mysqli_error($Connection)) . "</pre>";
                            }
                        }
                        echo "<pre>Total fotos asociadas con éxito: " . $fotos_asociadas . " de " . count($_POST['photo_name']) . "</pre>";
                        // ¿Qué hacer si falla la asociación de alguna foto? ¿Continuar?
                    } elseif ($insert && $last_ad > 0) {
                        echo "<pre>No se encontraron fotos para asociar (campo photo_name[] ausente o vacío).</pre>";
                    }

                    // ----- Lógica Adicional y Redirección -----
                    if ($insert && $last_ad > 0) { // Solo continuar si la inserción principal fue exitosa
                        echo "<pre>Inserción/Actualización principal exitosa (last_ad=$last_ad). Ejecutando lógica adicional...</pre>";
                        checkRepeat($last_ad); // Función existente
                        echo "<pre>checkRepeat($last_ad) ejecutado.</pre>";

                        // Notificación por email
                        if (!isset($datos_ad['notifications']) || !$datos_ad['notifications']) {
                            echo "<pre>Usuario NO quiere notificaciones. Llamando (o no) a mailAdNotNotification...</pre>";
                            // mailAdNotNotification($last_ad); // Comentado por si acaso
                        } else {
                            echo "<pre>Usuario SÍ quiere notificaciones.</pre>";
                        }

                        // mailNewAd($last_ad); // Función existente
                        echo "<pre>mailNewAd($last_ad) ejecutado (o debería haberse ejecutado).</pre>";

                        // ----- Redirección Final -----
                        if ($_POST['order'] != 0) {
                            $redirectUrl = "/publicado?payad=" . $_POST['order'];
                            echo "<pre>REDIRECCIÓN (Orden Paga): " . htmlspecialchars($redirectUrl) . "</pre>";
                            // exit; // Descomenta para ver esto antes de redirigir
                            echo '<script type="text/javascript">location.href = "' . $redirectUrl . '";</script>';
                        } else {
                            $redirectUrl = "/publicado";
                            echo "<pre>REDIRECCIÓN (Normal): " . htmlspecialchars($redirectUrl) . "</pre>";
                            // exit; // Descomenta para ver esto antes de redirigir
                            echo '<script type="text/javascript">location.href = "' . $redirectUrl . '";</script>';
                        }
                        // Salir SIEMPRE después de imprimir el script de redirección
                        exit();
                    } else {
                        // Flujo llega aquí si $insert fue false o $last_ad fue 0
                        echo "<pre>ERROR: La inserción/actualización principal falló o no se realizó. No se ejecuta lógica adicional ni redirección.</pre>";
                        // El mensaje de error debería haberse puesto en $_SESSION antes
                        if (!isset($_SESSION['form_error_message'])) { // Mensaje de fallback
                            $_SESSION['form_error_message'] = "Error inesperado durante el guardado del anuncio.";
                        }
                        $_SESSION['form_data_error'] = $_POST; // Guardar datos para repoblar
                        // No redirigir, dejar que el script continúe y muestre el formulario de nuevo con el error
                    }
                } else {
                    // Falla la comprobación final (Checkpoint 6)
                    echo "<pre>ERROR: La comprobación final de datos (Checkpoint 6) ha fallado. No se intenta insertar.</pre>";
                    $error_insert = true; // Marcar error
                    // Guardar los datos $_POST en sesión para repoblar el formulario
                    $_SESSION['form_data_error'] = $_POST;
                    // Añadir un mensaje de error específico si es posible
                    if (!$condicion_user) {
                        $_SESSION['form_error_message'] = "Error al identificar o crear el usuario.";
                    } elseif (!$condicion_region) {
                        $_SESSION['form_error_message'] = "Debes seleccionar una provincia.";
                    } elseif (!$condicion_cat) {
                        $_SESSION['form_error_message'] = "Debes seleccionar una categoría.";
                    } elseif (!$condicion_seller_type) {
                        $_SESSION['form_error_message'] = "Tipo de vendedor inválido.";
                    } elseif (!$condicion_titulo) {
                        $_SESSION['form_error_message'] = "El título es obligatorio.";
                    } elseif (!$condicion_texto) {
                        $_SESSION['form_error_message'] = "La descripción es obligatoria.";
                    } else {
                        $_SESSION['form_error_message'] = "Faltan datos obligatorios o son incorrectos. Revisa el formulario.";
                    }
                    // No redirigir, dejar que el script continúe
                }
            } else {
                // Límite alcanzado (Checkpoint 5)
                echo "<pre>ERROR: Límite de anuncios gratuitos alcanzado (Checkpoint 5). No se intenta insertar.</pre>";
                $error_insert = true; // Marcar error
                $_SESSION['form_error_message'] = "Has alcanzado el límite de anuncios gratuitos.";
                // Guardar datos para repoblar
                $_SESSION['form_data_error'] = $_POST;
                // No redirigir, dejar que el script continúe
            }
        } else {
            // Error token CSRF (Checkpoint 2)
            echo "<pre>ERROR: Token CSRF inválido (Checkpoint 2). No se procesa el formulario.</pre>";
            $error_insert = true; // Marcar error
            $_SESSION['form_error_message'] = "Error de seguridad al procesar el formulario (token inválido). Intenta de nuevo.";
            // Guardar datos para repoblar
            $_SESSION['form_data_error'] = $_POST;
            // No redirigir, dejar que el script continúe
        }
    } else {
        // No se recibió 'category' (Checkpoint 2)
        echo "<pre>ERROR: No se recibió el campo 'category' (Checkpoint 2). No se procesa el formulario.</pre>";
        $error_insert = true; // Marcar error
        $_SESSION['form_error_message'] = "No se recibió la categoría. Revisa el formulario.";
        // Guardar datos para repoblar
        $_SESSION['form_data_error'] = $_POST;
        // No redirigir, dejar que el script continúe
    }

    // Si el script llega aquí, significa que hubo un error ANTES de la redirección exitosa.
    // El formulario se mostrará de nuevo más abajo, recogiendo los mensajes de error de la sesión.
    echo "<pre>Fin del bloque de procesamiento POST (con errores).</pre>";
} // ----- FIN Procesamiento del formulario POST -----

// --- Repoblar Datos y Mensaje de Error (si los hubo) ---
$form_data = [];
if (isset($_SESSION['form_data_error'])) {
    $form_data = $_SESSION['form_data_error'];
    // Limpia los datos de la sesión para no mostrarlos en recargas posteriores
    unset($_SESSION['form_data_error']);
}
$form_error_message = '';
if (isset($_SESSION['form_error_message'])) {
    $form_error_message = $_SESSION['form_error_message'];
    // Limpia el mensaje de la sesión
    unset($_SESSION['form_error_message']);
}

?>

<?php // --- HTML del Formulario y Resto de la Página --- 
?>

<?php if (!user::checkLogin() && check_ip()): ?>
    <dialog class="dialog" open>
        <div class="dialog-modal">
            <a style="color: black;" href="/"><i class="fa-times-circle fa"></i></a>
            <p class="text-underline">Usuario ya registrado</p>
            <p class="mb-3">Para publicar un anuncio accede a tu cuenta.</p>
            <button onclick="gotToLogin()" class="payment-btn">
                Acceder a mi cuenta
            </button>
        </div>
    </dialog>
<?php endif ?>

<ul class="post-help">
    <li class="post-help-item">
        <a target="_blank" href="/ayuda/">
            Normas de <b>publicación</b>
        </a>
    </li>
    <li class="post-help-item">
        <a target="_blank" href="/ayuda/">
            Ayuda
        </a>
    </li>
    <li class="post-help-item">
        <a target="_blank" href="https://www.solomasajistas.com/blog/">
            Visita nuestro blog
        </a>
    </li>
</ul>
<h2 class="title">Publica tu anuncio ¡Gratis!</h2>
<div class="col_single post_item_col">

    <?php // --- Mostrar Mensaje de Error General ---
    if (!empty($form_error_message)): ?>
        <div class="error_msg" style="display: block; border: 1px solid red; padding: 10px; margin-bottom: 15px; background-color: #ffebeb;">
            <strong>Error:</strong> <?php echo htmlspecialchars($form_error_message); ?>
        </div>
    <?php endif; ?>

    <?php // --- Inicio del Formulario HTML --- 
    ?>
    <?php echo newForm($form_data); ?>
    <?php 
    ?>

</div> <?php // Fin .col_single 
        ?>

<?php // --- Resto de la página (bloques, diálogos, scripts JS) --- 
?>
<?php
loadBlock('post-warning');
if ($limite == 1)
    $ANUNCIO_NUEVO_PREMIUM = true;

loadBlock('payment_dialog');
// loadBlock('editor'); // ¿Sigue siendo necesario? El nuevo form usa textarea simple. Quizás quitar.

// ----- Mensajes emergentes del sistema antiguo -----
// (Mantenerlos por si la lógica $limite sigue aplicando)
?>
<?php if (isset($_SESSION['data']['ID_user']) && $limite == 2): ?>
    <dialog class="dialog" open>
        <div class="dialog-modal">
            <a href="/index.php" style="color: black;">
                <i class="fa-times-circle fa"></i>
            </a>
            <p>Haz alcanzado el límite de anuncios publicados</p>
        </div>
    </dialog>
<?php endif ?>
<?php if (isset($_SESSION['data']['ID_user']) && $limite == 0 && checkLastDelete($_SESSION['data']['ID_user'])): ?>
    <dialog class="dialog" open>
        <div class="dialog-modal">
            <a href="/index.php" style="color: black;">
                <i class="fa-times-circle fa"></i>
            </a>
            <p>Aún no puedes publicar anuncio <b>Gratis</b></p>
            <p>Ver nuestro planos o Intente más tarde</p>
            <button onclick="openPayment(); " class="payment-btn">
                Ver opciones de pago
            </button>
        </div>
    </dialog>
<?php endif ?>

<?php // Scripts JS 
?>
<script src="<?= getConfParam('SITE_URL') ?>src/js/newPost.js"></script>
<?php // Script Select2/Sortable comentados 
?>
<?php loadBlock('datajson'); ?>