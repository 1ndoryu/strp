<?php

user::updateSesion();

// TODO: La lógica de límite de items podría depender ahora del TIPO de usuario y del PLAN elegido.
// $limite = check_item_limit();
$limite = 0; // Temporalmente desactivado para el ejemplo

// TODO: Estas variables podrían necesitarse o no dependiendo de la lógica final.
$ANUNCIO_NUEVO_PREMIUM = false;
$user_registered = false;
$DATAJSON = array();
$DATAJSON['max_photos'] = getConfParam('MAX_PHOTOS_AD');
$DATAJSON['edit'] = 0;

#AGREGADO POR WANDORIUS
#require_once 'delete_temp_image.php'; 
require_once 'upload_temp_image.php';
# WANDORIUS 
# NO ENTIENDO QUE HACE ESTO 10/04/25

// TODO: Revisar si la regla de registro obligatorio para publicar sigue aplicando igual.
// if (getConfParam('POST_ITEM_REG') == 1) {
//     check_login(); // check_login() podría usarse para redirigir si se requiere estar logueado SIEMPRE antes del form.
// }

// --- INICIO PROCESAMIENTO DEL FORMULARIO (POST) ---
// ESTA SECCIÓN REQUIERE UNA ADAPTACIÓN PROFUNDA PARA EL NUEVO FORMULARIO MULTI-ETAPA
if (isset($_POST['g-recaptcha-response'])) {

    $Return = getCaptcha($_POST['g-recaptcha-response']);

    // TODO: Validar reCAPTCHA como antes
    if ($Return->success == true && $Return->score > 0.5) {

        // TODO: Validar Token CSRF como antes
        if (verifyFormToken('nuevoAnuncioToken', $_POST['token']) || defined('DEBUG') && DEBUG) {

            // ---------------------------------------------------------------
            // PASO 1: Recoger datos de TODAS las etapas (asumiendo que se envían al final)
            // ---------------------------------------------------------------

            // --- Datos Etapa 0: Tipo de Usuario (si aplica, si no está logueado) ---
            $tipo_usuario_seleccionado = isset($_POST['tipo_usuario']) ? $_POST['tipo_usuario'] : null; // 'masajista', 'centro', 'publicista', 'visitante'

            // --- Datos Etapa 1: Plan ---
            $plan_seleccionado = isset($_POST['plan']) ? $_POST['plan'] : null; // 'gratis', 'silver', 'gold'
            // TODO: Guardar el plan seleccionado y la fecha de inicio/fin asociada.
            // TODO: Si el plan es de pago (silver, gold), aquí podría iniciarse el proceso de pago ANTES de crear el anuncio,
            //       o marcar el anuncio como pendiente de pago. Para este ejemplo, asumimos que se crea y luego se paga o activa.

            // --- Datos Etapa 2: Perfil/Anuncio ---
            $datos_ad = array();
            $datos_perfil = array(); // Podría ser útil separar datos del anuncio vs datos del usuario/perfil

            // Datos Usuario (si es un nuevo registro o para actualizar)
            // TODO: Determinar si es un nuevo usuario o uno existente basado en el email o sesión.
            $id_user = isset($_SESSION['data']['ID_user']) ? $_SESSION['data']['ID_user'] : null;
            $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : null;
            $nombre_contacto = isset($_POST['nombre']) ? formatName($_POST['nombre']) : null; // Usar formatName si existe
            $telefono = isset($_POST['telefono']) ? preg_replace('/[^0-9]/', '', $_POST['telefono']) : null;
            $tiene_whatsapp = isset($_POST['tiene_whatsapp']) ? 1 : 0;
            // TODO: Si es nuevo usuario, generar contraseña, definir rol inicial (basado en $tipo_usuario_seleccionado?), etc.
            //       La lógica de registro/login necesita integrarse con las etapas 0 y 1.

            // Datos Anuncio
            $datos_ad['ID_cat'] = isset($_POST['categoria']) ? (int)$_POST['categoria'] : null; // Nuevo campo 'categoria'
            // TODO: 'parent_cat' podría no ser necesario o cambiar según la nueva estructura de categorías.
            // $category_ad = selectSQL('sc_category', $w=array('ID_cat'=>$datos_ad['ID_cat']));
            // $datos_ad['parent_cat'] = $category_ad[0]['parent_cat'];

            $datos_ad['ID_region'] = isset($_POST['provincia']) ? (int)$_POST['provincia'] : null; // Renombrado a 'provincia'
            $datos_ad['location'] = isset($_POST['ciudad']) ? htmlspecialchars($_POST['ciudad']) : null; // Nuevo campo 'ciudad' (si se mantiene input text)
            $datos_ad['title'] = isset($_POST['titulo_anuncio']) ? htmlspecialchars($_POST['titulo_anuncio']) : null; // Nuevo campo 'titulo_anuncio'
            $datos_ad['title_seo'] = isset($_POST['titulo_anuncio']) ? toAscii($_POST['titulo_anuncio']) : null; // Usar toAscii si existe
            $datos_ad['texto'] = isset($_POST['descripcion']) ? htmlspecialchars($_POST['descripcion']) : null; // Nuevo campo 'descripcion'

            // Servicios (Checkbox multiple)
            $servicios_ofrecidos = isset($_POST['servicios']) && is_array($_POST['servicios']) ? $_POST['servicios'] : [];
            // TODO: Guardar los servicios. Probablemente como JSON en un campo de la tabla de anuncios, o en una tabla relacionada (ad_services).
            $datos_ad['servicios_json'] = json_encode($servicios_ofrecidos); // Ejemplo guardando como JSON

            // Horario Detallado
            $horario_detallado = isset($_POST['horario_dia']) && is_array($_POST['horario_dia']) ? $_POST['horario_dia'] : [];
            // TODO: Procesar y guardar el horario detallado. También JSON o tabla relacionada.
            // Ejemplo de limpieza básica:
            $horario_final = [];
            foreach ($horario_detallado as $dia => $horas) {
                if (isset($horas['activo'])) {
                    $horario_final[$dia] = [
                        'inicio' => isset($horas['inicio']) ? htmlspecialchars($horas['inicio']) : '00:00',
                        'fin' => isset($horas['fin']) ? htmlspecialchars($horas['fin']) : '00:00',
                    ];
                }
            }
            $datos_ad['horario_detallado_json'] = json_encode($horario_final); // Ejemplo guardando como JSON

            // Idiomas y Nivel
            $idiomas = [];
            if (!empty($_POST['idioma_1']) && !empty($_POST['nivel_idioma_1'])) {
                $idiomas[] = ['idioma' => htmlspecialchars($_POST['idioma_1']), 'nivel' => htmlspecialchars($_POST['nivel_idioma_1'])];
            }
            if (!empty($_POST['idioma_2']) && !empty($_POST['nivel_idioma_2'])) {
                $idiomas[] = ['idioma' => htmlspecialchars($_POST['idioma_2']), 'nivel' => htmlspecialchars($_POST['nivel_idioma_2'])];
            }
            // TODO: Guardar idiomas. JSON o tabla relacionada.
            $datos_ad['idiomas_json'] = json_encode($idiomas); // Ejemplo

            $datos_ad['realiza_salidas'] = isset($_POST['realiza_salidas']) ? (int)$_POST['realiza_salidas'] : 0; // Nuevo campo

            // Campos obsoletos o renombrados (a eliminar o ajustar)
            // $datos_ad['ad_type'] = $_POST['ad_type']; // Ya no existe este campo? O se deduce del tipo de usuario/categoría?
            // $datos_ad['price'] = $_POST['precio'] ? $_POST['precio'] : 0; // No hay campo precio en el nuevo form?
            // $datos_ad['mileage'] = $_POST['km_car']; // Obsoleto
            // $datos_ad['fuel'] = $_POST['fuel_car']; // Obsoleto
            // $datos_ad['date_car'] = $_POST['date_car']; // Obsoleto
            // $datos_ad['area'] = $_POST['area']; // Obsoleto
            // $datos_ad['room'] = $_POST['room']; // Obsoleto
            // $datos_ad['broom'] = $_POST['bathroom']; // Obsoleto
            // $datos_ad['address'] = $_POST['city']; // Usar location o campo específico si se necesita dirección completa
            // $datos_ad['phone1'] = $_POST['phone1']; // Obsoleto
            // $datos_ad['whatsapp1'] = isset($_POST['whatsapp1']) ? 1 : 0; // Obsoleto
            // $datos_ad['seller_type'] = $_POST['seller_type']; // Reemplazado por tipo_usuario? O sigue siendo relevante para el *anuncio*?
            // $datos_ad['dis'] = $_POST['dis']; // Reemplazado por horario_detallado
            // $datos_ad['hor_start'] = $_POST['horario-inicio']; // Reemplazado por horario_detallado
            // $datos_ad['hor_end'] = $_POST['horario-final']; // Reemplazado por horario_detallado
            // $datos_ad['payment'] = isset($_POST['pago']) ? json_encode($_POST['pago']) : "[]"; // Obsoleto? No hay formas de pago en el nuevo form.

            // --- Datos Etapa 3: Extras ---
            $extras_seleccionados = isset($_POST['extras']) && is_array($_POST['extras']) ? $_POST['extras'] : [];
            // TODO: Procesar los extras seleccionados. Marcar el anuncio para destacarlo según las reglas de cada extra.
            // TODO: Calcular el coste total si hay extras de pago y proceder al pago o marcar como pendiente.

            // ---------------------------------------------------------------
            // PASO 2: Lógica de Usuario (Registro / Verificación)
            // ---------------------------------------------------------------
            if (!$id_user) { // Si no hay sesión activa, intentar registrar o encontrar usuario por email
                $checkUser = selectSQL("sc_user", $a = array('mail' => $email));
                $ip = get_client_ip(); // Asegúrate que esta función exista

                if (count($checkUser) == 0) {
                    // Usuario nuevo
                    // TODO: Verificar si el teléfono ya está registrado (User::check_registered) si esa regla aplica.
                    // if (!User::check_registered($ip, $telefono)) {
                    // TODO: Copiar imagen de perfil si se sube una específica (no está en el form actual)
                    // $banner_img = "";
                    $pass = randomString(6); // Asegúrate que esta función exista
                    // TODO: Definir el 'rol' basado en $tipo_usuario_seleccionado
                    $rol_usuario = UserRole::Particular; // Valor por defecto, ajustar según $tipo_usuario_seleccionado
                    // TODO: Definir límites iniciales basados en el PLAN seleccionado ('gratis')
                    $limite_anuncios = 1; // Ejemplo para plan gratis

                    $datos_u = array(
                        'name' => $nombre_contacto,
                        'mail' => $email,
                        'phone' => $telefono,
                        'whatsapp' => $tiene_whatsapp, // Añadido campo WhatsApp al usuario
                        'pass' => password_hash($pass, PASSWORD_DEFAULT), // ¡Hashear contraseña!
                        'date_reg' => time(),
                        'active' => 1, // O 0 si requiere verificación por email
                        'rol' => $rol_usuario,
                        'date_credits' => "0", // Ajustar según lógica de planes/créditos si aplica
                        'credits' => "0", // Ajustar según lógica de planes/créditos si aplica
                        'IP_user' => $ip,
                        'anun_limit' => $limite_anuncios // Ajustar según plan
                    );
                    $result_user = insertSQL("sc_user", $datos_u);
                    if ($result_user) {
                        $id_user = lastIdSQL();
                        // TODO: Enviar email de bienvenida con la contraseña generada ($pass)
                        // mailWelcome($nombre_contacto, $email, $pass);
                        // TODO: Iniciar sesión automáticamente al nuevo usuario
                        // user::forceLogin($id_user);
                    } else {
                        // Error insertando usuario
                        $error_insert = true; // Marcar error
                        echo '<div class="alerta error">Error al registrar el usuario. Inténtalo de nuevo.</div>';
                    }
                    // } else {
                    //     // Teléfono ya registrado
                    //     $user_registered = true; // Marcar para mostrar mensaje
                    //     $error_insert = true;
                    //     echo '<div class="alerta error">El teléfono ya está registrado. Inicia sesión.</div>';
                    // }
                } else {
                    // Email ya existe
                    $id_user = $checkUser[0]['ID_user'];
                    // TODO: ¿Debería permitirse publicar si el email existe pero no está logueado?
                    //       Quizás redirigir a login o mostrar un mensaje claro.
                    //       Por ahora, se asocia el anuncio al usuario existente.
                    //       ¡Cuidado! Esto podría permitir a alguien añadir anuncios a otra cuenta si conoce el email.
                    //       Es MÁS SEGURO requerir login si el email existe.
                    echo '<div class="alerta error">El email ya está registrado. Por favor, <a href="/login">inicia sesión</a> para publicar.</div>';
                    $error_insert = true; // Marcar error para no continuar
                }
            } else {
                // Usuario ya logueado, $id_user ya está definido.
                // TODO: Opcionalmente, actualizar datos del perfil si se modificaron en el formulario (nombre, teléfono, whatsapp?)
                // updateSQL("sc_user", ['name' => $nombre_contacto, 'phone' => $telefono, 'whatsapp' => $tiene_whatsapp], ['ID_user' => $id_user]);
            }

            // ---------------------------------------------------------------
            // PASO 3: Validación y Creación del Anuncio (si no hubo errores previos)
            // ---------------------------------------------------------------
            if (isset($error_insert) && $error_insert) {
                // No continuar si hubo error en pasos previos (registro, email existente, etc.)
                echo '<div class="alerta error">No se pudo procesar el formulario debido a errores previos.</div>';
            }
            // TODO: Revisar la lógica de límites de anuncios ($limite) según el plan y usuario
            // $limite = check_item_limit($id_user); // Recalcular límite si es necesario
            // elseif ($limite == 0 /*|| es_plan_pago || tiene_extras_pago */) { // Ajustar condición de límite
            elseif ($id_user) { // Simplificado: Si tenemos un ID de usuario válido

                // TODO: Aplicar lógica de renovación basada en el PLAN seleccionado
                // if ($plan_seleccionado == 'silver' || $plan_seleccionado == 'gold') {
                //     $datos_ad['renovable'] = renovationType::Cada12Horas; // Asumiendo que tienes constantes/enum
                // } elseif ($plan_seleccionado == 'gratis') {
                //     $datos_ad['renovable'] = renovationType::Cada24Horas;
                // }
                // $datos_ad['renovable_limit'] = time() + (30 * 24 * 60 * 60); // Ejemplo caducidad plan gratis 30 días

                // TODO: Procesar rotación de imágenes si esa lógica se mantiene
                // if (isset($_POST['photo_name']) && isset($_POST['optImgage'])) {
                //     foreach ($_POST['photo_name'] as $photo => $name) {
                //         if(isset($_POST['optImgage'][$photo]['rotation'])) {
                //             Images::rotateImage($name, $_POST['optImgage'][$photo]['rotation']);
                //         }
                //     }
                // }

                $datos_ad['ID_user'] = $id_user;
                $datos_ad['date_ad'] = time();
                // TODO: Revisar si la moderación aplica igual para todos los planes/usuarios
                // if (getConfParam('REVIEW_ITEM') == 1) {
                //     $datos_ad['review'] = 1; // Pendiente de revisión
                // } else {
                $datos_ad['review'] = 0; // Publicado directamente
                // }
                // TODO: Añadir campo para el plan seleccionado
                $datos_ad['plan_type'] = $plan_seleccionado;
                // TODO: Añadir campo para marcar si tiene extras activos (o guardar detalles en JSON/tabla)
                $datos_ad['has_extras'] = !empty($extras_seleccionados) ? 1 : 0;


                // --- Validación de campos obligatorios del NUEVO formulario ---
                if (
                    !empty($datos_ad['ID_cat']) &&
                    !empty($datos_ad['ID_region']) &&
                    !empty($datos_ad['title']) && mb_strlen($datos_ad['title']) >= 10 && mb_strlen($datos_ad['title']) <= 50 && // Añadir validación longitud
                    !empty($datos_ad['texto']) && mb_strlen($datos_ad['texto']) >= 30 && mb_strlen($datos_ad['texto']) <= 500 && // Añadir validación longitud
                    !empty($nombre_contacto) &&
                    !empty($telefono) && strlen($telefono) >= 9 && // Validar longitud mínima teléfono
                    !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && // Validar formato email
                    isset($_POST['terminos']) && $_POST['terminos'] == '1' && // Validar términos
                    $id_user // Asegurarse de tener un ID de usuario
                    // TODO: Añadir más validaciones si son necesarias (ej: al menos un servicio, horario válido, etc.)
                ) {
                    // Insertar el anuncio
                    $insert = insertSQL("sc_ad", $datos_ad);
                    if ($insert) {
                        $last_ad = lastIdSQL();

                        // TODO: Asociar las imágenes subidas al anuncio recién creado
                        //       La lógica dependerá de cómo manejes las subidas temporales.
                        //       Asumiendo que 'photo_name' contiene los nombres de archivo temporales subidos vía AJAX.
                        // Dentro de if ($insert) { ... }

                        if (isset($_POST['photo_name']) && is_array($_POST['photo_name'])) {
                            // $foto_principal_index = isset($_POST['foto_principal']) ? (int)$_POST['foto_principal'] : 0; // Ya no necesitamos el índice si usamos position=0
                            $position = 0; // La primera foto (índice 0 del array recibido) será la principal (position=0)

                            define('FINAL_UPLOAD_DIR', __DIR__ . '/uploads/ads/'); // Ruta base para anuncios finales
                            define('TEMP_UPLOAD_DIR_REL', __DIR__ . '/uploads/temp/'); // Ruta temporal para mover desde

                            $ad_final_dir = FINAL_UPLOAD_DIR . $last_ad . '/';

                            // Crear directorio final para el anuncio si no existe
                            if (!is_dir($ad_final_dir)) {
                                if (!mkdir($ad_final_dir, 0755, true)) {
                                    error_log("Error creando directorio final para AD ID: " . $last_ad);
                                    // Considerar qué hacer aquí, ¿continuar sin mover o detener?
                                }
                            }

                            foreach ($_POST['photo_name'] as $name_image) {
                                $name_image = basename(filter_var($name_image, FILTER_SANITIZE_STRING)); // ¡Sanitizar!
                                if (empty($name_image)) continue;

                                $temp_path = TEMP_UPLOAD_DIR_REL . $name_image;
                                $final_path = $ad_final_dir . $name_image;

                                // 1. Mover primero el archivo físico
                                $moved = false;
                                if (file_exists($temp_path) && is_dir($ad_final_dir) && is_writable($ad_final_dir)) {
                                    if (rename($temp_path, $final_path)) {
                                        $moved = true;
                                    } else {
                                        error_log("Error moviendo imagen de temp a final: $temp_path -> $final_path para AD ID: $last_ad");
                                    }
                                } else {
                                    error_log("Error: Origen no existe ($temp_path) o destino no es directorio/escribible ($ad_final_dir) para AD ID: $last_ad");
                                }

                                // 2. Si se movió correctamente, actualizar la BD
                                if ($moved) {
                                    // La columna 'edit' no parece tener un propósito claro aquí, usamos 0
                                    // La columna 'is_main' no existe, usamos position = 0 para la principal
                                    $update_result = updateSQL(
                                        "sc_images",
                                        $data = array(
                                            'ID_ad' => $last_ad,       // Asociar al anuncio
                                            'position' => $position,    // Guardar el orden (0 es principal)
                                            "status" => 1,            // Marcar como activa
                                            // 'edit' => 0, // Opcional si necesitas actualizar este campo
                                        ),
                                        $wa = array(
                                            'name_image' => $name_image, // Identificar la imagen
                                            'ID_ad'      => null        // Asegurarse de que es una temporal (coincidir con insert)
                                            // TODO: Añadir 'ID_user_uploader' => $id_user si es relevante y se guardó
                                        )
                                    );

                                    if ($update_result) {
                                        echo "Imagen $name_image asociada y movida correctamente (Posición: $position).<br>"; // Log mejorado
                                        $position++; // Incrementar para la siguiente imagen
                                    } else {
                                        // El archivo se movió pero la BD falló. ¡INCONSISTENCIA!
                                        // Intentar mover de vuelta a temp? O registrar el error claramente.
                                        error_log("¡ERROR CRÍTICO! Imagen movida ($final_path) pero fallo al actualizar BD para AD ID: $last_ad. Imagen: $name_image");
                                        // Quizás intentar borrar el archivo final: unlink($final_path);
                                        echo "<div class='alerta error'>Error crítico al asociar la imagen $name_image en la base de datos después de moverla. Contacta soporte.</div>";
                                    }
                                } else {
                                    // No se pudo mover el archivo, no intentar actualizar la BD para esta imagen
                                    echo "<div class='alerta error'>Error al procesar/mover la imagen $name_image. No se asociará.</div>";
                                }
                            } // end foreach

                            // Limpiar imágenes temporales NO usadas si es necesario (opcional, mejor un cron job)

                        } // end if isset($_POST['photo_name'])

                        // TODO: Actualizar estadísticas si se mantiene esa lógica
                        // Statistic::addAnuncioNuevo(); // O Premium si aplica

                        // TODO: Enviar notificaciones por email si aplica
                        // if (!isset($_POST['notifications'])) { // Si el usuario NO quiere recibir notificaciones de contacto
                        //      mailAdNotNotification($last_ad); // Informar al admin?
                        // }
                        // mailNewAd($last_ad); // Notificar al admin sobre nuevo anuncio

                        // TODO: Redirigir a una página de éxito.
                        //       Si hay pago pendiente (extras/plan), redirigir a la pasarela de pago.
                        if (!empty($extras_seleccionados) /* || $plan_seleccionado == 'silver' || $plan_seleccionado == 'gold' */) {
                            // Redirigir a pago, pasando ID del anuncio ($last_ad)
                            // header('Location: /pago?ad_id=' . $last_ad);
                            echo '<script type="text/javascript">location.href = "/publicado?id=' . $last_ad . '&pago=pendiente";</script>'; // Ejemplo redirección JS
                        } else {
                            // Redirigir a "anuncio publicado"
                            // header('Location: /publicado?id=' . $last_ad);
                            echo '<script type="text/javascript">location.href = "/publicado?id=' . $last_ad . '";</script>'; // Ejemplo redirección JS
                        }
                        exit(); // Detener script después de redirigir

                    } else {
                        // Error insertando anuncio
                        $error_insert = true;
                        echo '<div class="alerta error">Error al guardar el anuncio en la base de datos.</div>';
                    }
                } else {
                    // Error de validación de campos
                    $error_insert = true;
                    echo '<div class="alerta error">Faltan campos obligatorios o algunos datos no son válidos. Por favor, revisa el formulario.</div>';
                    // TODO: Sería ideal resaltar los campos con error en el formulario.
                }
            } else {
                // Límite de anuncios alcanzado para el plan actual
                $error_insert = true;
                echo '<div class="alerta error">Has alcanzado el límite de anuncios para tu plan actual.</div>';
            }
        } else {
            // Error de Token CSRF
            echo '<div class="alerta error">Error de seguridad al procesar el formulario. Recarga la página e inténtalo de nuevo.</div>';
        }
    } else {
        // Error de reCAPTCHA
        echo '<div class="alerta error" id="error_recaptcha">Verificación reCAPTCHA fallida. Inténtalo de nuevo.</div><br>';
    }
}
// --- FIN PROCESAMIENTO DEL FORMULARIO (POST) ---
?>

