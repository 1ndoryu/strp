<?php
// COMENTARIO: Asegúrate de que las sesiones están iniciadas correctamente en tu CMS.
// session_start(); // Descomenta si tu CMS no maneja el inicio de sesión automáticamente.

// COMENTARIO: Incluye aquí las dependencias necesarias de tu CMS (clases User, Orders, Images, etc.)
// require_once('path/to/cms/bootstrap.php');
// require_once('path/to/user_class.php');
// require_once('path/to/image_class.php');
// ... etc ...

// COMENTARIO: Mantengo la actualización de sesión si es necesaria.
user::updateSesion(); // Asumiendo que esta función existe y es relevante.

// --- Definiciones y Constantes del Nuevo Formulario ---
define('ETAPA_TIPO_USUARIO_PLAN', 1);
define('ETAPA_DETALLES_ANUNCIO', 2);
define('ETAPA_EXTRAS_PAGO', 3);
define('ETAPA_FINALIZADO', 4); // Estado después de enviar

$tipos_usuario = [
    'masajista' => 'Masajista Particular (1 perfil individual)',
    'centro' => 'Centro (Perfiles individuales)',
    'publicista' => 'Publicista (Perfiles individuales)',
    'visitante' => 'Visitante (Guarda perfiles y contacta)',
];

$planes = [
    'gratis' => ['nombre' => 'Gratis (Prueba 30 días)', 'precio' => 0, 'duracion' => 30, 'renovacion_horas' => 24],
    'silver' => ['nombre' => 'Silver', 'precio' => 12, 'duracion' => 60, 'renovacion_horas' => 12],
    'gold' => ['nombre' => 'Gold', 'precio' => 30, 'duracion' => 90, 'renovacion_horas' => 12],
];

$categorias_nuevas = [
    'terapeuticos' => 'Masajes Terapéuticos',
    'erotica' => 'Masajista Erótica',
    'hetero_gay' => 'Masajista Hetero/Gay',
    'empleo' => 'Bolsa de Empleo',
    'otros' => 'Otros', // Añadido por si acaso
];

$servicios_ofrecidos = [
    'relajante' => 'Masaje Relajante',
    'deportivo' => 'Masaje Deportivo',
    'podal' => 'Masaje Podal',
    'antiestres' => 'Masaje Antiestrés',
    'linfatico' => 'Masaje Linfático',
    'shiatsu' => 'Masaje Shiatsu', // Corregido 'shaisu'
    'descontracturante' => 'Masaje Descontracturante', // Corregido 'descontruactuales'
    'ayurvedico' => 'Masaje Ayurvédico', // Corregido 'ayuvedico'
    'circulatorio' => 'Masaje Circulatorio',
    'tailandes' => 'Masaje Tailandés',
];

$extras_disponibles = [
    'premium' => ['nombre' => 'Premium', 'descripcion' => 'Anuncios aleatorios en la parte superior.', 'precio' => 35],
    'premium_mini' => ['nombre' => 'Premium Mini', 'descripcion' => 'Anuncios aleatorios debajo de Premium.', 'precio' => 27], // Asumiendo 27€ como precio
    'destacado' => ['nombre' => 'Destacado', 'descripcion' => 'Anuncios aleatorios en posiciones destacadas.', 'precio' => 20], // Asumiendo 20€ como precio
    'autosubida' => ['nombre' => 'Autosubida', 'descripcion' => 'Anuncios aleatorios debajo de Destacados.', 'precio' => 25],
    'banner_superior' => ['nombre' => 'Banner Superior', 'descripcion' => 'Banners aleatorios en la parte superior.', 'precio' => 50],
    'banner_lateral' => ['nombre' => 'Banner Lateral', 'descripcion' => 'Banners aleatorios en la parte lateral.', 'precio' => 50],
];

$idiomas_hablados = ['Español', 'Inglés', 'Francés', 'Alemán', 'Portugués', 'Italiano', 'Otro']; // Ejemplo
$niveles_idioma = ['Nativo', 'Alto', 'Medio', 'Bajo'];
$dias_semana = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

// --- Lógica de Etapas y Procesamiento ---

$is_logged_in = user::checkLogin(); // Verificar si el usuario está logueado
$etapa_actual = ETAPA_DETALLES_ANUNCIO; // Por defecto, si está logueado, va a la etapa 2
$errores = []; // Array para guardar mensajes de error
$datos_formulario = $_SESSION['nuevo_anuncio_datos'] ?? []; // Cargar datos de sesión si existen

// Si no está logueado, empieza en la etapa 1
if (!$is_logged_in) {
    $etapa_actual = ETAPA_TIPO_USUARIO_PLAN;
}

