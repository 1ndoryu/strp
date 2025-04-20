// Asegúrate de que este script se ejecute después de que el DOM esté listo
// y después de que jQuery esté cargado.

// Selecciona la lista de servicios
const serviceList = document.querySelector('#serviceList'); // ID cambiado a serviceList

$(document).ready(() => {
    // Solo ejecuta si la lista de servicios existe en la página
    if (serviceList) {
        updateIndex(); // Establece el data-index inicial

        // Evento cuando se empieza a arrastrar un elemento <li>
        $(serviceList).on('dragstart', 'li', function (e) {
            $(this).addClass('dragging'); // Añade clase para styling/identificación
        });

        // Evento mientras un elemento se arrastra sobre la lista
        $(serviceList).on('dragover', updateList); // Llama a la función que calcula la posición

        // Necesario para permitir el 'drop'
        $(serviceList).on('dragenter', e => e.preventDefault());

        // Evento cuando se suelta el elemento arrastrado
        $(serviceList).on('dragend', 'li', function (e) {
            $(this).removeClass('dragging'); // Quita la clase
            restablecerPosiciones(); // Resetea cualquier transformación visual temporal
            updateIndex(); // Actualiza los data-index según el nuevo orden
        });
    } // Fin if(serviceList)

    // Evento click en el botón "Guardar Orden"
    // ID del botón cambiado a save_order_btn
    $('#save_order_btn').click(() => {
        if (!serviceList) return; // No hacer nada si la lista no existe

        const items = serviceList.querySelectorAll('li'); // Obtiene todos los <li> ordenados
        const data = {}; // Objeto para almacenar {id_servicio: indice}

        // Recorre los items y guarda su ID y su índice actual (del atributo data-index)
        items.forEach(item => {
            data[item.dataset.id] = item.dataset.index;
        });

        // Convierte el objeto a JSON y lo pone en el input oculto
        $('#save_order_data').val(JSON.stringify(data));
        // Envía el formulario que contiene los datos del orden
        $('#save_order_form').submit();
    });

    // --- Código eliminado ---
    // Se han eliminado los manejadores de eventos change para #image_cat_file y #image_icon_file
    // ya que no se usan en la gestión de servicios (según el PHP generado).
    // También se han eliminado las funciones addFilter y editFilter.
}); // Fin $(document).ready

/**
 * Actualiza el atributo data-index de cada <li> en la lista
 * según su posición actual en el DOM.
 */
function updateIndex() {
    if (!serviceList) return;
    serviceList.querySelectorAll('li').forEach(function (item, index) {
        item.setAttribute('data-index', index);
    });
}

/**
 * Manejador del evento dragover. Determina dónde insertar el elemento
 * que se está arrastrando (.dragging) en relación al elemento sobre
 * el cual está el cursor.
 * Incluye la lógica compleja para encontrar el <li> correcto incluso
 * si el cursor está sobre elementos hijos.
 * Aplica animaciones visuales temporales.
 */
function updateList(e) {
    e.preventDefault(); // Necesario para dragover y drop
    if (!serviceList) return;

    const dragging = serviceList.querySelector('.dragging'); // El <li> que se está arrastrando
    if (!dragging) return; // Salir si no hay nada arrastrándose

    // Encuentra el elemento directamente debajo del cursor
    let nextSibling = document.elementFromPoint(e.clientX, e.clientY);

    // --- Lógica para encontrar el <li> padre si se está sobre un hijo ---
    // (Esta lógica es compleja y se copia directamente del original, asumiendo que funcionaba)
    if (nextSibling && nextSibling.parentElement !== serviceList) {
        if (nextSibling.parentElement && nextSibling.parentElement.parentElement === serviceList) {
            // Cursor sobre hijo directo del LI (ej: span, b, img)
            nextSibling = nextSibling.parentElement;
        } else if (nextSibling.parentElement && nextSibling.parentElement.parentElement && nextSibling.parentElement.parentElement.parentElement === serviceList) {
            // Cursor sobre nieto del LI (ej: contenido dentro de un span)
            nextSibling = nextSibling.parentElement.parentElement;
        } else {
            // No se pudo determinar un <li> válido debajo del cursor, salir.
            return;
        }
    }
    // --- Fin lógica compleja ---

    // Si no se encontró un sibling válido (ej: fuera de la lista) o es el mismo elemento que se arrastra
    if (!nextSibling || nextSibling === dragging) {
        return;
    }

    // Determina si el elemento arrastrado debe ir antes o después del 'nextSibling'
    if (!isBefore(dragging, nextSibling)) {
        // Mover 'nextSibling' hacia abajo visualmente
        moverElemento(nextSibling, -50); // Ajustar 50 si la altura es diferente
        // Esperar un poco y luego insertar el 'dragging' DESPUÉS del 'nextSibling'
        setTimeout(() => {
            restablecerMovimiento(nextSibling);
            // Insertar después es insertar antes del siguiente hermano de nextSibling
            serviceList.insertBefore(dragging, nextSibling.nextSibling);
        }, 300); // ms de delay
    } else {
        // Mover 'nextSibling' hacia arriba visualmente
        moverElemento(nextSibling, 50); // Ajustar 50 si la altura es diferente
        // Esperar un poco y luego insertar el 'dragging' ANTES del 'nextSibling'
        setTimeout(() => {
            restablecerMovimiento(nextSibling);
            serviceList.insertBefore(dragging, nextSibling);
        }, 300); // ms de delay
    }
}

/**
 * Comprueba si el elemento 'a' aparece antes que el elemento 'b'
 * dentro del mismo contenedor padre.
 * @param {Element} a Elemento 1
 * @param {Element} b Elemento 2
 * @returns {boolean} True si 'a' está antes que 'b', false en caso contrario.
 */
function isBefore(a, b) {
    if (!a || !b || a.parentElement !== b.parentElement) return false; // Deben ser hermanos

    for (let cur = a.previousSibling; cur; cur = cur.previousSibling) {
        if (cur === b) return true; // Se encontró 'b' antes que 'a'
    }
    return false; // No se encontró 'b' antes que 'a'
}

/**
 * Aplica una transformación CSS para mover visualmente un elemento.
 * @param {Element} elemento El elemento a mover.
 * @param {number} desplazamientoY Píxeles a mover verticalmente.
 */
function moverElemento(elemento, desplazamientoY) {
    if (!elemento) return;
    elemento.style.transition = 'transform 0.3s ease'; // Añadir transición suave
    elemento.style.transform = `translate(0, ${desplazamientoY}px)`;
}

/**
 * Restablece la transformación de movimiento de un elemento,
 * desactivando temporalmente la transición para que sea instantáneo.
 * @param {Element} elemento El elemento a restablecer.
 */
function restablecerMovimiento(elemento) {
    if (!elemento) return;
    elemento.style.transition = 'none'; // Quitar transición para reseteo instantáneo
    elemento.style.transform = `translate(0, 0)`;
    // Restaurar la transición después de un pequeño delay para futuras animaciones
    setTimeout(() => {
        elemento.style.transition = ''; // Restaura la transición definida en CSS o por moverElemento
    }, 50); // Pequeño delay
}

/**
 * Restablece la transformación de todos los elementos <li> en la lista
 * a su posición original (translate(0, 0)) después de que termina el arrastre.
 */
function restablecerPosiciones() {
    if (!serviceList) return;
    const items = serviceList.querySelectorAll('li');
    items.forEach(item => {
        // Podrías añadir una transición aquí si quieres que vuelvan suavemente
        // item.style.transition = "transform 0.2s ease";
        item.style.transform = 'translate(0, 0)';
    });
}
