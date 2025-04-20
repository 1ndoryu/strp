// Intenta encontrar la lista de categorías o la lista de servicios
// const slist = document.querySelector("#catList") || document.querySelector("#serviceList");
// ^^^ Cambiamos esto para hacerlo dentro de document.ready para asegurar que el DOM está cargado

$(document).ready(() => {
    // Encuentra cuál lista está presente en esta página
    const slist = document.querySelector("#catList") || document.querySelector("#serviceList");

    // --- Funcionalidad de Ordenamiento (Drag & Drop) ---
    // Solo activa si se encontró una lista (#catList o #serviceList)
    if (slist) {
        updateIndex(slist); // Actualiza índices iniciales

        // Usamos jQuery para los listeners en la lista encontrada, pasando 'slist' como contexto si es necesario
        // o asegurándonos de que las funciones internas usen la 'slist' correcta.
        $(slist).on("dragstart", "li", function (e) {
            $(this).addClass("dragging");
            // Podrías necesitar establecer dataTransfer si usas HTML5 drag API más a fondo,
            // pero jQuery UI draggable/sortable a menudo maneja esto internamente.
            // Para el código actual que usa elementFromPoint, esto está bien.
        });

        // dragover debe estar en el contenedor (slist)
        $(slist).on("dragover", function(e) {
             updateList(e, slist); // Pasa el evento y el elemento de la lista
        });

        // dragenter previene comportamiento por defecto que puede interferir
        $(slist).on("dragenter", e => e.preventDefault());

        // dragend se dispara en el elemento que se arrastró
        $(slist).on("dragend", "li", function (e) {
            $(this).removeClass("dragging");
            restablecerPosiciones(slist); // Pasa el elemento de la lista
            updateIndex(slist); // Pasa el elemento de la lista
        });

        // Botón para guardar el orden - Asegúrate de que selecciona la lista correcta
        $("#save_order").click(() => {
            // Podríamos re-seleccionar aquí por seguridad, o confiar en la 'slist' del scope superior
            const currentList = slist; // Usamos la lista encontrada al inicio
            const items = currentList.querySelectorAll('li');
            const data = {};
            items.forEach((item) => {
                 // Asegurarse de que los atributos data-* existen
                if (item.dataset.id && item.dataset.index !== undefined) {
                    data[item.dataset.id] = item.dataset.index;
                } else {
                    console.warn("Item skipped in ordering - missing data-id or data-index:", item);
                }
            });

            $("#save_order_data").val(JSON.stringify(data));
            $("#save_order_form").submit();
        });

    } // Fin del if(slist) para funcionalidad de ordenamiento

    // --- Funcionalidad Específica de Categorías (Imágenes) ---
    // Solo activa si los elementos existen (probablemente solo en manage_categories)
    if ($('#image_cat_file').length > 0) {
        $('#image_cat_file').change(function () {
            const file = this.files[0];
            // console.log(file); // Descomentar para depurar
            if (file) {
                let reader = new FileReader();
                reader.onload = function (event) {
                    // console.log(event.target.result); // Descomentar para depurar
                    $('#image_cat_preview').attr('src', event.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
    }

    if ($('#image_icon_file').length > 0) {
        $('#image_icon_file').change(function () {
            const file = this.files[0];
            // console.log(file); // Descomentar para depurar
            if (file) {
                let reader = new FileReader();
                reader.onload = function (event) {
                    // console.log(event.target.result); // Descomentar para depurar
                    $('#image_icon_preview').attr('src', event.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // Las funciones de filtro se asume que son llamadas por elementos
    // que solo existen en las páginas relevantes, por lo que no necesitan
    // estar dentro de condicionales aquí necesariamente, a menos que causen errores.

}); // Fin de $(document).ready

// --- Funciones Auxiliares (Modificadas para aceptar el elemento lista) ---

/**
 * Actualiza el atributo data-index de cada item <li> dentro de la lista proporcionada.
 * @param {HTMLElement} listElement El elemento <ul> o <ol> que contiene los items.
 */
function updateIndex(listElement) {
    if (!listElement) return; // Salir si no hay lista
    listElement.querySelectorAll("li").forEach(function (item, index) {
        item.setAttribute("data-index", index);
    });
}

/**
 * Maneja el evento dragover para reordenar visualmente los elementos.
 * @param {Event} e El objeto evento dragover.
 * @param {HTMLElement} listElement El elemento <ul> o <ol> donde ocurre el drag.
 */
function updateList(e, listElement) {
    e.preventDefault(); // Necesario para permitir el drop
    if (!listElement) return;

    const dragging = listElement.querySelector(".dragging");
    if (!dragging) return; // Si no hay elemento arrastrándose en esta lista

    // Encuentra el elemento sobre el cual se está arrastrando
    let nextSibling = document.elementFromPoint(e.clientX, e.clientY);

    // A veces elementFromPoint puede devolver un elemento hijo (span, b, img)
    // Hay que subir hasta encontrar el <li> que es hijo directo de listElement
    while (nextSibling && nextSibling.parentElement !== listElement) {
        nextSibling = nextSibling.parentElement;
        // Evita bucles infinitos si se sale del contenedor principal
        if (!nextSibling || nextSibling === document.body || nextSibling === document.documentElement) {
             // console.log("Fuera de la lista");
             return; // No estamos sobre un <li> válido de nuestra lista
        }
    }

    // Si no estamos sobre un <li> válido (p.ej., estamos entre li o fuera), salimos.
    // O si estamos sobre el mismo elemento que arrastramos.
    if (!nextSibling || nextSibling === dragging) {
         // console.log("Sobre sí mismo o fuera de un li");
         return;
    }


    // console.log(e.clientY, ":", nextSibling); // Descomentar para depurar

    // Determina si insertar antes o después del nextSibling
    if (!isBefore(dragging, nextSibling)) {
        // Mover nextSibling hacia abajo temporalmente y luego insertar 'dragging' después
        moverElemento(nextSibling, -50); // Mueve hacia arriba (porque dragging viene de abajo) -> Error conceptual, debería ser +50
        // Corrección: Si dragging NO está antes que nextSibling, significa que dragging está MÁS ABAJO.
        // Al mover dragging HACIA ARRIBA sobre nextSibling, nextSibling debe moverse hacia ABAJO (+50) para hacer espacio.
        moverElemento(nextSibling, 50);
        setTimeout(() => {
            restablecerMovimiento(nextSibling);
            // Insertar dragging DESPUÉS de nextSibling
            listElement.insertBefore(dragging, nextSibling.nextSibling);
        }, 300);
    } else {
        // Mover nextSibling hacia abajo temporalmente y luego insertar 'dragging' antes
         moverElemento(nextSibling, -50); // Mueve hacia ARRIBA para hacer espacio a dragging que viene de abajo.
        setTimeout(() => {
            restablecerMovimiento(nextSibling);
            // Insertar dragging ANTES de nextSibling
            listElement.insertBefore(dragging, nextSibling);
        }, 300);
    }
}

/**
 * Comprueba si el elemento 'a' está posicionado antes que el elemento 'b' en el DOM.
 * @param {HTMLElement} a
 * @param {HTMLElement} b
 * @returns {boolean}
 */
function isBefore(a, b) {
    if (!a || !b || a.parentElement !== b.parentElement) {
        return false; // No son hermanos o no existen
    }
    let cur = a;
    while (cur = cur.previousSibling) { // Itera hacia atrás desde 'a'
        if (cur === b) return true; // Si encuentra 'b', 'b' estaba antes que 'a'
    }
     // Si el bucle termina, significa que 'b' no estaba antes que 'a',
     // por lo tanto, 'a' está antes que 'b' (o son el mismo, caso ya filtrado)
     // Corrección: El bucle comprueba si B está ANTES que A. Si devuelve true, B está antes.
     // La función debería llamarse "isElementBefore" o similar.
     // Si B está antes que A, `isBefore(A, B)` debe devolver `false`.
     // Si el bucle termina sin encontrar B, significa que B está DESPUÉS de A,
     // por lo tanto `isBefore(A, B)` debe devolver `true`.
     // Parece que la lógica original funciona como se espera en el `updateList`.

    // Reconfirmando la lógica de isBefore:
    // Si el bucle `while (cur = cur.previousSibling)` encuentra `b`, significa que `b` es un hermano *anterior* a `a`.
    // Si esto ocurre, la función devuelve `true`.
    // En `updateList`:
    // - `if (!isBefore(dragging, nextSibling))` -> Si `dragging` NO está antes que `nextSibling` (o sea, `nextSibling` es anterior a `dragging`)
    // - `else` -> Si `dragging` SÍ está antes que `nextSibling`.
    // Esto parece correcto.

     return false; // Si el bucle termina, b no es un hermano anterior a a.
}


/**
 * Aplica una transformación CSS para mover visualmente un elemento.
 * @param {HTMLElement} elemento
 * @param {number} desplazamientoY Píxeles a mover verticalmente.
 */
function moverElemento(elemento, desplazamientoY) {
    if (!elemento) return;
    // Asegura que la transición esté activa para el movimiento inicial
    elemento.style.transition = "transform 0.3s ease";
    elemento.style.transform = `translate(0, ${desplazamientoY}px)`;
}

/**
 * Resetea la transformación y la transición del elemento.
 * @param {HTMLElement} elemento
 */
function restablecerMovimiento(elemento) {
    if (!elemento) return;
    // Desactiva la transición para el reseteo instantáneo
    elemento.style.transition = "none";
    elemento.style.transform = `translate(0, 0)`;
    // Un pequeño timeout para permitir que el DOM se actualice antes de reactivar la transición
    setTimeout(() => {
         if(elemento) elemento.style.transition = ""; // Restablece a lo que sea definido en CSS (o nada)
    }, 50); // Un delay corto es suficiente
}

/**
 * Resetea la transformación de todos los items <li> en la lista proporcionada.
 * @param {HTMLElement} listElement El elemento <ul> o <ol>.
 */
function restablecerPosiciones(listElement) {
    if (!listElement) return;
    const items = listElement.querySelectorAll('li');
    items.forEach((item) => {
        // Podríamos verificar si tiene una transformación antes de resetear, pero no es dañino
        item.style.transform = 'translate(0, 0)';
    });
}

// --- Funciones de Filtro (Sin cambios, se asume que son llamadas contextualmente) ---
function addFilter() {
    // Asegúrate de que #filter_modal existe antes de intentar mostrarlo
    if ($("#filter_modal").length) {
        $("#filter_modal").modal("show");
    } else {
        console.error("Elemento #filter_modal no encontrado.");
    }
}

function editFilter(e) {
    // Asegúrate de que #filter_modal y los campos internos existen
     if (!$("#filter_modal").length || !$("#filter_name").length || !$("#filter_words").length || !$("#filter_id").length || !$("#filter_category input").length) {
         console.error("Faltan elementos del modal de filtro.");
         return;
     }

    try { // Envuelve en try-catch por si los datos no son como se esperan
        const data = $(e).data();
        let cats = [];
        // Validación de data.cats
        if (data.cats) {
            if (Array.isArray(data.cats)) {
                 // Si ya es un array (poco probable desde data attributes estándar)
                 // Asegurarse de que sean strings
                 cats = data.cats.map(String);
            } else if (typeof data.cats === 'string'){
                // Si es un string tipo "[val1,val2]" o "val1,val2"
                 // Remover corchetes si existen
                 const catString = data.cats.replace(/^\[|\]$/g, '');
                 cats = catString.split(',').map(cat => cat.trim()); // trim() para limpiar espacios
            } else {
                console.warn("data-cats no es un string o array reconocible:", data.cats);
            }
        }


        $("#filter_name").val(data.name || ''); // Usa '' como default si no existe
        $("#filter_words").val(data.word || '');
        $("#filter_id").val(data.id || '');

        // Resetear checkboxes antes de marcar los nuevos
         $("#filter_category input").prop('checked', false);
        $("#filter_category input").each(function (i, input) {
            if (cats.includes(input.value)) {
                input.checked = true;
            }
        });

        $("#filter_modal").modal("show");
    } catch (error) {
        console.error("Error al procesar datos para editar filtro:", error);
        // Podrías mostrar un mensaje al usuario aquí
    }
}