// Si se envió un formulario, procesar la etapa correspondiente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['etapa_enviada'])) {
    $etapa_enviada = (int)$_POST['etapa_enviada'];
    // Guardar todos los datos POST en la sesión para repoblar
    $datos_formulario = array_merge($datos_formulario, $_POST);
    $_SESSION['nuevo_anuncio_datos'] = $datos_formulario;

    // --- Validación Etapa 1: Tipo Usuario y Plan (Solo si no está logueado) ---
    if ($etapa_enviada == ETAPA_TIPO_USUARIO_PLAN && !$is_logged_in) {
        if (empty($_POST['tipo_usuario']) || !array_key_exists($_POST['tipo_usuario'], $tipos_usuario)) {
            $errores['tipo_usuario'] = 'Por favor, selecciona un tipo de usuario.';
        }
        if (empty($_POST['plan']) || !array_key_exists($_POST['plan'], $planes)) {
            $errores['plan'] = 'Por favor, selecciona un plan.';
        }

        if (empty($errores)) {
            $etapa_actual = ETAPA_DETALLES_ANUNCIO; // Avanza a la siguiente etapa
        } else {
            $etapa_actual = ETAPA_TIPO_USUARIO_PLAN; // Permanece en la etapa actual
        }
    }

    // --- Validación Etapa 2: Detalles del Anuncio ---
    elseif ($etapa_enviada == ETAPA_DETALLES_ANUNCIO) {
        // COMENTARIO: Aquí iría la validación detallada de los campos de la etapa 2.
        // Similar a la validación que tenías en JS (longitud de título, descripción, campos obligatorios, etc.)
        // Adaptar las validaciones del JS `pre_validate_form` aquí en PHP.

        if (empty($_POST['nombre'])) $errores['nombre'] = 'El nombre es obligatorio.';
        if (empty($_POST['categoria_nueva']) || !array_key_exists($_POST['categoria_nueva'], $categorias_nuevas)) $errores['categoria_nueva'] = 'Selecciona una categoría válida.';
        // COMENTARIO: Usar la variable $provincias para validar. Asumiendo que $cat de tu código original son las provincias.
        $provincias_db = selectSQL("sc_region", [], "name ASC"); // Cargar provincias para validar
        $id_provincias_validas = array_column($provincias_db, 'ID_region');
        if (empty($_POST['provincia']) || !in_array($_POST['provincia'], $id_provincias_validas)) $errores['provincia'] = 'Selecciona una provincia válida.';
        if (empty($_POST['titulo']) || strlen($_POST['titulo']) < 10 || strlen($_POST['titulo']) > 50) $errores['titulo'] = 'El título debe tener entre 10 y 50 caracteres.';
        // COMENTARIO: Aquí deberías aplicar el filtro de palabras no permitidas si es necesario.
        if (empty($_POST['descripcion']) || strlen($_POST['descripcion']) < 30 || strlen($_POST['descripcion']) > 500) $errores['descripcion'] = 'La descripción debe tener entre 30 y 500 caracteres.';
        // COMENTARIO: Aquí deberías aplicar el filtro de palabras no permitidas si es necesario.
        if (!isset($_POST['servicios']) || !is_array($_POST['servicios']) || count($_POST['servicios']) == 0) $errores['servicios'] = 'Selecciona al menos un servicio.';
        else {
            foreach ($_POST['servicios'] as $servicio_sel) {
                if (!array_key_exists($servicio_sel, $servicios_ofrecidos)) {
                    $errores['servicios'] = 'Has seleccionado un servicio inválido.';
                    break;
                }
            }
        }
        // COMENTARIO: Validación de imágenes (existencia, tipo, tamaño) debería hacerse aquí o al procesar la subida.
        // Por ahora, solo verificamos si se marcó una principal si hay imágenes subidas (la lógica de subida iría en el paso final).
        if (isset($_SESSION['uploaded_images']) && count($_SESSION['uploaded_images']) > 0 && empty($_POST['foto_principal'])) {
            $errores['foto_principal'] = 'Debes seleccionar una foto como principal.';
        } elseif (!empty($_POST['foto_principal']) && (!isset($_SESSION['uploaded_images']) || !in_array($_POST['foto_principal'], $_SESSION['uploaded_images']))) {
            $errores['foto_principal'] = 'La foto principal seleccionada no es válida.';
        }
        // COMENTARIO: Validar horario semanal, teléfono, whatsapp, idiomas, salidas...
        if (empty($_POST['telefono'])) $errores['telefono'] = 'El teléfono es obligatorio.';
        // COMENTARIO: Añadir validación para formato de teléfono si es necesario.
        // COMENTARIO: Validar estructura de horario_semanal e idiomas.

        if (empty($errores)) {
            // COMENTARIO: Procesar la subida de imágenes aquí o marcar los archivos temporales para procesarlos en la etapa final.
            // Podrías mover los archivos subidos a una carpeta temporal y guardar sus nombres en la sesión.
            // Ejemplo simple (necesitaría más robustez):
            /*
            if (isset($_FILES['fotos'])) {
                $_SESSION['uploaded_images'] = []; // Guardar nombres de archivo temporales
                $upload_dir = '/ruta/a/uploads/temporales/'; // Asegúrate que esta ruta exista y tenga permisos
                $max_fotos = 3;
                $count = 0;
                foreach ($_FILES['fotos']['tmp_name'] as $key => $tmp_name) {
                    if ($count >= $max_fotos) break;
                    if ($_FILES['fotos']['error'][$key] === UPLOAD_ERR_OK) {
                        $file_info = pathinfo($_FILES['fotos']['name'][$key]);
                        $extension = strtolower($file_info['extension']);
                        if (in_array($extension, ['jpg', 'jpeg', 'png']) && $_FILES['fotos']['size'][$key] <= 2 * 1024 * 1024) { // Max 2MB
                            $new_filename = uniqid('img_', true) . '.' . $extension;
                            if (move_uploaded_file($tmp_name, $upload_dir . $new_filename)) {
                                $_SESSION['uploaded_images'][] = $new_filename; // Guardar nombre para referencia
                                $count++;
                            } else {
                                $errores['fotos'] = 'Error al mover el archivo subido.';
                            }
                        } else {
                            $errores['fotos'] = 'Archivo inválido (tipo o tamaño incorrecto).';
                        }
                    }
                }
                 if (count($_SESSION['uploaded_images']) == 0 && !isset($datos_formulario['fotos_existentes'])) { // Si no hay fotos existentes (edición)
                     $errores['fotos'] = 'Debes subir al menos una foto.';
                 }
            } elseif (!isset($datos_formulario['fotos_existentes'])) { // Si no se subió nada y no hay existentes
                 $errores['fotos'] = 'Debes subir al menos una foto.';
            }
            */
            // Si no hubo errores de validación (ni de subida), avanza
            if (empty($errores)) {
                $etapa_actual = ETAPA_EXTRAS_PAGO;
            } else {
                $etapa_actual = ETAPA_DETALLES_ANUNCIO; // Quédate si hubo error de subida/validación
            }
        } else {
            $etapa_actual = ETAPA_DETALLES_ANUNCIO; // Permanece en la etapa actual si hay errores de validación
        }
    }

    // --- Procesamiento Etapa 3: Extras y Finalización ---
    elseif ($etapa_enviada == ETAPA_EXTRAS_PAGO) {
        // Validar selección de extras (opcional, pueden ser ninguno)
        $extras_seleccionados = [];
        if (isset($_POST['extras']) && is_array($_POST['extras'])) {
            foreach ($_POST['extras'] as $extra_key) {
                if (array_key_exists($extra_key, $extras_disponibles)) {
                    $extras_seleccionados[] = $extra_key;
                } else {
                    $errores['extras'] = 'Has seleccionado un extra inválido.';
                    break; // Salir si uno es inválido
                }
            }
        }
        // Validar Términos y Condiciones
        if (empty($_POST['terminos'])) {
            $errores['terminos'] = 'Debes aceptar los términos y condiciones para publicar.';
        }

        // COMENTARIO: Validación reCAPTCHA (Importante en la etapa final)
        $recaptcha_valido = false;
        if (isset($_POST['g-recaptcha-response'])) {
            // COMENTARIO: Reutiliza tu función `getCaptcha` si existe y funciona.
            // $Return = getCaptcha($_POST['g-recaptcha-response']);
            // if ($Return && $Return->success == true && $Return->score > 0.5) {
            //     $recaptcha_valido = true;
            // }
            // ---- Placeholder si getCaptcha no está disponible ----
            $secretKey = "TU_CLAVE_SECRETA_RECAPTCHA_V3"; // ¡CONFIGURA ESTO!
            $response = $_POST['g-recaptcha-response'];
            $remoteIp = $_SERVER['REMOTE_ADDR'];
            $url = "https://www.google.com/recaptcha/api/siteverify";
            $data = ['secret' => $secretKey, 'response' => $response, 'remoteip' => $remoteIp];
            $options = ['http' => ['header' => "Content-type: application/x-www-form-urlencoded\r\n", 'method' => 'POST', 'content' => http_build_query($data)]];
            $context = stream_context_create($options);
            $result = file_get_contents($url, false, $context);
            $responseKeys = json_decode($result, true);
            if ($responseKeys["success"] && $responseKeys["score"] >= 0.5) { // Ajusta el umbral si es necesario
                $recaptcha_valido = true;
            }
            // ---- Fin Placeholder ----

        }
        if (!$recaptcha_valido) {
            $errores['recaptcha'] = 'La verificación reCAPTCHA ha fallado. Inténtalo de nuevo.';
        }


        if (empty($errores)) {
            // --- ¡PROCESO FINAL DE CREACIÓN DEL ANUNCIO! ---
            $datos_finales = $_SESSION['nuevo_anuncio_datos']; // Todos los datos acumulados

            // COMENTARIO: Lógica de creación/obtención de usuario (si no está logueado)
            $id_user = null;
            if ($is_logged_in) {
                $id_user = $_SESSION['data']['ID_user'];
            } else {
                // COMENTARIO: Reutiliza o adapta tu lógica original para crear usuarios aquí.
                // Necesitarás el email, nombre, teléfono, tipo_usuario de $datos_finales.
                // Ejemplo adaptado:
                $checkUser = selectSQL("sc_user", $a = array('mail' => $datos_finales['email'] ?? '')); // Asumiendo que el email se pidió en etapa 2
                $ip = get_client_ip(); // Asumiendo que esta función existe

                if (count($checkUser) == 0) {
                    // COMENTARIO: Revisa si `check_registered` sigue siendo relevante o si la lógica debe cambiar.
                    // if (!User::check_registered($ip, $datos_finales['telefono'])) {
                    $pass = randomString(6); // Asumiendo que esta función existe
                    // COMENTARIO: El 'rol' ahora podría venir de 'tipo_usuario' o necesitar una lógica diferente.
                    // COMENTARIO: Los límites (`anun_limit`) ahora dependerán del 'plan' seleccionado.
                    $limit = 0; // TODO: Calcular límite según $datos_finales['plan']
                    $rol_usuario = $datos_finales['tipo_usuario']; // Asignar rol según selección
                    // COMENTARIO: ¿Banner de usuario? ¿De dónde sale ahora? Quizás de la foto principal del primer anuncio.
                    $banner_img_user = ""; // TODO: Decidir cómo manejar el banner de usuario

                    $datos_u = array(
                        'name' => formatName($datos_finales['nombre']), // Asumiendo que formatName existe
                        'mail' => $datos_finales['email'],
                        'phone' => $datos_finales['telefono'],
                        'banner_img' => $banner_img_user,
                        'pass' => password_hash($pass, PASSWORD_DEFAULT), // ¡IMPORTANTE! Hashear la contraseña
                        'date_reg' => time(),
                        'active' => 1, // O quizás necesita activación por email
                        'rol' => $rol_usuario,
                        'date_credits' => "0", // Relacionado con el plan?
                        'credits' => "0",      // Relacionado con el plan?
                        'IP_user' => $ip,
                        'anun_limit' => $limit
                    );
                    $result_user = insertSQL("sc_user", $datos_u);
                    if ($result_user) {
                        $id_user = lastIdSQL();
                        // COMENTARIO: Enviar email de bienvenida con $pass (o un enlace de activación)
                        // mailWelcome($datos_finales['nombre'], $datos_finales['email'], $pass);
                    } else {
                        $errores['final'] = "Error crítico: No se pudo crear la cuenta de usuario.";
                    }
                    // } else {
                    //     $errores['final'] = "Parece que ya existe un usuario registrado con datos similares.";
                    //     $user_registered = true; // Variable del código original
                    // }
                } else {
                    $id_user = $checkUser[0]['ID_user'];
                    // COMENTARIO: ¿Qué pasa si el email existe pero el usuario no está logueado? ¿Forzar login? ¿Mostrar error?
                    // Por ahora, asignamos el ID, pero esto podría necesitar revisión.
                    $errores['final'] = "Ya existe una cuenta con este correo electrónico. Por favor, inicia sesión.";
                    // Podrías redirigir a login o mostrar un mensaje más claro.
                    $id_user = null; // Impedir la creación del anuncio si el email existe y no está logueado.

                }
            }

            // COMENTARIO: Verificar límite de anuncios (si aplica, basado en plan/usuario)
            // $limite = check_item_limit($id_user); // Revisa si esta función sigue siendo válida o necesita adaptarse al plan.
            $limite_alcanzado = false; // TODO: Implementar chequeo de límite según el plan y anuncios activos del $id_user.
            // if ($limite == 0 /* && $plan_es_gratis_o_sin_pago_extra */ ) { ... }

            // --- Si el usuario es válido y no hay errores críticos ---
            if ($id_user && empty($errores) && !$limite_alcanzado) {

                // COMENTARIO: Preparar datos para la tabla `sc_ad`
                $datos_ad = [];
                $datos_ad['ID_user'] = $id_user;
                // COMENTARIO: Mapear la nueva categoría. Necesitas decidir cómo guardar esto. ¿Un ID nuevo? ¿El string?
                // Asumamos que guardas el string key por ahora. Podrías necesitar una tabla nueva o adaptar `sc_category`.
                $datos_ad['nueva_categoria'] = $datos_finales['categoria_nueva']; // CAMPO NUEVO (o adaptar uno existente)
                $datos_ad['ID_region'] = $datos_finales['provincia']; // Ya existía como 'region'
                $datos_ad['location'] = $datos_finales['ciudad'] ?? ''; // Ya existía como 'city' o 'location'
                $datos_ad['title'] = $datos_finales['titulo']; // Ya existía
                $datos_ad['title_seo'] = toAscii($datos_finales['titulo']); // Ya existía (asumiendo toAscii existe)
                $datos_ad['texto'] = htmlspecialchars($datos_finales['descripcion']); // Ya existía
                $datos_ad['name'] = formatName($datos_finales['nombre']); // Ya existía
                $datos_ad['phone'] = $datos_finales['telefono']; // Ya existía
                $datos_ad['whatsapp'] = isset($datos_finales['whatsapp']) ? 1 : 0; // Ya existía
                $datos_ad['out'] = $datos_finales['salidas'] ?? 0; // Ya existía (campo 'out')
                $datos_ad['date_ad'] = time();
                // COMENTARIO: Revisar si los anuncios necesitan revisión admin
                $datos_ad['review'] = (getConfParam('REVIEW_ITEM') == 1) ? 1 : 0; // Lógica original

                // COMENTARIO: Campos Nuevos
                $datos_ad['servicios'] = json_encode($datos_finales['servicios'] ?? []); // Guardar como JSON
                $datos_ad['idiomas'] = json_encode($datos_finales['idiomas'] ?? []); // Guardar como JSON { "idioma": "Español", "nivel": "Nativo" }
                $datos_ad['horario_semanal'] = json_encode($datos_finales['horario_semanal'] ?? []); // Guardar como JSON { "lunes": {"disponible": true, "inicio": "09:00", "fin": "18:00"}, ... }
                $datos_ad['plan_seleccionado'] = $datos_finales['plan'] ?? 'gratis'; // Guardar el plan
                $datos_ad['extras_seleccionados'] = json_encode($extras_seleccionados); // Guardar extras como JSON

                // COMENTARIO: Campos del formulario antiguo que ya no se usan directamente o se manejan diferente:
                // ID_cat, parent_cat (reemplazado por nueva_categoria)
                // ad_type (¿sigue siendo relevante?)
                // price (ahora parte del plan/extras?)
                // mileage, fuel, date_car, area, room, broom (parecen irrelevantes ahora)
                // address (usar location/ciudad?)
                // phone1, whatsapp1 (simplificado a un teléfono)
                // seller_type (reemplazado por tipo_usuario/rol)
                // notifications (mantener si es necesario)
                // dis (reemplazado por horario_semanal)
                // lang1, lang2 (reemplazado por idiomas JSON)
                // hor_start, hor_end (reemplazado por horario_semanal)
                // payment (¿formas de pago aceptadas por el masajista? Podría volver a añadirse si es necesario)
                // ID_order (¿relacionado con pagos de planes/extras ahora?)

                // COMENTARIO: Lógica de renovación basada en plan/extras (Adaptar de la original si es necesario)
                // list($extras, $extra_limit) = User::updateExtras($id_user); // ¿Sigue siendo relevante?
                // if ($extras) {
                //     $datos_ad['renovable'] = renovationType::Diario; // O según plan/extras
                //     $datos_ad['renovable_limit'] = $extra_limit; // O según plan/extras
                // }

                // COMENTARIO: Insertar el anuncio en la base de datos
                $insert = insertSQL("sc_ad", $datos_ad);
                if ($insert) {
                    $last_ad = lastIdSQL();

                    // COMENTARIO: Procesar imágenes guardadas temporalmente
                    // Moverlas de la carpeta temporal a la definitiva (IMG_ADS)
                    // Crear registros en `sc_images` asociados a $last_ad
                    // Marcar la imagen principal
                    /*
                     if (isset($_SESSION['uploaded_images']) && is_array($_SESSION['uploaded_images'])) {
                         $upload_dir_temp = '/ruta/a/uploads/temporales/';
                         $upload_dir_final = IMG_ADS; // Constante del sistema original?
                         $foto_principal_nombre = $datos_finales['foto_principal'] ?? null;

                         foreach ($_SESSION['uploaded_images'] as $index => $temp_filename) {
                             $origen = $upload_dir_temp . $temp_filename;
                             $destino = $upload_dir_final . $temp_filename; // Podrías renombrar aquí si quieres

                             if (rename($origen, $destino)) {
                                 $is_principal = ($temp_filename === $foto_principal_nombre) ? 1 : 0;
                                 $datos_imagen = [
                                     'ID_ad' => $last_ad,
                                     'name_image' => $temp_filename, // O el nuevo nombre si renombraste
                                     'position' => $index,
                                     'status' => 1, // O estado pendiente si hay revisión
                                     'principal' => $is_principal // Nuevo campo para marcar la principal
                                 ];
                                 insertSQL("sc_images", $datos_imagen);
                             } else {
                                 // Loggear error de movimiento de archivo
                             }
                         }
                     }
                     */

                    // COMENTARIO: Lógica post-inserción (limpiar sesión, notificaciones, estadísticas, redirección)
                    // checkRepeat($last_ad); // Si sigue siendo relevante
                    // if (!$datos_ad['notifications']) { mailAdNotNotification($last_ad); } // Si sigue siendo relevante
                    // mailNewAd($last_ad); // Si sigue siendo relevante
                    // Statistic::addAnuncioNuevo(); // O basado en plan/extras

                    unset($_SESSION['nuevo_anuncio_datos']); // Limpiar datos del formulario de la sesión
                    unset($_SESSION['uploaded_images']); // Limpiar imágenes temporales de la sesión

                    // COMENTARIO: Redirigir a una página de éxito.
                    // Si se seleccionaron extras de pago, redirigir a la pasarela de pago o a una página intermedia.
                    if (!empty($extras_seleccionados)) {
                        // COMENTARIO: Aquí iría la lógica para iniciar el proceso de pago de los extras.
                        // Podría ser crear una orden y redirigir a una pasarela.
                        // header('Location: /pagina_pago?ad_id=' . $last_ad);
                        // exit;
                        // Por ahora, redirigimos a publicado con un flag de pago pendiente
                        header('Location: /publicado?ad_id=' . $last_ad . '&pago=pendiente');
                        exit;
                    } else {
                        // Si no hay extras de pago, redirigir directamente a la página de anuncio publicado.
                        header('Location: /publicado?ad_id=' . $last_ad);
                        exit;
                    }
                } else {
                    $errores['final'] = "Error al guardar el anuncio en la base de datos.";
                    $etapa_actual = ETAPA_EXTRAS_PAGO; // Volver a la última etapa para mostrar error
                }
            } else {
                // Si hubo error de usuario, límite o validación final, quédate en la etapa 3
                $etapa_actual = ETAPA_EXTRAS_PAGO;
                if ($limite_alcanzado) {
                    $errores['limite'] = "Has alcanzado el límite de anuncios para tu plan.";
                }
                if (empty($errores['final']) && !$id_user && !$is_logged_in) {
                    $errores['final'] = "No se pudo crear o verificar el usuario. Revisa los datos.";
                }
            }
        } else {
            // Si hay errores (extras, términos, recaptcha), permanece en la etapa 3
            $etapa_actual = ETAPA_EXTRAS_PAGO;
        }
    }
} else {
    // Si no es POST, resetea los datos si se accede a la primera página sin estar logueado
    if ($etapa_actual == ETAPA_TIPO_USUARIO_PLAN && !$is_logged_in) {
        // unset($_SESSION['nuevo_anuncio_datos']); // Opcional: limpiar al empezar de cero
        // unset($_SESSION['uploaded_images']);
        $datos_formulario = []; // Empezar con datos vacíos
    } elseif ($is_logged_in && !isset($_SESSION['nuevo_anuncio_datos'])) {
        // Si está logueado y no hay datos en sesión, precargar datos del usuario si es posible
        $datos_formulario['nombre'] = $_SESSION['data']['name'] ?? '';
        $datos_formulario['email'] = $_SESSION['data']['mail'] ?? '';
        $datos_formulario['telefono'] = $_SESSION['data']['phone'] ?? '';
        // Precargar otros datos si existen en $_SESSION['data']
    }
}

