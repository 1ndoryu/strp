(function () {
    'use strict';

    const form = document.getElementById('form-nuevo-anuncio');
    if (!form) {
        return;
    }

    const etapas = Array.from(form.querySelectorAll('.etapa'));
    const btnSiguiente = form.querySelectorAll('.btn-siguiente');
    const btnAnterior = form.querySelectorAll('.btn-anterior');
    const btnFinalizar = document.getElementById('btn-finalizar');

    const tipoUsuarioRadios = form.querySelectorAll('input[name="tipo_usuario"]');
    const planRadios = form.querySelectorAll('input[name="plan"]');
    const planSelectables = form.querySelectorAll('#etapa-plan .plan-selectable');
    const nombreInput = form.querySelector('#nombre');
    const categoriaSelect = form.querySelector('#categoria');
    const provinciaSelect = form.querySelector('#provincia');
    const tituloInput = form.querySelector('#titulo_anuncio');
    const descripcionTextarea = form.querySelector('#descripcion');
    const serviciosCheckboxes = form.querySelectorAll('input[name="servicios[]"]');
    const fotosInput = form.querySelector('#campo-subir-foto');
    const listaFotosContainer = form.querySelector('#lista-fotos-subidas');
    // --- NUEVOS Selectores para el Horario ---
    const btnMostrarHorario = document.getElementById('btn-mostrar-horario');
    const contenedorHorario = document.getElementById('contenedor-horario');
    const ayudaTextoHorario = document.getElementById('ayuda-horario');
    const diaEstadoBotones = form.querySelectorAll('.btn-dia-estado'); // Selector para los NUEVOS botones

    // --- SELECTOR ANTIGUO (ya no se usa directamente para listeners o toggle) ---
    // const horarioCheckboxes = form.querySelectorAll('.check-dia input[type="checkbox"]'); // Comentado o eliminado
    const telefonoInput = form.querySelector('#telefono');
    const whatsappCheckbox = form.querySelector('input[name="whatsapp"]');
    const idioma1Select = form.querySelector('#idioma_1');
    const idioma2Select = form.querySelector('#idioma_2');
    const salidasSelect = form.querySelector('#realiza_salidas');
    const emailInput = form.querySelector('#email');
    const terminosCheckbox = form.querySelector('#terminos');
    const notificacionesCheckbox = form.querySelector('#notifications');

    const contTitulo = document.getElementById('cont-titulo');
    const contDesc = document.getElementById('cont-desc');

    const hiddenSellerType = form.querySelector('#hidden_seller_type');
    const hiddenDis = form.querySelector('#hidden_dis');
    const hiddenHorarioInicio = form.querySelector('#hidden_horario_inicio');
    const hiddenHorarioFinal = form.querySelector('#hidden_horario_final');
    const hiddenLang1 = form.querySelector('#hidden_lang_1');
    const hiddenLang2 = form.querySelector('#hidden_lang_2');
    const hiddenPhotoInputsContainer = form.querySelector('#hidden-photo-inputs');

    let etapaActualIndex = 0;

    function inicializar() {
        etapas.forEach((etapa, index) => {
            if (etapa.classList.contains('activa')) {
                etapaActualIndex = index;
            } else {
                etapa.classList.add('oculto');
            }
        });
        agregarListeners();
        actualizarContadores();

        actualizarMarcadoVisualRadios(tipoUsuarioRadios);
        actualizarMarcadoVisualPlan();

        if (contenedorHorario && !contenedorHorario.classList.contains('oculto')) {
            // Esto es un fallback, el CSS debería manejarlo
            // contenedorHorario.classList.add('oculto');
            // ayudaTextoHorario?.classList.add('oculto');
        }
        if (btnMostrarHorario && contenedorHorario && contenedorHorario.classList.contains('oculto')) {
            btnMostrarHorario.classList.remove('oculto'); // Asegurarse que el botón se vea
        } else if (btnMostrarHorario && contenedorHorario && !contenedorHorario.classList.contains('oculto')) {
            btnMostrarHorario.classList.add('oculto'); // Si por alguna razón el horario ya está visible, ocultar botón
        }
    }

    function agregarListeners() {
        btnSiguiente.forEach(btn => btn.addEventListener('click', irASiguienteEtapa));
        btnAnterior.forEach(btn => btn.addEventListener('click', irAEtapaAnterior));

        if (tituloInput && contTitulo) tituloInput.addEventListener('input', actualizarContadores);
        if (descripcionTextarea && contDesc) descripcionTextarea.addEventListener('input', actualizarContadores);
        if (fotosInput) fotosInput.addEventListener('change', manejarSeleccionFotos);

        // --- Listener para el botón "Administrar horario" ---
        if (btnMostrarHorario && contenedorHorario) {
            btnMostrarHorario.addEventListener('click', () => {
                contenedorHorario.classList.remove('oculto');
                ayudaTextoHorario?.classList.remove('oculto'); // Muestra el texto de ayuda también
                btnMostrarHorario.classList.add('oculto'); // Oculta el botón una vez pulsado
                // Opcional: Mover el foco al primer elemento interactivo del horario
                contenedorHorario.querySelector('button, select')?.focus();
            });
        }

        // --- Listeners para los botones de estado del día ---
        diaEstadoBotones.forEach(boton => {
            boton.addEventListener('click', toggleDiaEstado);
        });

        form.addEventListener('submit', manejarEnvioFinal);

        tipoUsuarioRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                actualizarMarcadoVisualRadios(tipoUsuarioRadios);
                const etapaActual = etapas[etapaActualIndex];
                if (etapaActual && etapaActual.id === 'etapa-tipo-usuario' && radio.checked) {
                    avanzarSiValido();
                }
            });
        });

        planSelectables.forEach(selectableDiv => {
            selectableDiv.addEventListener('click', function () {
                const planValue = this.dataset.planValue;
                if (planValue) {
                    const radioToSelect = form.querySelector(`input[name="plan"][value="${planValue}"]`);
                    if (radioToSelect) {
                        radioToSelect.checked = true;
                        actualizarMarcadoVisualPlan();
                        const etapaActual = etapas[etapaActualIndex];
                        if (etapaActual && etapaActual.id === 'etapa-plan') {
                            avanzarSiValido();
                        }
                    }
                }
            });
        });
    }

    function avanzarSiValido() {
        if (validarEtapaActual()) {
            actualizarCamposOcultosEtapaActual();
            if (etapaActualIndex < etapas.length - 1) {
                cambiarEtapa(etapaActualIndex + 1);
            }
        } else {
            window.scrollTo({top: form.offsetTop - 20, behavior: 'smooth'});
        }
    }

    function irASiguienteEtapa(event) {
        event.preventDefault();
        avanzarSiValido();
    }

    function irAEtapaAnterior(event) {
        event.preventDefault();
        if (etapaActualIndex > 0) {
            cambiarEtapa(etapaActualIndex - 1);
        }
    }

    function cambiarEtapa(nuevoIndex) {
        if (nuevoIndex >= 0 && nuevoIndex < etapas.length) {
            etapas[etapaActualIndex].classList.remove('activa');
            etapas[etapaActualIndex].classList.add('oculto');
            etapaActualIndex = nuevoIndex;
            etapas[etapaActualIndex].classList.add('activa');
            etapas[etapaActualIndex].classList.remove('oculto');
            window.scrollTo({top: form.offsetTop - 20, behavior: 'smooth'});
        }
    }

    function validarEtapaActual() {
        const etapaActual = etapas[etapaActualIndex];
        limpiarErroresEtapa(etapaActual);
        let esValido = true;
        const inputsInvalidos = [];

        switch (etapaActual.id) {
            case 'etapa-tipo-usuario':
                const tipoUsuarioSeleccionado = form.querySelector('input[name="tipo_usuario"]:checked');
                if (!validarCampo(tipoUsuarioRadios[0]?.closest('.lista-opciones'), '#error-tipo-usuario', tipoUsuarioSeleccionado, 'Debes seleccionar un tipo de perfil.')) {
                    esValido = false;
                    inputsInvalidos.push(tipoUsuarioRadios[0]);
                }
                break;

            case 'etapa-plan':
                const planSeleccionado = form.querySelector('input[name="plan"]:checked');
                if (!validarCampo(form.querySelector('#etapa-plan .segundo-div-plan'), '#error-plan', planSeleccionado, 'Debes seleccionar un plan.')) {
                    esValido = false;
                    inputsInvalidos.push(planSelectables[0]);
                }
                break;

            case 'etapa-perfil':
                if (!validarCampo(nombreInput, '#error-nombre', nombreInput?.value.trim(), 'El nombre es obligatorio.')) {
                    esValido = false;
                    inputsInvalidos.push(nombreInput);
                }
                if (!validarCampo(categoriaSelect, '#error-categoria', categoriaSelect?.value, 'Debes seleccionar una categoría.')) {
                    esValido = false;
                    inputsInvalidos.push(categoriaSelect);
                }
                if (!validarCampo(provinciaSelect, '#error-provincia', provinciaSelect?.value, 'Debes seleccionar una provincia.')) {
                    esValido = false;
                    inputsInvalidos.push(provinciaSelect);
                }
                const tituloVal = tituloInput?.value.trim() || '';
                if (!validarCampo(tituloInput, '#error-titulo', tituloVal && tituloVal.length >= 10 && tituloVal.length <= 50, `El título es obligatorio (entre 10 y 50 caracteres). Actual: ${tituloVal.length}`)) {
                    esValido = false;
                    inputsInvalidos.push(tituloInput);
                }
                const descVal = descripcionTextarea?.value.trim() || '';
                if (!validarCampo(descripcionTextarea, '#error-descripcion', descVal && descVal.length >= 100 && descVal.length <= 500, `La descripción es obligatoria (entre 100 y 500 caracteres). Actual: ${descVal.length}`)) {
                    esValido = false;
                    inputsInvalidos.push(descripcionTextarea);
                }
                const serviciosSeleccionados = form.querySelectorAll('input[name="servicios[]"]:checked').length;
                if (!validarCampo(serviciosCheckboxes[0]?.closest('.grupo-checkboxes'), '#error-servicios', serviciosSeleccionados > 0, 'Debes seleccionar al menos un servicio.')) {
                    esValido = false;
                    inputsInvalidos.push(serviciosCheckboxes[0]?.closest('.grupo-checkboxes'));
                }
                break;

            case 'etapa-anuncio':
                const fotosSubidas = hiddenPhotoInputsContainer.querySelectorAll('input[name="photo_name[]"]').length;
                if (!validarCampo(listaFotosContainer, '#error-fotos', fotosSubidas > 0, 'Debes subir al menos una foto.')) {
                    esValido = false;
                    inputsInvalidos.push(fotosInput);
                }
                // Solo validar si el contenedor de horario está visible
                if (contenedorHorario && !contenedorHorario.classList.contains('oculto')) {
                    if (!validarHorarioSeleccionado()) {
                        esValido = false;
                        // Empujar el contenedor o el primer botón como referencia para scroll/focus
                        inputsInvalidos.push(form.querySelector('.horario-semanal .btn-dia-estado') || contenedorHorario);
                    }
                } // Si está oculto, no validamos (implica que no se ha administrado)

                const telefonoVal = telefonoInput?.value.replace(/\D/g, '') || '';
                if (!validarCampo(telefonoInput, '#error-telefono', /^[0-9]{9,15}$/.test(telefonoVal), 'Introduce un teléfono válido (9-15 dígitos).')) {
                    esValido = false;
                    inputsInvalidos.push(telefonoInput);
                }
                if (!validarCampo(salidasSelect, '#error-salidas', salidasSelect?.value !== '', 'Debes indicar si realizas salidas.')) {
                    esValido = false;
                    inputsInvalidos.push(salidasSelect);
                }
                if (emailInput && !emailInput.readOnly && !emailInput.closest('.frm-grupo').hidden) {
                    if (!validarCampo(emailInput, '#error-email', /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value), 'Introduce un email válido.')) {
                        esValido = false;
                        inputsInvalidos.push(emailInput);
                    }
                }
                break;

            case 'etapa-extras':
                if (!validarCampo(terminosCheckbox, '#error-terminos', terminosCheckbox?.checked, 'Debes aceptar los términos y condiciones.')) {
                    esValido = false;
                    inputsInvalidos.push(terminosCheckbox);
                }
                break;
        }

        if (!esValido && inputsInvalidos.length > 0) {
            const firstInvalid = inputsInvalidos[0];
            if (firstInvalid && typeof firstInvalid.focus === 'function') {
                firstInvalid.focus();
            }
        }

        return esValido;
    }

    function validarCampo(elemento, errorSelector, condition, message) {
        const errorMsgElement = form.querySelector(errorSelector);
        if (!errorMsgElement) {
            return condition;
        }

        let campo = elemento;
        if (elemento && !(elemento.nodeName === 'INPUT' || elemento.nodeName === 'SELECT' || elemento.nodeName === 'TEXTAREA')) {
            campo = elemento.querySelector('input, select, textarea');
        }

        if (!condition) {
            errorMsgElement.textContent = message;
            errorMsgElement.classList.remove('oculto');
            elemento?.classList.add('invalido');
            campo?.classList.add('invalido');
            return false;
        } else {
            errorMsgElement.classList.add('oculto');
            elemento?.classList.remove('invalido');
            campo?.classList.remove('invalido');
            return true;
        }
    }

    function validarHorarioSeleccionado() {
        const diasDisponibles = form.querySelectorAll('.btn-dia-estado.disponible');
        const contenedorHorarioElem = form.querySelector('.horario-semanal'); // Referencia al div
        const errorHorarioElem = form.querySelector('#error-horario'); // Referencia al div de error

        if (diasDisponibles.length === 0) {
            // Usa validarCampo para mostrar el error estandarizado
            return validarCampo(contenedorHorarioElem, '#error-horario', false, 'Debes marcar al menos un día como disponible.');
        } else {
            // Limpia el error si hay días disponibles
            return validarCampo(contenedorHorarioElem, '#error-horario', true, '');
        }
    }

    function limpiarErroresEtapa(etapa) {
        etapa.querySelectorAll('.error-msg').forEach(msg => msg.classList.add('oculto'));
        etapa.querySelectorAll('.invalido').forEach(el => el.classList.remove('invalido'));
    }

    function actualizarCamposOcultosEtapaActual() {
        const etapaId = etapas[etapaActualIndex]?.id;

        switch (etapaId) {
            case 'etapa-tipo-usuario':
                actualizarSellerTypeOculto();
                break;
            case 'etapa-anuncio':
                actualizarHorarioOculto();
                actualizarIdiomasOculto();
                break;
        }
    }

    function actualizarSellerTypeOculto() {
        const seleccionado = form.querySelector('input[name="tipo_usuario"]:checked');
        if (seleccionado && hiddenSellerType) {
            hiddenSellerType.value = seleccionado.value === 'visitante' ? '' : seleccionado.value;
        }
    }

    function actualizarHorarioOculto() {
        let diasSeleccionados = [];
        let horarios = {inicio: '23:59', fin: '00:00'};
        let primerDia = -1,
            ultimoDia = -1;
        const diasMapping = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo']; // Para obtener el índice

        // Iterar sobre los botones de estado para encontrar los disponibles
        const botonesDia = form.querySelectorAll('.btn-dia-estado');

        botonesDia.forEach(boton => {
            if (boton.classList.contains('disponible')) {
                const diaKey = boton.dataset.dia; // Obtener el día desde data-dia
                const diaIndex = diasMapping.indexOf(diaKey); // Encontrar el índice 0-6
                const contenedorDia = boton.closest('.dia-horario');

                if (diaKey && contenedorDia && diaIndex !== -1) {
                    diasSeleccionados.push(diaKey);
                    if (primerDia === -1) primerDia = diaIndex;
                    ultimoDia = diaIndex;

                    const inicioSelect = contenedorDia.querySelector('select[name$="[inicio]"]');
                    const finSelect = contenedorDia.querySelector('select[name$="[fin]"]');

                    if (inicioSelect && inicioSelect.value < horarios.inicio) {
                        horarios.inicio = inicioSelect.value;
                    }
                    if (finSelect && finSelect.value > horarios.fin) {
                        horarios.fin = finSelect.value;
                    }
                } else {
                    console.warn('Error al procesar día disponible:', boton);
                }
            }
        });

        // El resto de la lógica para calcular 'dis' y actualizar hidden inputs es la misma
        if (diasSeleccionados.length === 0) {
            hiddenDis.value = '0';
            hiddenHorarioInicio.value = '00:00';
            hiddenHorarioFinal.value = '00:00';
            return;
        }

        let valorDis = '0'; // Por defecto o personalizado
        // Lógica para determinar 'dis' (1=Todos, 2=L-V, 3=L-S, 4=S-D) - Mantenida
        if (diasSeleccionados.length === 7) valorDis = '1';
        else if (diasSeleccionados.length === 5 && primerDia === 0 && ultimoDia === 4) valorDis = '2'; // Lunes a Viernes
        else if (diasSeleccionados.length === 6 && primerDia === 0 && ultimoDia === 5) valorDis = '3'; // Lunes a Sábado
        else if (diasSeleccionados.length === 2 && primerDia === 5 && ultimoDia === 6) valorDis = '4'; // Sábado y Domingo
        else valorDis = '1'; // Si no coincide, quizás '1' (Todos los marcados) o un código específico? Ajustar según necesidad del backend. Asumiendo '1' por ahora.

        hiddenDis.value = valorDis;
        hiddenHorarioInicio.value = horarios.inicio;
        hiddenHorarioFinal.value = horarios.fin;
    }

    function actualizarIdiomasOculto() {
        if (idioma1Select && hiddenLang1) {
            hiddenLang1.value = idioma1Select.value;
        }
        if (idioma2Select && hiddenLang2) {
            hiddenLang2.value = idioma2Select.value;
        }
    }

    function actualizarContadores() {
        if (tituloInput && contTitulo) {
            contTitulo.textContent = tituloInput.value.length;
        }
        if (descripcionTextarea && contDesc) {
            contDesc.textContent = descripcionTextarea.value.length;
        }
    }

    function toggleDiaEstado(event) {
        const boton = event.currentTarget;
        const diaHorarioDiv = boton.closest('.dia-horario');
        const horasDiv = diaHorarioDiv.querySelector('.horas-dia');
        const selectsHora = horasDiv.querySelectorAll('select');
        const esDisponibleAhora = boton.classList.contains('disponible');

        if (esDisponibleAhora) {
            // Cambiar a No Disponible
            boton.textContent = 'No disponible';
            boton.classList.remove('disponible');
            boton.classList.add('no-disponible');
            horasDiv.classList.add('oculto');
            // Deshabilitar selects para que no se envíen
            selectsHora.forEach(select => (select.disabled = true));
            diaHorarioDiv.classList.remove('dia-activo'); // Clase visual opcional
        } else {
            // Cambiar a Disponible
            boton.textContent = 'Disponible';
            boton.classList.remove('no-disponible');
            boton.classList.add('disponible');
            horasDiv.classList.remove('oculto');
            // Habilitar selects
            selectsHora.forEach(select => (select.disabled = false));
            diaHorarioDiv.classList.add('dia-activo'); // Clase visual opcional
        }
        // Re-validar después de cada cambio para quitar el mensaje de error si se añade el primer día
        if (etapas[etapaActualIndex]?.id === 'etapa-anuncio') {
            validarHorarioSeleccionado();
        }
    }

    function manejarSeleccionFotos(event) {
        const files = event.target.files;
        const inputElement = event.target; // El input que disparó el evento
        const filenameToReplace = inputElement.dataset.replacingFilename; // Comprueba si estamos reemplazando
        const isReplacing = !!filenameToReplace;

        limpiarErroresEtapa(etapas[etapaActualIndex]); // Limpia errores de la etapa actual

        if (!files || files.length === 0) {
            // Si el usuario cancela, limpiar el estado de reemplazo si existía
            if (isReplacing) {
                delete inputElement.dataset.replacingFilename;
            }
            return; // No hacer nada si no se seleccionan archivos
        }

        if (isReplacing) {
            // --- LÓGICA DE REEMPLAZO ---
            if (files.length > 1) {
                mostrarErrorFotos('Solo puedes seleccionar una foto para reemplazar.');
                delete inputElement.dataset.replacingFilename; // Limpiar estado
                inputElement.value = null; // Limpiar selección del input
                return;
            }

            const file = files[0];

            // Validar el *nuevo* archivo
            if (!validarArchivoFoto(file)) {
                delete inputElement.dataset.replacingFilename; // Limpiar estado
                inputElement.value = null; // Limpiar selección del input
                return; // La validación muestra el error
            }

            // Subir la nueva foto. El callback de AJAX se encargará de eliminar la antigua.
            // Pasamos el inputElement para que el callback pueda acceder al dataset
            subirFotoAjax(file, inputElement);
        } else {
            // --- LÓGICA DE AÑADIR (Original) ---
            const maxPhotos = typeof maxPhotosAllowed !== 'undefined' ? maxPhotosAllowed : 3; // Usa variable global si existe
            const currentPhotosCount = hiddenPhotoInputsContainer.querySelectorAll('input[name="photo_name[]"]').length;
            let addedCount = 0;

            if (currentPhotosCount >= maxPhotos) {
                mostrarErrorFotos(`Ya has alcanzado el límite de ${maxPhotos} fotos.`);
                inputElement.value = null; // Limpiar selección
                return;
            }

            for (let i = 0; i < files.length; i++) {
                if (currentPhotosCount + addedCount >= maxPhotos) {
                    mostrarErrorFotos(`Solo puedes añadir ${maxPhotos - currentPhotosCount} foto(s) más.`);
                    break; // Detener el bucle si se alcanza el límite
                }
                const file = files[i];

                if (validarArchivoFoto(file)) {
                    // Pasamos inputElement (aunque no se use aquí, por consistencia con la llamada de reemplazo)
                    subirFotoAjax(file, inputElement);
                    addedCount++;
                }
                // Si validarArchivoFoto falla, ya muestra el error, continuamos con el siguiente archivo
            }
        }

        // Limpiar el valor del input DESPUÉS de procesar los archivos
        // Usamos setTimeout para evitar problemas si la subida es muy rápida
        setTimeout(() => {
            inputElement.value = null;
        }, 0);
    }

    function validarArchivoFoto(file) {
        const maxSize = 2 * 1024 * 1024; // 2MB
        const allowedTypes = ['image/jpeg', 'image/png'];

        if (!allowedTypes.includes(file.type)) {
            mostrarErrorFotos(`Archivo "${file.name}" no es JPG o PNG (tipo detectado: ${file.type || 'desconocido'}).`);
            return false;
        }
        if (file.size > maxSize) {
            const sizeInMB = (file.size / 1024 / 1024).toFixed(2);
            mostrarErrorFotos(`Archivo "${file.name}" excede los 2MB (tamaño: ${sizeInMB}MB).`);
            return false;
        }
        return true;
    }

    function subirFotoAjax(file, inputElement) {
        const loadingIndicator = crearLoadingPreview(file.name);
        listaFotosContainer.appendChild(loadingIndicator);

        const formData = new FormData();
        formData.append('userImage', file); // Asegúrate que 'userImage' es el nombre esperado por el backend

        const urlSubida = typeof uploadUrl !== 'undefined' ? uploadUrl : 'sc-includes/php/ajax/upload_picture.php'; // Usa variable global si existe

        fetch(urlSubida, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    // Intenta obtener más detalles del error si es posible
                    return response.text().then(text => {
                        throw new Error(`Error HTTP ${response.status}: ${response.statusText}. Respuesta: ${text}`);
                    });
                }
                return response.text(); // El backend devuelve HTML según el código original
            })
            .then(html => {
                loadingIndicator.remove();
                const photoData = parsePhotoUploadResponse(html);

                if (photoData.filename) {
                    const newFilename = photoData.filename;
                    const filenameToReplace = inputElement.dataset.replacingFilename; // Comprueba si estábamos reemplazando

                    if (filenameToReplace) {
                        // --- ESTAMOS REEMPLAZANDO ---
                        console.log(`Reemplazando: ${filenameToReplace} con ${newFilename}`);
                        // 1. Eliminar el preview antiguo
                        const oldPreview = listaFotosContainer.querySelector(`.foto-subida-item[data-filename="${filenameToReplace}"]`);
                        if (oldPreview) {
                            oldPreview.remove();
                            console.log(`Preview antiguo [${filenameToReplace}] eliminado.`);
                        } else {
                            console.warn(`No se encontró el preview antiguo para [${filenameToReplace}].`);
                        }

                        // 2. Eliminar el input oculto antiguo
                        const oldHiddenInput = hiddenPhotoInputsContainer.querySelector(`input[name="photo_name[]"][value="${filenameToReplace}"]`);
                        if (oldHiddenInput) {
                            oldHiddenInput.remove();
                            console.log(`Input oculto antiguo [${filenameToReplace}] eliminado.`);
                        } else {
                            console.warn(`No se encontró el input oculto antiguo para [${filenameToReplace}].`);
                        }

                        // 3. Limpiar el indicador del input de archivo
                        delete inputElement.dataset.replacingFilename;
                    } else {
                        console.log(`Añadiendo nueva foto: ${newFilename}`);
                    }

                    // --- AÑADIR NUEVO PREVIEW E INPUT (SIEMPRE) ---
                    const previewElement = crearPreviewFoto(photoData.previewHtml, newFilename);
                    listaFotosContainer.appendChild(previewElement);
                    console.log(`Nuevo preview [${newFilename}] añadido.`);

                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'photo_name[]'; // Nombre esperado por el backend
                    hiddenInput.value = newFilename;
                    hiddenPhotoInputsContainer.appendChild(hiddenInput);
                    console.log(`Nuevo input oculto [${newFilename}] añadido.`);

                    validarCampo(listaFotosContainer, '#error-fotos', true, ''); // Revalida que haya al menos una foto
                } else {
                    // El servidor no devolvió un filename válido
                    mostrarErrorFotos(`Error procesando la respuesta del servidor para "${file.name}". Respuesta: ${html}`);
                    // Limpiar el estado de reemplazo si falló
                    if (inputElement.dataset.replacingFilename) {
                        delete inputElement.dataset.replacingFilename;
                    }
                }
            })
            .catch(error => {
                loadingIndicator.remove();
                mostrarErrorFotos(`Error subiendo "${file.name}": ${error.message}`);
                console.error('Error en fetch:', error);
                // Limpiar el estado de reemplazo si falló
                if (inputElement.dataset.replacingFilename) {
                    delete inputElement.dataset.replacingFilename;
                }
            });
    }

    function parsePhotoUploadResponse(html) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        const hiddenInput = tempDiv.querySelector('input[type="hidden"][name="photo_name[]"]');
        const filename = hiddenInput ? hiddenInput.value : null;
        // Devolvemos el HTML original también, porque crearPreviewFoto lo usa
        return {filename: filename, previewHtml: html};
    }

    function crearLoadingPreview(filename) {
        const div = document.createElement('div');
        div.classList.add('foto-subida-item', 'loading');
        // Podrías añadir un spinner aquí en lugar de solo texto
        div.innerHTML = `
            <div class="loading-spinner"></div>
            <span>Subiendo ${filename}...</span>
        `;
        return div;
    }

    function crearPreviewFoto(htmlContent, filename) {
        const div = document.createElement('div');
        div.classList.add('foto-subida-item');
        // Guardamos el filename en el div principal para fácil acceso al reemplazar/eliminar
        div.dataset.filename = filename;
        div.innerHTML = htmlContent; // Inserta el HTML del servidor (ej: la imagen)

        // Elimina cualquier input oculto que pudiera venir en la respuesta HTML,
        // ya que gestionamos los inputs ocultos de forma centralizada.
        const hiddenInPreview = div.querySelector('input[name="photo_name[]"]');
        hiddenInPreview?.remove();

        // --- Crear Contenedor de Acciones ---
        const actionsDiv = document.createElement('div');
        actionsDiv.classList.add('preview-actions'); // Clase para estilizar el contenedor de botones

        // --- Botón Cambiar Foto ---
        const changeBtn = document.createElement('button');
        changeBtn.type = 'button';
        changeBtn.classList.add('btn-preview-action', 'btn-change-foto');
        changeBtn.title = 'Cambiar foto'; // Tooltip para el usuario
        changeBtn.setAttribute('aria-label', `Cambiar la foto ${filename}`); // Accesibilidad
        changeBtn.dataset.filename = filename; // Guardamos el filename para saber cuál cambiar
        changeBtn.innerHTML = `
            <svg data-testid="geist-icon" height="12" stroke-linejoin="round" viewBox="0 0 16 16" width="12" style="color: currentcolor;" aria-hidden="true">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M8.00002 1.25C5.33749 1.25 3.02334 2.73677 1.84047 4.92183L1.48342 5.58138L2.80253 6.29548L3.15958 5.63592C4.09084 3.91566 5.90986 2.75 8.00002 2.75C10.4897 2.75 12.5941 4.40488 13.2713 6.67462H11.8243H11.0743V8.17462H11.8243H15.2489C15.6631 8.17462 15.9989 7.83883 15.9989 7.42462V4V3.25H14.4989V4V5.64468C13.4653 3.06882 10.9456 1.25 8.00002 1.25ZM1.50122 10.8555V12.5V13.25H0.0012207V12.5V9.07538C0.0012207 8.66117 0.337007 8.32538 0.751221 8.32538H4.17584H4.92584V9.82538H4.17584H2.72876C3.40596 12.0951 5.51032 13.75 8.00002 13.75C10.0799 13.75 11.8912 12.5958 12.8266 10.8895L13.1871 10.2318L14.5025 10.9529L14.142 11.6105C12.9539 13.7779 10.6494 15.25 8.00002 15.25C5.05453 15.25 2.53485 13.4313 1.50122 10.8555Z" fill="currentColor"></path>
            </svg>`;
        changeBtn.addEventListener('click', handleChangeFotoClick); // Llama a la nueva función handler
        actionsDiv.appendChild(changeBtn);

        // --- Botón Eliminar Foto ---
        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.classList.add('btn-preview-action', 'btn-delete-foto'); // Mantenemos clase original si es útil
        deleteBtn.title = 'Eliminar foto'; // Tooltip
        deleteBtn.setAttribute('aria-label', `Eliminar la foto ${filename}`); // Accesibilidad
        deleteBtn.dataset.filename = filename; // Necesario para la función eliminarFoto
        deleteBtn.innerHTML = `
            <svg data-testid="geist-icon" height="12" stroke-linejoin="round" viewBox="0 0 16 16" width="12" style="color: currentcolor;" aria-hidden="true">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12.4697 13.5303L13 14.0607L14.0607 13L13.5303 12.4697L9.06065 7.99999L13.5303 3.53032L14.0607 2.99999L13 1.93933L12.4697 2.46966L7.99999 6.93933L3.53032 2.46966L2.99999 1.93933L1.93933 2.99999L2.46966 3.53032L6.93933 7.99999L2.46966 12.4697L1.93933 13L2.99999 14.0607L3.53032 13.5303L7.99999 9.06065L12.4697 13.5303Z" fill="currentColor"></path>
            </svg>`;
        deleteBtn.addEventListener('click', eliminarFoto); // Usa la función existente
        actionsDiv.appendChild(deleteBtn);

        // --- Adjuntar Contenedor de Acciones al Preview ---
        // Intentar encontrar un contenedor específico dentro del HTML devuelto si existe
        // (como el '.photos_options' que mencionaste). Si no, añadirlo al final del div principal.
        const optionsContainer = div.querySelector('.photos_options');
        if (optionsContainer) {
            // Limpiar contenido previo por si acaso y añadir el nuevo div de acciones
            optionsContainer.innerHTML = '';
            optionsContainer.appendChild(actionsDiv);
        } else {
            // Si no hay un div específico, intenta añadirlo después de la imagen principal
            const img = div.querySelector('img');
            if (img && img.parentNode) {
                // Inserta el div de acciones justo después de la imagen
                img.parentNode.insertBefore(actionsDiv, img.nextSibling);
            } else {
                // Fallback: Añadir al final del div principal si no hay imagen o contenedor
                div.appendChild(actionsDiv);
            }
        }

        return div;
    }

    function handleChangeFotoClick(event) {
        const button = event.currentTarget;
        const filenameToReplace = button.dataset.filename;

        if (!filenameToReplace || !fotosInput) {
            console.error('No se pudo obtener el filename o el input de fotos no existe.');
            return;
        }

        console.log(`Iniciando reemplazo para: ${filenameToReplace}`);
        // Guardamos el nombre del archivo que queremos reemplazar en un atributo data del input
        fotosInput.dataset.replacingFilename = filenameToReplace;

        // Limpiamos el valor actual del input por si acaso
        fotosInput.value = null;

        // Simulamos un clic en el input original para abrir el selector de archivos
        fotosInput.click();
    }

    function eliminarFoto(event) {
        const button = event.currentTarget; // El botón de eliminar que se presionó
        const filename = button.dataset.filename;
        if (!filename) {
            console.error('No se encontró el filename en el botón de eliminar.');
            return;
        }

        console.log(`Intentando eliminar: ${filename}`);

        // 1. Eliminar el elemento de previsualización (el div.foto-subida-item)
        const previewItem = button.closest('.foto-subida-item');
        if (previewItem) {
            previewItem.remove();
            console.log(`Preview [${filename}] eliminado.`);
        } else {
            console.warn(`No se encontró el elemento preview para [${filename}] (selector: .foto-subida-item).`);
        }

        // 2. Eliminar el input oculto correspondiente
        const hiddenInput = hiddenPhotoInputsContainer.querySelector(`input[name="photo_name[]"][value="${filename}"]`);
        if (hiddenInput) {
            hiddenInput.remove();
            console.log(`Input oculto [${filename}] eliminado.`);
        } else {
            console.warn(`No se encontró el input oculto para [${filename}].`);
        }

        // Revalidar si aún quedan fotos (por si se eliminó la última)
        const fotosRestantes = hiddenPhotoInputsContainer.querySelectorAll('input[name="photo_name[]"]').length;
        validarCampo(listaFotosContainer, '#error-fotos', fotosRestantes > 0, 'Debes subir al menos una foto.');

        // Si estábamos en proceso de reemplazar esta foto y la eliminamos, limpiar el estado
        if (fotosInput && fotosInput.dataset.replacingFilename === filename) {
            delete fotosInput.dataset.replacingFilename;
            console.log(`Estado de reemplazo limpiado para [${filename}] porque fue eliminada.`);
        }
    }

    function mostrarErrorFotos(mensaje) {
        const errorDiv = form.querySelector('#error-fotos');
        if (errorDiv) {
            errorDiv.textContent = mensaje;
            errorDiv.classList.remove('oculto');
            // Añadir clase de inválido al contenedor de la lista para resaltarlo visualmente
            listaFotosContainer?.classList.add('invalido');
            // También al input de fotos original, aunque esté oculto puede ser útil
            fotosInput?.classList.add('invalido');
        } else {
            // Fallback si no existe el div de error específico
            console.error('Error fotos (div #error-fotos no encontrado):', mensaje);
            alert(mensaje);
        }
    }

    function manejarEnvioFinal(event) {
        event.preventDefault(); // Previene el envío por defecto

        // Validaciones...
        if (!validarFormularioCompleto()) {
            alert('Por favor, revisa el formulario. Hay errores o campos incompletos en alguna de las etapas.');
            irAPrimeraEtapaConError();
            return; // Detiene la ejecución si hay errores
        }

        // Actualiza campos ocultos justo antes del (potencial) envío
        actualizarSellerTypeOculto();
        actualizarHorarioOculto();
        actualizarIdiomasOculto();

        // --- INICIO: Código añadido para ver los datos del formulario ---

        console.log('--- Preparando para enviar formulario ---');
        console.log('Valores que se enviarían:');

        // Usamos FormData para recopilar los datos tal como el navegador los enviaría
        const formData = new FormData(form);

        // Iteramos sobre los datos para mostrarlos en la consola
        console.group('Datos del FormData:'); // Agrupa los logs para mejor lectura
        let hasFiles = false;
        for (const [key, value] of formData.entries()) {
            if (value instanceof File) {
                // Si es un archivo, muestra info básica (no el contenido)
                console.log(`${key}: Archivo { nombre: "${value.name}", tamaño: ${value.size}, tipo: "${value.type}" }`);
                hasFiles = true;
            } else {
                // Si es un valor normal, lo muestra
                console.log(`${key}: ${value}`);
            }
        }
        console.groupEnd(); // Fin del grupo

        if (hasFiles) {
            console.log('(Nota: Los archivos no se pueden ver directamente en la consola, solo su información)');
        }

        // Opcional: Ver los datos como un objeto (puede ser más legible, pero ojo con campos con el mismo nombre, como arrays)
        try {
            const dataObject = Object.fromEntries(formData.entries());
            console.log('Datos como objeto (puede ocultar valores si hay claves repetidas):', dataObject);
        } catch (e) {
            console.warn('No se pudo convertir FormData a objeto:', e);
        }

        console.log('--- Inspección de datos finalizada ---');

        // --- FIN: Código añadido para ver los datos del formulario ---

        // IMPORTANTE: Decide qué hacer a continuación:
        // Opción 1: Ver los datos y LUEGO enviar (DESCOMENTA la línea de abajo)
        // form.submit();
        // console.log("Formulario enviado.");

        // Opción 2: Ver los datos y DETENER el envío para analizar (DEJA COMENTADA la línea form.submit())
        console.warn("¡Envío detenido! Descomenta 'form.submit();' en el código para permitir el envío real.");
        // No llames a form.submit() si solo quieres ver los datos por ahora
    } // Fin de manejarEnvioFinal

    function validarFormularioCompleto() {
        let todoValido = true;
        for (let i = 0; i < etapas.length; i++) {
            const originalIndex = etapaActualIndex;
            etapaActualIndex = i;
            if (!validarEtapaActual()) {
                todoValido = false;
            }
            etapaActualIndex = originalIndex;
        }
        if (!todoValido) {
            validarEtapaActual();
        }
        return todoValido;
    }

    function irAPrimeraEtapaConError() {
        for (let i = 0; i < etapas.length; i++) {
            const etapa = etapas[i];
            limpiarErroresEtapa(etapa);
            const originalIndex = etapaActualIndex;
            etapaActualIndex = i;
            const esValida = validarEtapaActual();
            etapaActualIndex = originalIndex;

            if (!esValida) {
                cambiarEtapa(i);
                break;
            }
        }
    }

    function actualizarMarcadoVisualPlan() {
        const planSeleccionado = form.querySelector('input[name="plan"]:checked');
        const valorSeleccionado = planSeleccionado ? planSeleccionado.value : null;

        planSelectables.forEach(div => {
            if (div.dataset.planValue === valorSeleccionado) {
                div.classList.add('marcado');
            } else {
                div.classList.remove('marcado');
            }
        });
    }

    function actualizarMarcadoVisualRadios(radiosNodeList) {
        radiosNodeList.forEach(radio => {
            const labelPadre = radio.closest('label.opcion-radio');
            if (labelPadre) {
                if (radio.checked) {
                    labelPadre.classList.add('marcado');
                } else {
                    labelPadre.classList.remove('marcado');
                }
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializar);
    } else {
        inicializar();
    }

    /////////////////

    function setupCustomSelect(wrapperId) {
        const wrapper = document.getElementById(wrapperId);
        if (!wrapper) {
            console.warn(`Custom select wrapper #${wrapperId} not found.`);
            return;
        }

        const trigger = wrapper.querySelector('.custom-select-trigger');
        const dropdown = wrapper.querySelector('.custom-select-dropdown');
        const searchInput = wrapper.querySelector('.custom-select-search');
        const optionsList = wrapper.querySelector('.custom-select-options');
        const valueDisplay = trigger.querySelector('.custom-select-value');
        const closeButton = wrapper.querySelector('.custom-select-close');
        const originalSelectId = trigger.getAttribute('data-select-id');
        const originalSelect = document.getElementById(originalSelectId);

        if (!trigger || !dropdown || !searchInput || !optionsList || !valueDisplay || !closeButton || !originalSelect) {
            console.error(`Missing elements within custom select wrapper #${wrapperId}`);
            return;
        }

        let allOptionsData = []; // Guardar datos {value, text, element}

        // 1. Poblar opciones personalizadas desde el select original
        function populateOptions() {
            optionsList.innerHTML = ''; // Limpiar opciones existentes
            allOptionsData = []; // Limpiar datos
            const originalOptions = originalSelect.querySelectorAll('option');
            let selectedText = '';

            originalOptions.forEach(option => {
                if (option.value === '') return; // Omitir la opción placeholder

                const li = document.createElement('li');
                li.textContent = option.textContent;
                li.dataset.value = option.value;
                li.setAttribute('role', 'option');
                li.setAttribute('tabindex', '-1'); // Para poder hacer focus con JS/teclado

                if (option.selected) {
                    li.classList.add('selected');
                    li.setAttribute('aria-selected', 'true');
                    selectedText = option.textContent; // Guardar texto seleccionado
                } else {
                    li.setAttribute('aria-selected', 'false');
                }

                optionsList.appendChild(li);
                allOptionsData.push({value: option.value, text: option.textContent.toLowerCase(), element: li});

                // Añadir listener a cada opción creada
                li.addEventListener('click', handleOptionSelect);
                li.addEventListener('keydown', e => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        handleOptionSelect(e);
                    }
                });
            });

            // Actualizar el texto del trigger con la opción seleccionada inicialmente (si la hay)
            if (selectedText) {
                valueDisplay.textContent = selectedText;
            } else {
                // Si no hay nada seleccionado, usar el texto del placeholder del select original
                const placeholderOption = originalSelect.querySelector('option[value=""]');
                valueDisplay.textContent = placeholderOption ? placeholderOption.textContent : 'Seleccionar...';
            }
        }

        // 2. Abrir/Cerrar el Dropdown
        function toggleDropdown(event, forceClose = false) {
            // Evitar que el click en el dropdown lo cierre inmediatamente
            if (event && dropdown.contains(event.target) && event.target !== closeButton) {
                return;
            }

            const isOpen = wrapper.classList.contains('open');
            if (forceClose || isOpen) {
                wrapper.classList.remove('open');
                trigger.setAttribute('aria-expanded', 'false');
                dropdown.hidden = true;
                document.removeEventListener('click', handleClickOutside, true); // Importante remover el listener
            } else {
                wrapper.classList.add('open');
                trigger.setAttribute('aria-expanded', 'true');
                dropdown.hidden = false;
                // Pequeño delay para asegurar que el dropdown es visible antes de añadir el listener
                setTimeout(() => {
                    document.addEventListener('click', handleClickOutside, true);
                    // Enfocar buscador al abrir (mejor UX)
                    searchInput.focus();
                    scrollToSelected(); // Mover scroll a la opción seleccionada
                }, 0);
            }
        }

        // Cerrar si se hace clic fuera del componente
        function handleClickOutside(event) {
            if (!wrapper.contains(event.target)) {
                toggleDropdown(null, true); // Forzar cierre
            }
        }

        // 3. Manejar Selección de Opción
        function handleOptionSelect(event) {
            const selectedLi = event.currentTarget; // El LI que recibió el evento
            const newValue = selectedLi.dataset.value;
            const newText = selectedLi.textContent;

            // Actualizar el select original
            originalSelect.value = newValue;

            // Disparar evento 'change' en el select original (IMPORTANTE para validación)
            originalSelect.dispatchEvent(new Event('change', {bubbles: true}));

            // Actualizar el texto del trigger
            valueDisplay.textContent = newText;

            // Actualizar estado visual de las opciones (aria y clase)
            allOptionsData.forEach(optData => {
                const isSelected = optData.value === newValue;
                optData.element.classList.toggle('selected', isSelected);
                optData.element.setAttribute('aria-selected', isSelected.toString());
            });

            // Cerrar dropdown
            toggleDropdown(null, true);

            // Devolver el foco al trigger (buena práctica de accesibilidad)
            trigger.focus();
        }

        // 4. Funcionalidad de Búsqueda/Filtrado
        function filterOptions() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            allOptionsData.forEach(optData => {
                const isMatch = optData.text.includes(searchTerm);
                optData.element.classList.toggle('filtered-out', !isMatch);
            });
        }

        // 5. Scroll a la opción seleccionada al abrir
        function scrollToSelected() {
            const selectedOption = optionsList.querySelector('li.selected');
            if (selectedOption) {
                // Opciones para scrollIntoView
                const scrollOptions = {
                    behavior: 'auto', // 'smooth' puede ser molesto si es rápido
                    block: 'nearest' // 'center', 'start', 'end'
                };
                // Retraso mínimo para asegurar que el elemento está renderizado y visible
                setTimeout(() => {
                    selectedOption.scrollIntoView(scrollOptions);
                }, 50); // 50ms suele ser suficiente
            }
        }

        // 6. Manejar teclado para navegación básica (opcional pero recomendado)
        function handleKeyDown(event) {
            const isOpen = wrapper.classList.contains('open');
            const currentFocus = document.activeElement;

            if (event.key === 'Escape' && isOpen) {
                toggleDropdown(null, true);
                trigger.focus();
                return;
            }

            if (!isOpen) return; // Si está cerrado, no hacer nada más

            const focusableOptions = Array.from(optionsList.querySelectorAll('li:not(.filtered-out)'));
            if (focusableOptions.length === 0) return;

            let currentIndex = focusableOptions.findIndex(opt => opt === currentFocus);

            switch (event.key) {
                case 'ArrowDown':
                    event.preventDefault(); // Evitar scroll de página
                    if (currentFocus === searchInput || currentIndex === -1 || currentIndex === focusableOptions.length - 1) {
                        focusableOptions[0].focus(); // Ir al primero si estamos en el buscador o al final
                    } else {
                        focusableOptions[currentIndex + 1].focus(); // Ir al siguiente
                    }
                    break;
                case 'ArrowUp':
                    event.preventDefault(); // Evitar scroll de página
                    if (currentFocus === searchInput) {
                        focusableOptions[focusableOptions.length - 1].focus(); // Ir al último
                    } else if (currentIndex === -1 || currentIndex === 0) {
                        searchInput.focus(); // Volver al buscador si estamos al principio
                    } else {
                        focusableOptions[currentIndex - 1].focus(); // Ir al anterior
                    }
                    break;
                case 'Home':
                    event.preventDefault();
                    focusableOptions[0]?.focus();
                    break;
                case 'End':
                    event.preventDefault();
                    focusableOptions[focusableOptions.length - 1]?.focus();
                    break;
                case 'Enter':
                case ' ': // Espacio también puede seleccionar
                    if (currentFocus && currentFocus.tagName === 'LI') {
                        event.preventDefault();
                        handleOptionSelect({currentTarget: currentFocus}); // Simular click en la opción focuseada
                    }
                    break;
            }
        }

        // --- Event Listeners ---
        trigger.addEventListener('click', toggleDropdown);
        trigger.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleDropdown();
            } else if (e.key === 'ArrowDown' && !wrapper.classList.contains('open')) {
                e.preventDefault();
                toggleDropdown(); // Abrir con flecha abajo
            }
        });
        closeButton.addEventListener('click', () => toggleDropdown(null, true));
        searchInput.addEventListener('input', filterOptions);
        wrapper.addEventListener('keydown', handleKeyDown); // Capturar teclado en todo el wrapper

        // Inicializar
        populateOptions();

        // Si ya existe un valor en el select original al cargar, actualiza el trigger
        if (originalSelect.value) {
            const selectedOptionElement = originalSelect.querySelector(`option[value="${originalSelect.value}"]`);
            if (selectedOptionElement) {
                valueDisplay.textContent = selectedOptionElement.textContent;
            }
        }

        // Observador por si las opciones del select original cambian dinámicamente (poco probable en tu caso, pero robusto)
        const observer = new MutationObserver(populateOptions);
        observer.observe(originalSelect, {childList: true});
    } // Fin de setupCustomSelect

    // Inicializar para el selector de provincias cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setupCustomSelect('custom-provincia-wrapper');
            setupCustomSelect('custom-categoria-wrapper');
        });
    } else {
        setupCustomSelect('custom-provincia-wrapper');
        setupCustomSelect('custom-categoria-wrapper');
    }
})();
