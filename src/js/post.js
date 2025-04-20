// Asumiendo que jQuery, jQuery UI (Sortable), Select2 ya están cargados
// Asumiendo que site_url, DATAJSON['max_photos'], lang_var, etc., están disponibles globalmente si son necesarios
// Asumiendo que addStyle, editImage, getConfParam (si se usa en JS), etc., están definidos si son necesarios

$(document).ready(function() {
    const photoListContainer = $(".photos_list.sortable"); // Contenedor de la lista de fotos ordenable

    // Inicializar Sortable (como ya estaba en el PHP)
    if (photoListContainer.length > 0 && typeof $.ui !== 'undefined' && typeof $.ui.sortable !== 'undefined') {
        photoListContainer.sortable({
            helper: "clone",
            items: ".photo_box",
            placeholder: "photo_box_placeholder",
            forcePlaceholderSize: true,
            tolerance: "pointer",
            update: function(event, ui) {
                updateBoxButtons(); // Actualizar botones de mover al reordenar
                 updatePhotoNameIndices(); // ¡NUEVO! Reasigna índices a los inputs de rotación
            }
        }).disableSelection();
        updateBoxButtons(); // Inicializar botones de mover al cargar
    } else {
        // console.warn("Sortable no inicializado. ¿jQuery UI cargado? ¿Selector correcto?");
    }

    // --- INICIO: Lógica de subida AJAX ELIMINADA ---
    /*
    $("#post_photo").on('change', function(){
        // ... Código de subida AJAX original ...
        // Esta lógica se elimina porque edit_item.php usa $_FILES['userImage']
    });
    */
    // --- FIN: Lógica de subida AJAX ELIMINADA ---

    // --- INICIO: Vista previa para NUEVAS fotos (Inputs userImage[]) ---
    // Escuchar cambios en *cualquier* input de tipo file dentro del contenedor
    photoListContainer.on('change', 'input.photoFile[name="userImage[]"]', function(event) {
        const fileInput = event.target;
        const photoContainerDiv = $(fileInput).closest('.photo_list.free'); // El div que contiene el input

        if (fileInput.files && fileInput.files[0]) {
            const file = fileInput.files[0];
            const reader = new FileReader();

            // Validar tipo y tamaño (opcional, pero bueno)
             const allowedTypes = ['image/jpeg', 'image/png'];
             const maxSizeMB = 5; // Ejemplo: 5 MB
             if (!allowedTypes.includes(file.type)) {
                 alert('Error: Solo se permiten imágenes JPG o PNG.');
                 fileInput.value = ''; // Limpiar input
                 // Restaurar placeholder si había uno
                 photoContainerDiv.empty().append('<input name="userImage[]" id="'+ fileInput.id +'" type="file" class="photoFile" accept="image/jpeg, image/png" /><span class="upload-placeholder-icon">+</span>');
                 return;
             }
             if (file.size > maxSizeMB * 1024 * 1024) {
                 alert(`Error: La imagen no debe superar los ${maxSizeMB} MB.`);
                 fileInput.value = ''; // Limpiar input
                 // Restaurar placeholder
                  photoContainerDiv.empty().append('<input name="userImage[]" id="'+ fileInput.id +'" type="file" class="photoFile" accept="image/jpeg, image/png" /><span class="upload-placeholder-icon">+</span>');
                 return;
             }


            reader.onload = function(e) {
                // Crear elemento img para la vista previa
                const imgPreview = $('<img>').attr('src', e.target.result).css({
                    'max-width': '100%',
                    'max-height': '100%',
                    'object-fit': 'contain'
                });
                // Reemplazar contenido del div con la imagen y el input (que ahora tiene el archivo)
                // Importante mantener el input original para que se envíe con el form
                photoContainerDiv.empty().append(imgPreview);
                // Mover el input (que ya tiene el archivo seleccionado) dentro, pero oculto visualmente si se prefiere
                // $(fileInput).css({'position': 'absolute', 'opacity': 0, 'width':'1px', 'height':'1px'}).appendTo(photoContainerDiv);
                // O simplemente dejarlo fuera del flujo visual normal, ya no se necesita interactuar con él directamente
                 $(fileInput).hide(); // Ocultarlo simplemente
                 photoContainerDiv.append(fileInput); // Asegurar que sigue dentro del form

                // Quizás añadir un botón de "cancelar/quitar" vista previa
                const cancelButton = $('<button type="button" class="removePreviewBtn">X</button>').css({
                     'position': 'absolute', 'top': '2px', 'right': '2px', 'background': 'rgba(255,0,0,0.7)',
                     'color': 'white', 'border': 'none', 'cursor': 'pointer', 'font-size': '12px', 'padding': '1px 4px'
                 });
                cancelButton.on('click', function() {
                    fileInput.value = ''; // Limpiar el input file
                    // Restaurar el contenido original del div (input + placeholder)
                     photoContainerDiv.empty().append('<input name="userImage[]" id="'+ fileInput.id +'" type="file" class="photoFile" accept="image/jpeg, image/png" /><span class="upload-placeholder-icon">+</span>');
                    // Volver a añadir el listener al nuevo input si es necesario (delegación ya lo hace)
                });
                photoContainerDiv.append(cancelButton);

                photoContainerDiv.removeClass('free'); // Ya no está libre
            }
            reader.readAsDataURL(file);
        }
    });
    // --- FIN: Vista previa para NUEVAS fotos ---


    // --- INICIO: Manejo de eliminación de fotos (EXISTENTES y PREVIEWS NUEVAS) ---
     photoListContainer.on('click', '.removeImg', function(e) { // Para fotos existentes cargadas por PHP
         e.preventDefault();
         if (confirm('¿Seguro que quieres eliminar esta imagen? No se podrá recuperar hasta guardar.')) {
             const photoBox = $(this).closest('.photo_box');
              // Aquí simplemente eliminamos el elemento del DOM.
              // El backend se encargará de borrarla si no encuentra su 'photo_name[]' en el POST.
             photoBox.remove();
             updateBoxButtons(); // Actualizar botones de mover
             // Podrías añadir un placeholder si ahora hay espacio
             addPlaceholderIfSpace();
         }
     });
     photoListContainer.on('click', '.removePreviewBtn', function(e) { // Para vistas previas de fotos nuevas
        e.preventDefault();
        const photoContainerDiv = $(this).closest('.photo_list');
        const fileInput = photoContainerDiv.find('input.photoFile[name="userImage[]"]');
        const fileInputId = fileInput.attr('id'); // Guardar el ID original

        // Limpiar el input file
        if (fileInput.length > 0) {
            fileInput.val(''); // O fileInput[0].value = '';
        }

        // Restaurar el contenido original del div (input + placeholder)
        photoContainerDiv.empty().append('<input name="userImage[]" id="'+ fileInputId +'" type="file" class="photoFile" accept="image/jpeg, image/png" /><span class="upload-placeholder-icon">+</span>');
        photoContainerDiv.addClass('free');
        // No es necesario re-añadir listeners gracias a la delegación de eventos .on() en el contenedor
    });
    // --- FIN: Manejo de eliminación de fotos ---


    // Mantener el formateo de teléfono si es necesario
    $("#phone").on('input', formatPhone);

    // Botón de información (si existe en edit_item.php)
    $("#post_info_btn").on('click', function() {
        $("#post_info").attr('open', $("#post_info").attr('open') !== "open");
        $(this).find('i').toggleClass('fa-info-circle fa-times-circle');
    });

    // Inicializar validación en tiempo real (adaptada)
    // real_time_validate_form_edit(); // Llamar a la versión adaptada

    // Listener para el botón de toggle del horario (del PHP)
    $('#btn-mostrar-horario').on('click', function() {
        $('#contenedor-horario-edit').toggleClass('oculto');
        $('#ayuda-horario').toggleClass('oculto');
    });

    // Listener para los botones de estado del día (del PHP)
    $('#contenedor-horario-edit').on('click', '.btn-dia-estado', function() {
        const diaDiv = $(this).closest('.dia-horario');
        const horasDiv = diaDiv.find('.horas-dia');
        const selects = horasDiv.find('select');
        let activoInput = diaDiv.find('.activo-hidden-input');

        if ($(this).hasClass('no-disponible')) {
            $(this).removeClass('no-disponible').addClass('disponible').text('Disponible');
            horasDiv.removeClass('oculto');
            selects.prop('disabled', false);
            if (!activoInput.length) {
                $('<input>').attr({
                    type: 'hidden',
                    name: `horario_dia[${$(this).data('dia')}][activo]`,
                    value: '1',
                    class: 'activo-hidden-input'
                }).insertAfter(this);
            } else {
                activoInput.val('1');
            }
        } else {
            $(this).removeClass('disponible').addClass('no-disponible').text('No disponible');
            horasDiv.addClass('oculto');
            selects.prop('disabled', true);
            if (activoInput.length) {
                activoInput.remove();
            }
        }
    });


    // --- INICIO: VALIDACIÓN EN SUBMIT (Más fiable que pre_validate_form) ---
    $('#edit_item_post').on('submit', function(e) {
        let isValid = true;
        const errors = []; // Para acumular mensajes de error

        // Ocultar errores previos
        $('.error_msg', this).hide();

        // 1. Categoría
        if ($('#category').val() == '' || $('#category').val() == '0') {
            isValid = false;
            $('#error_category').show();
            errors.push("Selecciona una categoría.");
        }

        // 2. Provincia
        if ($('#region').val() == '' || $('#region').val() == '0') {
            isValid = false;
            $('#error_region').show();
            errors.push("Selecciona una provincia.");
        }

        // 3. Tipo Anuncio (si es obligatorio)
        if ($('#ad_type').val() == '' || $('#ad_type').val() == '0') {
            isValid = false;
            $('#error_ad_type').show();
            errors.push("Selecciona un tipo de anuncio.");
        }

        // 4. Título
        const titleLen = $('#tit').val().trim().length;
        if (titleLen < 10 || titleLen > 50) {
            isValid = false;
            $('#error_tit').show().text('Título obligatorio (10-50 caracteres)'); // Actualizar texto si cambia
            errors.push("El título debe tener entre 10 y 50 caracteres.");
        }

        // 5. Descripción
        const textLen = $('#text').val().trim().length;
        if (textLen < 100 || textLen > 500) {
            isValid = false;
            $('#error_text').show().text('Descripción obligatoria (100-500 caracteres)'); // Actualizar texto
            errors.push("La descripción debe tener entre 100 y 500 caracteres.");
        }

        // 6. Servicios (al menos uno)
        if ($('input[name="servicios[]"]:checked').length === 0) {
            isValid = false;
            $('#error_servicios').show().text('Debes seleccionar al menos un servicio.');
            errors.push("Selecciona al menos un servicio.");
        }

        // 7. Horario (al menos un día disponible)
        let horarioOk = false;
        $('#contenedor-horario-edit .btn-dia-estado').each(function() {
            if ($(this).hasClass('disponible')) {
                horarioOk = true;
                return false; // Salir del each
            }
        });
        if (!horarioOk) {
            isValid = false;
            $('#error_horario').show().text('Debes marcar al menos un día como disponible.');
            errors.push("Configura el horario para al menos un día.");
        }

        // 8. Fotos (al menos una) - ¡Adaptado!
         if (!checkImagesEdit()) { // Usa la función adaptada
             isValid = false;
             $('#error_photo').show().text('Debes tener al menos una foto.');
             errors.push("Sube o mantén al menos una foto.");
         }

        // 9. Nombre Contacto
        if ($('#name').val().trim() === '') {
            isValid = false;
            $('#error_name').show();
            errors.push("Introduce el nombre de contacto.");
        }

        // 10. Tipo Vendedor
        if ($('#sellerType').val() == '' || $('#sellerType').val() == '0') {
            isValid = false;
            $('#error_sellerType').show();
            errors.push("Selecciona el tipo de vendedor.");
        }

        // 11. Teléfono
        const phone = $('#phone').val().trim();
        if (!/^[0-9]{9,15}$/.test(phone)) {
            isValid = false;
            $('#error_phone').show();
            errors.push("Introduce un teléfono válido (9-15 dígitos).");
        }

        // 12. Salidas (si es obligatorio)
        if ($('#realiza_salidas').val() === '') { // Asumiendo que el value "" no es válido
             isValid = false;
             $('#error_out').show(); // Asegúrate que existe este div de error
             errors.push("Indica si realizas salidas.");
         }

        // 13. Filtrar Palabras Prohibidas (si las funciones existen)
         if (typeof filterWordTitle === 'function' && !filterWordTitle()) {
             isValid = false;
             // Asumiendo que filterWordTitle muestra su propio error
             errors.push("El título contiene palabras no permitidas.");
         }
          if (typeof filterWordText === 'function' && !filterWordText()) {
             isValid = false;
             // Asumiendo que filterWordText muestra su propio error
              errors.push("La descripción contiene palabras no permitidas.");
         }


        // --- Decisión Final ---
        if (!isValid) {
            e.preventDefault(); // Detener el envío del formulario
            // Mostrar un resumen de errores (opcional)
            alert("Por favor, corrige los siguientes errores:\n- " + errors.join("\n- "));
            // O hacer scroll al primer error visible
             const firstError = $('.error_msg:visible').first();
             if (firstError.length) {
                 $('html, body').animate({
                     scrollTop: firstError.offset().top - 100 // Ajustar offset según sea necesario
                 }, 500);
             }
        } else {
            // El formulario es válido, se enviará.
            // Opcional: Mostrar un indicador de "guardando..."
            $('#submitEditAd').val('Guardando...').prop('disabled', true);
        }
    });
    // --- FIN: VALIDACIÓN EN SUBMIT ---


}); // Fin $(document).ready

