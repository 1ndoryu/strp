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
                 if (!validarCampo(descripcionTextarea, '#error-descripcion', descVal && descVal.length >= 100 && descVal.length <= 500, `La descripción es obligatoria (entre 30 y 500 caracteres). Actual: ${descVal.length}`)) { esValido = false; inputsInvalidos.push(descripcionTextarea); }
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

})();