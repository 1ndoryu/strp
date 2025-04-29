<?php
// Cargar módulos necesarios (si aplica)
// loadModule("filter"); // Asumiendo que podría ser necesario o parte del boilerplate

// Variables para mensajes de éxito/error
$error_div = false;
$exito_div = false;

// --- OBTENER CATEGORÍAS ---
// Obtenemos todas las categorías disponibles para los desplegables
$categories = selectSQL("sc_category", array(), "name ASC");
// Creamos un mapa ID => Nombre para facilitar la visualización en la lista
$category_map = array();
if ($categories && count($categories) > 0) {
    foreach ($categories as $cat) {
        $category_map[$cat['ID_cat']] = $cat['name'];
    }
}


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
    // Obtener el ID de la categoría seleccionada
    $category_id = isset($_POST['new_service_category']) && is_numeric($_POST['new_service_category']) ? (int)$_POST['new_service_category'] : null;

    // Validar campos obligatorios (Nombre, Valor). Categoría podría ser opcional (NULL) o requerida.
    // Asumiremos que la categoría es REQUERIDA por ahora. Ajustar si puede ser NULL.
    if (!empty($name) && !empty($value) && $category_id !== null) {
        insertSQL("sc_services", $a = array(
            'name' => $name,
            'value' => $value, // Guardamos el valor introducido
            'ord' => $ord,
            'category' => $category_id // Guardamos el ID de la categoría
        ));
        $exito_div = "Servicio creado correctamente."; // Placeholder: NUEVO_LANG_STRING['manage_services.service_created']
    } else {
        $missing_fields = [];
        if (empty($name)) $missing_fields[] = "Nombre";
        if (empty($value)) $missing_fields[] = "Valor";
        if ($category_id === null) $missing_fields[] = "Categoría"; // Asumiendo requerida
        $error_div = "Los siguientes campos son obligatorios: " . implode(', ', $missing_fields) . ".";
        // Placeholder: NUEVO_LANG_STRING['manage_services.add_error_required_fields']
    }
}

// Modificar servicio (cuando se envía el formulario de edición)
if (isset($_POST['edit_service']) && isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id_service_to_edit = (int)$_GET['edit'];
    $name = $_POST['mod_service_name'] ?? '';
    $value = $_POST['mod_service_value'] ?? '';
    $ord = isset($_POST['mod_service_ord']) && is_numeric($_POST['mod_service_ord']) ? (int)$_POST['mod_service_ord'] : 0;
    // Obtener el ID de la categoría seleccionada
    $category_id = isset($_POST['mod_service_category']) && is_numeric($_POST['mod_service_category']) ? (int)$_POST['mod_service_category'] : null;

    // Asumiremos que la categoría es REQUERIDA por ahora. Ajustar si puede ser NULL.
    if (!empty($name) && !empty($value) && $category_id !== null) {
        updateSQL("sc_services", $a = array(
            'name' => $name,
            'value' => $value,
            'ord' => $ord,
            'category' => $category_id // Actualizamos el ID de la categoría
        ), $s = array('ID_service' => $id_service_to_edit));
        $exito_div = "Servicio actualizado correctamente."; // Placeholder: NUEVO_LANG_STRING['manage_services.service_edited']
        // Importante: Forzar recarga de datos si continuamos mostrando el form de edición
        // $mod = selectSQL("sc_services", $b = array('ID_service' => $id_service_to_edit));
        // Esto es para que si el guardado fue exitoso y seguimos en la página de edición,
        // se muestren los datos recién guardados (especialmente la categoría).
        // Sin embargo, a menudo es mejor redirigir a la lista después de guardar.
        // Si no rediriges, descomenta la línea de arriba o actualiza $mod[0] directamente.
        // Por simplicidad, asumimos que la página se recarga o se vuelve a la lista.

    } else {
         $missing_fields = [];
         if (empty($name)) $missing_fields[] = "Nombre";
         if (empty($value)) $missing_fields[] = "Valor";
         if ($category_id === null) $missing_fields[] = "Categoría"; // Asumiendo requerida
         $error_div = "Los siguientes campos son obligatorios: " . implode(', ', $missing_fields) . ".";
         // Placeholder: NUEVO_LANG_STRING['manage_services.edit_error_required_fields']
    }
}

// --- LÓGICA DE VISUALIZACIÓN ---

