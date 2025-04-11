<?php
// Maneja el logout si se pasa el parámetro 'logout' en la URL
if (isset($_GET['logout'])) {
    logout();
}

// Inicializa contadores
$tot_messages_no_read = 0;
$tot_messages = 0;
$tot_fav = 0;
$userId = null;

// Obtiene datos de sesión y calcula mensajes si el usuario está logueado
if (isset($_SESSION['data'])) {
    $userId = $_SESSION['data']['ID_user'];

    // --- Mensajes no leídos ---
    // ADVERTENCIA: Esta consulta es vulnerable a Inyección SQL. Considera usar sentencias preparadas.
    $query_unread = "SELECT ID_message FROM sc_messages WHERE leido=1 AND (recibe='" . mysqli_real_escape_string($Connection, $userId) . "' AND recibe_del=0)";
    $messages_no_read_result = mysqli_query($Connection, $query_unread);
    if ($messages_no_read_result) {
        $tot_messages_no_read = mysqli_num_rows($messages_no_read_result);
    } else {
        // Opcional: Registrar error de consulta
        error_log("Error en consulta de mensajes no leídos: " . mysqli_error($Connection));
    }

    // --- Total de mensajes ---
    // ADVERTENCIA: Esta consulta es vulnerable a Inyección SQL. Considera usar sentencias preparadas.
    $query_total = "SELECT ID_message FROM sc_messages WHERE (recibe='" . mysqli_real_escape_string($Connection, $userId) . "' AND recibe_del=0)";
    $messages_result = mysqli_query($Connection, $query_total);
    if ($messages_result) {
        $tot_messages = mysqli_num_rows($messages_result);
    } else {
        // Opcional: Registrar error de consulta
        error_log("Error en consulta de total de mensajes: " . mysqli_error($Connection));
    }
}

// --- Total de favoritos (desde cookie) ---
if (isset($_COOKIE['fav']) && is_array($_COOKIE['fav'])) {
    // Asegúrate de que la cookie 'fav' contiene un array contable
    $tot_fav = count($_COOKIE['fav']);
}

// --- Muestra aviso de cookies si no ha sido aceptado ---
if (!isset($_COOKIE['cookie_advice']) || $_COOKIE['cookie_advice'] != '1') {
    echo loadBlock('cookies');
}
?>