// --- Funciones Auxiliares Adaptadas/Nuevas ---

/**
 * Función ADAPTADA para contar imágenes válidas en el formulario de edición.
 * Cuenta las imágenes existentes (con input photo_name[]) y
 * las nuevas imágenes seleccionadas (con input userImage[]).
 */
function checkImagesEdit() {
    let count = 0;
    // Contar imágenes existentes (cuyo input oculto no esté vacío)
    $('input[name="photo_name[]"]').each(function() {
        if ($(this).val().trim() !== '') {
            count++;
        }
    });
    // Contar inputs de archivo que tengan un archivo seleccionado
    $('input.photoFile[name="userImage[]"]').each(function() {
        if (this.files && this.files.length > 0) {
            count++;
        }
    });
    // console.log("Image count:", count); // Para depuración
    return count > 0;
}


/**
 * Rota la imagen visualmente y actualiza el valor del input oculto de rotación.
 * @param {number} index - El índice base 1 del photo_box.
 */
function rotateRight(index) {
    const photoBox = $(`#photo_box_${index}`); // Usar ID del box si existe
    const img = photoBox.find(`.photo_list img`); // Buscar img dentro del box
    const rotationInput = photoBox.find(`#rotation-${index}`); // Buscar input de rotación

    if (rotationInput.length === 0) {
         console.warn(`Input de rotación #rotation-${index} no encontrado.`);
         return;
    }

    let n_rotation = parseInt(rotationInput.val()) || 0; // Default a 0 si no es número
    n_rotation = (n_rotation + 90) % 360; // Sumar 90 y asegurar que esté en [0, 90, 180, 270]
    rotationInput.val(n_rotation);

    if (img.length > 0) {
        img.css('transform', `rotate(${n_rotation}deg)`);
    } else {
        // console.log(`No hay imagen para rotar en #photo_box_${index}`);
        // Si es una preview de FileReader, la rotación real debería hacerse en servidor
    }
}