// Si estamos editando un servicio específico
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $service_id = (int)$_GET['edit'];
    // Volvemos a seleccionar por si hubo un error en el POST anterior y necesitamos mostrar los datos originales
    $mod = selectSQL("sc_services", $b = array('ID_service' => $service_id));

    // Verificar si el servicio existe
    if ($mod && count($mod) > 0) {
        $current_service = $mod[0]; // Más fácil de leer
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
            <input name="mod_service_name" type="text" value="<?= htmlspecialchars($current_service['name']); ?>" required>
            <div class="clear"></div>

            <label>Valor (clave interna)</label> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.value'] -->
            <input name="mod_service_value" type="text" value="<?= htmlspecialchars($current_service['value']); ?>" placeholder="Ej: masaje_relajante" required>
            <div class="clear"></div>

            <label>Categoría</label> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.category'] -->
            <select name="mod_service_category" required>
                <option value="">-- Seleccionar Categoría --</option>
                <?php
                if ($categories && count($categories) > 0) {
                    foreach ($categories as $category) {
                        // Marcar la categoría actual como seleccionada
                        $selected = ($category['ID_cat'] == $current_service['category']) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($category['ID_cat']) . '" ' . $selected . '>' . htmlspecialchars($category['name']) . '</option>';
                    }
                } else {
                     echo '<option value="" disabled>No hay categorías disponibles</option>'; // Placeholder: NUEVO_LANG_STRING['manage_services.no_categories']
                }
                ?>
            </select>
            <div class="clear"></div>

            <label>Orden</label> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.order'] -->
            <input name="mod_service_ord" type="number" value="<?= htmlspecialchars($current_service['ord']); ?>" placeholder="0">
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
    <h2>Gestionar Servicios Anuncio</h2> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.title_h1'] -->

    <!-- Formulario para añadir nuevo servicio -->
    <h3>Añadir Nuevo Servicio</h3> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.add_title'] -->
    <form action="index.php?id=manage_services_anuncio" method="post" class="param_form white_form">
         <input type="hidden" name="add_service" value="1"> <!-- Flag para identificar el POST de añadir -->

        <label>Nombre del Nuevo Servicio</label> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.add_name'] -->
        <input name="new_service_name" type="text" required>
        <div class="clear"></div>

        <label>Valor (clave interna)</label> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.add_value'] -->
        <input name="new_service_value" type="text" placeholder="Ej: masaje_relajante" required>
        <div class="clear"></div>

        <label>Categoría</label> <!-- Placeholder: NUEVO_LANG_STRING['manage_services.category'] -->
        <select name="new_service_category" required>
             <option value="">-- Seleccionar Categoría --</option>
             <?php
             if ($categories && count($categories) > 0) {
                 foreach ($categories as $category) {
                     echo '<option value="' . htmlspecialchars($category['ID_cat']) . '">' . htmlspecialchars($category['name']) . '</option>';
                 }
             } else {
                 echo '<option value="" disabled>No hay categorías disponibles</option>'; // Placeholder: NUEVO_LANG_STRING['manage_services.no_categories']
             }
             ?>
        </select>
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
        <?php if ($services && count($services) > 0) { // Añadido check $services &&
            for ($i = 0; $i < count($services); $i++) {
                // Buscar el nombre de la categoría usando el mapa creado al principio
                $category_name = isset($category_map[$services[$i]['category']])
                                 ? htmlspecialchars($category_map[$services[$i]['category']])
                                 : '<em>Sin categoría</em>'; // Placeholder: NUEVO_LANG_STRING['manage_services.no_category_assigned']
            ?>
                <li data-id="<?= $services[$i]['ID_service']; ?>" draggable="true">
                    <span class="col_left">
                        <img draggable="false" src="<?= getConfParam('SITE_URL'); ?>sc-admin/res/images/drag_indicator.svg" alt="Mover" style="cursor: grab; margin-right: 10px;"> <!-- Asumiendo ruta y función getConfParam -->
                        <b><?= htmlspecialchars($services[$i]['name']); ?></b>
                        (Valor: <?= htmlspecialchars($services[$i]['value']); ?> | Cat: <?= $category_name; ?> | Orden: <?= htmlspecialchars($services[$i]['ord']); ?>)
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
    <?php if ($services && count($services) > 1) { // Añadido check $services && ?>
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
// Asumimos que getConfParam('SITE_URL') devuelve la URL base correcta.
?>
<script type="text/javascript" src="<?= getConfParam('SITE_URL'); ?>sc-admin/res/manage_categories.js"></script>