// --- Preparar datos para mostrar en el formulario ---
// Usar $datos_formulario para rellenar los campos 'value', 'checked', 'selected'
$form_val = function ($key, $default = '') use ($datos_formulario) {
    return htmlspecialchars($datos_formulario[$key] ?? $default, ENT_QUOTES, 'UTF-8');
};
$form_check = function ($key, $value) use ($datos_formulario) {
    return (isset($datos_formulario[$key]) && $datos_formulario[$key] == $value) ? 'checked' : '';
};
$form_check_array = function ($key, $value) use ($datos_formulario) {
    return (isset($datos_formulario[$key]) && is_array($datos_formulario[$key]) && in_array($value, $datos_formulario[$key])) ? 'checked' : '';
};
$form_select = function ($key, $value) use ($datos_formulario) {
    return (isset($datos_formulario[$key]) && $datos_formulario[$key] == $value) ? 'selected' : '';
};
$form_error = function ($key) use ($errores) {
    return isset($errores[$key]) ? '<span class="error-msg">' . htmlspecialchars($errores[$key]) . '</span>' : '';
};


// Cargar provincias para el select (como en el código original)
$provincias = selectSQL("sc_region", array(), "name ASC");


?>

<!-- COMENTARIO: Incluir aquí el JS de reCAPTCHA v3 (similar al original) -->
<script src='https://www.google.com/recaptcha/api.js?render=<?php echo SITE_KEY; // Asegúrate que SITE_KEY esté definida 
                                                            ?>'></script>