/**
 * Intercambia el contenido visual y los valores ocultos entre dos photo_box.
 * ¡OJO! Esto es complejo y puede fallar si la estructura HTML interna varía mucho
 * entre una foto existente y una preview nueva.
 * @param {number} index1 - Índice del primer elemento.
 * @param {number} index2 - Índice del segundo elemento.
 */
function transferPhoto(index1, index2) {
    // Seleccionar los CONTENEDORES principales de cada foto
    const box1 = $(`#photo_box_${index1}`);
    const box2 = $(`#photo_box_${index2}`);

    if (box1.length === 0 || box2.length === 0) {
        console.error("Error al transferir: No se encontraron los photo_box", index1, index2);
        return;
    }

    // --- Intercambio Simple de Nodos (Más robusto que intercambiar HTML interno) ---
    // Guardar la posición del segundo elemento para insertar el primero allí
    const box2_nextSibling = box2.next();

    // Mover el box2 antes del box1
    box2.insertBefore(box1);

    // Mover el box1 a la posición original del box2
    if (box2_nextSibling.length > 0) {
        box1.insertBefore(box2_nextSibling);
    } else {
        // Si box2 era el último, mover box1 al final del contenedor padre
        box1.appendTo(box1.parent());
    }

    // --- Actualizar IDs y llamadas onclick ---
     updatePhotoBoxIndicesAndButtons(); // Función separada para claridad

}


