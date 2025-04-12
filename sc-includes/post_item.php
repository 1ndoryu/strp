<?php
// --- Configuración y Entorno ---
// Determina la URL correcta para el action (con el ?id=...)
$formActionUrl = "/index.php?id=post_item"; // AJUSTA 'post_item' si tu archivo tiene otro nombre (ej: nuevo_anuncio)

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

/*

Mirad, te presento los datos que no maneja el servidor, esos datos deben manejarse y guardarse en la tabla del anuncio, el horario se suele guardar incorrectamente porque el formato cambio, haz una nueva columna para cada dato, creo que desde aquí se puede ejecutar un comando que creee las columnas para los servicios, horario e idiomas en caso de que no existan

const formDataForLog = new FormData(form);
console.log('FormData a enviar:');
for (let [key, value] of formDataForLog.entries()) {
    console.log(`${key}: ${value}`);
}

según asi se envian los datos (los que no se manejan aun, ignoro el resto que si funciona )

# Servicios elegidos, el servidor no los maneja 
newPost.js:601 servicios[]: masaje_relajante
newPost.js:601 servicios[]: masaje_podal

# Aqui solo selecione martes, miercoles y sabado, parece funcionar correctamente pero el servidor no lo maneja
newPost.js:601 horario_dia[lunes][inicio]: 00:00
newPost.js:601 horario_dia[lunes][fin]: 23:30
newPost.js:601 horario_dia[martes][activo]: 1
newPost.js:601 horario_dia[martes][inicio]: 00:00
newPost.js:601 horario_dia[martes][fin]: 23:30
newPost.js:601 horario_dia[miercoles][activo]: 1
newPost.js:601 horario_dia[miercoles][inicio]: 04:30
newPost.js:601 horario_dia[miercoles][fin]: 23:30
newPost.js:601 horario_dia[jueves][inicio]: 00:00
newPost.js:601 horario_dia[jueves][fin]: 23:30
newPost.js:601 horario_dia[viernes][inicio]: 00:00
newPost.js:601 horario_dia[viernes][fin]: 23:30
newPost.js:601 horario_dia[sabado][activo]: 1
newPost.js:601 horario_dia[sabado][inicio]: 06:30
newPost.js:601 horario_dia[sabado][fin]: 23:30
newPost.js:601 horario_dia[domingo][inicio]: 00:00
newPost.js:601 horario_dia[domingo][fin]: 23:30
newPost.js:601 phone: 0418545687

# Idiomas, el servidor no lo maneja
idioma_1: es
newPost.js:601 nivel_idioma_1: basico
newPost.js:601 idioma_2: en
newPost.js:601 nivel_idioma_2: nativo

*/

