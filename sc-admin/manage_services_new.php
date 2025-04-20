<?php
// Asegúrate de que las funciones del CMS estén cargadas
// Puede que necesites incluir un archivo bootstrap o config aquí si no se hace automáticamente
// Ejemplo: require_once('../config.php'); o similar

loadModule("filter"); // Asumiendo que esto es necesario

$error_div = false;
$exito_div = false;

// --- Lógica para Guardar Orden ---
if (isset($_POST['save_order'])) {
    $data = $_POST['data'];
    $data = json_decode($data, true);
    if (is_array($data)) {
        foreach ($data as $id => $order) {
            // Asegúrate de que $id y $order sean seguros antes de usarlos
            $service_id = intval($id);
            $service_order = intval($order);
            if ($service_id > 0) {
                updateSQL("sc_services", array('ord' => ($service_order + 1)), array('ID_service' => $service_id));
            }
        }
        // Deberías tener una variable de idioma para esto
        $exito_div = "Orden de servicios guardado correctamente."; 
    } else {
         // Deberías tener una variable de idioma para esto
        $error_div = "Error al decodificar los datos del orden.";
    }
}

// --- Capturar mensajes de éxito/error de otras operaciones (si aplica) ---
// $exito_div = ModFilter::catch(); // Descomenta si usas ModFilter para otros mensajes

// --- Lógica para Borrar Servicio ---
if (isset($_GET['del'])) {
    $service_id_to_delete = intval($_GET['del']);
    if ($service_id_to_delete > 0) {
        deleteSQL("sc_services", $w = array('ID_service' => $service_id_to_delete));
        // Deberías tener una variable de idioma para esto
        $exito_div = "Servicio eliminado correctamente."; 
    }
}

// --- Lógica para Nuevo Servicio ---
if (isset($_POST['new_service_name']) && !isset($_POST['edit_service_id'])) { // Asegurarse que no es una edición
    $new_service_name = trim($_POST['new_service_name']);
    $new_service_order = isset($_POST['new_service_order']) ? intval($_POST['new_service_order']) : 0;

    if (!empty($new_service_name)) {
        // Generar el 'value' a partir del nombre (o podrías tener un campo separado en el form)
        // Usando una función similar a toAscii si existe y es apropiada, o la lógica del ejemplo
        // $new_service_value = toAscii($new_service_name); // Opción 1: si toAscii genera slugs
        $new_service_value = strtolower(str_replace(' ', '_', $new_service_name)); // Opción 2: como en tu ejemplo
        // Quitar caracteres no alfanuméricos o guiones bajos para seguridad/compatibilidad
        $new_service_value = preg_replace('/[^a-z0-9_]/', '', $new_service_value); 

        // Comprobar si el valor ya existe (la columna 'value' debería ser UNIQUE en BD)
        $existing = selectSQL("sc_services", array('value' => $new_service_value));
        if (empty($existing)) {
             insertSQL("sc_services", $a = array(
                'name' => $new_service_name,
                'value' => $new_service_value,
                'ord' => $new_service_order
            ));
            // Deberías tener una variable de idioma para esto
            $exito_div = "Servicio creado correctamente."; 
        } else {
            // Deberías tener una variable de idioma para esto
            $error_div = "Error: El valor interno '{$new_service_value}' generado para este servicio ya existe.";
        }

    } else {
         // Deberías tener una variable de idioma para esto
        $error_div = "Error: El nombre del servicio no puede estar vacío.";
    }
}

// --- Lógica para Modificar Servicio ---
if (isset($_POST['edit_service_id'])) {
    $edit_service_id = intval($_POST['edit_service_id']);
    $edit_service_name = trim($_POST['edit_service_name']);
    $edit_service_order = isset($_POST['edit_service_order']) ? intval($_POST['edit_service_order']) : 0;

    if ($edit_service_id > 0 && !empty($edit_service_name)) {
        // Regenerar el 'value' basado en el nuevo nombre
        // $edit_service_value = toAscii($edit_service_name); // Opción 1
        $edit_service_value = strtolower(str_replace(' ', '_', $edit_service_name)); // Opción 2
        $edit_service_value = preg_replace('/[^a-z0-9_]/', '', $edit_service_value);

         // Comprobar si el NUEVO valor ya existe EN OTRO servicio
        $existing = selectSQL("sc_services", array('value' => $edit_service_value, 'ID_service' => "!= {$edit_service_id}"));

        if (empty($existing)) {
            updateSQL("sc_services", $a = array(
                'name' => $edit_service_name,
                'value' => $edit_service_value,
                'ord' => $edit_service_order
            ), $s = array('ID_service' => $edit_service_id));
            // Deberías tener una variable de idioma para esto
            $exito_div = "Servicio modificado correctamente."; 
             // Redirigir o limpiar variables GET para evitar re-edición accidental al recargar
             // header("Location: index.php?id=manage_services_new&success=edited"); // O una forma similar que use tu CMS
             // O simplemente limpiar el GET edit para que muestre la lista normal
             unset($_GET['edit']); 
        } else {
             // Deberías tener una variable de idioma para esto
            $error_div = "Error: El valor interno '{$edit_service_value}' generado para este servicio ya existe en otro servicio.";
             // Mantener $_GET['edit'] para que el formulario de edición se muestre de nuevo con el error
        }
    } else {
        // Deberías tener una variable de idioma para esto
        $error_div = "Error: El nombre del servicio no puede estar vacío.";
         // Mantener $_GET['edit'] para que el formulario de edición se muestre de nuevo con el error
    }
}