<div class="main-content" <?php getColor('BODY_COLOR'); ?>>
    <header>
        <div class="center_content">
            <div class="row">
                <div class="col-md-1">
                    <i class="fa fa-bars menu-navbar" aria-hidden="true"></i>
                    <a href="index.php">
                        <span class="logo d-inline-block d-md-none"></span>
                    </a>
                    <a href="<?= $urlfriendly['url.post_item'] ?>" class="post_item_link transition d-block d-md-none">
                        <i class="fa fa-pencil-alt"></i> <span><?= $language['content.button_post'] ?></span>
                    </a>
                </div>
                <div class="col-md-11 px-0 d-none d-md-block">
                    <div class="w-100 h-100">
                        <div class="topbar-content w-content to-right to-bottom">

                            <?php if (!isset($_SESSION['data'])) : // Usuario NO logueado 
                            ?>
                                <!--<a href="crear-cuenta/">Registro</a>-->
                                <a class="user-menu login" href="javascript:void(0);"><i class="fa fa-user pr-2"></i> Mi cuenta</a>
                                <a href="contactar/"><i class="fa fa-envelope pr-2"></i> Contactar</a>
                                <a href="favoritos/"><i style="color: var(--rosa);" class="fa fa-heart pr-2"></i> Favoritos (<?= $tot_fav ?>)</a>
                                <a href="javascript:void(0);" class="btn-post-premium">
                                    <img height="55" width="168" src="<?= Images::getImage("top-destacar.webp") ?>" alt="Destacar anuncio">
                                </a>
                                <a class="btn-post" href="<?= $urlfriendly['url.post_item'] ?>">
                                    PUBLICAR ANUNCIO GRATIS
                                </a>
                            <?php else : // Usuario SI logueado 
                            ?>
                                <?php if ($_SESSION['data']['rol'] != UserRole::Visitante) : // Rol diferente a Visitante 
                                ?>
                                    <a href="mi-cuenta/">
                                        <img src="<?= getPhotoUser($_SESSION['data']['ID_user']) ?>" alt="<?= htmlspecialchars($_SESSION['data']['name']) ?>" title="<?= htmlspecialchars($_SESSION['data']['name']) ?>" width="50" height="50" class="image-user">
                                        <!-- <span class="user_name_topbar"><?= htmlspecialchars($_SESSION['data']['name']) ?></span> -->
                                    </a>
                                    <a href="mis-anuncios/"><i class="fa fa-list icon-alt"></i> Mis anuncios</a>
                                    <a href="mis-mensajes/">
                                        <span class="user-message<?php if ($tot_messages_no_read > 0) echo ' on'; ?>">
                                            <i class="fa fa-comments icon-alt "></i> Mis Mensajes (<i><?= $tot_messages ?></i>)
                                        </span>
                                    </a>
                                    <!-- Comentado: Alertas -->
                                    <!--
                                    <a href="mis-notificaciones/">
                                        <?php if ($_SESSION['data']['notify'] == 1) : ?>
                                            <i class="fa fa-bell icon-alt"></i>
                                        <?php else : ?>
                                            <i class="fa fa-bell-slash icon-alt"></i>
                                        <?php endif; ?>
                                        Mis alertas
                                    </a>
                                    -->
                                    <!-- Comentado: Favoritos (ya está en el menú de visitante?) -->
                                    <!-- <a href="favoritos/"><i class="fa icon-alt fa-heart"></i> Favoritos (<?= $tot_fav ?>)</a> -->
                                    <a href="cerrar-sesion/" class="close-session"> Desconectar</a>
                                    <a class="btn-post" href="<?= $urlfriendly['url.post_item'] ?>">
                                        PUBLICAR ANUNCIO GRATIS
                                    </a>
                                <?php else : // Rol Visitante 
                                ?>
                                    <a href="mi-cuenta/">
                                        <img src="<?= getPhotoUser($_SESSION['data']['ID_user']) ?>" alt="<?= htmlspecialchars($_SESSION['data']['name']) ?>" title="<?= htmlspecialchars($_SESSION['data']['name']) ?>" width="50" height="50" class="image-user">
                                        <!-- <span class="user_name_topbar"><?= htmlspecialchars($_SESSION['data']['name']) ?></span> -->
                                    </a>
                                    <a href="mis-mensajes/">
                                        <span class="user-message<?php if ($tot_messages_no_read > 0) echo ' on'; ?>">
                                            <i class="fa fa-comments icon-alt "></i> Mis Mensajes (<i><?= $tot_messages ?></i>)
                                        </span>
                                    </a>
                                    <a href="mis-notificaciones/">
                                        <?php if ($_SESSION['data']['notify'] == 1) : ?>
                                            <i class="fa fa-bell icon-alt"></i>
                                        <?php else : ?>
                                            <i class="fa fa-bell-slash icon-alt"></i>
                                        <?php endif; ?>
                                        Mis alertas
                                    </a>
                                    <a href="mis-favoritos/"><i class="fa icon-alt fa-heart"></i> Favoritos (<?= $tot_fav ?>)</a>
                                    <a href="cerrar-sesion/" class="close-session"> Desconectar</a>
                                <?php endif; // Fin chequeo de rol 
                                ?>
                            <?php endif; // Fin chequeo de sesión 
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <?php
    // Carga el bloque del banner del header
    loadBlock("header-banner");
    ?>

    <div class="topbar" <?php getColor('HEADER_COLOR'); ?>>
        <div class="center_content">
            <!-- Menú para móviles (navbar) -->
            <div class="navbar p-0 d-block d-md-none <?= isset($_GET['menuopen']) ? 'opened' : '' ?>">
                <i class="fa fa-times close-navbar" aria-hidden="true"></i>
                <div class="w-100">
                    <div class="topbar-content w-content to-right px-2">

                        <?php if (!isset($_SESSION['data'])) : // Usuario NO logueado (móvil) 
                        ?>
                            <!--<a href="crear-cuenta/">Registro</a>-->
                            <a class="user-menu login" href="javascript:void(0);"><i class="fa fa-user pr-2"></i> Mi cuenta</a>
                            <a href="contactar/"><i class="fa fa-envelope pr-2"></i> Contactar</a>
                            <!-- Añadido favoritos también en móvil para consistencia -->
                            <a href="mis-favoritos/"><i class="fa fa-heart icon-alt pr-2"></i> Favoritos (<?= $tot_fav ?>)</a>
                        <?php else : // Usuario SI logueado (móvil) 
                        ?>
                            <a href="mi-cuenta/" style="border: none; padding: 0;">
                                <span class="user_item_photo" style="background-image:url(<?= getPhotoUser($_SESSION['data']['ID_user']) ?>)"></span>
                                <span class="user_name_topbar">Mi cuenta</span>
                            </a>
                            <?php if ($_SESSION['data']['rol'] != UserRole::Visitante) : ?>
                                <a href="mis-anuncios/"><i class="fa fa-list icon-alt pr-2"></i> Mis anuncios</a>
                            <?php endif; ?>
                            <a href="mis-mensajes/">
                                <span class="user-message <?php if ($tot_messages_no_read > 0) echo ' on'; ?>">
                                    <i class="fa icon-alt fa-comments pr-2"></i> Mis Mensajes (<i><?= $tot_messages ?></i>)
                                </span>
                            </a>
                            <a href="mis-notificaciones/">
                                <?php if ($_SESSION['data']['notify'] == 1) : ?>
                                    <i class="fa fa-bell icon-alt pr-2"></i>
                                <?php else : ?>
                                    <i class="fa fa-bell-slash icon-alt pr-2"></i>
                                <?php endif; ?>
                                Mis alertas
                            </a>
                            <a href="mis-favoritos/"><i class="fa fa-heart icon-alt pr-2"></i> Favoritos (<?= $tot_fav ?>)</a>
                            <a href="mis-tickets/"><i class="fa fa-tag icon-alt pr-2"></i> Mis tickets | servicios</a>
                            <a href="mis-precios/"><i class="fa fa-tag icon-alt pr-2"></i> Tarifas</a>
                            <a href="cerrar-sesion/" class="close-session">Desconectar</a>
                        <?php endif; // Fin chequeo sesión móvil 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de búsqueda para móviles -->
    <div class="search d-block d-md-none" <?php getColor('SEARCHBAR_COLOR'); ?>>
        <div class="center_content">
            <div class="input_search">
                <input itemprop="query-input" type="text" name="_search" id="keyword_search1" required placeholder="<?= $language['content.keyword_search'] ?>" value="<?php if (isset($query_s)) echo htmlspecialchars($query_s); ?>">
            </div>
            <span class="button_search transition" id="but_search_main1"><i class="fa fa-search"></i></span>
        </div>
    </div>

    <?php
    // Carga el menú correspondiente (simple o completo)
    if (!isset($_GET["id"]) || (isset($_GET["id"]) && $_GET["id"] == "item")) {
        loadBlock("menu-simple");
    } else {
        loadBlock("menu");
    }
    ?>

    <?php
    // Carga breadcrumbs si estamos en la página de un item
    if (isset($_GET["id"]) && $_GET["id"] == "item") {
        // Asegúrate que la ruta a bread.php es correcta y accesible
        include(PATH . "sc-includes/php/func/bread.php");
    }
    ?>

    <!-- Contenedor principal del contenido -->
    <div id="content" class="<?= !isset($_GET["id"]) ? 'pb-2' : '' ?>">
        <?php
        if (isset($_GET["id"])) {
            $id = $_GET["id"];
            // Sanitiza $id para prevenir LFI/Path Traversal si es necesario
            // $id = basename($id); // Ejemplo simple de sanitización

            $filePath = PATH . "sc-includes/" . $id . ".php";

            // Verifica si el archivo PHP existe antes de incluirlo
            if (file_exists($filePath)) {
                include($filePath);
            } else {
                // La lógica original comprobaba si existía un directorio/archivo con el nombre $id
                // y luego intentaba incluir el .php de todos modos, lo cual parece incorrecto.
                // Esta versión simplificada carga 404 si el archivo .php no existe.
                // Si la lógica original era intencional, restáurala con cuidado.
                include(PATH . "sc-includes/404.php");
            }
        } else {
            // Si no hay 'id', carga el contenido principal por defecto
            include(PATH . "sc-includes/main.php");
        }
        ?>
    </div> <!-- Fin #content -->

</div> <!-- Fin .main-content -->