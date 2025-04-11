<?php
// Determina la URL correcta para el action (con el ?id=...)
// Si $urlfriendly['url.post_item'] ya la tiene, úsala directamente.
// Si no, constrúyela. Asumiendo que el id es 'post_item':
$formActionUrl = "/index.php?id=post_item"; // O usa $urlfriendly['url.post_item'] si es correcto

ini_set('display_errors', 1); // Mantenlos por si el problema es ANTES del POST
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

user::updateSesion();
$limite = check_item_limit();
$ANUNCIO_NUEVO_PREMIUM = false;
$user_registered = false;
$DATAJSON = array();
$DATAJSON['max_photos'] = getConfParam('MAX_PHOTOS_AD');
$DATAJSON['edit'] = 0;
if (getConfParam('POST_ITEM_REG') == 1) {
    check_login();
}
// ############################################################
// ####### INICIO LÓGICA PROCESAMIENTO FORMULARIO ANTIGUO #####
// # Esta lógica espera los $_POST con los nombres del formulario viejo
// ############################################################

// ----- INICIO Procesamiento del formulario (SIN reCAPTCHA) -----
// Comprobamos si se ha enviado el formulario por POST y si existe un campo esencial como 'category' o 'token'
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['token']) || isset($_POST['category']))) {

    echo '<pre>¡AHORA SÍ ESTOY PROCESANDO EL POST EN EL SCRIPT CORRECTO!</pre>';
    var_dump($_POST);
    exit;

    $en_revision = false; // Inicializamos la variable

    // ----- Espera 'category', no 'categoria' -----
    if (isset($_POST['category'])) { // Comprobamos primero si la categoría existe
        if (verifyFormToken('postAdToken', $_POST['token']) || DEBUG) { // Luego el token CSRF

            $datos_ad = array();
            // ----- Mapeo de Campos $_POST -----
            $datos_ad['ID_cat'] = $_POST['category']; // Antes: $_POST['category']
            $category_ad = selectSQL('sc_category', $w = array('ID_cat' => $datos_ad['ID_cat']));
            $datos_ad['parent_cat'] = $category_ad[0]['parent_cat'];
            $datos_ad['ID_region'] = $_POST['region']; // Antes: $_POST['region']
            $datos_ad['location'] = $_POST['city']; // Antes: $_POST['city'] (era input text)
            // $datos_ad['ad_type'] = $_POST['ad_type']; // El nuevo form no tiene 'ad_type'. ¿De dónde saldría? ¿Quizás del tipo de usuario o categoría? Necesita clarificación. Asumiré un valor por defecto o necesitará JS.
            $datos_ad['ad_type'] = isset($_POST['ad_type']) ? $_POST['ad_type'] : 1; // Asumiendo valor por defecto 1 si no se envía. ¡REVISAR!
            $datos_ad['title'] = $_POST['tit']; // Antes: $_POST['tit']
            $datos_ad['title_seo'] = toAscii($_POST['tit']); // Antes: toAscii($_POST['tit'])
            $datos_ad['texto'] = isset($_POST['text']) ? htmlspecialchars($_POST['text']) : ''; // Antes: $_POST['text']
            // $datos_ad['price'] = $_POST['precio'] ? $_POST['precio'] : 0; // El nuevo form no tiene 'precio'. ¿Relacionado con plan/extras? Poner 0 por defecto.
            $datos_ad['price'] = 0; // ¡REVISAR!
            // Los siguientes campos no están en el nuevo form: mileage, fuel, date_car, area, room, broom, address (usamos location/city)
            $datos_ad['mileage'] = null;
            $datos_ad['fuel'] = null;
            $datos_ad['date_car'] = null;
            $datos_ad['area'] = null;
            $datos_ad['room'] = null;
            $datos_ad['broom'] = null;
            $datos_ad['address'] = $datos_ad['location']; // Usar city/location como address
            $datos_ad['name'] = isset($_POST['name']) ? formatName($_POST['name']) : ''; // Antes: $_POST['name']
            $datos_ad['phone'] = $_POST['phone']; // Antes: $_POST['phone']
            $datos_ad['whatsapp'] = isset($_POST['whatsapp']) ? 1 : 0; // Antes: $_POST['whatsapp']
            // $datos_ad['phone1'] = $_POST['phone1']; // No existe en el nuevo form
            // $datos_ad['whatsapp1'] = isset($_POST['whatsapp1']) ? 1 : 0; // No existe en el nuevo form
            $datos_ad['phone1'] = null;
            $datos_ad['whatsapp1'] = 0;
            $datos_ad['seller_type'] = $_POST['seller_type']; // Antes: $_POST['seller_type'] - DEBE ser populado por JS desde tipo_usuario
            $datos_ad['notifications'] = isset($_POST['notifications']) && $_POST['notifications'] == '1' ? 1 : 0; // Antes: $_POST['notifications'] ? $_POST['notifications'] : 0;
            // $datos_ad['dis'] = $_POST['dis']; // Antes: $_POST['dis'] - DEBE ser populado por JS desde horario_dia
            // $datos_ad['hor_start'] = $_POST['horario-inicio']; // Antes: $_POST['horario-inicio'] - DEBE ser populado por JS desde horario_dia
            // $datos_ad['hor_end'] = $_POST['horario-final']; // Antes: $_POST['horario-final'] - DEBE ser populado por JS desde horario_dia
            $datos_ad['dis'] = isset($_POST['dis']) ? $_POST['dis'] : null; // Asegurarse que JS los popule
            $datos_ad['hor_start'] = isset($_POST['horario-inicio']) ? $_POST['horario-inicio'] : null; // Asegurarse que JS los popule
            $datos_ad['hor_end'] = isset($_POST['horario-final']) ? $_POST['horario-final'] : null; // Asegurarse que JS los popule
            // $datos_ad['lang1'] = $_POST['lang-1']; // Antes: $_POST['lang-1'] - DEBE ser populado por JS desde idioma_1
            // $datos_ad['lang2'] = $_POST['lang-2']; // Antes: $_POST['lang-2'] - DEBE ser populado por JS desde idioma_2
            $datos_ad['lang1'] = isset($_POST['lang-1']) ? $_POST['lang-1'] : null; // Asegurarse que JS los popule
            $datos_ad['lang2'] = isset($_POST['lang-2']) ? $_POST['lang-2'] : null; // Asegurarse que JS los popule
            $datos_ad['out'] = $_POST['out']; // Antes: $_POST['out']
            $datos_ad['ID_order'] = $_POST['order']; // Antes: $_POST['order'] (era new_order en tu HTML, debe ser 'order')
            // $datos_ad['payment'] = isset($_POST['pago']) ? json_encode($_POST['pago']) : "[]"; // No existe en el nuevo form. Usar '[]'.
            $datos_ad['payment'] = "[]";

            // ----- Lógica de Usuario (parece similar) -----
            if (!isset($_SESSION['data']['ID_user'])) {
                $checkUser = selectSQL("sc_user", $a = array('mail' => $_POST['email']));
                $ip = get_client_ip();

                if (count($checkUser) == 0) {
                    if (!User::check_registered($ip, $_POST['phone'])) {
                        // ----- Foto de perfil del usuario (banner_img) -----
                        // El backend antiguo intentaba copiar la *primera* foto del anuncio como foto de perfil.
                        // Necesitamos asegurarnos que $_POST['photo_name'][0] exista si hay fotos.
                        // JS debe crear los inputs 'photo_name[]'.
                        if (isset($_POST['photo_name'][0])) // ¡JS NECESARIO para crear photo_name[]!
                            $banner_img = Images::copyImage($_POST['photo_name'][0], IMG_USER, IMG_ADS, true);
                        else
                            $banner_img = "";

                        $pass = randomString(6);
                        // Usar el 'seller_type' que viene del formulario (mapeado por JS)
                        $limit = user::getLimitsByRol($_POST['seller_type']);
                        $datos_u = array(
                            'name' => isset($_POST['name']) ? formatName($_POST['name']) : '', // Usa el nombre del anuncio
                            'mail' => $_POST['email'],
                            'phone' => $_POST['phone'],
                            'banner_img' => $banner_img,
                            'pass' => $pass,
                            'date_reg' => time(),
                            'active' => 1,
                            'rol' => $_POST['seller_type'], // Usa el rol seleccionado/mapeado
                            'date_credits' => "0",
                            'credits' => "0",
                            'IP_user' => $ip,
                            'anun_limit' => $limit
                            // Faltarían phone1, whatsapp, whatsapp1 si fueran necesarios en la tabla sc_user
                        );
                        $result = insertSQL("sc_user", $datos_u);
                        if ($result) {
                            $id_user = lastIdSQL();
                            //mailWelcome(formatName($_POST['name']),$_POST['email'],$pass);
                        }
                    } else {
                        $user_registered = true;
                        $id_user = 0;
                    }
                } else {
                    $id_user = $checkUser[0]['ID_user'];
                }
            } else {
                $id_user = $_SESSION['data']['ID_user'];
            }

            // ----- Lógica de Inserción del Anuncio (parece similar) -----
            $limite = check_item_limit($id_user);
            if ($limite == 0 || $_POST['order'] != 0) {
                list($extras, $extra_limit) = User::updateExtras($id_user);
                if ($extras) {
                    $datos_ad['renovable'] = renovationType::Diario;
                    $datos_ad['renovable_limit'] = $extra_limit;
                }

                // ----- Rotar Imágenes -----
                // El backend antiguo esperaba $_POST['optImgage'][$photo]['rotation']. Tu nuevo form no lo tiene.
                // Se podría omitir la rotación o añadir inputs ocultos si es necesaria.
                // foreach ($_POST['photo_name'] as $photo => $name) {
                //     // Asegurarse que $_POST['optImgage'][$photo]['rotation'] existe o poner valor por defecto (0)
                //     $rotation = isset($_POST['optImgage'][$photo]['rotation']) ? $_POST['optImgage'][$photo]['rotation'] : 0;
                //     Images::rotateImage($name, $rotation);
                // }

                $datos_ad['ID_user'] = $id_user;
                $datos_ad['date_ad'] = time();
                if (getConfParam('REVIEW_ITEM') == 1) {
                    $datos_ad['review'] = 1;
                }

                /// COMPROBACIÓN (Adaptada a los campos disponibles)
                if ($datos_ad['ID_region'] != 0 /*&& $datos_ad['ad_type'] != 0*/ && $datos_ad['seller_type'] != 0 && $datos_ad['ID_cat'] != 0 && ($datos_ad['price'] == 0 || is_numeric($datos_ad['price'])) && $datos_ad['ID_user'] > 0) {
                    if ($_POST['order'] != 0) {
                        $order = Orders::getOrderByID($_POST['order']);
                        if ($order['ID_ad'] == 0) {
                            $insert = insertSQL("sc_ad", $datos_ad);
                            $last_ad = lastIdSQL();
                            updateSQL("sc_orders", array("ID_ad" => $last_ad), array("ID_order" => $_POST['order']));
                            Statistic::addAnuncioNuevoPremium();
                        } else {
                            $error_insert = true;
                            $insert = false;
                            $last_ad = $order['ID_ad'];
                        }
                    } else {
                        $insert = insertSQL("sc_ad", $datos_ad);
                        $last_ad = lastIdSQL();
                        Statistic::addAnuncioNuevo();
                    }

                    // ----- Asociar Imágenes al Anuncio -----
                    // JS NECESARIO: Debe crear los inputs <input type="hidden" name="photo_name[]" value="nombre_archivo.jpg">
                    if (isset($_POST['photo_name']) && is_array($_POST['photo_name'])) {
                        foreach ($_POST['photo_name'] as $photo => $name) {
                            // El backend antiguo actualizaba sc_images. Asume que la subida AJAX ya insertó registros en sc_images con status pendiente.
                            updateSQL("sc_images", $data = array('ID_ad' => $last_ad, 'position' => $photo, "status" => 1), $wa = array('name_image' => $name));
                        }
                    }

                    if ($insert) {
                        checkRepeat($last_ad); // Función existente
                        // Notificación por email (usa el campo 'notifications' del formulario)
                        if (!isset($datos_ad['notifications']) || !$datos_ad['notifications']) {
                            // Parece que esta función se llama si *NO* quieren notificaciones. ¿Seguro?
                            // mailAdNotNotification($last_ad); // Comentado por si acaso
                        }

                        //mailNewAd($last_ad); // Función existente

                        // Redirección
                        if ($_POST['order'] != 0) {
                            // header('Location: /publicado?payad=' . $_POST['order']); // Causa error "headers already sent" si hay HTML antes.
                            echo '<script type="text/javascript">location.href = "/publicado?payad=' . $_POST['order'] . '";</script>';
                        } else {
                            echo '<script type="text/javascript">location.href = "/publicado";</script>';
                        }
                        exit(); // Importante salir después de la redirección JS/Header

                    } else {
                        $error_insert = true; // Hubo error en insertSQL o era un anuncio ya asociado a la orden
                        $_SESSION['form_error_message'] = "Error al guardar el anuncio. Posiblemente asociado a una orden existente o error de base de datos.";
                        $_SESSION['form_data_error'] = $_POST; // Guardar datos para repoblar
                    }
                } else {
                    // Error en los datos básicos del anuncio
                    $error_insert = true;
                    // Guardar los datos $_POST en sesión para repoblar el formulario
                    $_SESSION['form_data_error'] = $_POST;
                    // Añadir un mensaje de error específico si es posible
                    if ($datos_ad['ID_user'] <= 0) {
                        $_SESSION['form_error_message'] = "Error al identificar o crear el usuario.";
                    } elseif ($datos_ad['ID_region'] == 0) {
                        $_SESSION['form_error_message'] = "Debes seleccionar una provincia.";
                    } elseif ($datos_ad['ID_cat'] == 0) {
                        $_SESSION['form_error_message'] = "Debes seleccionar una categoría.";
                    } else {
                        $_SESSION['form_error_message'] = "Faltan datos obligatorios o son incorrectos. Revisa el formulario.";
                    }
                }
            } else {
                $error_insert = true; // Límite de anuncios alcanzado
                $_SESSION['form_error_message'] = "Has alcanzado el límite de anuncios gratuitos.";
                // Guardar datos para repoblar
                $_SESSION['form_data_error'] = $_POST;
            }
        } else {
            $error_insert = true; // Error token CSRF
            $_SESSION['form_error_message'] = "Error de seguridad al procesar el formulario (token inválido). Intenta de nuevo.";
            // Guardar datos para repoblar
            $_SESSION['form_data_error'] = $_POST;
        }
    } else {
        // Este bloque se ejecuta si se envió el POST pero sin el campo 'category'
        $error_insert = true; // No se recibió 'category'
        $_SESSION['form_error_message'] = "No se recibió la categoría. Revisa el formulario.";
        // Guardar datos para repoblar
        $_SESSION['form_data_error'] = $_POST;
    }
} // ----- FIN Procesamiento del formulario -----