// --- Mostrar Formulario de Edición si se solicita ---
if (isset($_GET['edit'])) {
    $service_id_to_edit = intval($_GET['edit']);
    $service_data = null;
    if ($service_id_to_edit > 0) {
        $mod = selectSQL("sc_services", $b = array('ID_service' => $service_id_to_edit));
        if (!empty($mod)) {
            $service_data = $mod[0];
        }
    }

    if ($service_data) {
        ?>
        <h2>Editar Servicio</h2> <? // Cambiar por variable de idioma ?>

        <? if ($exito_div !== false) { ?>
            <div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?= htmlspecialchars($exito_div); ?></div>
        <? } ?>
        <? if ($error_div !== false) { ?>
             <div class="info_error"><i class="fa fa-times-circle" aria-hidden="true"></i><?= htmlspecialchars($error_div); ?></div> <? // Asumiendo una clase info_error ?>
        <? } ?>

        <form action="index.php?id=manage_services_new&edit=<?= $service_id_to_edit; ?>" method="post" class="param_form white_form">
            <input type="hidden" name="edit_service_id" value="<?= $service_data['ID_service']; ?>">

            <label>Nombre del Servicio</label> <? // Cambiar por variable de idioma ?>
            <input name="edit_service_name" type="text" value="<?= htmlspecialchars($service_data['name']); ?>" required>
            <div class="clear"></div>

            <label>Orden</label> <? // Cambiar por variable de idioma ?>
            <input name="edit_service_order" type="number" value="<?= htmlspecialchars($service_data['ord']); ?>">
            <div class="clear"></div>

            <? /* Comentario: El campo 'value' se genera automáticamente a partir del nombre al guardar.
               <label>Valor Interno (Automático)</label>
               <input name="edit_service_value" type="text" value="<?= htmlspecialchars($service_data['value']); ?>" readonly disabled>
               <div class="clear"></div>
            */ ?>

            <input name="Modificar" type="submit" value="Guardar Cambios"> <? // Cambiar por variable de idioma ?>
        </form>
        <a href="index.php?id=manage_services_new" class="back">« Volver a la lista de Servicios</a> <? // Cambiar por variable de idioma ?>
        <?php
    } else {
        // Mostrar error si no se encontró el servicio a editar
        ?>
        <h2>Error</h2> <? // Cambiar por variable de idioma ?>
        <div class="info_error">Servicio no encontrado.</div> <? // Cambiar por variable de idioma ?>
        <a href="index.php?id=manage_services_new" class="back">« Volver a la lista de Servicios</a> <? // Cambiar por variable de idioma ?>
        <?php
    }

} else {
    // --- Mostrar Formulario para Añadir y Lista de Servicios (Vista Principal) ---

    // Obtener todos los servicios ordenados
    $services = selectSQL("sc_services", array(), "ord ASC, name ASC"); // Ordenar por 'ord' y luego por 'name'

    ?>
    <h2>Gestionar Servicios</h2> <? // Cambiar por variable de idioma ?>

    <form action="index.php?id=manage_services_new" method="post" class="param_form white_form">
        <label>Nombre del Nuevo Servicio</label> <? // Cambiar por variable de idioma ?>
        <input name="new_service_name" type="text" required>
        <div class="clear"></div>

        <label>Orden</label> <? // Cambiar por variable de idioma ?>
        <input name="new_service_order" type="number" value="0" placeholder="Ej: 0, 10, 20...">
        <div class="clear"></div>

        <input name="add" type="submit" value="Añadir Servicio"> <? // Cambiar por variable de idioma ?>
    </form>
    <hr />

    <? if ($exito_div !== false) { ?>
        <div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?= htmlspecialchars($exito_div); ?></div>
    <? } ?>
     <? if ($error_div !== false) { ?>
             <div class="info_error"><i class="fa fa-times-circle" aria-hidden="true"></i><?= htmlspecialchars($error_div); ?></div> <? // Asumiendo una clase info_error ?>
        <? } ?>

    <? if (!empty($services)) { ?>
        <ul class="list_categories" id="serviceList"> <? // Cambiar ID si es necesario para JS ?>
            <? foreach ($services as $service) { ?>
                <li data-id="<?= $service['ID_service']; ?>" draggable="true">
                    <span class="col_left">
                        <img draggable="false" src="<?= Images::getImage('drag_indicator.svg', Images::IMG); ?>" alt=""> <? // Asume que Images::getImage existe ?>
                        <b><?= htmlspecialchars($service['name']); ?></b> (Valor: <?= htmlspecialchars($service['value']); ?> | Orden: <?= htmlspecialchars($service['ord']); ?>)
                    </span>
                    <span class="col_right">
                        <a href="index.php?id=manage_services_new&edit=<?= $service['ID_service']; ?>">Editar</a> <? // Cambiar por variable de idioma ?>
                        <a href="index.php?id=manage_services_new&del=<?= $service['ID_service']; ?>" onclick="return confirm('¿Estás seguro de que quieres eliminar este servicio?');">Eliminar</a> <? // Cambiar por variable de idioma ?>
                    </span>
                </li>
            <? } ?>
        </ul>
        <div class="text-center">
            <button id="save_order_btn" class="btn btn-primary">Guardar Orden</button> <? // Cambiar por variable de idioma ?>
        </div>
        <form action="index.php?id=manage_services_new" method="post" id="save_order_form">
            <input type="hidden" name="save_order" value="1">
            <input type="hidden" id="save_order_data" name="data" value="">
        </form>
    <? } else { ?>
        <p>No hay servicios definidos todavía.</p> <? // Cambiar por variable de idioma ?>
    <? } ?>

    <?php
    // Incluir filtro si es necesario para esta página también
    // include('./block/manage_filter.php');
    ?>

<?php
} // Fin del else (cuando no se está editando)
?>

