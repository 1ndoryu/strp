<?php
// Cargar módulos necesarios (si aplica)
// Asumiendo que 'selectSQL', 'insertSQL', 'updateSQL', 'deleteSQL', 'getConfParam', 'Images' son funciones definidas en tu framework/entorno.
// loadModule("filter"); // Comentado si no es esencial para esta lógica

// Variables para mensajes de éxito/error
$error_div = false;
$exito_div = false;

// --- OBTENER CATEGORÍAS (Necesario para los formularios) ---
$categories = selectSQL("sc_category", array(), "name ASC"); // Obtener todas las categorías ordenadas por nombre
$category_map = []; // Crear un mapa ID => Nombre para facilitar la visualización en la lista
if ($categories) {
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
            updateSQL("sc_services", array('ord' => ($order + 1)), array('ID_service' => $id));
        }
        $exito_div = "Orden de los servicios actualizado correctamente.";
    } else {
        $error_div = "Error al procesar el orden de los servicios.";
    }
}

// Capturar mensajes de éxito de otros módulos (si aplica)
// $exito_div = ModFilter::catch();

// Borrar servicio
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    deleteSQL("sc_services", $w = array('ID_service' => $_GET['delete']));
    $exito_div = "Servicio eliminado correctamente.";
}

// Añadir nuevo servicio
if (isset($_POST['add_service'])) {
    $name = $_POST['new_service_name'] ?? '';
    $value = $_POST['new_service_value'] ?? '';
    $ord = isset($_POST['new_service_ord']) && is_numeric($_POST['new_service_ord']) ? (int)$_POST['new_service_ord'] : 0;
    // --- NUEVO: Capturar ID de categoría ---
    $category_id = isset($_POST['new_service_category']) && is_numeric($_POST['new_service_category']) ? (int)$_POST['new_service_category'] : null; // Puede ser NULL si no se selecciona

    // --- VALIDACIÓN: Asegurarse que la categoría sea obligatoria si es necesario ---
    // Descomenta la siguiente línea si la categoría es obligatoria
    // if (!empty($name) && !empty($value) && $category_id !== null) {
    if (!empty($name) && !empty($value)) { // Manteniendo la validación original, pero añadiendo la categoría al insert
        $data_to_insert = array(
            'name' => $name,
            'value' => $value,
            'ord' => $ord
            // --- NUEVO: Añadir categoría al array de inserción ---
            // Si category_id es null, se insertará NULL en la BD (si la columna lo permite)
        );
         if ($category_id !== null) {
             $data_to_insert['category'] = $category_id;
         }

        insertSQL("sc_services", $data_to_insert);
        $exito_div = "Servicio creado correctamente.";
    } else {
        // Ajusta el mensaje de error si la categoría es obligatoria
        $error_div = "El nombre y el valor del servicio son obligatorios.";
        // if ($category_id === null) { // Si la categoría es obligatoria y no se seleccionó
        //     $error_div = "El nombre, valor y categoría del servicio son obligatorios.";
        // }
    }
}

// Modificar servicio (cuando se envía el formulario de edición)
if (isset($_POST['edit_service']) && isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id_service_to_edit = (int)$_GET['edit'];
    $name = $_POST['mod_service_name'] ?? '';
    $value = $_POST['mod_service_value'] ?? '';
    $ord = isset($_POST['mod_service_ord']) && is_numeric($_POST['mod_service_ord']) ? (int)$_POST['mod_service_ord'] : 0;
    // --- NUEVO: Capturar ID de categoría ---
    $category_id = isset($_POST['mod_service_category']) && is_numeric($_POST['mod_service_category']) ? (int)$_POST['mod_service_category'] : null;

    // --- VALIDACIÓN: Asegurarse que la categoría sea obligatoria si es necesario ---
    // Descomenta la siguiente línea si la categoría es obligatoria
    // if (!empty($name) && !empty($value) && $category_id !== null) {
    if (!empty($name) && !empty($value)) { // Manteniendo la validación original
         $data_to_update = array(
            'name' => $name,
            'value' => $value,
            'ord' => $ord
            // --- NUEVO: Añadir categoría al array de actualización ---
            // Si es null, actualiza a NULL, si es un ID válido, actualiza con el ID
        );
        // Solo añadimos la categoría si se seleccionó una (o se dejó en "Sin categoría", que daría null)
        // Esto permite quitar una categoría asignada previamente si se elige "Sin categoría".
         $data_to_update['category'] = $category_id;


        updateSQL("sc_services", $data_to_update , $s = array('ID_service' => $id_service_to_edit));
        $exito_div = "Servicio actualizado correctamente.";
    } else {
         // Ajusta el mensaje de error si la categoría es obligatoria
         $error_div = "El nombre y el valor del servicio son obligatorios.";
         // if ($category_id === null) { // Si la categoría es obligatoria y no se seleccionó
         //    $error_div = "El nombre, valor y categoría del servicio son obligatorios.";
         // }
    }
}