<!-- Incluir JS de reCAPTCHA v3 -->
<script src='https://www.google.com/recaptcha/api.js?render=<?php echo SITE_KEY; // Asegúrate que SITE_KEY esté definida 
                                                            ?>'></script>

<!-- TODO: Revisar si estos diálogos siguen siendo necesarios o se integran en el flujo -->
<?php /* if (!user::checkLogin() && check_ip()): ?>
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
<?php endif */ ?>

<!-- TODO: Eliminar o adaptar estos enlaces de ayuda si es necesario -->
<ul class="ayuda-post">
    <li class="ayuda-post-item"><a target="_blank" href="/ayuda/">Normas de <b>publicación</b></a></li>
    <li class="ayuda-post-item"><a target="_blank" href="/ayuda/">Ayuda</a></li>
    <li class="ayuda-post-item"><a target="_blank" href="https://www.solomasajistas.com/blog/">Visita nuestro blog</a></li>
</ul>

<h1 class="titulo-principal">Publica tu Anuncio</h1>

<div class="contenedor-formulario">

    <?php // Mostrar errores generales si ocurrieron en el procesamiento POST y no se redirigió
    if (isset($error_insert) && $error_insert) {
        echo '<div class="alerta error">Hubo un problema al procesar tu solicitud. Revisa los mensajes y el formulario.</div>';
    }
    if (isset($user_registered) && $user_registered) {
        echo '<div class="alerta info">Parece que ya tienes una cuenta con ese número de teléfono. <a href="/login">Inicia sesión</a>.</div>';
    }
    ?>

    <!-- Formulario principal -->
    <form id="form-nuevo-anuncio" class="formulario-multi-etapa" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data" autocomplete="off">

        <?php // Generar Token CSRF
        $token_q = generateFormToken('nuevoAnuncioToken');
        ?>
        <input type="hidden" name="token" id="token" value="<?= $token_q; ?>">
        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" />
        <!-- TODO: Mantener el input order si la lógica de pago previo sigue existiendo -->
        <!-- <input type="hidden" id="new_order" name="order" value="0" /> -->

        <!-- ======================= ETAPA 0: TIPO DE USUARIO (Solo si no está logueado) ======================= -->
        <?php if (!checkSession()): // Asume que checkSession() devuelve true si está logueado 
        ?>
            <div id="etapa-tipo-usuario" class="etapa activa">
                <h2 class="titulo-etapa">Paso 1: Elige tu tipo de perfil</h2>
                <p>Selecciona cómo quieres usar la plataforma.</p>

                <div class="lista-opciones grupo-radios">
                    <label class="opcion-radio">
                        <input type="radio" name="tipo_usuario" value="masajista" required>
                        <div class="opcion-contenido">
                            <strong>Masajista Particular</strong>
                            <span>Crea tu perfil individual para ofrecer tus servicios.</span>
                        </div>
                    </label>
                    <label class="opcion-radio">
                        <input type="radio" name="tipo_usuario" value="centro">
                        <div class="opcion-contenido">
                            <strong>Centro de Masajes</strong>
                            <span>Gestiona varios perfiles de masajistas de tu centro.</span>
                        </div>
                    </label>
                    <label class="opcion-radio">
                        <input type="radio" name="tipo_usuario" value="publicista">
                        <div class="opcion-contenido">
                            <strong>Publicista</strong>
                            <span>Promociona productos o servicios relacionados.</span>
                        </div>
                    </label>
                    <label class="opcion-radio">
                        <input type="radio" name="tipo_usuario" value="visitante">
                        <div class="opcion-contenido">
                            <strong>Visitante</strong>
                            <span>Guarda perfiles favoritos y contacta fácilmente. (No publica anuncios)</span>
                        </div>
                    </label>
                </div>
                <div class="error-msg oculto" id="error-tipo-usuario">Debes seleccionar un tipo de usuario.</div>

                <div class="navegacion-etapa">
                    <button type="button" class="frm-boton btn-siguiente">Siguiente</button>
                </div>
            </div>
        <?php endif; ?>

        <!-- ======================= ETAPA 1: ELECCIÓN DE PLAN ======================= -->
        <div id="etapa-plan" class="etapa <?php echo checkSession() ? 'activa' : 'oculto'; // Activa si ya está logueado 
                                            ?>">
            <h2 class="titulo-etapa"><?php echo checkSession() ? 'Paso 1' : 'Paso 2'; ?>: Elige tu Plan</h2>
            <p>Selecciona el plan que mejor se adapte a tus necesidades.</p>

            <div class="lista-opciones grupo-radios-plan">
                <label class="opcion-radio opcion-plan">
                    <input type="radio" name="plan" value="gratis" required checked> <!-- Marcado por defecto -->
                    <div class="opcion-contenido">
                        <strong>Plan Gratis</strong>
                        <span>Prueba gratuita de 30 días.</span>
                        <span>Renovación manual de anuncios cada 24 horas.</span>
                        <span class="precio-plan">0 €</span>
                    </div>
                </label>
                <label class="opcion-radio opcion-plan">
                    <input type="radio" name="plan" value="silver">
                    <div class="opcion-contenido">
                        <strong>Plan Silver</strong>
                        <span>Visibilidad mejorada por 60 días.</span>
                        <span>Renovación automática de anuncios cada 12 horas.</span>
                        <span class="precio-plan">12 €</span>
                    </div>
                </label>
                <label class="opcion-radio opcion-plan">
                    <input type="radio" name="plan" value="gold">
                    <div class="opcion-contenido">
                        <strong>Plan Gold</strong>
                        <span>Máxima visibilidad por 90 días.</span>
                        <span>Renovación automática de anuncios cada 12 horas.</span>
                        <!-- TODO: Añadir beneficios extra si los hay -->
                        <span class="precio-plan">30 €</span>
                    </div>
                </label>
            </div>
            <div class="error-msg oculto" id="error-plan">Debes seleccionar un plan.</div>

            <div class="navegacion-etapa">
                <?php if (!checkSession()): ?>
                    <button type="button" class="frm-boton btn-anterior">Anterior</button>
                <?php endif; ?>
                <button type="button" class="frm-boton btn-siguiente">Siguiente</button>
            </div>
        </div>

        <!-- ======================= ETAPA 2: DATOS DEL PERFIL/ANUNCIO ======================= -->
        <div id="etapa-perfil" class="etapa oculto">
            <h2 class="titulo-etapa"><?php echo checkSession() ? 'Paso 2' : 'Paso 3'; ?>: Completa tu Anuncio</h2>

            <fieldset class="frm-seccion">
                <legend>Información Básica</legend>

                <div class="frm-grupo">
                    <label for="nombre" class="frm-etiqueta">Tu Nombre o Alias *</label>
                    <input type="text" name="nombre" id="nombre" class="frm-campo" required maxlength="50" value="<?php echo isset($_SESSION['data']['name']) ? htmlspecialchars($_SESSION['data']['name']) : ''; ?>">
                    <div class="error-msg oculto" id="error-nombre">El nombre es obligatorio.</div>
                </div>

                <div class="frm-grupo">
                    <label for="categoria" class="frm-etiqueta">Categoría Principal *</label>
                    <select name="categoria" id="categoria" class="frm-campo frm-select" required>
                        <option value="">-- Selecciona una categoría --</option>
                        <option value="1">Masajes Terapéuticos</option>
                        <option value="2">Masajista Erótica</option>
                        <option value="3">Masajista Hetero/Gay</option>
                        <option value="4">Bolsa de Empleo</option>
                        <!-- <option value="99">Otros</option> --> <?php // Añadir si es necesario 
                                                                    ?>
                    </select>
                    <div class="error-msg oculto" id="error-categoria">Debes seleccionar una categoría.</div>
                </div>

                <div class="frm-grupo">
                    <label for="provincia" class="frm-etiqueta">Provincia *</label>
                    <select name="provincia" id="provincia" class="frm-campo frm-select" required>
                        <option value="">-- Selecciona una provincia --</option>
                        <?php
                        // Reutilizar la carga de provincias del código original
                        $provincias = selectSQL("sc_region", [], "name ASC"); // Asume que la tabla es sc_region
                        foreach ($provincias as $prov) {
                            echo '<option value="' . $prov['ID_region'] . '">' . htmlspecialchars($prov['name']) . '</option>';
                        }
                        ?>
                    </select>
                    <div class="error-msg oculto" id="error-provincia">Debes seleccionar una provincia.</div>
                </div>

                <div class="frm-grupo">
                    <label for="ciudad" class="frm-etiqueta">Ciudad / Zona (Opcional)</label>
                    <input type="text" name="ciudad" id="ciudad" class="frm-campo" maxlength="100" placeholder="Ej: Centro, Nervión, etc.">
                </div>

            </fieldset>

            <fieldset class="frm-seccion">
                <legend>Detalles del Anuncio</legend>

                <div class="frm-grupo">
                    <label for="titulo_anuncio" class="frm-etiqueta">Título del Anuncio *</label>
                    <input type="text" name="titulo_anuncio" id="titulo_anuncio" class="frm-campo" required minlength="10" maxlength="50" placeholder="Ej: Masajista Profesional en Madrid Centro">
                    <div class="contador-caracteres">Caracteres: <span id="cont-titulo">0</span> (min 10 / máx 50)</div>
                    <div class="error-msg oculto" id="error-titulo">El título es obligatorio (entre 10 y 50 caracteres).</div>
                    <div class="error-msg oculto" id="error-titulo-palabras">El título contiene palabras no permitidas.</div> <!-- TODO: Añadir validación JS/PHP -->
                </div>

                <div class="frm-grupo">
                    <label for="descripcion" class="frm-etiqueta">Descripción del Anuncio *</label>
                    <textarea name="descripcion" id="descripcion" class="frm-campo frm-textarea" rows="6" required minlength="30" maxlength="500" placeholder="Describe tus servicios, experiencia, ambiente, etc."></textarea>
                    <div class="contador-caracteres">Caracteres: <span id="cont-desc">0</span> (min 30 / máx 500)</div>
                    <div class="error-msg oculto" id="error-descripcion">La descripción es obligatoria (entre 30 y 500 caracteres).</div>
                    <div class="error-msg oculto" id="error-desc-palabras">La descripción contiene palabras no permitidas.</div> <!-- TODO: Añadir validación JS/PHP -->
                </div>

                <div class="frm-grupo">
                    <label class="frm-etiqueta">Servicios Ofrecidos *</label>
                    <div class="grupo-checkboxes">
                        <?php
                        $servicios = ["Masaje relajante", "Masaje deportivo", "Masaje podal", "Masaje antiestrés", "Masaje linfático", "Masaje shiatsu", "Masaje descontracturante", "Masaje ayurvédico", "Masaje circulatorio", "Masaje tailandés"];
                        foreach ($servicios as $servicio) {
                            $valor = strtolower(str_replace(' ', '_', $servicio)); // Genera un valor simple
                            echo '<label class="frm-checkbox"><input type="checkbox" name="servicios[]" value="' . htmlspecialchars($valor) . '"> ' . htmlspecialchars($servicio) . '</label>';
                        }
                        ?>
                    </div>
                    <div class="error-msg oculto" id="error-servicios">Debes seleccionar al menos un servicio.</div>
                </div>

            </fieldset>

            <fieldset class="frm-seccion">
                <legend>Fotografías</legend>
                <div class="frm-grupo">
                    <label class="frm-etiqueta">Sube tus fotos (hasta <?= htmlspecialchars($DATAJSON['max_photos'] ?? 3) ?>)</label>
                    <div class="ayuda-texto">Puedes arrastrar y soltar las imágenes. Tamaño máx. 2MB (JPG, PNG).</div>
                    <!-- Reutilizar la estructura de subida de fotos del código original, adaptando clases -->
                    <div class="subida-fotos-contenedor">
                        <div id="boton-subir-foto" class="boton-subir">
                            <span>Haz click o arrastra para subir</span>
                            <input type="file" name="fotos_anuncio" id="campo-subir-foto" multiple accept="image/jpeg, image/png" style="display: none;"> <!-- Ocultar input real -->
                        </div>
                        <div id="lista-fotos-subidas" class="lista-fotos sortable">
                            <!-- Las previsualizaciones de las fotos se añadirán aquí vía JS -->
                        </div>
                        <input type="hidden" name="foto_principal" id="foto_principal_input" value="0"> <!-- Input para saber índice de la foto principal -->
                    </div>
                    <div class="error-msg oculto" id="error-fotos">Debes subir al menos una foto. Selecciona cuál será la principal haciendo click en ella.</div>
                </div>
            </fieldset>

            <fieldset class="frm-seccion">
                <legend>Disponibilidad y Contacto</legend>

                <div class="frm-grupo">
                    <label class="frm-etiqueta">Horario Detallado *</label>
                    <div class="ayuda-texto">Marca los días que estás disponible y selecciona tu horario.</div>
                    <div class="horario-semanal">
                        <?php
                        $dias = ['lunes' => 'Lunes', 'martes' => 'Martes', 'miercoles' => 'Miércoles', 'jueves' => 'Jueves', 'viernes' => 'Viernes', 'sabado' => 'Sábado', 'domingo' => 'Domingo'];
                        foreach ($dias as $key => $nombre) {
                        ?>
                            <div class="dia-horario" id="horario-<?= $key ?>">
                                <label class="frm-checkbox check-dia">
                                    <input type="checkbox" name="horario_dia[<?= $key ?>][activo]" value="1"> <?= $nombre ?>
                                </label>
                                <div class="horas-dia oculto">
                                    <label>De:</label>
                                    <select name="horario_dia[<?= $key ?>][inicio]" class="frm-campo frm-select corto">
                                        <?php // Generar opciones de hora
                                        for ($h = 0; $h < 24; $h++) {
                                            $hora = sprintf('%02d', $h);
                                            echo "<option value='{$hora}:00'>{$hora}:00</option>";
                                            echo "<option value='{$hora}:30'>{$hora}:30</option>";
                                        }
                                        ?>
                                    </select>
                                    <label>A:</label>
                                    <select name="horario_dia[<?= $key ?>][fin]" class="frm-campo frm-select corto">
                                        <?php // Generar opciones de hora
                                        for ($h = 0; $h < 24; $h++) {
                                            $hora = sprintf('%02d', $h);
                                            $selected = ($hora == 23) ? 'selected' : ''; // Preseleccionar última hora
                                            echo "<option value='{$hora}:00'>{$hora}:00</option>";
                                            echo "<option value='{$hora}:30' $selected>{$hora}:30</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="error-msg oculto" id="error-horario">Debes marcar al menos un día y configurar su horario.</div>
                </div>

                <div class="frm-grupo">
                    <label for="telefono" class="frm-etiqueta">Teléfono de Contacto *</label>
                    <div class="grupo-telefono">
                        <input type="tel" name="telefono" id="telefono" class="frm-campo" required pattern="[0-9]{9,15}" placeholder="Ej: 612345678" value="<?php echo isset($_SESSION['data']['phone']) ? htmlspecialchars($_SESSION['data']['phone']) : ''; ?>">
                        <label class="frm-checkbox check-whatsapp">
                            <input type="checkbox" name="tiene_whatsapp" value="1" <?php echo (isset($_SESSION['data']['whatsapp']) && $_SESSION['data']['whatsapp'] == 1) ? 'checked' : ''; ?>> ¿Tienes WhatsApp?
                        </label>
                    </div>
                    <div class="error-msg oculto" id="error-telefono">Introduce un teléfono válido (solo números, 9-15 dígitos).</div>
                </div>

                <div class="frm-grupo">
                    <label class="frm-etiqueta">Idiomas que Hablas (Opcional)</label>
                    <div class="grupo-idiomas">
                        <div class="par-idioma">
                            <select name="idioma_1" class="frm-campo frm-select">
                                <option value="">-- Idioma 1 --</option>
                                <?php // TODO: Cargar lista de idiomas (ej: de constantes o BD)
                                echo '<option value="es">Español</option><option value="en">Inglés</option><option value="fr">Francés</option>';
                                ?>
                            </select>
                            <select name="nivel_idioma_1" class="frm-campo frm-select">
                                <option value="">-- Nivel --</option>
                                <option value="basico">Básico</option>
                                <option value="intermedio">Intermedio</option>
                                <option value="avanzado">Avanzado</option>
                                <option value="nativo">Nativo</option>
                            </select>
                        </div>
                        <div class="par-idioma">
                            <select name="idioma_2" class="frm-campo frm-select">
                                <option value="">-- Idioma 2 --</option>
                                <?php // TODO: Cargar lista de idiomas
                                echo '<option value="es">Español</option><option value="en">Inglés</option><option value="fr">Francés</option><option value="de">Alemán</option>';
                                ?>
                            </select>
                            <select name="nivel_idioma_2" class="frm-campo frm-select">
                                <option value="">-- Nivel --</option>
                                <option value="basico">Básico</option>
                                <option value="intermedio">Intermedio</option>
                                <option value="avanzado">Avanzado</option>
                                <option value="nativo">Nativo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="frm-grupo">
                    <label for="realiza_salidas" class="frm-etiqueta">¿Realizas salidas a domicilio/hotel? *</label>
                    <select name="realiza_salidas" id="realiza_salidas" class="frm-campo frm-select" required>
                        <option value="0">No</option>
                        <option value="1">Sí</option>
                    </select>
                    <div class="error-msg oculto" id="error-salidas">Debes indicar si realizas salidas.</div>
                </div>

                <?php // Campo de email solo si no está logueado 
                ?>
                <?php if (!checkSession()): ?>
                    <div class="frm-grupo">
                        <label for="email" class="frm-etiqueta">Tu Email de Contacto *</label>
                        <input type="email" name="email" id="email" class="frm-campo" required placeholder="Necesario para gestionar tu anuncio">
                        <div class="ayuda-texto">Si ya tienes cuenta, usa el mismo email. Si no, crearemos una cuenta para ti.</div>
                        <div class="error-msg oculto" id="error-email">Introduce un email válido.</div>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($_SESSION['data']['mail']); ?>">
                    <div class="frm-grupo">
                        <label class="frm-etiqueta">Email de Contacto:</label>
                        <span class="texto-fijo"><?php echo htmlspecialchars($_SESSION['data']['mail']); ?></span>
                    </div>
                <?php endif; ?>

            </fieldset>

            <div class="navegacion-etapa">
                <button type="button" class="frm-boton btn-anterior">Anterior</button>
                <button type="button" class="frm-boton btn-siguiente">Siguiente (Extras Opcionales)</button>
                <!-- <button type="submit" class="frm-boton btn-publicar">Publicar Anuncio Gratis</button> --> <!-- Opción alternativa si no quieren extras -->
            </div>
        </div>

        <!-- ======================= ETAPA 3: EXTRAS OPCIONALES ======================= -->
        <div id="etapa-extras" class="etapa oculto">
            <h2 class="titulo-etapa"><?php echo checkSession() ? 'Paso 3' : 'Paso 4'; ?>: Destaca tu Anuncio (Opcional)</h2>
            <p>Aumenta la visibilidad de tu anuncio con nuestros servicios extra.</p>

            <div class="lista-opciones grupo-checkboxes-extra">
                <label class="opcion-checkbox opcion-extra">
                    <input type="checkbox" name="extras[]" value="premium">
                    <div class="opcion-contenido">
                        <strong>Premium (35 €)</strong>
                        <span>Tu anuncio aparecerá aleatoriamente en las posiciones superiores.</span>
                    </div>
                </label>
                <label class="opcion-checkbox opcion-extra">
                    <input type="checkbox" name="extras[]" value="premium_mini">
                    <div class="opcion-contenido">
                        <strong>Premium Mini (27 €)</strong>
                        <span>Tu anuncio aparecerá aleatoriamente bajo los Premium.</span>
                    </div>
                </label>
                <label class="opcion-checkbox opcion-extra">
                    <input type="checkbox" name="extras[]" value="destacado">
                    <div class="opcion-contenido">
                        <strong>Destacado (20 €)</strong>
                        <span>Tu anuncio aparecerá aleatoriamente con un diseño destacado.</span>
                    </div>
                </label>
                <label class="opcion-checkbox opcion-extra">
                    <input type="checkbox" name="extras[]" value="autosubida">
                    <div class="opcion-contenido">
                        <strong>Autosubida (25 €)</strong>
                        <span>Tu anuncio subirá posiciones automáticamente (debajo de Destacados).</span>
                    </div>
                </label>
                <label class="opcion-checkbox opcion-extra">
                    <input type="checkbox" name="extras[]" value="banner_superior">
                    <div class="opcion-contenido">
                        <strong>Banner Superior (50 €)</strong>
                        <span>Muestra tu banner aleatoriamente en la cabecera de la página.</span>
                        <!-- TODO: Añadir campo para subir imagen del banner si se selecciona -->
                    </div>
                </label>
                <label class="opcion-checkbox opcion-extra">
                    <input type="checkbox" name="extras[]" value="banner_lateral">
                    <div class="opcion-contenido">
                        <strong>Banner Lateral (50 €)</strong>
                        <span>Muestra tu banner aleatoriamente en la barra lateral.</span>
                        <!-- TODO: Añadir campo para subir imagen del banner si se selecciona -->
                    </div>
                </label>
            </div>

            <fieldset class="frm-seccion terminos-finales">
                <div class="frm-grupo">
                    <label class="frm-checkbox">
                        <input name="terminos" type="checkbox" id="terminos" value="1" required />
                        He leído y acepto los <a href="/terminos-y-condiciones" target="_blank">Términos y Condiciones</a> y la <a href="/politica-privacidad" target="_blank">Política de Privacidad</a>. *
                    </label>
                    <div class="error-msg oculto" id="error-terminos">Debes aceptar los términos y condiciones.</div>
                </div>

                <div class="frm-grupo">
                    <label class="frm-checkbox">
                        <input name="notifications" type="checkbox" id="notifications" value="1" checked /> <!-- Marcado por defecto -->
                        Quiero recibir notificaciones por email cuando alguien contacte a través de mi anuncio.
                    </label>
                </div>
            </fieldset>

            <div class="navegacion-etapa">
                <button type="button" class="frm-boton btn-anterior">Anterior</button>
                <!-- El texto del botón final cambiará con JS dependiendo si se eligen extras -->
                <button type="submit" id="btn-finalizar" class="frm-boton btn-publicar">Finalizar y Publicar Gratis</button>
            </div>
        </div>

    </form>
</div>

<!-- ======================= BLOQUES ADICIONALES (Revisar si son necesarios) ======================= -->
<?php
// loadBlock('post-warning'); // ¿Qué hace este bloque? Adaptar o eliminar.

// TODO: La lógica de mostrar el diálogo de pago debe integrarse con la selección de Plan y Extras.
// if ($limite == 1) $ANUNCIO_NUEVO_PREMIUM = true; // Lógica obsoleta?
// loadBlock('payment_dialog'); // Adaptar para el nuevo flujo de pago

// loadBlock('editor'); // ¿Se usa algún editor WYSIWYG? Si no, eliminar.
?>

<!-- ======================= DIÁLOGOS DE LÍMITE (Revisar y adaptar lógica) ======================= -->
<?php /* if (isset($_SESSION['data']['ID_user']) && $limite == 2): ?>
    <dialog class="dialog" open>
        <div class="dialog-modal">
            <a href="/index.php" style="color: black;"><i class="fa-times-circle fa"></i></a>
            <p>Haz alcanzado el límite de anuncios publicados para tu plan.</p>
            <button onclick="location.href='/planes'" class="payment-btn">Ver Planes</button> <!-- Ejemplo -->
        </div>
    </dialog>
<?php endif */ ?>

<?php /* if (isset($_SESSION['data']['ID_user']) && $limite == 0 && checkLastDelete($_SESSION['data']['ID_user'])): // Lógica específica de borrado reciente ?>
    <dialog class="dialog" open>
        <div class="dialog-modal">
             <a href="/index.php" style="color: black;"><i class="fa-times-circle fa"></i></a>
            <p>Aún no puedes publicar un anuncio <b>Gratis</b> debido a una publicación reciente.</p>
            <p>Intenta más tarde o elige un plan de pago.</p>
            <button onclick="location.href='/planes'" class="payment-btn">Ver Planes</button> <!-- Ejemplo -->
        </div>
    </dialog>
<?php endif */ ?>

<!-- ======================= SCRIPTS JS ======================= -->
<!-- Incluir jQuery si no está ya incluido globalmente -->
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

<!-- Incluir Select2 si se sigue usando -->
<!-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> -->
<!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> -->

<!-- Incluir jQuery UI Sortable si se usa para ordenar fotos -->
<!-- <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script> -->

<!-- Scripts personalizados -->
<!-- <script src="<?= getConfParam('SITE_URL') ?>src/js/filter.js"></script> --> <?php // ¿Necesario aquí? 
                                                                                    ?>
<!-- <script src="<?= getConfParam('SITE_URL') ?>src/js/post.js"></script> --> <?php // El JS de post.js necesitará una REESCRITURA COMPLETA 
                                                                                ?>

<script>
    // --- Lógica básica para Navegación entre Etapas y Validación Simple ---
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('form-nuevo-anuncio');
        const etapas = form.querySelectorAll('.etapa');
        const btnSiguiente = form.querySelectorAll('.btn-siguiente');
        const btnAnterior = form.querySelectorAll('.btn-anterior');
        const btnFinalizar = document.getElementById('btn-finalizar');
        let etapaActual = 0;

        function mostrarEtapa(indice) {
            etapas.forEach((etapa, i) => {
                etapa.classList.remove('activa');
                etapa.classList.add('oculto');
                if (i === indice) {
                    etapa.classList.add('activa');
                    etapa.classList.remove('oculto');
                }
            });
            etapaActual = indice;
            window.scrollTo(0, 0); // Subir al inicio de la página
        }

        function validarEtapa(indice) {
            const etapa = etapas[indice];
            let esValida = true;
            // Ocultar errores previos
            etapa.querySelectorAll('.error-msg').forEach(msg => msg.classList.add('oculto'));

            // --- Validación Etapa 0: Tipo Usuario ---
            if (etapa.id === 'etapa-tipo-usuario') {
                const tipoUsuario = form.querySelector('input[name="tipo_usuario"]:checked');
                if (!tipoUsuario) {
                    document.getElementById('error-tipo-usuario').classList.remove('oculto');
                    esValida = false;
                } else if (tipoUsuario.value === 'visitante') {
                    // TODO: Si elige visitante, ¿qué pasa? ¿Se salta el resto? Redirigir?
                    alert("La opción Visitante no permite publicar anuncios. Serás redirigido...");
                    // window.location.href = '/'; // O a donde corresponda
                    esValida = false; // Prevenir seguir
                }
            }

            // --- Validación Etapa 1: Plan ---
            if (etapa.id === 'etapa-plan') {
                const plan = form.querySelector('input[name="plan"]:checked');
                if (!plan) {
                    document.getElementById('error-plan').classList.remove('oculto');
                    esValida = false;
                }
            }

            // --- Validación Etapa 2: Perfil/Anuncio (Más compleja) ---
            if (etapa.id === 'etapa-perfil') {
                const camposObligatorios = etapa.querySelectorAll('[required]');
                camposObligatorios.forEach(campo => {
                    let errorId = 'error-' + campo.id;
                    let errorElement = document.getElementById(errorId);
                    let valido = true;

                    if (campo.type === 'checkbox' && !campo.checked) {
                        valido = false;
                    } else if (campo.tagName === 'SELECT' && campo.value === '') {
                        valido = false;
                    } else if (campo.type !== 'checkbox' && campo.tagName !== 'SELECT' && campo.value.trim() === '') {
                        valido = false;
                    }
                    // Validaciones específicas de longitud/patrón
                    if (valido && campo.minLength > 0 && campo.value.trim().length < campo.minLength) valido = false;
                    if (valido && campo.maxLength > 0 && campo.value.trim().length > campo.maxLength) valido = false;
                    if (valido && campo.pattern && !new RegExp(campo.pattern).test(campo.value)) valido = false;

                    if (!valido && errorElement) {
                        errorElement.classList.remove('oculto');
                        esValida = false;
                    }
                });

                // Validación Servicios (al menos uno)
                if (!etapa.querySelector('input[name="servicios[]"]:checked')) {
                    document.getElementById('error-servicios').classList.remove('oculto');
                    esValida = false;
                }
                // Validación Horario (al menos un día activo)
                if (!etapa.querySelector('.check-dia input:checked')) {
                    document.getElementById('error-horario').classList.remove('oculto');
                    esValida = false;
                }
                // Validación Fotos (si la lógica JS de subida lo requiere)
                // if (document.getElementById('lista-fotos-subidas').children.length === 0) {
                //      document.getElementById('error-fotos').classList.remove('oculto');
                //      esValida = false;
                // }
                // TODO: Añadir validación de palabras prohibidas si es necesario (requiere JS más complejo o AJAX)
            }

            // --- Validación Etapa 3: Extras (Solo términos) ---
            if (etapa.id === 'etapa-extras') {
                const terminos = document.getElementById('terminos');
                if (!terminos.checked) {
                    document.getElementById('error-terminos').classList.remove('oculto');
                    esValida = false;
                }
            }


            return esValida;
        }

        btnSiguiente.forEach(boton => {
            boton.addEventListener('click', () => {
                if (validarEtapa(etapaActual)) {
                    mostrarEtapa(etapaActual + 1);
                }
            });
        });

        btnAnterior.forEach(boton => {
            boton.addEventListener('click', () => {
                mostrarEtapa(etapaActual - 1);
            });
        });

        // --- Lógica para el botón Finalizar ---
        const extrasCheckboxes = form.querySelectorAll('#etapa-extras input[name="extras[]"]');
        extrasCheckboxes.forEach(check => {
            check.addEventListener('change', actualizarBotonFinalizar);
        });

        function actualizarBotonFinalizar() {
            const algunExtraPago = Array.from(extrasCheckboxes).some(c => c.checked);
            if (algunExtraPago) {
                btnFinalizar.textContent = 'Continuar al Pago';
                btnFinalizar.classList.remove('btn-publicar');
                btnFinalizar.classList.add('btn-pago'); // Clase diferente para estilo/lógica
            } else {
                btnFinalizar.textContent = 'Finalizar y Publicar Gratis';
                btnFinalizar.classList.add('btn-publicar');
                btnFinalizar.classList.remove('btn-pago');
            }
        }
        actualizarBotonFinalizar(); // Llamar al inicio

        // --- Envío final con reCAPTCHA ---
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevenir envío normal

            // Validar la última etapa antes de enviar
            if (!validarEtapa(etapaActual)) {
                console.error("Fallo de validación en la última etapa.");
                return; // No enviar si la última etapa no es válida
            }

            grecaptcha.ready(function() {
                grecaptcha.execute('<?php echo SITE_KEY; // Asegúrate que SITE_KEY esté definida 
                                    ?>', {
                        action: 'submit_anuncio'
                    }) // Cambiar acción si es necesario
                    .then(function(token) {
                        document.getElementById('g-recaptcha-response').value = token;
                        console.log("reCAPTCHA token obtenido, enviando formulario...");
                        form.submit(); // Enviar el formulario programáticamente
                    })
                    .catch(function(error) {
                        console.error("Error al obtener token reCAPTCHA:", error);
                        alert("Error en la verificación reCAPTCHA. Inténtalo de nuevo.");
                    });
            });
        });

        // --- Lógica Adicional (Ejemplos) ---

        // Contadores de caracteres
        const tituloInput = document.getElementById('titulo_anuncio');
        const descInput = document.getElementById('descripcion');
        const contTitulo = document.getElementById('cont-titulo');
        const contDesc = document.getElementById('cont-desc');

        if (tituloInput && contTitulo) {
            tituloInput.addEventListener('input', () => contTitulo.textContent = tituloInput.value.length);
            contTitulo.textContent = tituloInput.value.length; // Inicial
        }
        if (descInput && contDesc) {
            descInput.addEventListener('input', () => contDesc.textContent = descInput.value.length);
            contDesc.textContent = descInput.value.length; // Inicial
        }

        // Mostrar/ocultar horas del día al marcar checkbox
        const diasHorario = document.querySelectorAll('.dia-horario .check-dia input');
        diasHorario.forEach(check => {
            const horasDiv = check.closest('.dia-horario').querySelector('.horas-dia');
            check.addEventListener('change', () => {
                if (check.checked) {
                    horasDiv.classList.remove('oculto');
                } else {
                    horasDiv.classList.add('oculto');
                }
            });
            // Estado inicial
            if (check.checked) horasDiv.classList.remove('oculto');
        });

        // TODO: Añadir aquí la lógica JS para:
        // 1. Subida de fotos AJAX (más compleja, requiere backend para manejar subidas temporales)
        // 2. Previsualización de fotos
        // 3. Selección de foto principal (ej: añadir clase 'principal' al contenedor de la foto clickeada y actualizar input hidden)
        // 4. Ordenación de fotos (Drag & Drop con SortableJS o jQuery UI)
        // 5. Inicialización de Select2 si se usa.
        // 6. Validación más avanzada (palabras prohibidas, etc.).

        // Ejemplo inicialización Select2 (si se usa)
        // $(document).ready(function() {
        //     $('.frm-select').select2({ width: '100%' });
        // });

    });
