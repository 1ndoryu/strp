<?php
// Cargar módulos necesarios (si aplica)
loadModule("filter"); // Asumiendo que podría ser necesario o parte del boilerplate

// Variables para mensajes de éxito/error
$error_div = false;
$exito_div = false;

// --- MANEJO DE ACCIONES POST/GET ---

// Guardar orden de los servicios (Drag & Drop)
if (isset($_POST['save_order'])) {
    $data = $_POST['data'];
    $data = json_decode($data, true);
    if (is_array($data)) {
        foreach ($data as $id => $order) {
            // Actualiza la tabla 'sc_services' con la columna 'ID_service'
            updateSQL("sc_services", array('ord' => ($order + 1)), array('ID_service' => $id));
        }
        $exito_div = "Orden de los servicios actualizado correctamente."; // Placeholder: NUEVO_LANG_STRING['manage_services.order_saved']
    } else {
        $error_div = "Error al procesar el orden de los servicios."; // Placeholder: NUEVO_LANG_STRING['manage_services.order_error']
    }
}

// Capturar mensajes de éxito de otros módulos (si aplica)
// $exito_div = ModFilter::catch(); // Comentado si no es relevante para servicios

// Borrar servicio
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    deleteSQL("sc_services", $w = array('ID_service' => $_GET['delete']));
    $exito_div = "Servicio eliminado correctamente."; // Placeholder: NUEVO_LANG_STRING['manage_services.service_deleted']
}

// Añadir nuevo servicio
if (isset($_POST['add_service'])) {
    $name = $_POST['new_service_name'] ?? '';
    $value = $_POST['new_service_value'] ?? ''; // El 'value' se introduce manualmente
    $ord = isset($_POST['new_service_ord']) && is_numeric($_POST['new_service_ord']) ? (int)$_POST['new_service_ord'] : 0;

    if (!empty($name) && !empty($value)) {
        insertSQL("sc_services", $a = array(
            'name' => $name,
            'value' => $value, // Guardamos el valor introducido
            'ord' => $ord
        ));
        $exito_div = "Servicio creado correctamente."; // Placeholder: NUEVO_LANG_STRING['manage_services.service_created']
    } else {
        $error_div = "El nombre y el valor del servicio son obligatorios."; // Placeholder: NUEVO_LANG_STRING['manage_services.add_error_required']
    }
}

// Modificar servicio (cuando se envía el formulario de edición)
if (isset($_POST['edit_service']) && isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id_service_to_edit = (int)$_GET['edit'];
    $name = $_POST['mod_service_name'] ?? '';
    $value = $_POST['mod_service_value'] ?? '';
    $ord = isset($_POST['mod_service_ord']) && is_numeric($_POST['mod_service_ord']) ? (int)$_POST['mod_service_ord'] : 0;

    if (!empty($name) && !empty($value)) {
        updateSQL("sc_services", $a = array(
            'name' => $name,
            'value' => $value,
            'ord' => $ord
        ), $s = array('ID_service' => $id_service_to_edit));
        $exito_div = "Servicio actualizado correctamente."; // Placeholder: NUEVO_LANG_STRING['manage_services.service_edited']
    } else {
         $error_div = "El nombre y el valor del servicio son obligatorios."; // Placeholder: NUEVO_LANG_STRING['manage_services.edit_error_required']
    }
}

// --- LÓGICA DE VISUALIZACIÓN ---

