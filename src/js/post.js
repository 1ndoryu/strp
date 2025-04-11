// post.js - Adaptado para el nuevo formulario multi-etapa

$(document).ready(function() {

    const MAX_PHOTOS = 3;
    const MAX_FILE_SIZE_MB = 2;
    const MAX_FILE_SIZE_BYTES = MAX_FILE_SIZE_MB * 1024 * 1024;

    // --- Inicialización General ---

    // Formatear teléfono mientras se escribe
    $('#telefono').on('input', formatPhone);

    // Inicializar listeners para la previsualización de fotos
    $('#fotos').on('change', handleFileSelect);

    // Inicializar contenedor de previsualización (para estado inicial y borrado)
    updatePhotoPreviewUI();

    // Listener para el botón de añadir idioma (si el del PHP no fuera suficiente)
    // $('#agregar-idioma').on('click', addLanguageRow); // El PHP ya lo maneja, pero lo dejamos comentado por si acaso

    // --- Manejo de Envío y Validación por Etapas ---

    // Asociar validación al envío del formulario
    $('#nuevo_anuncio_form').on('submit', function(e) {
        const isValid = validateCurrentStage();
        if (!isValid) {
            e.preventDefault(); // Detener el envío si la validación JS falla
            // Encontrar el primer error y hacer scroll hacia él (opcional)
            const firstError = $('.error-msg:visible').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.closest('.campo, .campo-grupo').offset().top - 100 // Ajusta el offset según necesites
                }, 500);
            }
            alert('Por favor, revisa los campos marcados en rojo.');
            // Reactivar el botón de submit si fue desactivado (la lógica reCAPTCHA ya lo hace)
            // $('button[type="submit"]').prop('disabled', false).text('...'); // Ajustar texto según el botón
        }
        // Si isValid es true, el formulario se enviará y la lógica reCAPTCHA del PHP actuará.
    });

    // --- Funciones de Ayuda ---

    // Función para formatear número de teléfono (simple, elimina no dígitos)
    function formatPhone(e) {
        let telefono = e.target.value.replace(/\D/g, '');
        e.target.value = telefono;
    }

    // --- Lógica de Previsualización de Imágenes ---
    let currentFiles = []; // Array para mantener los archivos seleccionados

    function handleFileSelect(event) {
        const files = event.target.files;
        const previewContainer = $('#previsualizacion-fotos');
        const existingFileCount = currentFiles.length;

        let filesProcessed = 0;

        if (existingFileCount + files.length > MAX_PHOTOS) {
            showPhotoError(`Puedes subir un máximo de ${MAX_PHOTOS} fotos.`);
            // Limpiar el input para permitir nueva selección si el usuario corrige
            $(event.target).val('');
            return;
        }

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const fileIndex = existingFileCount + i; // Índice único para este archivo

            if (!file.type.match('image/jpeg') && !file.type.match('image/png')) {
                showPhotoError(`Archivo "${file.name}" no es JPG o PNG.`);
                continue; // Saltar archivo inválido
            }

            if (file.size > MAX_FILE_SIZE_BYTES) {
                showPhotoError(`Archivo "${file.name}" excede los ${MAX_FILE_SIZE_MB} MB.`);
                continue; // Saltar archivo inválido
            }

            // Añadir archivo válido al array global
            currentFiles.push({ file: file, id: `file_${Date.now()}_${i}` }); // ID único

            const reader = new FileReader();

            reader.onload = (function(theFile, fileId) {
                return function(e) {
                    // Crear elementos de previsualización
                    const previewDiv = $('<div>').addClass('previs-item');
                    const img = $('<img>').addClass('previs-img').attr('src', e.target.result);
                    const actionsDiv = $('<div>').addClass('previs-acciones');

                    // Radio button para seleccionar principal
                    const radioLabel = $('<label>').addClass('opcion-principal');
                    const radioInput = $('<input>')
                        .attr('type', 'radio')
                        .attr('name', 'foto_principal') // Nombre debe coincidir con el PHP
                        .val(fileId) // Usar el ID único como valor
                        .data('file-id', fileId); // Guardar ID para referencia

                    // Marcar el primero como principal por defecto si no hay otro
                    if ($('input[name="foto_principal"]:checked', previewContainer).length === 0 && $('.previs-item', previewContainer).length === 0) {
                       radioInput.prop('checked', true);
                    }
                    radioLabel.append(radioInput).append(' Principal');

                    // Botón para quitar la imagen
                    const removeButton = $('<button>')
                        .attr('type', 'button')
                        .addClass('boton-quitar')
                        .text('Quitar')
                        .on('click', function() {
                            removePhotoPreview(fileId);
                        });

                    actionsDiv.append(radioLabel).append(removeButton);
                    previewDiv.append(img).append(actionsDiv).data('file-id', fileId); // Asociar ID al div
                    previewContainer.append(previewDiv);

                    filesProcessed++;
                    if(filesProcessed === files.length || fileIndex === MAX_PHOTOS - 1) {
                       updatePhotoPreviewUI(); // Actualizar UI después de procesar
                    }
                };
            })(file, currentFiles[fileIndex].id); // Pasar file y el ID único

            reader.readAsDataURL(file);
        }
         // Limpiar el input después de procesar para evitar problemas si se seleccionan los mismos archivos
        $(event.target).val('');
        hidePhotoError(); // Ocultar errores si se procesó algo
    }

    function removePhotoPreview(fileIdToRemove) {
        // Eliminar el elemento visual
        $(`.previs-item`).filter(function() {
            return $(this).data('file-id') === fileIdToRemove;
        }).remove();

        // Eliminar del array de archivos
        const initialLength = currentFiles.length;
        currentFiles = currentFiles.filter(item => item.id !== fileIdToRemove);

        // Si se eliminó la foto que estaba marcada como principal,
        // marcar la nueva primera foto como principal (si existe)
        if (initialLength > currentFiles.length && $('input[name="foto_principal"]:checked').length === 0 && currentFiles.length > 0) {
             $('.previs-item input[name="foto_principal"]').first().prop('checked', true);
        }

        updatePhotoPreviewUI();
    }

    function updatePhotoPreviewUI() {
        const previewContainer = $('#previsualizacion-fotos');
        const fileInput = $('#fotos');
        const photoCount = currentFiles.length;

        if (photoCount >= MAX_PHOTOS) {
            fileInput.prop('disabled', true);
            fileInput.siblings('small').text(`Has alcanzado el límite de ${MAX_PHOTOS} fotos.`);
        } else {
            fileInput.prop('disabled', false);
             fileInput.siblings('small').text(`Sube hasta ${MAX_PHOTOS} fotos (JPG, PNG, máx ${MAX_FILE_SIZE_MB}MB cada una).`);
        }

        if (photoCount === 0) {
            previewContainer.append('<p class="sin-fotos">Aún no has subido fotos.</p>');
        } else {
            previewContainer.find('.sin-fotos').remove();
            // Asegurarse de que siempre haya una foto principal seleccionada si hay fotos
            if ($('input[name="foto_principal"]:checked', previewContainer).length === 0) {
                $('.previs-item input[name="foto_principal"]', previewContainer).first().prop('checked', true);
            }
        }
         hidePhotoError(); // Ocultar mensajes de error al actualizar
    }

    function showPhotoError(message) {
        let errorDiv = $('#error_fotos_js');
        if (errorDiv.length === 0) {
            errorDiv = $('<span>').attr('id', 'error_fotos_js').addClass('error-msg').css('display', 'block');
            $('#previsualizacion-fotos').after(errorDiv);
        }
        errorDiv.text(message).show();
    }

    function hidePhotoError() {
        $('#error_fotos_js').hide().text('');
    }

    // --- Validación del Formulario por Etapa ---

    function validateCurrentStage() {
        let isValid = true;
        const etapaActual = parseInt($('input[name="etapa_actual"]').val(), 10);

        // Ocultar todos los errores previos
        $('.error-msg').hide();

        // --- Validación Etapa 1: Tipo Usuario y Plan (Solo si existe en el DOM) ---
        if (etapaActual === 1 && $('.etapa-1').length) {
            if (!$('input[name="tipo_usuario"]:checked').val()) {
                showError('tipo_usuario', 'Selecciona un tipo de usuario.');
                isValid = false;
            }
            if (!$('input[name="plan"]:checked').val()) {
                showError('plan', 'Selecciona un plan.');
                isValid = false;
            }
        }

        // --- Validación Etapa 2: Detalles del Anuncio ---
        if (etapaActual === 2) {
            // Nombre
            if ($('#nombre').val().trim() === '') {
                showError('nombre', 'El nombre es obligatorio.');
                isValid = false;
            }
            // Email (si existe - para no logueados)
            if ($('#email').length && !isValidEmail($('#email').val())) {
                 showError('email', 'Introduce un correo electrónico válido.');
                 isValid = false;
            }
            // Categoría Nueva
            if ($('#categoria_nueva').val() === '') {
                showError('categoria_nueva', 'Selecciona una categoría.');
                isValid = false;
            }
            // Provincia
            if ($('#provincia').val() === '') {
                showError('provincia', 'Selecciona una provincia.');
                isValid = false;
            }
            // Título
            const titulo = $('#titulo').val().trim();
            if (titulo === '' || titulo.length < 10 || titulo.length > 50) {
                showError('titulo', 'El título debe tener entre 10 y 50 caracteres.');
                isValid = false;
            }
            // Descripción
            const descripcion = $('#descripcion').val().trim();
            if (descripcion === '' || descripcion.length < 30 || descripcion.length > 500) {
                showError('descripcion', 'La descripción debe tener entre 30 y 500 caracteres.');
                isValid = false;
            }
            // Servicios
            if ($('input[name="servicios[]"]:checked').length === 0) {
                showError('servicios[]', 'Selecciona al menos un servicio.');
                isValid = false;
            }
            // Fotos
            if (currentFiles.length === 0) {
                showError('fotos', 'Debes subir al menos una foto.');
                isValid = false;
            } else if ($('input[name="foto_principal"]:checked').length === 0) {
                // Esto no debería pasar si updatePhotoPreviewUI funciona bien, pero por si acaso
                showError('fotos', 'Debes seleccionar una foto como principal.');
                isValid = false;
            }
            // Horario Semanal (Validación básica: al menos un día disponible?)
            // Podría ser más compleja verificando horas si está disponible.
            let diaDisponible = false;
            $('.dia-horario input[type="checkbox"]:checked').each(function() {
                 diaDisponible = true;
                 // Opcional: Validar horas inicio/fin para este día
                 // const inicio = $(this).siblings('input[type="time"][name$="[inicio]"]').val();
                 // const fin = $(this).siblings('input[type="time"][name$="[fin]"]').val();
                 // if (!inicio || !fin || inicio >= fin) {
                 //     showError('horario_semanal', `Horario inválido para ${$(this).closest('.dia-horario').find('.etiqueta-dia').text()}.`);
                 //     isValid = false;
                 // }
            });
            // if (!diaDisponible) {
            //     showError('horario_semanal', 'Indica tu disponibilidad para al menos un día.');
            //     isValid = false;
            // }

            // Teléfono
             const telefonoVal = $('#telefono').val().trim();
            if (telefonoVal === '' || !/^\d{9,}$/.test(telefonoVal)) { // Ejemplo: al menos 9 dígitos
                showError('telefono', 'Introduce un número de teléfono válido (mínimo 9 dígitos).');
                isValid = false;
            }
            // Idiomas (Validación básica: si hay fila, debe tener idioma y nivel)
            $('#lista-idiomas .item-idioma').each(function() {
                const idioma = $(this).find('select[name$="[idioma]"]').val();
                const nivel = $(this).find('select[name$="[nivel]"]').val();
                if ((idioma && !nivel) || (!idioma && nivel)) {
                    showError('idiomas', 'Completa la información de idioma y nivel para cada fila añadida.');
                    isValid = false;
                    return false; // Salir del each
                }
            });
            // Salidas
            if ($('#salidas').val() === null || $('#salidas').val() === '') { // Asumiendo que el valor por defecto es "" o null
                showError('salidas', 'Indica si realizas salidas.');
                isValid = false;
            }
        }

        // --- Validación Etapa 3: Extras y Finalización ---
        if (etapaActual === 3) {
             // Extras (al menos una opción seleccionada, incluyendo 'ninguno')
            if ($('input[name="extras[]"]:checked').length === 0) {
                 // Si el radio 'ninguno' existe y no hay checkboxes, esto no debería pasar.
                 // Si solo hay checkboxes, esta validación tiene sentido.
                 // Adaptar según cómo esté implementado exactamente el HTML final.
                 // showError('extras[]', 'Debes seleccionar una opción de extras o continuar gratis.');
                 // isValid = false;
            }
             // Términos y condiciones
             if (!$('#terminos').is(':checked')) {
                 showError('terminos', 'Debes aceptar los términos y condiciones.');
                 isValid = false;
             }
        }

        return isValid;
    }

    function showError(fieldName, message) {
        // Intenta encontrar el span de error específico generado por PHP o uno genérico cercano
        let errorElement = $(`#${fieldName}`).siblings('.error-msg');
        if (!errorElement.length) {
             errorElement = $(`input[name="${fieldName}"]`).closest('.campo, .campo-grupo').find('.error-msg');
        }
         // Caso especial para radios/checkboxes agrupados
         if (!errorElement.length && (fieldName.includes('[]') || $(`input[name="${fieldName}"]`).attr('type') === 'radio')) {
              errorElement = $(`input[name="${fieldName}"]`).closest('.campo-grupo').find('.error-msg');
         }
          // Caso especial para fotos
          if (fieldName === 'fotos') {
               errorElement = $('#error_fotos_js'); // Usar el span creado por JS
               if (!errorElement.length) { // Crear si no existe
                    showPhotoError(message); // Usa la función de fotos para crear y mostrar
                    return;
               }
          }


        if (errorElement.length) {
            errorElement.text(message).show();
        } else {
            // Fallback: si no se encuentra un span específico, mostrar alerta (menos ideal)
            console.warn(`No se encontró contenedor de error para el campo: ${fieldName}`);
            // alert(`Error en ${fieldName}: ${message}`); // Evitar alertas si es posible
        }
    }

     function isValidEmail(email) {
         const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
         return emailRegex.test(email);
     }

    // --- Fin $(document).ready ---
});