<?php // --- JavaScript para Ordenación (Adaptado de manage_categories.js) --- ?>
<?php // Necesitas asegurarte de que un script similar a manage_categories.js se cargue ?>
<?php // O puedes incluir el JS aquí directamente si es más fácil en tu CMS ?>
<script type="text/javascript" src="<?=getConfParam('SITE_URL');?>sc-admin/res/manage_services.js"></script>
<script type="text/javascript">
    // Asegúrate de que este código se ejecute después de que el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        const list = document.getElementById('serviceList'); // Cambiado a serviceList
        let draggedItem = null;

        if (list) { // Solo ejecutar si la lista existe
            const items = list.querySelectorAll('li[draggable="true"]');

            items.forEach(item => {
                item.addEventListener('dragstart', function(e) {
                    draggedItem = item;
                    // e.dataTransfer.setData('text/plain', null); // Opcional
                    setTimeout(() => item.classList.add('dragging'), 0); // Estilo visual
                });

                item.addEventListener('dragend', function() {
                    setTimeout(() => {
                        draggedItem.classList.remove('dragging');
                        draggedItem = null;
                    }, 0);
                });

                item.addEventListener('dragover', function(e) {
                    e.preventDefault(); // Necesario para permitir drop
                    const afterElement = getDragAfterElement(list, e.clientY);
                    if (afterElement == null) {
                        list.appendChild(draggedItem);
                    } else {
                        list.insertBefore(draggedItem, afterElement);
                    }
                });
            });
        }

        function getDragAfterElement(container, y) {
            const draggableElements = [...container.querySelectorAll('li[draggable="true"]:not(.dragging)')];

            return draggableElements.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;
                // console.log(offset);
                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }

        // --- Guardar Orden ---
        const saveOrderBtn = document.getElementById('save_order_btn'); // Cambiado id
        const saveOrderForm = document.getElementById('save_order_form');
        const saveOrderData = document.getElementById('save_order_data');

        if (saveOrderBtn && saveOrderForm && saveOrderData && list) {
            saveOrderBtn.addEventListener('click', function() {
                const orderData = {};
                const orderedItems = list.querySelectorAll('li[draggable="true"]');
                orderedItems.forEach((item, index) => {
                    orderData[item.dataset.id] = index;
                });
                saveOrderData.value = JSON.stringify(orderData);
                saveOrderForm.submit();
            });
        } else {
             if (!saveOrderBtn) console.error("Botón #save_order_btn no encontrado.");
             if (!saveOrderForm) console.error("Formulario #save_order_form no encontrado.");
             if (!saveOrderData) console.error("Campo #save_order_data no encontrado.");
             if (!list) console.error("Lista #serviceList no encontrada para JS de ordenación.");
        }

    });
</script>

<?php /*
 Comentario Final: Si tu `manage_categories.js` original hacía más cosas,
 necesitarás revisar y adaptar esas funcionalidades también, o crear un
 nuevo `manage_services.js` basado en él y cargarlo.
 Este script básico solo cubre el drag-and-drop y el guardado del orden.
*/ ?>