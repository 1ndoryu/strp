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
    const horarioCheckboxes = form.querySelectorAll('.check-dia input[type="checkbox"]');
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
        inicializarHorarioVisual();
        actualizarMarcadoVisualRadios(tipoUsuarioRadios);
        actualizarMarcadoVisualPlan();
    }

    function agregarListeners() {
        btnSiguiente.forEach(btn => btn.addEventListener('click', irASiguienteEtapa));
        btnAnterior.forEach(btn => btn.addEventListener('click', irAEtapaAnterior));

        if (tituloInput && contTitulo) tituloInput.addEventListener('input', actualizarContadores);
        if (descripcionTextarea && contDesc) descripcionTextarea.addEventListener('input', actualizarContadores);
        if (fotosInput) fotosInput.addEventListener('change', manejarSeleccionFotos);

        horarioCheckboxes.forEach(check => {
            check.addEventListener('change', toggleHorasDia);
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
            selectableDiv.addEventListener('click', function() {
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
            window.scrollTo({ top: form.offsetTop - 20, behavior: 'smooth' });
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
            window.scrollTo({ top: form.offsetTop - 20, behavior: 'smooth' });
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
                 if (!validarCampo(nombreInput, '#error-nombre', nombreInput?.value.trim(), 'El nombre es obligatorio.')) { esValido = false; inputsInvalidos.push(nombreInput); }
                 if (!validarCampo(categoriaSelect, '#error-categoria', categoriaSelect?.value, 'Debes seleccionar una categoría.')) { esValido = false; inputsInvalidos.push(categoriaSelect); }
                 if (!validarCampo(provinciaSelect, '#error-provincia', provinciaSelect?.value, 'Debes seleccionar una provincia.')) { esValido = false; inputsInvalidos.push(provinciaSelect); }
                 const tituloVal = tituloInput?.value.trim() || '';
                 if (!validarCampo(tituloInput, '#error-titulo', tituloVal && tituloVal.length >= 10 && tituloVal.length <= 50, `El título es obligatorio (entre 10 y 50 caracteres). Actual: ${tituloVal.length}`)) { esValido = false; inputsInvalidos.push(tituloInput); }
                 const descVal = descripcionTextarea?.value.trim() || '';
                 if (!validarCampo(descripcionTextarea, '#error-descripcion', descVal && descVal.length >= 100 && descVal.length <= 500, `La descripción es obligatoria (entre 100 y 500 caracteres). Actual: ${descVal.length}`)) { esValido = false; inputsInvalidos.push(descripcionTextarea); }
                 const serviciosSeleccionados = form.querySelectorAll('input[name="servicios[]"]:checked').length;
                 if (!validarCampo(serviciosCheckboxes[0]?.closest('.grupo-checkboxes'), '#error-servicios', serviciosSeleccionados > 0, 'Debes seleccionar al menos un servicio.')) { esValido = false; inputsInvalidos.push(serviciosCheckboxes[0]?.closest('.grupo-checkboxes')); }
                 break;

            case 'etapa-anuncio':
                const fotosSubidas = hiddenPhotoInputsContainer.querySelectorAll('input[name="photo_name[]"]').length;
                if (!validarCampo(listaFotosContainer, '#error-fotos', fotosSubidas > 0, 'Debes subir al menos una foto.')) { esValido = false; inputsInvalidos.push(fotosInput); }
                if (!validarHorarioSeleccionado()) { esValido = false; inputsInvalidos.push(form.querySelector('.horario-semanal')); }
                const telefonoVal = telefonoInput?.value.replace(/\D/g, '') || '';
                if (!validarCampo(telefonoInput, '#error-telefono', /^[0-9]{9,15}$/.test(telefonoVal), 'Introduce un teléfono válido (9-15 dígitos).')) { esValido = false; inputsInvalidos.push(telefonoInput); }
                if (!validarCampo(salidasSelect, '#error-salidas', salidasSelect?.value !== '', 'Debes indicar si realizas salidas.')) { esValido = false; inputsInvalidos.push(salidasSelect); }
                if (emailInput && !emailInput.readOnly && !emailInput.closest('.frm-grupo').hidden) {
                    if (!validarCampo(emailInput, '#error-email', /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value), 'Introduce un email válido.')) { esValido = false; inputsInvalidos.push(emailInput); }
                }
                break;

            case 'etapa-extras':
                 if (!validarCampo(terminosCheckbox, '#error-terminos', terminosCheckbox?.checked, 'Debes aceptar los términos y condiciones.')) { esValido = false; inputsInvalidos.push(terminosCheckbox); }
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
        if(elemento && !(elemento.nodeName === 'INPUT' || elemento.nodeName === 'SELECT' || elemento.nodeName === 'TEXTAREA')) {
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
        const diasActivos = form.querySelectorAll('.check-dia input[type="checkbox"]:checked');
        const contenedorHorario = form.querySelector('.horario-semanal');
        if (diasActivos.length === 0) {
            return validarCampo(contenedorHorario, '#error-horario', false, 'Debes marcar al menos un día de disponibilidad.');
        }
        return validarCampo(contenedorHorario, '#error-horario', true, '');
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
        let primerDia = -1, ultimoDia = -1;

        horarioCheckboxes.forEach((check, index) => {
            if (check.checked) {
                const diaKey = check.name.match(/\[(.*?)\]/)[1];
                diasSeleccionados.push(diaKey);
                if (primerDia === -1) primerDia = index;
                ultimoDia = index;

                const contenedorDia = check.closest('.dia-horario');
                const inicioSelect = contenedorDia.querySelector('select[name$="[inicio]"]');
                const finSelect = contenedorDia.querySelector('select[name$="[fin]"]');

                if (inicioSelect.value < horarios.inicio) horarios.inicio = inicioSelect.value;
                if (finSelect.value > horarios.fin) horarios.fin = finSelect.value;
            }
        });

        if (diasSeleccionados.length === 0) {
            hiddenDis.value = '0';
            hiddenHorarioInicio.value = '00:00';
            hiddenHorarioFinal.value = '00:00';
            return;
        }

        let valorDis = '0';
        if (diasSeleccionados.length === 7) valorDis = '1';
        else if (diasSeleccionados.length === 5 && primerDia === 0 && ultimoDia === 4) valorDis = '2';
        else if (diasSeleccionados.length === 6 && primerDia === 0 && ultimoDia === 5) valorDis = '3';
        else if (diasSeleccionados.length === 2 && primerDia === 5 && ultimoDia === 6) valorDis = '4';
        else valorDis = '1';

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

    function inicializarHorarioVisual() {
        horarioCheckboxes.forEach(check => {
            toggleHorasDia({target: check});
        });
    }

    function toggleHorasDia(event) {
        const checkbox = event.target;
        const horasDiv = checkbox.closest('.dia-horario').querySelector('.horas-dia');
        if (horasDiv) {
            horasDiv.classList.toggle('oculto', !checkbox.checked);
        }
    }

    function manejarSeleccionFotos(event) {
        const files = event.target.files;
        const maxPhotos = typeof maxPhotosAllowed !== 'undefined' ? maxPhotosAllowed : 3;
        const currentPhotosCount = hiddenPhotoInputsContainer.querySelectorAll('input[name="photo_name[]"]').length;
        let addedCount = 0;

        limpiarErroresEtapa(etapas[etapaActualIndex]);

        if (currentPhotosCount >= maxPhotos) {
            mostrarErrorFotos(`Ya has alcanzado el límite de ${maxPhotos} fotos.`);
            event.target.value = null;
            return;
        }

        for (let i = 0; i < files.length; i++) {
            if (currentPhotosCount + addedCount >= maxPhotos) {
                mostrarErrorFotos(`Solo puedes añadir ${maxPhotos - currentPhotosCount} foto(s) más.`);
                break;
            }
            const file = files[i];

            if (!file.type.match('image/jpeg') && !file.type.match('image/png')) {
                mostrarErrorFotos(`Archivo "${file.name}" no es JPG o PNG.`);
                continue;
            }
            if (file.size > 2 * 1024 * 1024) {
                mostrarErrorFotos(`Archivo "${file.name}" excede los 2MB.`);
                continue;
            }

            subirFotoAjax(file);
            addedCount++;
        }
        event.target.value = null;
    }

    function subirFotoAjax(file) {
        const loadingIndicator = crearLoadingPreview(file.name);
        listaFotosContainer.appendChild(loadingIndicator);

        const formData = new FormData();
        formData.append('userImage', file);

        const urlSubida = typeof uploadUrl !== 'undefined' ? uploadUrl : 'sc-includes/php/ajax/upload_picture.php';

        fetch(urlSubida, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP ${response.status}`);
                }
                return response.text();
            })
            .then(html => {
                loadingIndicator.remove();
                const photoData = parsePhotoUploadResponse(html);

                if (photoData.filename) {
                    const previewElement = crearPreviewFoto(photoData.previewHtml, photoData.filename);
                    listaFotosContainer.appendChild(previewElement);

                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'photo_name[]';
                    hiddenInput.value = photoData.filename;
                    hiddenPhotoInputsContainer.appendChild(hiddenInput);

                    validarCampo(listaFotosContainer, '#error-fotos', true, '');
                } else {
                    mostrarErrorFotos(`Error procesando la respuesta del servidor para "${file.name}".`);
                }
            })
            .catch(error => {
                loadingIndicator.remove();
                mostrarErrorFotos(`Error subiendo "${file.name}": ${error.message}`);
            });
    }

    function parsePhotoUploadResponse(html) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        const hiddenInput = tempDiv.querySelector('input[name="photo_name[]"]');
        const filename = hiddenInput ? hiddenInput.value : null;
        return {filename: filename, previewHtml: html};
    }

    function crearLoadingPreview(filename) {
        const div = document.createElement('div');
        div.classList.add('foto-subida-item', 'loading');
        div.textContent = `Subiendo ${filename}...`;
        return div;
    }

    function crearPreviewFoto(htmlContent, filename) {
        const div = document.createElement('div');
        div.classList.add('foto-subida-item');
        div.innerHTML = htmlContent;

        const hiddenInPreview = div.querySelector('input[name="photo_name[]"]');
        hiddenInPreview?.remove();

        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.textContent = 'Eliminar';
        deleteBtn.classList.add('btn-eliminar-foto');
        deleteBtn.dataset.filename = filename;
        deleteBtn.addEventListener('click', eliminarFoto);

        const optionsDiv = div.querySelector('.photos_options');
        if (optionsDiv) {
            optionsDiv.appendChild(deleteBtn);
        } else {
            div.appendChild(deleteBtn);
        }
        return div;
    }

    function eliminarFoto(event) {
        const filename = event.target.dataset.filename;
        if (!filename) return;

        event.target.closest('.foto-subida-item')?.remove();

        const hiddenInput = hiddenPhotoInputsContainer.querySelector(`input[name="photo_name[]"][value="${filename}"]`);
        hiddenInput?.remove();
    }

    function mostrarErrorFotos(mensaje) {
        const errorDiv = form.querySelector('#error-fotos');
        if (errorDiv) {
            errorDiv.textContent = mensaje;
            errorDiv.classList.remove('oculto');
        } else {
            alert(mensaje);
        }
    }

    function manejarEnvioFinal(event) {
        event.preventDefault();

        if (!validarFormularioCompleto()) {
            alert('Por favor, revisa el formulario. Hay errores o campos incompletos en alguna de las etapas.');
            irAPrimeraEtapaConError();
            return;
        }

        actualizarSellerTypeOculto();
        actualizarHorarioOculto();
        actualizarIdiomasOculto();

        form.submit();
    }

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
                allOptionsData.push({ value: option.value, text: option.textContent.toLowerCase(), element: li });

                // Añadir listener a cada opción creada
                li.addEventListener('click', handleOptionSelect);
                li.addEventListener('keydown', (e) => {
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
            originalSelect.dispatchEvent(new Event('change', { bubbles: true }));

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
                         handleOptionSelect({ currentTarget: currentFocus }); // Simular click en la opción focuseada
                     }
                    break;
            }
         }

        // --- Event Listeners ---
        trigger.addEventListener('click', toggleDropdown);
        trigger.addEventListener('keydown', (e) => {
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
        observer.observe(originalSelect, { childList: true });

    } // Fin de setupCustomSelect

    // Inicializar para el selector de provincias cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setupCustomSelect('custom-provincia-wrapper');
            // Si tienes MÁS selects para personalizar, llama a setupCustomSelect con sus IDs aquí
            // setupCustomSelect('custom-otro-select-wrapper');
        });
    } else {
        setupCustomSelect('custom-provincia-wrapper');
        // setupCustomSelect('custom-otro-select-wrapper');
    }

})();