/**
 * Recorre todos los photo_box visibles, reasigna sus IDs (photo_box_N, photo_container-N, rotation-N)
 * y actualiza los parámetros de índice en las llamadas onclick (rotateRight, transferPhoto).
 * También actualiza la visibilidad de los botones de mover.
 */
function updatePhotoBoxIndicesAndButtons() {
    const container = $(".photos_list.sortable");
    const boxes = container.children(".photo_box"); // Obtener solo los hijos directos
    const totalBoxes = boxes.length;

    boxes.each(function(i) {
        const current_index = i + 1;
        const box = $(this);

        // 1. Actualizar ID del photo_box
        box.attr('id', `photo_box_${current_index}`);

        // 2. Actualizar ID del photo_list (si existe)
        const photoListDiv = box.find('.photo_list');
        if (photoListDiv.length > 0) {
            photoListDiv.attr('id', `photo_container-${current_index}`);
        }

        // 3. Actualizar ID y name del input de rotación (si existe)
        const rotationInput = box.find('input[id^="rotation-"]');
        if (rotationInput.length > 0) {
            rotationInput.attr('id', `rotation-${current_index}`);
            // ¡Importante! Asegurar que el NAME del input de rotación tenga el índice correcto
             // El PHP espera optImgage[INDEX][rotation] donde INDEX es base 0
            rotationInput.attr('name', `optImgage[${i}][rotation]`);
        }

        // 4. Actualizar ID del input de archivo (si es un placeholder nuevo)
         const fileInput = box.find('input.photoFile[id^="photo-"]');
         if(fileInput.length > 0) {
             fileInput.attr('id', `photo-${current_index}`);
         }

        // 5. Actualizar llamadas onclick en los botones de opciones
        const optionsDiv = box.find('.photos_options');
        if (optionsDiv.length > 0) {
            // Botón Rotar
             const rotateBtn = optionsDiv.find('a[onclick*="rotateRight"]');
             if(rotateBtn.length > 0) rotateBtn.attr('onclick', `rotateRight(${current_index}); return false;`);

            // Botón Mover Izquierda
             const moveLeftBtn = optionsDiv.find('a[onclick*="transferPhoto"][onclick*=",${current_index-1}"]'); // Busca el que mueve a la izquierda
             if(moveLeftBtn.length > 0) {
                 moveLeftBtn.attr('onclick', `transferPhoto(${current_index}, ${current_index - 1}); return false;`);
                 // Ocultar si es el primero
                 if (current_index === 1) moveLeftBtn.hide(); else moveLeftBtn.show();
             } else { // Puede que necesitemos encontrarlo de otra forma si el onclick original era diferente
                 const btnL = optionsDiv.find('svg[d*="m313-440"]').closest('a'); // Busca por el path del SVG izquierdo
                 if(btnL.length > 0) {
                     btnL.attr('onclick', `transferPhoto(${current_index}, ${current_index - 1}); return false;`);
                     if (current_index === 1) btnL.hide(); else btnL.show();
                 }
             }

            // Botón Mover Derecha
             const moveRightBtn = optionsDiv.find('a[onclick*="transferPhoto"][onclick*=",${current_index+1}"]'); // Busca el que mueve a la derecha
              if(moveRightBtn.length > 0) {
                 moveRightBtn.attr('onclick', `transferPhoto(${current_index}, ${current_index + 1}); return false;`);
                  // Ocultar si es el último
                  if (current_index === totalBoxes) moveRightBtn.hide(); else moveRightBtn.show();
             } else {
                  const btnR = optionsDiv.find('svg[d*="M647-440H160v-80h487"]').closest('a'); // Busca por el path del SVG derecho
                  if(btnR.length > 0) {
                     btnR.attr('onclick', `transferPhoto(${current_index}, ${current_index + 1}); return false;`);
                     if (current_index === totalBoxes) btnR.hide(); else btnR.show();
                 }
             }
        }
    });
}