<!-- COMENTARIO: Aquí empiezan los bloques HTML del formulario, separados por etapas -->

<h2 class="titulo-form">Publica tu Anuncio</h2>

<?php
// Mostrar errores generales o de la etapa final
if (!empty($errores['final']) || !empty($errores['recaptcha']) || !empty($errores['limite']) || !empty($errores['user'])) {
    echo '<div class="alerta alerta-error">';
    if (isset($errores['final'])) echo '<p>' . htmlspecialchars($errores['final']) . '</p>';
    if (isset($errores['recaptcha'])) echo '<p>' . htmlspecialchars($errores['recaptcha']) . '</p>';
    if (isset($errores['limite'])) echo '<p>' . htmlspecialchars($errores['limite']) . '</p>';
    if (isset($errores['user'])) echo '<p>' . htmlspecialchars($errores['user']) . '</p>';
    echo '</div>';
}
?>

<form id="nuevo_anuncio_form" class="form-anuncio" method="post" action="<?php echo $_SERVER['PHP_SELF']; // O la URL de procesamiento 
                                                                            ?>" enctype="multipart/form-data" autocomplete="off">

    <!-- Campos ocultos para manejar el estado -->
    <input type="hidden" name="etapa_actual" value="<?php echo $etapa_actual; ?>">
    <input type="hidden" name="etapa_enviada" value="<?php echo $etapa_actual; ?>"> <!-- Indica qué etapa se está enviando -->
    <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response"> <!-- Para reCAPTCHA -->
    <?php // COMENTARIO: Incluir token CSRF si tu sistema lo usa (como el `token` original) 
    ?>
    <?php // $token_q = generateFormToken('nuevoAnuncioToken'); // Generar token si es necesario 
    ?>
    <!-- <input type="hidden" name="csrf_token" value="<?php // echo $token_q; 
                                                        ?>"> -->


    <?php // ================== ETAPA 1: TIPO USUARIO Y PLAN (si no logueado) ================== 
    ?>
    <?php if ($etapa_actual == ETAPA_TIPO_USUARIO_PLAN && !$is_logged_in): ?>
        <fieldset class="etapa etapa-1">
            <legend class="etapa-titulo">Etapa 1 de 3: Elige tu acceso</legend>

            <div class="campo-grupo">
                <label class="etiqueta">Tipo de Usuario *</label>
                <div class="opciones-radio">
                    <?php foreach ($tipos_usuario as $key => $descripcion): ?>
                        <label class="opcion">
                            <input type="radio" name="tipo_usuario" value="<?php echo $key; ?>" <?php echo $form_check('tipo_usuario', $key); ?>>
                            <?php echo htmlspecialchars($descripcion); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <?php echo $form_error('tipo_usuario'); ?>
            </div>

            <div class="campo-grupo">
                <label class="etiqueta">Elige un Plan *</label>
                <div class="opciones-radio">
                    <?php foreach ($planes as $key => $plan): ?>
                        <label class="opcion plan plan-<?php echo $key; ?>">
                            <input type="radio" name="plan" value="<?php echo $key; ?>" <?php echo $form_check('plan', $key); ?>>
                            <strong><?php echo htmlspecialchars($plan['nombre']); ?></strong>
                            (<?php echo $plan['precio'] > 0 ? $plan['precio'] . '€ / ' . $plan['duracion'] . ' días' : 'Gratis'; ?>)
                            <small>Renovación cada <?php echo $plan['renovacion_horas']; ?>h</small>
                        </label>
                    <?php endforeach; ?>
                </div>
                <?php echo $form_error('plan'); ?>
            </div>

            <div class="acciones-etapa">
                <button type="submit" class="boton boton-primario">Siguiente: Detalles del Anuncio</button>
            </div>
        </fieldset>
    <?php endif; ?>

    <?php // ================== ETAPA 2: DETALLES DEL ANUNCIO ================== 
    ?>
    <?php if ($etapa_actual == ETAPA_DETALLES_ANUNCIO): ?>
        <fieldset class="etapa etapa-2">
            <legend class="etapa-titulo">Etapa <?php echo $is_logged_in ? '1' : '2'; ?> de <?php echo $is_logged_in ? '2' : '3'; ?>: Detalles del Anuncio</legend>

            <?php // Si no está logueado, mostrar info de Etapa 1 o pedirla si falta 
            ?>
            <?php if (!$is_logged_in): ?>
                <?php if (!isset($datos_formulario['tipo_usuario']) || !isset($datos_formulario['plan'])): ?>
                    <p class="alerta alerta-aviso">Por favor, <a href="<?php echo $_SERVER['PHP_SELF']; // O URL al inicio del form 
                                                                        ?>">vuelve al paso anterior</a> para seleccionar tu tipo de usuario y plan.</p>
                    <?php // Podrías deshabilitar el resto del formulario aquí 
                    ?>
                <?php else: ?>
                    <p>Tipo Usuario: <strong><?php echo htmlspecialchars($tipos_usuario[$datos_formulario['tipo_usuario']] ?? 'N/A'); ?></strong> | Plan: <strong><?php echo htmlspecialchars($planes[$datos_formulario['plan']]['nombre'] ?? 'N/A'); ?></strong></p>
                    <hr>
                <?php endif; ?>
            <?php endif; ?>


            <div class="campo">
                <label for="nombre" class="etiqueta">Tu Nombre o Nick *</label>
                <input type="text" id="nombre" name="nombre" class="entrada" value="<?php echo $form_val('nombre'); ?>" required maxlength="50">
                <?php echo $form_error('nombre'); ?>
            </div>

            <!-- COMENTARIO: Si el usuario no está logueado, pedir email aquí -->
            <?php if (!$is_logged_in): ?>
                <div class="campo">
                    <label for="email" class="etiqueta">Correo Electrónico *</label>
                    <input type="email" id="email" name="email" class="entrada" value="<?php echo $form_val('email'); ?>" required maxlength="100">
                    <small>Se usará para crear tu cuenta si no tienes una.</small>
                    <?php echo $form_error('email'); ?>
                </div>
            <?php endif; ?>

            <div class="campo">
                <label for="categoria_nueva" class="etiqueta">Categoría del Anuncio *</label>
                <select id="categoria_nueva" name="categoria_nueva" class="selector" required>
                    <option value="">-- Selecciona una categoría --</option>
                    <?php foreach ($categorias_nuevas as $key => $nombre): ?>
                        <option value="<?php echo $key; ?>" <?php echo $form_select('categoria_nueva', $key); ?>><?php echo htmlspecialchars($nombre); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php echo $form_error('categoria_nueva'); ?>
            </div>

            <div class="campo-fila">
                <div class="campo">
                    <label for="provincia" class="etiqueta">Provincia *</label>
                    <select id="provincia" name="provincia" class="selector" required>
                        <option value="">-- Selecciona una provincia --</option>
                        <?php foreach ($provincias as $prov): ?>
                            <option value="<?php echo $prov['ID_region']; ?>" <?php echo $form_select('provincia', $prov['ID_region']); ?>>
                                <?php echo htmlspecialchars($prov['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php echo $form_error('provincia'); ?>
                </div>
                <div class="campo">
                    <label for="ciudad" class="etiqueta">Ciudad / Zona (Opcional)</label>
                    <input type="text" id="ciudad" name="ciudad" class="entrada" value="<?php echo $form_val('ciudad'); ?>" maxlength="100">
                    <?php echo $form_error('ciudad'); ?>
                </div>
            </div>


            <div class="campo">
                <label for="titulo" class="etiqueta">Título del Anuncio *</label>
                <input type="text" id="titulo" name="titulo" class="entrada" value="<?php echo $form_val('titulo'); ?>" required minlength="10" maxlength="50">
                <small>Entre 10 y 50 caracteres.</small>
                <?php echo $form_error('titulo'); ?>
            </div>

            <div class="campo">
                <label for="descripcion" class="etiqueta">Descripción del Anuncio *</label>
                <textarea id="descripcion" name="descripcion" class="textarea" rows="6" required minlength="30" maxlength="500"><?php echo $form_val('descripcion'); ?></textarea>
                <small>Entre 30 y 500 caracteres. Describe qué ofreces.</small>
                <?php echo $form_error('descripcion'); ?>
            </div>

            <div class="campo-grupo">
                <label class="etiqueta">Servicios a Ofrecer *</label>
                <div class="opciones-check">
                    <?php foreach ($servicios_ofrecidos as $key => $nombre): ?>
                        <label class="opcion">
                            <input type="checkbox" name="servicios[]" value="<?php echo $key; ?>" <?php echo $form_check_array('servicios', $key); ?>>
                            <?php echo htmlspecialchars($nombre); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
                <small>Selecciona todos los que apliquen.</small>
                <?php echo $form_error('servicios'); ?>
            </div>

            <div class="campo-grupo">
                <label class="etiqueta">Fotos (Máximo 3) *</label>
                <input type="file" id="fotos" name="fotos[]" class="entrada-archivo" multiple accept="image/jpeg, image/png" data-max-files="3">
                <small>Sube hasta 3 fotos (JPG, PNG, máx 2MB cada una). La primera que subas será la principal por defecto.</small>
                <!-- COMENTARIO: Aquí necesitarás JS para previsualizar las fotos y permitir elegir la principal -->
                <div id="previsualizacion-fotos" class="previs-fotos">
                    <?php
                    // COMENTARIO: Si se está editando o volviendo a esta etapa, mostrar fotos ya subidas (temporalmente)
                    if (!empty($_SESSION['uploaded_images'])) {
                        echo "<p>Fotos cargadas:</p>";
                        foreach ($_SESSION['uploaded_images'] as $img_name) {
                            // Deberías mostrar una miniatura y la opción de marcar como principal/eliminar
                            echo "<div>" . htmlspecialchars($img_name) .
                                " <label><input type='radio' name='foto_principal' value='" . htmlspecialchars($img_name) . "' " . $form_check('foto_principal', $img_name) . "> Principal</label>" .
                                "</div>";
                        }
                    }
                    ?>
                </div>
                <?php echo $form_error('fotos'); ?>
                <?php echo $form_error('foto_principal'); ?>
                <!-- Campo oculto para la foto principal seleccionada por JS -->
                <!-- <input type="hidden" name="foto_principal_seleccionada" id="foto_principal_seleccionada" value="<?php echo $form_val('foto_principal'); ?>"> -->
            </div>

            <div class="campo-grupo">
                <label class="etiqueta">Horario Semanal Detallado *</label>
                <div class="horario-semanal">
                    <?php foreach ($dias_semana as $dia): ?>
                        <div class="dia-horario">
                            <label class="etiqueta-dia"><?php echo ucfirst($dia); ?></label>
                            <input type="checkbox" name="horario_semanal[<?php echo $dia; ?>][disponible]" value="1" <?php echo isset($datos_formulario['horario_semanal'][$dia]['disponible']) ? 'checked' : ''; ?>> Disponible
                            <label>De:</label>
                            <input type="time" name="horario_semanal[<?php echo $dia; ?>][inicio]" value="<?php echo $form_val("horario_semanal[$dia][inicio]", '09:00'); ?>" class="entrada-tiempo">
                            <label>A:</label>
                            <input type="time" name="horario_semanal[<?php echo $dia; ?>][fin]" value="<?php echo $form_val("horario_semanal[$dia][fin]", '18:00'); ?>" class="entrada-tiempo">
                        </div>
                    <?php endforeach; ?>
                </div>
                <small>Marca los días que trabajas e indica tu horario.</small>
                <?php echo $form_error('horario_semanal'); ?>
            </div>

            <div class="campo-fila">
                <div class="campo">
                    <label for="telefono" class="etiqueta">Teléfono de Contacto *</label>
                    <input type="tel" id="telefono" name="telefono" class="entrada" value="<?php echo $form_val('telefono'); ?>" required maxlength="15">
                    <?php echo $form_error('telefono'); ?>
                </div>
                <div class="campo campo-check">
                    <label class="opcion">
                        <input type="checkbox" id="whatsapp" name="whatsapp" value="1" <?php echo $form_check('whatsapp', '1'); ?>>
                        Tengo WhatsApp
                    </label>
                </div>
            </div>

            <div class="campo-grupo">
                <label class="etiqueta">Idiomas que Hablas</label>
                <div id="lista-idiomas" class="lista-items-dinamica">
                    <?php
                    // COMENTARIO: Mostrar idiomas existentes si se vuelve a la etapa
                    $idiomas_guardados = $datos_formulario['idiomas'] ?? [];
                    if (empty($idiomas_guardados)) {
                        $idiomas_guardados = [['idioma' => '', 'nivel' => '']];
                    } // Añadir uno vacío por defecto

                    foreach ($idiomas_guardados as $index => $idioma_data):
                    ?>
                        <div class="item-idioma">
                            <select name="idiomas[<?php echo $index; ?>][idioma]" class="selector-corto">
                                <option value="">-- Idioma --</option>
                                <?php foreach ($idiomas_hablados as $idioma): ?>
                                    <option value="<?php echo $idioma; ?>" <?php echo ($idioma_data['idioma'] ?? '') == $idioma ? 'selected' : ''; ?>><?php echo $idioma; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select name="idiomas[<?php echo $index; ?>][nivel]" class="selector-corto">
                                <option value="">-- Nivel --</option>
                                <?php foreach ($niveles_idioma as $nivel): ?>
                                    <option value="<?php echo $nivel; ?>" <?php echo ($idioma_data['nivel'] ?? '') == $nivel ? 'selected' : ''; ?>><?php echo $nivel; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($index > 0): // Permitir borrar excepto el primero 
                            ?>
                                <button type="button" class="boton-quitar" onclick="this.parentNode.remove()">Quitar</button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" id="agregar-idioma" class="boton-secundario boton-agregar">Añadir Idioma</button>
                <?php echo $form_error('idiomas'); ?>
            </div>

            <div class="campo">
                <label for="salidas" class="etiqueta">¿Realizas Salidas? *</label>
                <select id="salidas" name="salidas" class="selector" required>
                    <option value="0" <?php echo $form_select('salidas', '0'); ?>>No</option>
                    <option value="1" <?php echo $form_select('salidas', '1'); ?>>Sí</option>
                </select>
                <?php echo $form_error('salidas'); ?>
            </div>

            <div class="acciones-etapa">
                <?php if (!$is_logged_in): ?>
                    <button type="button" onclick="history.back()" class="boton boton-secundario">Volver</button>
                <?php endif; ?>
                <button type="submit" class="boton boton-primario">Siguiente: Extras Opcionales</button>
            </div>
        </fieldset>
    <?php endif; ?>


    <?php // ================== ETAPA 3: EXTRAS Y FINALIZACIÓN ================== 
    ?>
    <?php if ($etapa_actual == ETAPA_EXTRAS_PAGO): ?>
        <fieldset class="etapa etapa-3">
            <legend class="etapa-titulo">Etapa <?php echo $is_logged_in ? '2' : '3'; ?> de <?php echo $is_logged_in ? '2' : '3'; ?>: Extras y Publicación</legend>

            <div class="campo-grupo">
                <label class="etiqueta">Servicios Extra (Opcional)</label>
                <p>Selecciona si quieres destacar tu anuncio (conlleva coste adicional).</p>
                <div class="opciones-check">
                    <?php foreach ($extras_disponibles as $key => $extra): ?>
                        <label class="opcion extra extra-<?php echo $key; ?>">
                            <input type="checkbox" name="extras[]" value="<?php echo $key; ?>" <?php echo $form_check_array('extras', $key); ?>>
                            <strong><?php echo htmlspecialchars($extra['nombre']); ?> (€<?php echo $extra['precio']; ?>)</strong>
                            <small><?php echo htmlspecialchars($extra['descripcion']); ?></small>
                        </label>
                    <?php endforeach; ?>
                    <label class="opcion extra extra-gratis">
                        <input type="radio" name="extras[]" value="ninguno" <?php echo (!isset($datos_formulario['extras']) || empty($datos_formulario['extras']) || in_array('ninguno', $datos_formulario['extras'] ?? [])) ? 'checked' : ''; ?>>
                        <strong>Continuar sin extras (Gratis)</strong>
                    </label>
                </div>
                <?php echo $form_error('extras'); ?>
            </div>

            <div class="campo-grupo">
                <label class="opcion">
                    <input type="checkbox" id="terminos" name="terminos" value="1" <?php echo $form_check('terminos', '1'); ?>>
                    Acepto los <a href="/terminos-y-condiciones" target="_blank">Términos y Condiciones</a> y las <a href="/normas-publicacion" target="_blank">Normas de Publicación</a> *
                </label>
                <?php echo $form_error('terminos'); ?>
            </div>

            <!-- COMENTARIO: Otros checkboxes opcionales del formulario original -->
            <div class="campo-grupo">
                <label class="opcion">
                    <input type="checkbox" id="notifications" name="notifications" value="1" <?php echo $form_check('notifications', '1'); ?>>
                    <?php // echo $language['post.label_notifications'] ?? 'Quiero recibir notificaciones sobre mi anuncio.'; 
                    ?>
                    Quiero recibir notificaciones sobre mi anuncio. <!-- Texto placeholder -->
                </label>
            </div>


            <div class="acciones-etapa">
                <button type="button" onclick="window.location.href='<?php echo $_SERVER['PHP_SELF'] . '?etapa=2'; // Forzar ir a etapa 2 
                                                                        ?>'" class="boton boton-secundario">Volver a Detalles</button>
                <button type="submit" id="finalizar_btn" class="boton boton-primario boton-finalizar">
                    <?php echo (!empty($datos_formulario['extras']) && !in_array('ninguno', $datos_formulario['extras'] ?? [])) ? 'Proceder al Pago y Publicar' : 'Publicar Anuncio'; ?>
                </button>
            </div>
        </fieldset>
    <?php endif; ?>

</form>

<!-- COMENTARIO: Incluir JS necesario para interacciones (previsualización fotos, añadir idiomas, etc.) -->
<script>
    // --- Script para añadir idiomas dinámicamente (Ejemplo básico) ---
    document.getElementById('agregar-idioma')?.addEventListener('click', function() {
        const contenedor = document.getElementById('lista-idiomas');
        const index = contenedor.children.length;
        const nuevoIdioma = document.createElement('div');
        nuevoIdioma.classList.add('item-idioma');
        nuevoIdioma.innerHTML = `
        <select name="idiomas[${index}][idioma]" class="selector-corto">
            <option value="">-- Idioma --</option>
            <?php foreach ($idiomas_hablados as $idioma): ?>
            <option value="<?php echo $idioma; ?>"><?php echo $idioma; ?></option>
            <?php endforeach; ?>
        </select>
        <select name="idiomas[${index}][nivel]" class="selector-corto">
             <option value="">-- Nivel --</option>
             <?php foreach ($niveles_idioma as $nivel): ?>
             <option value="<?php echo $nivel; ?>"><?php echo $nivel; ?></option>
             <?php endforeach; ?>
        </select>
        <button type="button" class="boton-quitar" onclick="this.parentNode.remove()">Quitar</button>
    `;
        contenedor.appendChild(nuevoIdioma);
    });

    // --- Script para manejar reCAPTCHA v3 en el envío final ---
    const form = document.getElementById('nuevo_anuncio_form');
    const submitButton = document.getElementById('finalizar_btn');
    const currentStageInput = form.querySelector('input[name="etapa_actual"]');

    if (submitButton && currentStageInput && parseInt(currentStageInput.value, 10) === <?php echo ETAPA_EXTRAS_PAGO; ?>) {
        submitButton.addEventListener('click', function(e) {
            e.preventDefault(); // Prevenir envío normal
            submitButton.disabled = true; // Deshabilitar botón
            submitButton.textContent = 'Procesando...';

            grecaptcha.ready(function() {
                grecaptcha.execute('<?php echo SITE_KEY; // Usa tu Site Key de reCAPTCHA v3 
                                    ?>', {
                        action: 'submit_anuncio'
                    }) // Acción descriptiva
                    .then(function(token) {
                        // Añadir token al formulario y enviarlo
                        document.getElementById('g-recaptcha-response').value = token;
                        form.submit();
                    })
                    .catch(function(error) {
                        console.error('Error reCAPTCHA:', error);
                        submitButton.disabled = false; // Rehabilitar botón si hay error
                        submitButton.textContent = 'Publicar Anuncio'; // Restaurar texto
                        // Mostrar un mensaje de error al usuario si falla la ejecución de reCAPTCHA
                        alert('Error en la verificación reCAPTCHA. Por favor, inténtalo de nuevo.');
                    });
            });
        });
    }

    // COMENTARIO: Añadir aquí el JS para previsualización de fotos, límite de subida, selección de principal, etc.
    // Deberías adaptar partes del 'post.js' original o crear nuevas funciones.
</script>

<?php
// COMENTARIO: Cargar bloques adicionales si es necesario (como payment_dialog, editor, etc. del código original)
// loadBlock('payment_dialog');
// loadBlock('editor'); // Si el campo descripción usa un editor WYSIWYG
?>