// Si estamos editando un servicio específico
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $service_id = (int)$_GET['edit'];
    $mod = selectSQL("sc_services", $b = array('ID_service' => $service_id));

    // Verificar si el servicio existe
    if ($mod && count($mod) > 0) {
        ?>
        <h2>Editar Servicio</h2> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.title_edit'] -->
        <?php if ($exito_div !== FALSE) { ?>
            <div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?= htmlspecialchars($exito_div); ?></div>
        <?php } ?>
         <?php if ($error_div !== FALSE) { ?>
            <div class="info_error"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><?= htmlspecialchars($error_div); ?></div> <!-- Asumiendo una clase info_error -->
        <?php } ?>

        <form action="index.php?id=manage_services_anuncio&edit=<?= $service_id; ?>" method="post" class="param_form white_form">
            <input type="hidden" name="edit_service" value="1"> <!-- Flag para identificar el POST de edición -->

            <label>Nombre del Servicio</label> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.name'] -->
            <input name="mod_service_name" type="text" value="<?= htmlspecialchars($mod[0]['name']); ?>">
            <div class="clear"></div>

            <label>Valor (clave interna)</label> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.value'] -->
            <input name="mod_service_value" type="text" value="<?= htmlspecialchars($mod[0]['value']); ?>" placeholder="Ej: masaje_relajante">
            <div class="clear"></div>

            <label>Orden</label> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.order'] -->
            <input name="mod_service_ord" type="number" value="<?= htmlspecialchars($mod[0]['ord']); ?>" placeholder="0">
            <div class="clear"></div>

            <input name="Modificar" type="submit" value="Guardar Cambios"> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.button_save'] -->
        </form>
        <a href="index.php?id=manage_services_anuncio" class="back">« Volver a la lista de servicios</a> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.back_list'] -->
        <?php
    } else {
        // Servicio no encontrado
        echo "<h2>Error</h2>"; // Placeholder: NUEVO_LANG_STRING['manage_services.error_title']
        echo "<div class='info_error'>Servicio no encontrado.</div>"; // Placeholder: NUEVO_LANG_STRING['manage_services.error_not_found']
        echo '<a href="index.php?id=manage_services_anuncio" class="back">« Volver a la lista de servicios</a>'; // Placeholder: NUEVO_LANG_STRING['manage_services.back_list']
    }
}
// Si estamos en la vista principal (listar y añadir)
else {
    // Obtener todos los servicios ordenados
    // Asegúrate de que la consulta usa 'ord ASC' si quieres que el orden inicial se respete
    $services = selectSQL("sc_services", array(), "ord ASC");
    ?>
    <h2>Gestionar Servicios</h2> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.title_h1'] -->

    <!-- Formulario para añadir nuevo servicio -->
    <form action="index.php?id=manage_services_anuncio" method="post" class="param_form white_form">
         <input type="hidden" name="add_service" value="1"> <!-- Flag para identificar el POST de añadir -->

        <label>Nombre del Nuevo Servicio</label> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.add_name'] -->
        <input name="new_service_name" type="text" required>
        <div class="clear"></div>

        <label>Valor (clave interna)</label> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.add_value'] -->
        <input name="new_service_value" type="text" placeholder="Ej: masaje_relajante" required>
        <div class="clear"></div>

        <label>Orden (Opcional)</label> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.add_order'] -->
        <input name="new_service_ord" type="number" placeholder="0">
        <div class="clear"></div>

        <input name="add" type="submit" value="Añadir Servicio"> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.button_add'] -->
    </form>
    <hr />

    <!-- Mensajes de éxito/error -->
    <?php if ($exito_div !== FALSE) { ?>
        <div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?= htmlspecialchars($exito_div); ?></div>
    <?php } ?>
    <?php if ($error_div !== FALSE) { ?>
            <div class="info_error"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><?= htmlspecialchars($error_div); ?></div> <!-- Asumiendo una clase info_error -->
        <?php } ?>

    <!-- Lista de servicios existentes -->
    <h3>Servicios Existentes</h3> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.existing_list'] -->
    <ul class="list_categories" id="serviceList"> <!-- Cambiado ID a serviceList para el JS -->
        <?php if (count($services) > 0) {
            for ($i = 0; $i < count($services); $i++) { ?>
                <li data-id="<?= $services[$i]['ID_service']; ?>" draggable="true">
                    <span class="col_left">
                        <img draggable="false" src="<?= Images::getImage('drag_indicator.svg', Images::IMG); ?>" alt="Mover" style="cursor: grab; margin-right: 10px;"> <!-- Asumiendo que Images::getImage funciona -->
                        <b><?= htmlspecialchars($services[$i]['name']); ?></b> (Valor: <?= htmlspecialchars($services[$i]['value']); ?> | Orden: <?= htmlspecialchars($services[$i]['ord']); ?>)
                    </span>
                    <span class="col_right">
                        <a href="index.php?id=manage_services_anuncio&edit=<?= $services[$i]['ID_service']; ?>">Editar</a> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.edit'] -->
                        <a href="index.php?id=manage_services_anuncio&delete=<?= $services[$i]['ID_service']; ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este servicio?');">Eliminar</a> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.delete'] y NUEVO_LANG_STRING['manage_services.delete_confirm'] -->
                    </span>
                </li>
            <?php }
        } else {
            echo "<li>No hay servicios definidos todavía.</li>"; // Placeholder: NUEVO_LANG_STRING['manage_services.no_services']
        } ?>
    </ul>

    <!-- Botón y formulario para guardar el orden -->
    <?php if (count($services) > 1) { // Solo mostrar si hay más de un servicio para ordenar ?>
        <div class="text-center" style="margin-top: 20px;">
            <button id="save_order" class="btn btn-primary">Guardar Orden</button> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.button_save_order'] -->
        </div>
        <form action="index.php?id=manage_services_anuncio" method="post" id="save_order_form">
            <input type="hidden" name="save_order" value="1">
            <input type="hidden" id="save_order_data" name="data" value=""> <!-- ID que usa el JS -->
        </form>
    <?php } ?>

<?php
} // Fin del else (vista principal)

// Incluir el JavaScript para drag & drop (el mismo que para categorías)
// Asegúrate de que este JS pueda manejar el ID #serviceList o ajústalo si es necesario.
?>
<script type="text/javascript" src="<?= getConfParam('SITE_URL'); ?>sc-admin/res/manage_categories.js"></script>