// --- Inicio Procesamiento POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error_insert = false;
    $_SESSION['form_data_error'] = $_POST; // Guardar POST para repoblar en caso de error

    if (isset($_POST['category']) && !empty($_POST['category'])) {
        $tokenValido = false;
        if (isset($_POST['token'])) {
            // Asume que DEBUG está definido en algún sitio si lo usas
            $tokenValido = verifyFormToken('postAdToken', $_POST['token']) || (defined('DEBUG') && DEBUG);
        }

        if ($tokenValido) {
            global $Connection; // Necesario para funciones SQL
            $datos_ad = array();

            // Datos básicos y de categoría
            $datos_ad['ID_cat'] = $_POST['category'];
            $category_ad = selectSQL('sc_category', array('ID_cat' => $datos_ad['ID_cat']));
            $datos_ad['parent_cat'] = ($category_ad && isset($category_ad[0]['parent_cat'])) ? $category_ad[0]['parent_cat'] : 0;

            // Recopilación de datos existentes del formulario
            $datos_ad['ID_region'] = isset($_POST['region']) ? $_POST['region'] : 0;
            $datos_ad['location'] = isset($_POST['city']) ? $_POST['city'] : '';
            $datos_ad['ad_type'] = isset($_POST['ad_type']) ? $_POST['ad_type'] : 1;
            $datos_ad['title'] = isset($_POST['tit']) ? $_POST['tit'] : '';
            $datos_ad['title_seo'] = isset($_POST['tit']) ? toAscii($_POST['tit']) : '';
            $datos_ad['texto'] = isset($_POST['text']) ? htmlspecialchars($_POST['text'], ENT_QUOTES, 'UTF-8') : '';
            $datos_ad['price'] = 0; 
            $datos_ad['address'] = $datos_ad['location']; // Ajustar si es necesario
            $datos_ad['name'] = isset($_POST['name']) ? formatName($_POST['name']) : '';
            $datos_ad['phone'] = isset($_POST['phone']) ? $_POST['phone'] : '';
            $datos_ad['whatsapp'] = isset($_POST['whatsapp']) && $_POST['whatsapp'] == '1' ? 1 : 0;
            $datos_ad['phone1'] = ''; // Ajustar si es necesario
            $datos_ad['whatsapp1'] = 0; // Ajustar si es necesario
            $datos_ad['seller_type'] = isset($_POST['seller_type']) ? $_POST['seller_type'] : 0;
            $datos_ad['notifications'] = isset($_POST['notifications']) && $_POST['notifications'] == '1' ? 1 : 0;
            $datos_ad['dis'] = isset($_POST['dis']) ? $_POST['dis'] : null;
            //$datos_ad['hor_start'] = isset($_POST['horario-inicio']) && !empty($_POST['horario-inicio']) ? $_POST['horario-inicio'] : '';
            //$datos_ad['hor_end'] = isset($_POST['horario-final']) && !empty($_POST['horario-final']) ? $_POST['horario-final'] : '';
            // $datos_ad['lang1'] = isset($_POST['lang-1']) && !empty($_POST['lang-1']) ? (int)$_POST['lang-1'] : 0; // Ignorado según solicitud
            // $datos_ad['lang2'] = isset($_POST['lang-2']) && !empty($_POST['lang-2']) ? (int)$_POST['lang-2'] : 0; // Ignorado según solicitud
            $datos_ad['out'] = isset($_POST['out']) ? $_POST['out'] : 0;
            $datos_ad['ID_order'] = isset($_POST['order']) ? $_POST['order'] : 0;
            $datos_ad['payment'] = "[]"; // Ajustar si es necesario

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

            // --- Lógica de Usuario ---
            $id_user = 0;
            if (!isset($_SESSION['data']['ID_user'])) { // Si no está logueado
                if (isset($_POST['email']) && !empty($_POST['email'])) {
                    $checkUser = selectSQL("sc_user", array('mail' => $_POST['email']));
                    $ip = get_client_ip();

                    if ($checkUser !== false && count($checkUser) == 0) { // Usuario no existe, intentar crear
                        if (!User::check_registered($ip, $datos_ad['phone'])) { // Comprobar restricciones
                            $banner_img = "";
                            if (isset($_POST['photo_name'][0])) {
                                $banner_img = Images::copyImage($_POST['photo_name'][0], 'user_path', 'ads_path', true); // Ajustar paths
                            }
                            $pass = randomString(6);
                            $limit_u = User::getLimitsByRol($datos_ad['seller_type']);
                            $datos_u = array(
                                'name' => $datos_ad['name'], 'mail' => $_POST['email'], 'phone' => $datos_ad['phone'],
                                'banner_img' => $banner_img, 'pass' => password_hash($pass, PASSWORD_DEFAULT), // Hashear contraseña
                                'date_reg' => time(), 'active' => 1, 'rol' => $datos_ad['seller_type'],
                                'date_credits' => "0", 'credits' => "0", 'IP_user' => $ip, 'anun_limit' => $limit_u
                            );
                            $result_u = insertSQL("sc_user", $datos_u);
                            if ($result_u) {
                                $id_user = lastIdSQL();
                                // Opcional: Enviar email de bienvenida con $pass
                            } else {
                                $_SESSION['form_error_message'] = "Error al crear la cuenta de usuario."; $error_insert = true;
                            }
                        } else {
                            $_SESSION['form_error_message'] = "Ya existe una cuenta reciente asociada a este teléfono o conexión."; $error_insert = true;
                        }
                    } elseif ($checkUser !== false && count($checkUser) > 0) { // Usuario ya existe
                        $id_user = $checkUser[0]['ID_user'];
                    } else { // Error consultando DB de usuarios
                        $_SESSION['form_error_message'] = "Error al verificar la cuenta de usuario existente."; $error_insert = true;
                    }
                } else { // Email obligatorio si no hay sesión
                     $_SESSION['form_error_message'] = "Se requiere un email para publicar."; $error_insert = true;
                }
            } else { // Usuario ya logueado
                $id_user = $_SESSION['data']['ID_user'];
            }

            // --- Proceso de Guardado del Anuncio ---
            if (!$error_insert && $id_user > 0) {
                $limite = check_item_limit($id_user);

                if (($limite > 0 || $limite == -1) || $datos_ad['ID_order'] != 0) { // Asumiendo -1 es ilimitado
                    $datos_ad['ID_user'] = $id_user;
                    $datos_ad['date_ad'] = time();
                    $datos_ad['review'] = (getConfParam('REVIEW_ITEM') == 1) ? 1 : 0;

                    // Validaciones básicas
                    $condicion_region = ($datos_ad['ID_region'] != 0);
                    $condicion_seller_type = ($datos_ad['seller_type'] != 0);
                    $condicion_cat = ($datos_ad['ID_cat'] != 0);
                    $condicion_user = ($datos_ad['ID_user'] > 0);
                    $condicion_titulo = (!empty($datos_ad['title']));
                    $condicion_texto = (!empty($datos_ad['texto']));

                    if ($condicion_region && $condicion_seller_type && $condicion_cat && $condicion_user && $condicion_titulo && $condicion_texto) {
                        $insert = false;
                        $last_ad = 0;
                        $db_error = '';

                        // Inserción en DB
                        if ($datos_ad['ID_order'] != 0) { // Con Orden asociada
                            $order = Orders::getOrderByID($datos_ad['ID_order']);
                            if ($order && isset($order['ID_ad']) && $order['ID_ad'] == 0) {
                                $insert = insertSQL("sc_ad", $datos_ad);
                                $last_ad = lastIdSQL();
                                if ($insert && $last_ad > 0) {
                                    updateSQL("sc_orders", array("ID_ad" => $last_ad), array("ID_order" => $datos_ad['ID_order']));
                                    Statistic::addAnuncioNuevoPremium();
                                } else if (!$insert && isset($Connection)) { $db_error = mysqli_error($Connection); }
                            } else {
                                $error_insert = true;
                                $_SESSION['form_error_message'] = "Error: La orden de pago no es válida o ya está asignada.";
                            }
                        } else { // Sin Orden (o gratuita)
                            $insert = insertSQL("sc_ad", $datos_ad);
                            $last_ad = lastIdSQL();
                            if ($insert && $last_ad > 0) {
                                Statistic::addAnuncioNuevo();
                            } else if (!$insert && isset($Connection)) { $db_error = mysqli_error($Connection); }
                        }

                        // Resultado de la inserción
                        if ($insert === false && !$error_insert) { // Fallo SQL directo
                           $error_insert = true;
                           $_SESSION['form_error_message'] = "Error al guardar los datos." . (!empty($db_error) ? " (DB: ".$db_error.")" : "");
                        }

                        // Post-inserción (imágenes, etc.)
                        if ($insert && $last_ad > 0) {
                            if (isset($_POST['photo_name']) && is_array($_POST['photo_name'])) {
                                foreach ($_POST['photo_name'] as $photo => $name) {
                                    if (!empty($name)) {
                                        updateSQL("sc_images", array('ID_ad' => $last_ad, 'position' => $photo, "status" => 1), array('name_image' => $name));
                                    }
                                }
                            }

                            checkRepeat($last_ad); // Función existente

                            // Éxito: Limpiar sesión y redirigir
                            unset($_SESSION['form_data_error']);
                            unset($_SESSION['form_error_message']);

                            $redirectUrl = ($datos_ad['ID_order'] != 0) ? "/publicado?payad=" . $datos_ad['ID_order'] : "/publicado";
                            echo '<script type="text/javascript">location.href = "' . $redirectUrl . '";</script>';
                            exit(); // Detener script

                        } else { // Fallo en inserción o lógica previa
                           $error_insert = true; // Marcar error si no lo estaba ya
                           if (!isset($_SESSION['form_error_message'])) {
                                $_SESSION['form_error_message'] = "Error inesperado al guardar el anuncio.";
                           }
                           // No redirigir, mostrar formulario con error
                        }

                    } else { // Fallaron validaciones básicas
                        $error_insert = true;
                        if (!$condicion_region) { $_SESSION['form_error_message'] = "Debes seleccionar una provincia."; }
                        elseif (!$condicion_cat) { $_SESSION['form_error_message'] = "Debes seleccionar una categoría."; }
                        elseif (!$condicion_seller_type) { $_SESSION['form_error_message'] = "Tipo de vendedor inválido."; }
                        elseif (!$condicion_titulo) { $_SESSION['form_error_message'] = "El título es obligatorio."; }
                        elseif (!$condicion_texto) { $_SESSION['form_error_message'] = "La descripción es obligatoria."; }
                        elseif (!$condicion_user) { $_SESSION['form_error_message'] = "Error procesando el usuario."; } // Este caso es menos probable aquí si ya pasó el bloque de usuario
                        else { $_SESSION['form_error_message'] = "Faltan datos obligatorios o son incorrectos."; }
                    }
                } else { // Límite de anuncios alcanzado
                    $error_insert = true;
                    $_SESSION['form_error_message'] = "Has alcanzado el límite de anuncios gratuitos.";
                }
            } // Fin if (!$error_insert && $id_user > 0)
            // Si hubo error de usuario, $error_insert ya es true y el mensaje está en sesión

        } else { // Token inválido
            $error_insert = true;
            $_SESSION['form_error_message'] = "Error de seguridad (token inválido). Intenta de nuevo.";
        }
    } else { // No se recibió categoría
        $error_insert = true;
        $_SESSION['form_error_message'] = "No se recibió la categoría.";
    }

    // Si $error_insert es true, el script continuará aquí, permitiendo mostrar el formulario con errores.
    // Los datos para repoblar están en $_SESSION['form_data_error']
    // El mensaje de error está en $_SESSION['form_error_message']

}

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
    <form id="form-nuevo-anuncio" class="formulario-multi-etapa" method="post" action="<?php echo htmlspecialchars($formActionUrl); ?>" enctype="multipart/form-data" autocomplete="off">
        <?php
        // Generar Token CSRF
        $token_q = generateFormToken('postAdToken');
        ?>
        <input type="hidden" name="token" id="token" value="<?= $token_q; ?>">
        <input type="hidden" id="new_order" name="order" value="<?php echo htmlspecialchars($form_data['order'] ?? '0'); ?>" />

        <?php // Campos ocultos (asegúrate que JS los actualice) 
        ?>
        <input type="hidden" name="seller_type" id="hidden_seller_type" value="<?php echo htmlspecialchars($form_data['seller_type'] ?? ''); ?>">
        <input type="hidden" name="dis" id="hidden_dis" value="<?php echo htmlspecialchars($form_data['dis'] ?? ''); ?>">
        <input type="hidden" name="horario-inicio" id="hidden_horario_inicio" value="<?php echo htmlspecialchars($form_data['horario-inicio'] ?? ''); ?>">
        <input type="hidden" name="horario-final" id="hidden_horario_final" value="<?php echo htmlspecialchars($form_data['horario-final'] ?? ''); ?>">
        <input type="hidden" name="lang-1" id="hidden_lang_1" value="<?php echo htmlspecialchars($form_data['lang-1'] ?? ''); ?>">
        <input type="hidden" name="lang-2" id="hidden_lang_2" value="<?php echo htmlspecialchars($form_data['lang-2'] ?? ''); ?>">
        <div id="hidden-photo-inputs">
            <?php // Repoblar fotos es complejo, mejor que JS lo maneje al cargar si hubo error 
            ?>
        </div>

        <?php // --- Etapas del Formulario (Tipo Usuario, Plan, Perfil, Extras) --- 
        ?>
        <?php // (El HTML de las etapas va aquí, usando $form_data para repoblar valores) 
        ?>
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
            <?php // Script para setear hidden_seller_type si está logueado 
            ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (document.getElementById("hidden_seller_type")) {
                        document.getElementById("hidden_seller_type").value = "<?php echo htmlspecialchars($_SESSION['data']['rol'] ?? ''); ?>";
                    }
                });
            </script>
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

    </form>
    <?php // --- Fin del Formulario HTML --- 
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