// ############################################################
// ###### FIN LÓGICA PROCESAMIENTO FORMULARIO ANTIGUO #########
// ############################################################


// Repoblar datos si hubo error
$form_data = [];
if (isset($_SESSION['form_data_error'])) {
    $form_data = $_SESSION['form_data_error'];
    unset($_SESSION['form_data_error']);
}
$form_error_message = '';
if (isset($_SESSION['form_error_message'])) {
    $form_error_message = $_SESSION['form_error_message'];
    unset($_SESSION['form_error_message']);
}

?>

<?php // <!-- ELIMINADO: Script de carga de reCAPTCHA API --> 
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

    <?php // Mostrar mensaje de error general si existe
    if (!empty($form_error_message)): ?>
        <div class="error_msg" style="display: block; border: 1px solid red; padding: 10px; margin-bottom: 15px; background-color: #ffebeb;">
            <strong>Error:</strong> <?php echo htmlspecialchars($form_error_message); ?>
        </div>
    <?php endif; ?>

    <?php // <!-- ############################################################ -->
    ?>
    <?php // <!-- ##### INICIO NUEVO FORMULARIO ADAPTADO ##### -->
    ?>
    <?php // <!-- ############################################################ -->
    ?>

    <form id="form-nuevo-anuncio" class="formulario-multi-etapa" method="post" action="<?php echo htmlspecialchars($formActionUrl); ?>" enctype="multipart/form-data" autocomplete="off">

        <?php // Generar Token CSRF - Asegúrate que 'nuevoAnuncioToken' sea el nombre esperado por verifyFormToken o cambia verifyFormToken a 'nuevoAnuncioToken'
        // El código viejo usa 'postAdToken'. Usaremos ese por seguridad.
        $token_q = generateFormToken('postAdToken');
        ?>
        <input type="hidden" name="token" id="token" value="<?= $token_q; ?>">
        <?php // <!-- ELIMINADO: Input oculto para g-recaptcha-response --> 
        ?>
        <!-- MAPEO: El backend espera 'order'. El id puede ser 'new_order' para JS si quieres. -->
        <input type="hidden" id="new_order" name="order" value="<?php echo isset($form_data['order']) ? htmlspecialchars($form_data['order']) : '0'; ?>" />

        <!-- JS NECESARIO: Campo oculto para seller_type. JS debe actualizar su valor al cambiar tipo_usuario -->
        <input type="hidden" name="seller_type" id="hidden_seller_type" value="<?php echo isset($form_data['seller_type']) ? htmlspecialchars($form_data['seller_type']) : ''; ?>">
        <!-- JS NECESARIO: Campos ocultos para horario simplificado -->
        <input type="hidden" name="dis" id="hidden_dis" value="<?php echo isset($form_data['dis']) ? htmlspecialchars($form_data['dis']) : ''; ?>">
        <input type="hidden" name="horario-inicio" id="hidden_horario_inicio" value="<?php echo isset($form_data['horario-inicio']) ? htmlspecialchars($form_data['horario-inicio']) : ''; ?>">
        <input type="hidden" name="horario-final" id="hidden_horario_final" value="<?php echo isset($form_data['horario-final']) ? htmlspecialchars($form_data['horario-final']) : ''; ?>">
        <!-- JS NECESARIO: Campos ocultos para idiomas -->
        <input type="hidden" name="lang-1" id="hidden_lang_1" value="<?php echo isset($form_data['lang-1']) ? htmlspecialchars($form_data['lang-1']) : ''; ?>">
        <input type="hidden" name="lang-2" id="hidden_lang_2" value="<?php echo isset($form_data['lang-2']) ? htmlspecialchars($form_data['lang-2']) : ''; ?>">
        <!-- JS NECESARIO: Contenedor para los inputs ocultos de las fotos generados por JS -->
        <div id="hidden-photo-inputs">
            <?php // Si hubo error y hay fotos, intentar repoblarlas (requiere JS complejo)
            /* if (isset($form_data['photo_name']) && is_array($form_data['photo_name'])) {
                 foreach($form_data['photo_name'] as $photo_name) {
                     echo '<input type="hidden" name="photo_name[]" value="'.htmlspecialchars($photo_name).'">';
                 }
             } */
            ?>
        </div>


        <!-- ======================= ETAPA 0: TIPO DE USUARIO (Solo si no está logueado) ======================= -->
        <?php if (!checkSession()): ?>
            <div id="etapa-tipo-usuario" class="etapa activa">
                <h2 class="titulo-etapa">Paso 1: Elige tu tipo de perfil</h2>
                <p>Selecciona cómo quieres usar la plataforma.</p>

                <div class="lista-opciones grupo-radios">
                    <!-- JS NECESARIO: Estos radios deben actualizar el input oculto 'seller_type'.
                          Valores posibles esperados por backend: Particular=1, Centro=2, Publicista=3 (según UserRole::NAME) -->
                    <label class="opcion-radio">
                        <input type="radio" name="tipo_usuario" value="1" <?php echo (isset($form_data['seller_type']) && $form_data['seller_type'] == '1') ? 'checked' : ''; ?> required>
                        <div class="opcion-contenido">
                            <strong>Masajista Particular</strong>
                            <span>Crea tu perfil individual para ofrecer tus servicios.</span>
                        </div>
                    </label>
                    <label class="opcion-radio">
                        <input type="radio" name="tipo_usuario" value="2" <?php echo (isset($form_data['seller_type']) && $form_data['seller_type'] == '2') ? 'checked' : ''; ?>>
                        <div class="opcion-contenido">
                            <strong>Centro de Masajes</strong>
                            <span>Gestiona varios perfiles de masajistas de tu centro.</span>
                        </div>
                    </label>
                    <label class="opcion-radio">
                        <input type="radio" name="tipo_usuario" value="3" <?php echo (isset($form_data['seller_type']) && $form_data['seller_type'] == '3') ? 'checked' : ''; ?>>
                        <div class="opcion-contenido">
                            <strong>Publicista</strong>
                            <span>Promociona productos o servicios relacionados.</span>
                        </div>
                    </label>
                    <label class="opcion-radio">
                        <input type="radio" name="tipo_usuario" value="visitante" <?php echo (isset($form_data['seller_type']) && !in_array($form_data['seller_type'], ['1', '2', '3'])) ? 'checked' : ''; ?>>
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
        <?php else: ?>
            <?php // Si está logueado, establecer el seller_type del usuario de sesión
            echo '<script>document.getElementById("hidden_seller_type").value = "' . htmlspecialchars($_SESSION['data']['rol']) . '";</script>';
            ?>
        <?php endif; ?>

        <!-- ======================= ETAPA 1: ELECCIÓN DE PLAN ======================= -->
        <div id="etapa-plan" class="etapa <?php echo checkSession() ? 'activa' : 'oculto'; ?>">
            <h2 class="titulo-etapa"><?php echo checkSession() ? 'Paso 1' : 'Paso 2'; ?>: Elige tu Plan</h2>
            <p>Selecciona el plan que mejor se adapte a tus necesidades.</p>
            <!-- ADVERTENCIA: El campo 'plan' no existía. El backend podría ignorarlo. -->
            <div class="lista-opciones grupo-radios-plan">
                <label class="opcion-radio opcion-plan">
                    <input type="radio" name="plan" value="gratis" <?php echo (isset($form_data['plan']) && $form_data['plan'] == 'gratis') ? 'checked' : (!isset($form_data['plan']) ? 'checked' : ''); ?> required>
                    <div class="opcion-contenido">
                        <strong>Plan Gratis</strong>
                        <span>Prueba gratuita de 30 días.</span>
                        <span>Renovación manual de anuncios cada 24 horas.</span>
                        <span class="precio-plan">0 €</span>
                    </div>
                </label>
                <label class="opcion-radio opcion-plan">
                    <input type="radio" name="plan" value="silver" <?php echo (isset($form_data['plan']) && $form_data['plan'] == 'silver') ? 'checked' : ''; ?>>
                    <div class="opcion-contenido">
                        <strong>Plan Silver</strong>
                        <span>Visibilidad mejorada por 60 días.</span>
                        <span>Renovación automática de anuncios cada 12 horas.</span>
                        <span class="precio-plan">12 €</span>
                    </div>
                </label>
                <label class="opcion-radio opcion-plan">
                    <input type="radio" name="plan" value="gold" <?php echo (isset($form_data['plan']) && $form_data['plan'] == 'gold') ? 'checked' : ''; ?>>
                    <div class="opcion-contenido">
                        <strong>Plan Gold</strong>
                        <span>Máxima visibilidad por 90 días.</span>
                        <span>Renovación automática de anuncios cada 12 horas.</span>
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
                    <!-- MAPEO: name="name" esperado por backend -->
                    <input type="text" name="name" id="nombre" class="frm-campo" required maxlength="50" value="<?php echo htmlspecialchars($form_data['name'] ?? ($_SESSION['data']['name'] ?? '')); ?>">
                    <div class="error-msg oculto" id="error-nombre">El nombre es obligatorio.</div>
                </div>

                <div class="frm-grupo">
                    <label for="categoria" class="frm-etiqueta">Categoría Principal *</label>
                    <!-- MAPEO: name="category" esperado por backend -->
                    <!-- Asegúrate que los 'value' coincidan con los ID_cat del sistema antiguo -->
                    <select name="category" id="categoria" class="frm-campo frm-select" required>
                        <option value="">-- Selecciona una categoría --</option>
                        <?php
                        // Copiado del form antiguo para asegurar compatibilidad
                        $parent = selectSQL("sc_category", $where = array('parent_cat' => -1), "ord ASC");
                        $selected_cat = $form_data['category'] ?? null;
                        foreach ($parent as $p) {
                            $child = selectSQL("sc_category", $where = array('parent_cat' => $p['ID_cat']), "name ASC");
                            if (count($child) > 0) { // Solo mostrar optgroup si hay hijos
                                echo '<optgroup label="' . htmlspecialchars(mb_strtoupper($p['name'], 'UTF-8')) . '">';
                                $otros_html_grp = '';
                                foreach ($child as $c) {
                                    $selected = ($selected_cat == $c['ID_cat']) ? 'selected' : '';
                                    if ((strpos($c['name'], 'Otros') !== false) || (strpos($c['name'], 'Otras') !== false)) {
                                        $otros_html_grp .= '<option value="' . $c['ID_cat'] . '" ' . $selected . '>  ' . htmlspecialchars($c['name']) . '</option>';
                                    } else {
                                        echo '<option value="' . $c['ID_cat'] . '" ' . $selected . '>  ' . htmlspecialchars($c['name']) . '</option>';
                                    }
                                }
                                echo $otros_html_grp; // Imprimir 'Otros' al final del grupo
                                echo '</optgroup>';
                            }
                        }
                        ?>
                    </select>
                    <div class="error-msg oculto" id="error-categoria">Debes seleccionar una categoría.</div>
                </div>

                <div class="frm-grupo">
                    <label for="provincia" class="frm-etiqueta">Provincia *</label>
                    <!-- MAPEO: name="region" esperado por backend -->
                    <!-- Asegúrate que los 'value' coincidan con los ID_region -->
                    <select name="region" id="provincia" class="frm-campo frm-select" required>
                        <option value="">-- Selecciona una provincia --</option>
                        <?php
                        $provincias = selectSQL("sc_region", [], "name ASC");
                        $selected_region = $form_data['region'] ?? null;
                        foreach ($provincias as $prov) {
                            $selected = ($selected_region == $prov['ID_region']) ? 'selected' : '';
                            echo '<option value="' . $prov['ID_region'] . '" ' . $selected . '>' . htmlspecialchars($prov['name']) . '</option>';
                        }
                        ?>
                    </select>
                    <div class="error-msg oculto" id="error-provincia">Debes seleccionar una provincia.</div>
                </div>

                <div class="frm-grupo">
                    <label for="ciudad" class="frm-etiqueta">Ciudad / Zona (Opcional)</label>
                    <!-- MAPEO: name="city" esperado por backend -->
                    <input type="text" name="city" id="ciudad" class="frm-campo" maxlength="100" placeholder="Ej: Centro, Nervión, etc." value="<?php echo htmlspecialchars($form_data['city'] ?? ''); ?>">
                </div>

            </fieldset>

            <fieldset class="frm-seccion">
                <legend>Detalles del Anuncio</legend>

                <div class="frm-grupo">
                    <label for="titulo_anuncio" class="frm-etiqueta">Título del Anuncio *</label>
                    <!-- MAPEO: name="tit" esperado por backend -->
                    <input type="text" name="tit" id="titulo_anuncio" class="frm-campo" required minlength="10" maxlength="50" placeholder="Ej: Masajista Profesional en Madrid Centro" value="<?php echo htmlspecialchars($form_data['tit'] ?? ''); ?>">
                    <div class="contador-caracteres">Caracteres: <span id="cont-titulo">0</span> (min 10 / máx 50)</div>
                    <div class="error-msg oculto" id="error-titulo">El título es obligatorio (entre 10 y 50 caracteres).</div>
                    <div class="error-msg oculto" id="error-titulo-palabras">El título contiene palabras no permitidas.</div>
                </div>

                <div class="frm-grupo">
                    <label for="descripcion" class="frm-etiqueta">Descripción del Anuncio *</label>
                    <!-- MAPEO: name="text" esperado por backend -->
                    <textarea name="text" id="descripcion" class="frm-campo frm-textarea" rows="6" required minlength="30" maxlength="500" placeholder="Describe tus servicios, experiencia, ambiente, etc."><?php echo htmlspecialchars($form_data['text'] ?? ''); ?></textarea>
                    <div class="contador-caracteres">Caracteres: <span id="cont-desc">0</span> (min 30 / máx 500)</div>
                    <div class="error-msg oculto" id="error-descripcion">La descripción es obligatoria (entre 30 y 500 caracteres).</div>
                    <div class="error-msg oculto" id="error-desc-palabras">La descripción contiene palabras no permitidas.</div>
                </div>

                <div class="frm-grupo">
                    <label class="frm-etiqueta">Servicios Ofrecidos *</label>
                    <!-- ADVERTENCIA: El campo 'servicios[]' no existía. El backend podría ignorarlo o dar error. -->
                    <!-- Considera quitar el atributo 'name' si causa problemas: name="servicios_DISABLED[]" -->
                    <div class="grupo-checkboxes">
                        <?php
                        $servicios = ["Masaje relajante", "Masaje deportivo", "Masaje podal", "Masaje antiestrés", "Masaje linfático", "Masaje shiatsu", "Masaje descontracturante", "Masaje ayurvédico", "Masaje circulatorio", "Masaje tailandés"];
                        // Repoblar si hubo error
                        $selected_services = $form_data['servicios'] ?? [];
                        foreach ($servicios as $servicio) {
                            $valor = strtolower(str_replace(' ', '_', $servicio));
                            $checked = in_array($valor, $selected_services) ? 'checked' : '';
                            echo '<label class="frm-checkbox"><input type="checkbox" name="servicios[]" value="' . htmlspecialchars($valor) . '" ' . $checked . '> ' . htmlspecialchars($servicio) . '</label>';
                        }
                        ?>
                    </div>
                    <div class="error-msg oculto" id="error-servicios">Debes seleccionar al menos un servicio.</div>
                </div>

            </fieldset>

            <fieldset class="frm-seccion">
                <legend>Fotografías</legend>
                <!-- JS NECESARIO: Se necesita JS para manejar la subida (AJAX), previsualización, ordenación y generación de inputs ocultos name="photo_name[]" -->
                <div class="frm-grupo">
                    <label class="frm-etiqueta">Sube tus fotos (hasta <?= htmlspecialchars($DATAJSON['max_photos'] ?? 3) ?>)</label>
                    <div class="ayuda-texto">Puedes arrastrar y soltar las imágenes. Tamaño máx. 2MB (JPG, PNG). La primera foto será la principal.</div>
                    <div class="subida-fotos-contenedor">
                        <div id="boton-subir-foto" class="boton-subir">
                            <span>Haz click o arrastra para subir</span>
                            <!-- Este input es para SELECCIONAR. La subida real y la creación de 'photo_name[]' necesita JS -->
                            <input type="file" id="campo-subir-foto" multiple accept="image/jpeg, image/png" style="/* display: none; */ position:absolute; opacity: 0; top:0; left:0; bottom:0; right:0; cursor:pointer;">
                        </div>
                        <div id="lista-fotos-subidas" class="lista-fotos sortable">
                            <!-- Las previsualizaciones de las fotos se añadirán aquí vía JS -->
                            <!-- JS también debe añadir aquí los inputs ocultos photo_name[] O en el div #hidden-photo-inputs -->
                        </div>
                        <!-- El backend viejo no usa 'foto_principal_input'. Probablemente usa el orden del array 'photo_name[]'. -->
                        <!-- <input type="hidden" name="foto_principal" id="foto_principal_input" value="0"> -->
                    </div>
                    <div class="error-msg oculto" id="error-fotos">Debes subir al menos una foto. La primera que subas será la principal.</div>
                    <div class="error_msg" id="error_photo_generic" style="<?php echo (isset($form_data['photo_name']) && count($form_data['photo_name']) == 0 && $error_insert) ? 'display:block;' : 'display:none;'; ?>">Sube al menos una foto para tu anuncio.</div>
                </div>
            </fieldset>


            <fieldset class="frm-seccion">
                <legend>Disponibilidad y Contacto</legend>

                <div class="frm-grupo">
                    <label class="frm-etiqueta">Horario Detallado *</label>
                    <div class="ayuda-texto">Marca los días que estás disponible y selecciona tu horario.</div>
                    <!-- JS NECESARIO: Este bloque debe usarse para calcular y rellenar los campos ocultos: hidden_dis, hidden_horario_inicio, hidden_horario_final -->
                    <div class="horario-semanal">
                        <?php
                        // Repoblar horario es complejo con la estructura nueva y el error_data viejo. Requiere JS.
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
                                        <?php for ($h = 0; $h < 24; $h++) {
                                            $hora = sprintf('%02d', $h);
                                            echo "<option value='{$hora}:00'>{$hora}:00</option><option value='{$hora}:30'>{$hora}:30</option>";
                                        } ?>
                                    </select>
                                    <label>A:</label>
                                    <select name="horario_dia[<?= $key ?>][fin]" class="frm-campo frm-select corto">
                                        <?php for ($h = 0; $h < 24; $h++) {
                                            $hora = sprintf('%02d', $h);
                                            $selected = ($hora == 23) ? 'selected' : ''; // Default end time selection might need JS adjustment
                                            echo "<option value='{$hora}:00'>{$hora}:00</option><option value='{$hora}:30' " . (($hora == 23) ? 'selected' : '') . ">{$hora}:30</option>"; // Simplified default end selection
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="error-msg oculto" id="error-horario">Debes marcar al menos un día y configurar su horario.</div>
                    <!-- Mensaje de error si el backend falló por horario (mapeado) -->
                    <div class="error_msg" id="error_backend_horario" style="<?php echo (isset($form_data['dis'], $form_data['horario-inicio'], $form_data['horario-final']) && (!$form_data['dis'] || !$form_data['horario-inicio'] || !$form_data['horario-final']) && $error_insert) ? 'display:block;' : 'display:none;'; ?>">Error al procesar el horario. Asegúrate de marcar días y horas.</div>
                </div>

                <div class="frm-grupo">
                    <label for="telefono" class="frm-etiqueta">Teléfono de Contacto *</label>
                    <div class="grupo-telefono">
                        <!-- MAPEO: name="phone" esperado por backend -->
                        <input type="tel" name="phone" id="telefono" class="frm-campo" required pattern="[0-9]{9,15}" placeholder="Ej: 612345678" value="<?php echo htmlspecialchars($form_data['phone'] ?? ($_SESSION['data']['phone'] ?? '')); ?>">
                        <label class="frm-checkbox check-whatsapp">
                            <!-- MAPEO: name="whatsapp" esperado por backend, value debe ser 1 -->
                            <input type="checkbox" name="whatsapp" value="1" <?php echo (isset($form_data['whatsapp']) && $form_data['whatsapp'] == 1) || (!isset($form_data['whatsapp']) && isset($_SESSION['data']['whatsapp']) && $_SESSION['data']['whatsapp'] == 1) ? 'checked' : ''; ?>> ¿Tienes WhatsApp?
                        </label>
                    </div>
                    <div class="error-msg oculto" id="error-telefono">Introduce un teléfono válido (solo números, 9-15 dígitos).</div>
                </div>

                <div class="frm-grupo">
                    <label class="frm-etiqueta">Idiomas que Hablas (Opcional)</label>
                    <!-- JS NECESARIO: Los selects idioma_1 e idioma_2 deben usarse para rellenar los campos ocultos hidden_lang_1 y hidden_lang_2 -->
                    <div class="grupo-idiomas">
                        <?php
                        $selected_lang1 = $form_data['lang-1'] ?? null;
                        $selected_lang2 = $form_data['lang-2'] ?? null;
                        ?>
                        <div class="par-idioma">
                            <select name="idioma_1" id="idioma_1" class="frm-campo frm-select">
                                <option value="">-- Idioma 1 --</option>
                                <?php // TODO: Cargar lista de idiomas COMPLETA como en el form antiguo
                                $idiomas_lista = ['es' => 'Español', 'en' => 'Inglés', 'fr' => 'Francés', 'de' => 'Alemán', 'pt' => 'Portugués', 'it' => 'Italiano']; // Ejemplo
                                foreach ($idiomas_lista as $code => $name) {
                                    // Usar el valor del campo oculto mapeado para seleccionar
                                    echo '<option value="' . htmlspecialchars($code) . '" ' . ($selected_lang1 == $code ? 'selected' : '') . '>' . htmlspecialchars($name) . '</option>';
                                }
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
                            <select name="idioma_2" id="idioma_2" class="frm-campo frm-select">
                                <option value="">-- Idioma 2 --</option>
                                <?php foreach ($idiomas_lista as $code => $name) {
                                    echo '<option value="' . htmlspecialchars($code) . '" ' . ($selected_lang2 == $code ? 'selected' : '') . '>' . htmlspecialchars($name) . '</option>';
                                } ?>
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
                    <!-- MAPEO: name="out" esperado por backend -->
                    <select name="out" id="realiza_salidas" class="frm-campo frm-select" required>
                        <?php $selected_out = $form_data['out'] ?? '0'; ?>
                        <option value="0" <?php echo ($selected_out == '0') ? 'selected' : ''; ?>>No</option>
                        <option value="1" <?php echo ($selected_out == '1') ? 'selected' : ''; ?>>Sí</option>
                    </select>
                    <div class="error-msg oculto" id="error-salidas">Debes indicar si realizas salidas.</div>
                </div>

                <?php if (!checkSession()): ?>
                    <div class="frm-grupo">
                        <label for="email" class="frm-etiqueta">Tu Email de Contacto *</label>
                        <!-- MAPEO: name="email" esperado por backend -->
                        <input type="email" name="email" id="email" class="frm-campo" required placeholder="Necesario para gestionar tu anuncio" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>">
                        <div class="ayuda-texto">Si ya tienes cuenta, usa el mismo email. Si no, crearemos una cuenta para ti.</div>
                        <div class="error-msg oculto" id="error-email">Introduce un email válido.</div>
                    </div>
                <?php else: ?>
                    <!-- Si está logueado, el backend espera el email igualmente -->
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
            </div>
        </div>

        <!-- ======================= ETAPA 3: EXTRAS OPCIONALES ======================= -->
        <div id="etapa-extras" class="etapa oculto">
            <h2 class="titulo-etapa"><?php echo checkSession() ? 'Paso 3' : 'Paso 4'; ?>: Destaca tu Anuncio (Opcional)</h2>
            <p>Aumenta la visibilidad de tu anuncio con nuestros servicios extra.</p>

            <!-- ADVERTENCIA: El campo 'extras[]' no existía. El backend podría ignorarlo o dar error. -->
            <!-- Considera quitar el atributo 'name' si causa problemas: name="extras_DISABLED[]" -->
            <div class="lista-opciones grupo-checkboxes-extra">
                <?php $selected_extras = $form_data['extras'] ?? []; ?>
                <label class="opcion-checkbox opcion-extra">
                    <input type="checkbox" name="extras[]" value="premium" <?php echo in_array('premium', $selected_extras) ? 'checked' : ''; ?>>
                    <div class="opcion-contenido"><strong>Premium (35 €)</strong><span>Tu anuncio aparecerá aleatoriamente en las posiciones superiores.</span></div>
                </label>
                <label class="opcion-checkbox opcion-extra">
                    <input type="checkbox" name="extras[]" value="premium_mini" <?php echo in_array('premium_mini', $selected_extras) ? 'checked' : ''; ?>>
                    <div class="opcion-contenido"><strong>Premium Mini (27 €)</strong><span>Tu anuncio aparecerá aleatoriamente bajo los Premium.</span></div>
                </label>
                <label class="opcion-checkbox opcion-extra">
                    <input type="checkbox" name="extras[]" value="destacado" <?php echo in_array('destacado', $selected_extras) ? 'checked' : ''; ?>>
                    <div class="opcion-contenido"><strong>Destacado (20 €)</strong><span>Tu anuncio aparecerá aleatoriamente con un diseño destacado.</span></div>
                </label>
                <label class="opcion-checkbox opcion-extra">
                    <input type="checkbox" name="extras[]" value="autosubida" <?php echo in_array('autosubida', $selected_extras) ? 'checked' : ''; ?>>
                    <div class="opcion-contenido"><strong>Autosubida (25 €)</strong><span>Tu anuncio subirá posiciones automáticamente (debajo de Destacados).</span></div>
                </label>
                <label class="opcion-checkbox opcion-extra">
                    <input type="checkbox" name="extras[]" value="banner_superior" <?php echo in_array('banner_superior', $selected_extras) ? 'checked' : ''; ?>>
                    <div class="opcion-contenido"><strong>Banner Superior (50 €)</strong><span>Muestra tu banner aleatoriamente en la cabecera de la página.</span></div>
                </label>
                <label class="opcion-checkbox opcion-extra">
                    <input type="checkbox" name="extras[]" value="banner_lateral" <?php echo in_array('banner_lateral', $selected_extras) ? 'checked' : ''; ?>>
                    <div class="opcion-contenido"><strong>Banner Lateral (50 €)</strong><span>Muestra tu banner aleatoriamente en la barra lateral.</span></div>
                </label>
                <!-- TODO: Añadir lógica JS para mostrar campos de subida de banner si se seleccionan -->
            </div>

            <fieldset class="frm-seccion terminos-finales">
                <div class="frm-grupo">
                    <label class="frm-checkbox">
                        <!-- MAPEO: name="terminos" esperado por backend -->
                        <input name="terminos" type="checkbox" id="terminos" value="1" required <?php echo (isset($form_data['terminos']) && $form_data['terminos'] == '1') ? 'checked' : ''; ?> />
                        He leído y acepto los <a href="/terminos-y-condiciones" target="_blank">Términos y Condiciones</a> y la <a href="/politica-privacidad" target="_blank">Política de Privacidad</a>. *
                    </label>
                    <div class="error-msg oculto" id="error-terminos">Debes aceptar los términos y condiciones.</div>
                </div>

                <div class="frm-grupo">
                    <label class="frm-checkbox">
                        <!-- MAPEO: name="notifications" esperado por backend -->
                        <?php
                        // Marcado por defecto si no hay datos previos, o según los datos previos si existen
                        $notifications_checked = true; // Por defecto
                        if (isset($form_data['notifications'])) {
                            $notifications_checked = ($form_data['notifications'] == '1');
                        }
                        ?>
                        <input name="notifications" type="checkbox" id="notifications" value="1" <?php echo $notifications_checked ? 'checked' : ''; ?> />
                        Quiero recibir notificaciones por email cuando alguien contacte a través de mi anuncio.
                    </label>
                </div>
            </fieldset>

            <div class="navegacion-etapa">
                <button type="button" class="frm-boton btn-anterior">Anterior</button>
                <!-- JS NECESARIO: El texto/acción de este botón podría cambiar según plan/extras -->
                <!-- El type="submit" es correcto para enviar el formulario directamente (sin JS reCAPTCHA) -->
                <button type="submit" id="btn-finalizar" class="frm-boton btn-publicar">Finalizar y Publicar</button>
            </div>
        </div>

    </form>

    <?php // <!-- ############################################################ -->
    ?>
    <?php // <!-- ##### FIN NUEVO FORMULARIO ADAPTADO ##### -->
    ?>
    <?php // <!-- ############################################################ -->
    ?>

</div>

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

<!-- Mantener los scripts JS antiguos si son necesarios para funciones fuera del form (payment_dialog, etc.) -->
<!-- ¡PERO CUIDADO! post.js interactuaba con el form antiguo (#new_item_post) -->
<!-- Necesitarás un NUEVO archivo JS para manejar el form multi-etapa y las interacciones específicas del nuevo form -->
<!-- <script src="<?= getConfParam('SITE_URL') ?>src/js/filter.js"></script> -->
<script src="<?= getConfParam('SITE_URL') ?>src/js/newPost.js"></script>

<?php // <!-- ELIMINADO: Script de ejecución de reCAPTCHA --> 
?>

<!-- Script Select2 del form antiguo. Puede que no sea necesario si no usas Select2 en el nuevo -->
<!-- O si lo usas, necesitas inicializarlo en los selects del NUEVO formulario -->
<!-- <script type="text/javascript">
    $(document).ready(function() {
        // Inicializar Select2 en los selects del nuevo formulario si los usas
        // $('#form-nuevo-anuncio select').select2(...);

        // La lógica de Sortable para fotos necesita adaptarse al nuevo contenedor #lista-fotos-subidas
        // $('.sortable').sortable(...); -> Cambiar a $('#lista-fotos-subidas').sortable(...);
    });
</script> -->
<?php loadBlock('datajson'); // Mantener si es necesario para otras partes de la página
?>