/**
 * ¡NUEVO! Reasigna los índices en los names de los inputs de rotación `optImgage[INDEX][rotation]`
 * después de un reordenamiento, para que coincidan con el orden visual.
 */
function updatePhotoNameIndices() {
    const container = $(".photos_list.sortable");
    const boxes = container.children(".photo_box");

    boxes.each(function(i) {
        const box = $(this);
        const rotationInput = box.find('input[id^="rotation-"]');
        if (rotationInput.length > 0) {
            // El índice para el name debe ser base 0
            rotationInput.attr('name', `optImgage[${i}][rotation]`);
        }
         // También actualizar el índice de photo_name[] si existe
         const nameInput = box.find('input[name="photo_name[]"]');
         if(nameInput.length > 0) {
             // El name 'photo_name[]' no necesita índice explícito, PHP lo recibe como array
             // Pero si dependieras del índice para algo más, lo harías aquí.
         }
    });
}


/**
 * Añade un nuevo slot (placeholder) para subir fotos si
 * el número actual de fotos es menor que el máximo permitido.
 */
 function addPlaceholderIfSpace() {
    const container = $(".photos_list.sortable");
    const currentPhotoCount = container.children(".photo_box").length;
    const maxPhotos = parseInt(DATAJSON['max_photos'] || 3); // Usar default

    if (currentPhotoCount < maxPhotos) {
        // Comprobar si ya existe un placeholder al final (por si acaso)
         if (container.find('.photo_list.free').length === 0) {
             const nextId = currentPhotoCount + 1; // El ID para el nuevo placeholder
             const placeholderHtml = `
                 <div class="photo_box" id="photo_box_${nextId}">
                     <div id="photo_container-${nextId}" class="photo_list free">
                         <input name="userImage[]" id="photo-${nextId}" type="file" class="photoFile" accept="image/jpeg, image/png" />
                         <span class="upload-placeholder-icon">+</span>
                     </div>
                     <div class="photos_options">
                         <!-- Opciones vacías o botones de mover si es necesario -->
                     </div>
                     <!-- No input de rotación para placeholder -->
                 </div>`;
             container.append(placeholderHtml);
             updatePhotoBoxIndicesAndButtons(); // Re-calcular botones y IDs
         }
    }
 }