</script>

<script>
    // --- Variables y Constantes de Subida de Fotos ---
    const MAX_PHOTOS = <?php echo (int)($DATAJSON['max_photos'] ?? 3); ?>; // Obtener del PHP
    const MAX_FILE_SIZE_BYTES = 2 * 1024 * 1024; // 2MB - ¡AJUSTAR SI ES NECESARIO!
    const UPLOAD_TEMP_URL = 'upload_temp_image.php'; // Ruta al script PHP de subida
    const DELETE_TEMP_URL = 'delete_temp_image.php'; // Ruta al script PHP de borrado

    const fileInput = document.getElementById('campo-subir-foto');
    const dropZone = document.getElementById('boton-subir-foto');
    const photoList = document.getElementById('lista-fotos-subidas');
    const mainPhotoInput = document.getElementById('foto_principal_input');
    const photoErrorDiv = document.getElementById('error-fotos');
    let currentPhotoCount = 0;

    // --- Función para manejar los archivos seleccionados/arrastrados ---
    function handleFiles(files) {
        if (!files || files.length === 0) return;
        photoErrorDiv.classList.add('oculto'); // Ocultar error previo

        Array.from(files).forEach(file => {
            if (currentPhotoCount >= MAX_PHOTOS) {
                alert(`No puedes subir más de ${MAX_PHOTOS} fotos.`);
                return; // Saltar el resto si ya se alcanzó el límite
            }
            if (!['image/jpeg', 'image/png'].includes(file.type)) {
                alert(`El archivo "${file.name}" no es un JPG o PNG válido.`);
                return; // Saltar este archivo
            }
            if (file.size > MAX_FILE_SIZE_BYTES) {
                alert(`El archivo "${file.name}" es demasiado grande (Máx. ${MAX_FILE_SIZE_BYTES / 1024 / 1024} MB).`);
                return; // Saltar este archivo
            }

            uploadFile(file);
            currentPhotoCount++; // Incrementar contador (optimista, se decrementa si falla)
        });
        fileInput.value = ''; // Limpiar el input para permitir seleccionar el mismo archivo de nuevo si se borra
    }

    // --- Función para subir un archivo individual vía AJAX ---
    function uploadFile(file) {
        const formData = new FormData();
        formData.append('file', file); // El backend espera el archivo con la clave 'file'

        // Mostrar algún indicador de carga si se desea (ej. en el dropZone)
        dropZone.querySelector('span').textContent = 'Subiendo...';

        fetch(UPLOAD_TEMP_URL, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addPreview(data.identifier, data.previewUrl);
                } else {
                    alert(`Error al subir "${file.name}": ${data.error || 'Error desconocido'}`);
                    currentPhotoCount--; // Decrementar si la subida falló
                }
            })
            .catch(error => {
                console.error('Error en fetch:', error);
                alert(`Error de red al subir "${file.name}". Inténtalo de nuevo.`);
                currentPhotoCount--; // Decrementar si hubo error de red
            })
            .finally(() => {
                // Restablecer texto del botón cuando termine (o gestione carga por foto)
                if (document.querySelectorAll('.preview-container').length < MAX_PHOTOS) {
                    dropZone.querySelector('span').textContent = 'Haz click o arrastra para subir';
                } else {
                    dropZone.querySelector('span').textContent = `Máximo ${MAX_PHOTOS} fotos alcanzado`;
                }
            });
    }

    // --- Función para añadir la previsualización de la foto ---
    function addPreview(identifier, previewUrl) {
        const previewContainer = document.createElement('div');
        previewContainer.classList.add('preview-container');
        previewContainer.dataset.identifier = identifier; // Guardar identificador

        const img = document.createElement('img');
        img.src = previewUrl;
        img.alt = 'Previsualización';
        img.classList.add('preview-image');

        const deleteButton = document.createElement('button');
        deleteButton.type = 'button'; // Importante para no enviar el form
        deleteButton.textContent = 'X';
        deleteButton.classList.add('delete-preview-btn');
        deleteButton.title = 'Eliminar foto';
        deleteButton.addEventListener('click', (e) => {
            e.stopPropagation(); // Evitar que el click active la selección de principal
            if (confirm('¿Seguro que quieres eliminar esta foto?')) {
                deletePhoto(identifier, previewContainer);
            }
        });

        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'photo_name[]'; // El backend espera un array con este nombre
        hiddenInput.value = identifier;

        previewContainer.appendChild(img);
        previewContainer.appendChild(deleteButton);
        previewContainer.appendChild(hiddenInput); // Añadir input oculto

        // Evento para seleccionar como principal
        previewContainer.addEventListener('click', () => {
            selectMainPhoto(previewContainer);
        });

        photoList.appendChild(previewContainer);

        // Si es la primera foto, marcarla como principal automáticamente
        if (photoList.querySelectorAll('.preview-container').length === 1) {
            selectMainPhoto(previewContainer);
        }

        // Habilitar/Deshabilitar dropzone si se alcanza el límite
        if (currentPhotoCount >= MAX_PHOTOS) {
            dropZone.style.display = 'none'; // O deshabilitarlo de otra forma
            dropZone.querySelector('span').textContent = `Máximo ${MAX_PHOTOS} fotos alcanzado`;
        } else {
            dropZone.style.display = 'block';
        }
    }

    // --- Función para seleccionar la foto principal ---
    function selectMainPhoto(selectedContainer) {
        // Quitar clase 'principal' de todas
        photoList.querySelectorAll('.preview-container').forEach(container => {
            container.classList.remove('principal');
        });

        // Añadir clase a la seleccionada
        selectedContainer.classList.add('principal');

        // Encontrar el índice de la foto principal y actualizar el input oculto
        const previews = Array.from(photoList.querySelectorAll('.preview-container'));
        const mainIndex = previews.findIndex(container => container === selectedContainer);
        mainPhotoInput.value = mainIndex >= 0 ? mainIndex : 0; // Asegurarse que siempre haya un valor
    }

    // --- Función para borrar una foto (Frontend + Backend) ---
    function deletePhoto(identifier, previewContainer) {
        // Mostrar indicador de borrado si se desea
        previewContainer.style.opacity = '0.5';

        const formData = new FormData();
        formData.append('identifier', identifier);

        fetch(DELETE_TEMP_URL, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const wasMain = previewContainer.classList.contains('principal');
                    previewContainer.remove(); // Eliminar del DOM
                    currentPhotoCount--;

                    // Volver a habilitar dropzone si baja del límite
                    if (currentPhotoCount < MAX_PHOTOS) {
                        dropZone.style.display = 'block';
                        dropZone.querySelector('span').textContent = 'Haz click o arrastra para subir';
                    }

                    // Si la borrada era la principal, seleccionar la nueva primera como principal
                    if (wasMain && photoList.children.length > 0) {
                        selectMainPhoto(photoList.children[0]);
                    } else if (photoList.children.length === 0) {
                        mainPhotoInput.value = '0'; // Resetear si no quedan fotos
                    }
                    // Actualizar el índice de la principal por si acaso
                    const currentMain = photoList.querySelector('.principal');
                    if (currentMain) {
                        const previews = Array.from(photoList.querySelectorAll('.preview-container'));
                        mainPhotoInput.value = previews.findIndex(container => container === currentMain);
                    }


                } else {
                    alert(`Error al borrar la foto: ${data.error || 'Error desconocido'}`);
                    previewContainer.style.opacity = '1'; // Restaurar opacidad si falla
                }
            })
            .catch(error => {
                console.error('Error en fetch delete:', error);
                alert('Error de red al borrar la foto.');
                previewContainer.style.opacity = '1'; // Restaurar opacidad si falla
            });
    }

    // --- Event Listeners para Subida ---

    // Click en el botón/zona activa el input real
    dropZone.addEventListener('click', () => {
        if (currentPhotoCount < MAX_PHOTOS) {
            fileInput.click();
        }
    });

    // Cuando se seleccionan archivos con el input
    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });

    // Eventos Drag & Drop
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault(); // Necesario para permitir drop
        if (currentPhotoCount < MAX_PHOTOS) {
            dropZone.classList.add('dragover'); // Estilo visual
        }
    });

    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault(); // Evita que el navegador abra el archivo
        dropZone.classList.remove('dragover');
        if (currentPhotoCount < MAX_PHOTOS) {
            handleFiles(e.dataTransfer.files);
        }
    });

    // --- Inicialización y Validación Final ---
    // Contar fotos ya existentes (si se está editando un anuncio y se cargan las fotos existentes)
    currentPhotoCount = photoList.querySelectorAll('.preview-container').length;
    if (currentPhotoCount >= MAX_PHOTOS) {
        dropZone.style.display = 'none';
        dropZone.querySelector('span').textContent = `Máximo ${MAX_PHOTOS} fotos alcanzado`;
    }

    // Añadir validación al enviar el formulario principal
    form.addEventListener('submit', function(event) {
        // (El código de validación de etapas y reCAPTCHA ya está aquí...)

        // Validación específica de fotos en la etapa de perfil
        const etapaPerfil = document.getElementById('etapa-perfil');
        if (etapas[etapaActual] && etapas[etapaActual].id === 'etapa-perfil') {
            if (photoList.children.length === 0) {
                photoErrorDiv.classList.remove('oculto');
                // Detener envío si es necesario (aunque la validación de etapas ya debería hacerlo)
                // event.preventDefault(); // Comentar si la validación de etapas ya lo maneja
                console.error("Validación de fotos: Se requiere al menos una foto.");
                // Asegurarse que la validación general detecte esto
                // La función validarEtapa debería marcarse como inválida si no hay fotos.
            } else if (!photoList.querySelector('.principal')) {
                // Asegurarse que siempre haya una principal seleccionada si hay fotos
                selectMainPhoto(photoList.children[0]);
            }
        }
    });
</script>

<?php loadBlock('datajson'); // ¿Necesario? 
?>