// --- LÓGICA DE VISUALIZACIÓN ---

// Si estamos editando un servicio específico
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $service_id = (int)$_GET['edit'];
    $mod = selectSQL("sc_services", $b = array('ID_service' => $service_id));

    // Verificar si el servicio existe
    if ($mod && count($mod) > 0) {
        $current_service = $mod[0]; // Datos del servicio actual
        ?>
        <h2>Editar Servicio</h2>
        <?php if ($exito_div !== FALSE) { ?>
            <div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?= htmlspecialchars($exito_div); ?></div>
        <?php } ?>
         <?php if ($error_div !== FALSE) { ?>
            <div class="info_error"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><?= htmlspecialchars($error_div); ?></div>
        <?php } ?>

        <form action="index.php?id=manage_services_anuncio&edit=<?= $service_id; ?>" method="post" class="param_form white_form">
            <input type="hidden" name="edit_service" value="1">

            <label>Nombre del Servicio</label>
            <input name="mod_service_name" type="text" value="<?= htmlspecialchars($current_service['name']); ?>" required>
            <div class="clear"></div>

            <label>Valor (clave interna)</label>
            <input name="mod_service_value" type="text" value="<?= htmlspecialchars($current_service['value']); ?>" placeholder="Ej: masaje_relajante" required>
            <div class="clear"></div>

            <label>Orden</label>
            <input name="mod_service_ord" type="number" value="<?= htmlspecialchars($current_service['ord']); ?>" placeholder="0">
            <div class="clear"></div>

            <!-- === NUEVO: Selector de Categoría === -->
            <label>Categoría</label>
            <select name="mod_service_category">
                <option value="">-- Sin categoría --</option> <!-- Opción para desasignar categoría -->
                <?php
                if ($categories) {
                    foreach ($categories as $category) {
                        $selected = ($current_service['category'] == $category['ID_cat']) ? 'selected' : ''; // Preseleccionar la actual
                        echo "<option value=\"".htmlspecialchars($category['ID_cat'])."\" $selected>".htmlspecialchars($category['name'])."</option>";
                    }
                }
                ?>
            </select>
            <div class="clear"></div>
            <!-- === FIN NUEVO === -->

            <input name="Modificar" type="submit" value="Guardar Cambios">
        </form>
        <a href="index.php?id=manage_services_anuncio" class="back">« Volver a la lista de servicios</a>
        <?php
    } else {
        // Servicio no encontrado
        echo "<h2>Error</h2>";
        echo "<div class='info_error'>Servicio no encontrado.</div>";
        echo '<a href="index.php?id=manage_services_anuncio" class="back">« Volver a la lista de servicios</a>';
    }
}
// Si estamos en la vista principal (listar y añadir)
else {
    // Obtener todos los servicios ordenados
    $services = selectSQL("sc_services", array(), "ord ASC");
    ?>
    <h2>Gestionar Servicios Anuncio</h2>

    <!-- Formulario para añadir nuevo servicio -->
    <form action="index.php?id=manage_services_anuncio" method="post" class="param_form white_form">
         <input type="hidden" name="add_service" value="1">

        <label>Nombre del Nuevo Servicio</label>
        <input name="new_service_name" type="text" required>
        <div class="clear"></div>

        <label>Valor (clave interna)</label>
        <input name="new_service_value" type="text" placeholder="Ej: masaje_relajante" required>
        <div class="clear"></div>

        <label>Orden (Opcional)</label>
        <input name="new_service_ord" type="number" placeholder="0">
        <div class="clear"></div>

        <!-- === NUEVO: Selector de Categoría === -->
        <label>Categoría</label>
         <select name="new_service_category">
            <option value="">-- Sin categoría --</option> <!-- Opción por defecto o para no asignar -->
             <?php
             if ($categories) {
                 foreach ($categories as $category) {
                     echo "<option value=\"".htmlspecialchars($category['ID_cat'])."\">".htmlspecialchars($category['name'])."</option>";
                 }
             } else {
                 echo "<option value=\"\" disabled>No hay categorías creadas</option>";
             }
             ?>
         </select>
        <div class="clear"></div>
        <!-- === FIN NUEVO === -->


        <input name="add" type="submit" value="Añadir Servicio">
    </form>
    <hr />

    <!-- Mensajes de éxito/error -->
    <?php if ($exito_div !== FALSE) { ?>
        <div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?= htmlspecialchars($exito_div); ?></div>
    <?php } ?>
    <?php if ($error_div !== FALSE) { ?>
            <div class="info_error"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i><?= htmlspecialchars($error_div); ?></div>
        <?php } ?>

    <!-- Lista de servicios existentes -->
    <h3>Servicios Existentes</h3>
    <ul class="list_categories" id="serviceList"> <!-- ID 'serviceList' para el JS -->
        <?php if (count($services) > 0) {
            for ($i = 0; $i < count($services); $i++) {
                // --- NUEVO: Obtener nombre de categoría desde el mapa ---
                $category_name = 'Sin categoría'; // Valor por defecto
                if (isset($services[$i]['category']) && isset($category_map[$services[$i]['category']])) {
                    $category_name = htmlspecialchars($category_map[$services[$i]['category']]);
                }
                ?>
                <li data-id="<?= $services[$i]['ID_service']; ?>" draggable="true">
                    <span class="col_left">
                        <img draggable="false" src="<?= getConfParam('SITE_URL'); ?>sc-admin/res/img/drag_indicator.svg" alt="Mover" style="cursor: grab; margin-right: 10px;"> <!-- Ajusta la ruta si es necesario -->
                        <b><?= htmlspecialchars($services[$i]['name']); ?></b>
                        (Valor: <?= htmlspecialchars($services[$i]['value']); ?> |
                         Orden: <?= htmlspecialchars($services[$i]['ord']); ?> |
                         <!-- NUEVO: Mostrar nombre de categoría -->
                         Cat: <?= $category_name; ?>)
                    </span>
                    <span class="col_right">
                        <a href="index.php?id=manage_services_anuncio&edit=<?= $services[$i]['ID_service']; ?>">Editar</a>
                        <a href="index.php?id=manage_services_anuncio&delete=<?= $services[$i]['ID_service']; ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este servicio?');">Eliminar</a>
                    </span>
                </li>
            <?php }
        } else {
            echo "<li>No hay servicios definidos todavía.</li>";
        } ?>
    </ul>

    <!-- Botón y formulario para guardar el orden -->
    <?php if (count($services) > 1) { ?>
        <div class="text-center" style="margin-top: 20px;">
            <button id="save_order" class="btn btn-primary">Guardar Orden</button>
        </div>
        <form action="index.php?id=manage_services_anuncio" method="post" id="save_order_form">
            <input type="hidden" name="save_order" value="1">
            <input type="hidden" id="save_order_data" name="data" value="">
        </form>
    <?php } ?>

<?php
} // Fin del else (vista principal)

// Incluir el JavaScript para drag & drop
// Asumiendo que el JS 'manage_categories.js' funciona correctamente con el id 'serviceList'
?>
<script type="text/javascript" src="<?= getConfParam('SITE_URL'); ?>sc-admin/res/manage_categories.js"></script>