/**
 * Formatea el número de teléfono mientras se escribe (elimina no numéricos).
 */
function formatPhone(e) {
    let telefono = e.target.value.replace(/\D/g, '');
    // Limitar longitud si es necesario (ej: 15 dígitos)
    // telefono = telefono.substring(0, 15);
    e.target.value = telefono;
}

// --- Funciones que probablemente NO se necesiten en edit_item.php ---

/*
// Función original para crear elemento, usada por AJAX upload (eliminado)
function createImageBoxElement(rotation, index) {
    // ...
}

// Función original para crear opciones, usada por AJAX upload (eliminado)
function createImageOptions(index) {
    // ...
}

// Función original que actualizaba botones después de AJAX (reemplazada/integrada en updatePhotoBoxIndicesAndButtons)
function updateBoxButtons() { // Nombre original mantenido por si acaso
    updatePhotoBoxIndicesAndButtons(); // Llama a la nueva función más completa
}

// Función original para filtrar fieldset (mantener si la lógica de categoría aún aplica)
function filterFieldset(cat) {
    const fieldset = $("#fieldset_photos"); // Revisar si este ID existe
    const espFields = $("#esp_fields");     // Revisar si este ID existe

    // Ejemplo: Asumiendo que la lógica de ocultar/mostrar sigue siendo válida
    if(cat == 331) { // Asegurarse que este ID de categoría es correcto
        if(fieldset.length) fieldset.hide();
        if(espFields.length) espFields.hide();
        return;
    }
    if(espFields.length) espFields.show();
    if(fieldset.length) fieldset.show();
}

// Función para verificar límites de usuario (no relevante para admin editando)
function checkLimits() {
    // No hacer nada o devolver una promesa resuelta
    return Promise.resolve();
}

// Función original de validación paso a paso (reemplazada por validación en submit)
function pre_validate_form( step){
    // ... Código original ...
    // Esta función es menos fiable que la validación en submit. Se podría eliminar.
}

// Función original de validación en tiempo real (reemplazada por validación en submit y listeners específicos)
function real_time_validate_form() {
    // ... Código original ...
    // Los listeners específicos y la validación en submit son más efectivos.
}
*/

// --- Helpers (mantener si se usan) ---
function asegurarPuntoFinal(texto) {
    texto = String(texto || '').trim(); // Asegurar que es string y trim
    if (texto === "") return "";
    if (!texto.endsWith('.')) return texto + '.';
    return texto.replace(/\.\s*$/, '.');
}

// Funciones de utilidad que podrían faltar (definir si no existen globalmente)
function hiddenError() { // Asumiendo que oculta todos los .error_msg
    $('.error_msg').hide();
}

function scroll_To(elementId) { // Asumiendo que hace scroll a un elemento
    const element = $('#' + elementId);
    if (element.length) {
        $('html, body').animate({
            scrollTop: element.offset().top - 100 // Ajustar offset
        }, 500);
    }
}
// function validMail(selector) { ... }
// function valSelect(value) { ... }
// function req(value) { ... }
// function filterWordTitle() { ... } // ¡IMPORTANTE: Asegurarse que estas funciones existen y funcionan!
// function filterWordText() { ... } // ¡IMPORTANTE: Asegurarse que estas funciones existen y funcionan!