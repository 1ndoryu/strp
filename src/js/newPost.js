(function () {
    'use strict';

    const HORARIO_STORAGE_KEY = 'userPendingSchedule';
    const MAX_PHOTOS = 3;
    const SVG_PLACEHOLDER = `<?xml version="1.0" encoding="UTF-8"?> <svg id="uuid-0ca005e1-d9fe-4045-a665-2e60e21962d4" data-name="Capa 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 145.83 120.73"> <defs> <style> .uuid-4e0375d0-00b6-45fd-960c-a83a919e3c21 { fill: #383a39; } .uuid-e42eef17-cbac-4bbb-bccc-8dc7fa78a734 { fill: none; stroke: #383a39; stroke-miterlimit: 10; stroke-width: 4.33px; } </style> </defs> <polygon class="uuid-4e0375d0-00b6-45fd-960c-a83a919e3c21" points="19.95 103.93 45.95 72.93 62.9 88.43 87.95 55.93 125.88 103.93 19.95 103.93"/> <circle class="uuid-4e0375d0-00b6-45fd-960c-a83a919e3c21" cx="31.95" cy="34.93" r="12"/> <rect class="uuid-e42eef17-cbac-4bbb-bccc-8dc7fa78a734" x="2.17" y="2.17" width="141.5" height="116.4" rx="18.8" ry="18.8"/> </svg>`;

    const form = document.getElementById('form-nuevo-anuncio');
    if (!form) {
        return;
    }

    const idioma1Select = document.getElementById('idioma_1');
    const nivelIdioma1Select = document.getElementById('nivel_idioma_1');
    const idioma2Select = document.getElementById('idioma_2');
    const nivelIdioma2Select = document.getElementById('nivel_idioma_2');
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
    const btnMostrarHorario = document.getElementById('btn-mostrar-horario');
    const contenedorHorario = document.getElementById('contenedor-horario');
    const ayudaTextoHorario = document.getElementById('ayuda-horario');
    const diaEstadoBotones = form.querySelectorAll('.btn-dia-estado');
    const contenedorInput = document.getElementById('input-url');
    const inputUrl = document.getElementById('url-banner');
    const inputUrlLateral = document.getElementById('input-url-banner-lateral');
    const contenedorInputLateral = document.getElementById('url-banner-lateral');
    const horarioFeedbackDiv = document.createElement('div');
    horarioFeedbackDiv.id = 'horario-feedback';
    horarioFeedbackDiv.style.marginTop = '10px';
    if (contenedorHorario) {
        contenedorHorario.parentNode.insertBefore(horarioFeedbackDiv, contenedorHorario.nextSibling);
    }
    const horarioSubmitErrorDiv = document.createElement('div');
    horarioSubmitErrorDiv.id = 'error-horario-submit';
    horarioSubmitErrorDiv.classList.add('error-msg', 'oculto');
    if (horarioFeedbackDiv) {
        horarioFeedbackDiv.parentNode.insertBefore(horarioSubmitErrorDiv, horarioFeedbackDiv.nextSibling);
    }
    const telefonoInput = form.querySelector('#telefono');
    const whatsappCheckbox = form.querySelector('input[name="whatsapp"]');
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

    let selectPosicion = null;
    let etapaActualIndex = 0;

    if (contenedorInputLateral) {
        contenedorInputLateral.addEventListener('click', () => {
            if (inputUrlLateral.classList.contains('active')) {
                inputUrlLateral.classList.remove('active');
                return;
            }
            inputUrlLateral.classList.toggle('active');
        });
    }

    if (contenedorInput) {
        contenedorInput.addEventListener('click', () => {
            if (inputUrl.classList.contains('active')) {
                inputUrl.classList.remove('active');
                return;
            }
            inputUrl.classList.toggle('active');
        });
    }

    function inicializar() {
        selectPosicion = document.getElementById('select-posicion-foto');

        etapas.forEach((etapa, index) => {
            if (etapa.classList.contains('activa')) {
                etapaActualIndex = index;
            } else {
                etapa.classList.add('oculto');
            }
        });
        agregarListeners();
        actualizarContadores();
        agregarListenersNuevos();
        actualizarPlaceholders();
        actualizarMarcadoVisualRadios(tipoUsuarioRadios);
        actualizarMarcadoVisualPlan();

        if (contenedorHorario) contenedorHorario.classList.add('oculto');
        if (ayudaTextoHorario) ayudaTextoHorario.classList.add('oculto');
        if (btnMostrarHorario) {
            const btnClone = btnMostrarHorario.cloneNode(true);
            btnMostrarHorario.parentNode.replaceChild(btnClone, btnMostrarHorario);
            const newBtnMostrarHorario = document.getElementById('btn-mostrar-horario');
            if (newBtnMostrarHorario) {
                newBtnMostrarHorario.addEventListener('click', abrirGestorHorario);
            } else {
                console.error('No se pudo encontrar el botón de horario clonado.');
            }
        }
        cargarHorarioDesdeStorage();
    }

    function abrirGestorHorario(event) {
        event.preventDefault();
        const urlGestor = 'sc-includes/gestionar_horario.php/';
        window.open(urlGestor, 'gestorHorarioTab', 'width=600,height=700,scrollbars=yes,resizable=yes');
        actualizarFeedbackHorario('gestionando');
    }

    function cargarHorarioDesdeStorage() {
        const savedData = localStorage.getItem(HORARIO_STORAGE_KEY);
        if (horarioSubmitErrorDiv) horarioSubmitErrorDiv.classList.add('oculto');

        if (savedData) {
            try {
                const schedule = JSON.parse(savedData);
                let diasSeleccionados = [];
                let horarios = {inicio: '23:59', fin: '00:00'};
                let primerDia = -1,
                    ultimoDia = -1;
                const diasMapping = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
                let diaDisponibleEncontrado = false;

                diasMapping.forEach((key, index) => {
                    if (schedule[key] && schedule[key].disponible) {
                        diaDisponibleEncontrado = true;
                        diasSeleccionados.push(key);
                        if (primerDia === -1) primerDia = index;
                        ultimoDia = index;
                        if (schedule[key].inicio < horarios.inicio) horarios.inicio = schedule[key].inicio;
                        if (schedule[key].fin > horarios.fin) horarios.fin = schedule[key].fin;
                    }
                });

                if (diaDisponibleEncontrado) {
                    let valorDis = '0';
                    if (diasSeleccionados.length === 7) valorDis = '1';
                    else if (diasSeleccionados.length === 5 && primerDia === 0 && ultimoDia === 4) valorDis = '2';
                    else if (diasSeleccionados.length === 6 && primerDia === 0 && ultimoDia === 5) valorDis = '3';
                    else if (diasSeleccionados.length === 2 && primerDia === 5 && ultimoDia === 6) valorDis = '4';
                    else valorDis = '1';

                    hiddenDis.value = valorDis;
                    hiddenHorarioInicio.value = horarios.inicio === '23:59' ? '00:00' : horarios.inicio;
                    hiddenHorarioFinal.value = horarios.fin === '00:00' ? '23:59' : horarios.fin;

                    actualizarFeedbackHorario('cargado', {dias: diasSeleccionados.length, inicio: hiddenHorarioInicio.value, fin: hiddenHorarioFinal.value});

                    if (contenedorHorario) {
                        validarCampo(contenedorHorario, '#error-horario', true, '');
                    }
                    if (horarioSubmitErrorDiv) horarioSubmitErrorDiv.classList.add('oculto');
                } else {
                    limpiarDatosHorarioOcultosYStorage(false);
                    actualizarFeedbackHorario('error_carga', {message: 'Los datos guardados no tienen días disponibles.'});
                }
            } catch (e) {
                console.error('Error al parsear horario desde localStorage:', e);
                limpiarDatosHorarioOcultosYStorage(false);
                actualizarFeedbackHorario('error_carga', {message: 'Error al leer los datos guardados.'});
            }
        } else {
            limpiarDatosHorarioOcultosYStorage(false);
            actualizarFeedbackHorario('no_configurado');
        }
    }

    function limpiarDatosHorarioOcultosYStorage(removeFromStorage = true) {
        hiddenDis.value = '0';
        hiddenHorarioInicio.value = '00:00';
        hiddenHorarioFinal.value = '00:00';
        if (removeFromStorage) {
            localStorage.removeItem(HORARIO_STORAGE_KEY);
            console.log('Datos del horario borrados de localStorage.');
            actualizarFeedbackHorario('no_configurado');
        }
    }

    function actualizarFeedbackHorario(estado, data = {}) {
        let mensajeHTML = '';
        switch (estado) {
            case 'gestionando':
                mensajeHTML = `<button type="button" class="botones-horario-pestana" id="btn-recargar-horario">Recargar horario guardado</button>`;
                break;
            case 'cargado':
                mensajeHTML = `<button class="botones-horario-pestana" type="button" id="btn-modificar-horario">Administrar horario</button>`;
                break;
            case 'error_carga':
                mensajeHTML = `<button type="button" class="botones-horario-pestana" id="btn-intentar-gestor">Administrar horario</button>`;
                break;
            case 'no_configurado':
            default:
                mensajeHTML = `<button type="button" class="botones-horario-pestana" id="btn-administrar-horario-feedback">Administrar horario</button>`;
                break;
        }
        horarioFeedbackDiv.innerHTML = mensajeHTML;

        horarioFeedbackDiv.querySelector('#btn-recargar-horario')?.addEventListener('click', cargarHorarioDesdeStorage);
        horarioFeedbackDiv.querySelector('#btn-modificar-horario')?.addEventListener('click', abrirGestorHorario);
        horarioFeedbackDiv.querySelector('#btn-borrar-horario')?.addEventListener('click', () => {
            if (confirm('¿Estás seguro de que quieres borrar la configuración del horario?')) {
                limpiarDatosHorarioOcultosYStorage(true);
            }
        });
        horarioFeedbackDiv.querySelector('#btn-intentar-gestor')?.addEventListener('click', abrirGestorHorario);
        horarioFeedbackDiv.querySelector('#btn-administrar-horario-feedback')?.addEventListener('click', abrirGestorHorario);
    }

    function agregarListeners() {
        btnSiguiente.forEach(btn => btn.addEventListener('click', irASiguienteEtapa));
        btnAnterior.forEach(btn => btn.addEventListener('click', irAEtapaAnterior));

        if (tituloInput && contTitulo) tituloInput.addEventListener('input', actualizarContadores);
        if (descripcionTextarea && contDesc) descripcionTextarea.addEventListener('input', actualizarContadores);
        if (fotosInput) fotosInput.addEventListener('change', manejarSeleccionFotos);

        window.addEventListener('focus', cargarHorarioDesdeStorage);

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
                // Use categoriaSelect.closest('.custom-select-wrapper') to target the visual element
                const categoriaWrapper = document.getElementById('custom-categoria-wrapper');
                if (!validarCampo(categoriaWrapper, '#error-categoria', categoriaSelect?.value, 'Debes seleccionar una categoría.')) {
                    esValido = false;
                    inputsInvalidos.push(categoriaWrapper?.querySelector('.custom-select-trigger')); // Focus trigger
                }
                // Use provinciaSelect.closest('.custom-select-wrapper') for validation styling/focus
                const provinciaWrapper = document.getElementById('custom-provincia-wrapper');
                if (!validarCampo(provinciaWrapper, '#error-provincia', provinciaSelect?.value, 'Debes seleccionar una provincia.')) {
                    esValido = false;
                    inputsInvalidos.push(provinciaWrapper?.querySelector('.custom-select-trigger')); // Focus trigger
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
                if (!validarCampo(serviciosCheckboxes[0]?.closest('.grupo-checkboxes'), '#error-servicios', serviciosSeleccionados > 0 && serviciosSeleccionados <= 12, 'Debes seleccionar de 1 a 12 servicios.')) {
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

                const idioma1Seleccionado = idioma1Select?.value !== '';
                const idioma2Seleccionado = idioma2Select?.value !== '';
                const alMenosUnIdioma = idioma1Seleccionado || idioma2Seleccionado;
                const grupoIdiomasDiv = form.querySelector('.grupo-idiomas');

                if (!validarCampo(grupoIdiomasDiv, '#error-idiomas', alMenosUnIdioma, 'Debes seleccionar al menos un idioma.')) {
                    esValido = false;
                    inputsInvalidos.push(idioma1Select || grupoIdiomasDiv);
                }

                if (idioma1Seleccionado && !validarCampo(nivelIdioma1Select, '#error-idiomas', nivelIdioma1Select?.value !== '', 'Debes seleccionar el nivel para el Idioma 1.')) {
                    esValido = false;
                    if (!inputsInvalidos.includes(nivelIdioma1Select)) {
                        inputsInvalidos.push(nivelIdioma1Select);
                    }
                }

                const horarioGuardadoEtapa = localStorage.getItem(HORARIO_STORAGE_KEY);
                const horarioRequeridoEtapa = true;
                const errorHorarioEtapaSelector = '#error-horario-etapa';

                if (horarioRequeridoEtapa && !horarioGuardadoEtapa) {
                    if (!validarCampo(horarioFeedbackDiv, errorHorarioEtapaSelector, false, 'Debes configurar y guardar tu horario.')) {
                        esValido = false;
                        const currentBtnMostrarHorario = document.getElementById('btn-mostrar-horario');
                        inputsInvalidos.push(currentBtnMostrarHorario || horarioFeedbackDiv);
                    }
                    if (horarioSubmitErrorDiv) horarioSubmitErrorDiv.classList.add('oculto');
                } else {
                    validarCampo(horarioFeedbackDiv, errorHorarioEtapaSelector, true, '');
                }

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
                break;
        }

        if (!esValido && inputsInvalidos.length > 0) {
            const firstInvalid = inputsInvalidos[0];
            if (firstInvalid && typeof firstInvalid.focus === 'function') {
                // For custom selects, focus the trigger button instead of the hidden select
                if (firstInvalid.classList.contains('custom-select-trigger')) {
                    firstInvalid.focus();
                } else if (firstInvalid.closest('.custom-select-wrapper')) {
                    firstInvalid.closest('.custom-select-wrapper').querySelector('.custom-select-trigger')?.focus();
                } else if (firstInvalid && typeof firstInvalid.focus === 'function') {
                    firstInvalid.focus();
                }
            }
        }

        return esValido;
    }

    function validarCampo(elemento, errorSelector, condition, message) {
        const errorMsgElement = form.querySelector(errorSelector);
        if (!errorMsgElement) {

            return condition;
        }

        let campoVisual = elemento;
        let campoOriginal = null; 

        if (elemento && elemento.classList.contains('custom-select-wrapper')) {
            campoVisual = elemento.querySelector('.custom-select-trigger'); 
            const originalSelectId = campoVisual?.dataset.selectId;
            if (originalSelectId) {
                campoOriginal = document.getElementById(originalSelectId);
            }
        }

        else if (elemento && !(elemento.nodeName === 'INPUT' || elemento.nodeName === 'SELECT' || elemento.nodeName === 'TEXTAREA')) {
            campoOriginal = elemento.querySelector('input, select, textarea'); 
        } else {
            campoOriginal = elemento; 
        }

        if (!condition) {
            errorMsgElement.textContent = message;
            errorMsgElement.classList.remove('oculto');
            campoVisual?.classList.add('invalido');
            return false;
        } else {
            errorMsgElement.classList.add('oculto');
            campoVisual?.classList.remove('invalido');
            return true;
        }
    }

    function limpiarErroresEtapa(etapa) {
        etapa.querySelectorAll('.error-msg').forEach(msg => msg.classList.add('oculto'));
        etapa.querySelectorAll('.invalido').forEach(el => el.classList.remove('invalido'));
        
        etapa.querySelectorAll('.custom-select-trigger.invalido').forEach(el => el.classList.remove('invalido'));
    }

    function actualizarCamposOcultosEtapaActual() {
        const etapaId = etapas[etapaActualIndex]?.id;

        switch (etapaId) {
            case 'etapa-tipo-usuario':
                actualizarSellerTypeOculto();
                break;
            case 'etapa-anuncio':
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

    function manejarSeleccionFotos(event) {
        const files = event.target.files;
        const inputElement = event.target;
        const filenameToReplace = inputElement.dataset.replacingFilename;
        const isReplacing = !!filenameToReplace;

        limpiarErroresEtapa(etapas[etapaActualIndex]);

        if (!files || files.length === 0) {
            if (isReplacing) {
                delete inputElement.dataset.replacingFilename;
            }
            return;
        }

        if (isReplacing) {
            if (files.length > 1) {
                mostrarErrorFotos('Solo puedes seleccionar una foto para reemplazar.');
                delete inputElement.dataset.replacingFilename;
                inputElement.value = null;
                return;
            }

            const file = files[0];

            if (!validarArchivoFoto(file)) {
                delete inputElement.dataset.replacingFilename;
                inputElement.value = null;
                return;
            }

            subirFotoAjax(file, inputElement);
        } else {
            const maxPhotos = typeof maxPhotosAllowed !== 'undefined' ? maxPhotosAllowed : 3;
            const currentPhotosCount = hiddenPhotoInputsContainer.querySelectorAll('input[name="photo_name[]"]').length;
            let addedCount = 0;

            if (currentPhotosCount >= maxPhotos) {
                mostrarErrorFotos(`Ya has alcanzado el límite de ${maxPhotos} fotos.`);
                inputElement.value = null;
                return;
            }

            for (let i = 0; i < files.length; i++) {
                if (currentPhotosCount + addedCount >= maxPhotos) {
                    mostrarErrorFotos(`Solo puedes añadir ${maxPhotos - currentPhotosCount} foto(s) más.`);
                    break;
                }
                const file = files[i];

                if (validarArchivoFoto(file)) {
                    subirFotoAjax(file, inputElement);
                    addedCount++;
                }
            }
        }

        setTimeout(() => {
            inputElement.value = null;
        }, 0);
    }

    function validarArchivoFoto(file) {
        const maxSize = 2 * 1024 * 1024; // 2MB
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp']; // Added GIF and WebP

        if (!allowedTypes.includes(file.type)) {
            mostrarErrorFotos(`Archivo "${file.name}" no es JPG, PNG, GIF o WebP (tipo detectado: ${file.type || 'desconocido'}).`);
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
        if (listaFotosContainer) {
            listaFotosContainer.appendChild(loadingIndicator);
        }

        const formData = new FormData();
        formData.append('userImage', file);

        const urlSubida = typeof uploadUrl !== 'undefined' ? uploadUrl : 'sc-includes/php/ajax/upload_picture.php';

        fetch(urlSubida, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`Error HTTP ${response.status}: ${response.statusText}. Respuesta: ${text}`);
                    });
                }
                return response.text();
            })
            .then(html => {
                loadingIndicator.remove();
                const photoData = parsePhotoUploadResponse(html);

                if (photoData.filename) {
                    const newFilename = photoData.filename;
                    const filenameToReplace = inputElement.dataset.replacingFilename;

                    if (filenameToReplace) {
                        console.log(`Reemplazando: ${filenameToReplace} con ${newFilename}`);

                        const oldPreview = listaFotosContainer.querySelector(`.foto-subida-item[data-filename="${filenameToReplace}"]`);
                        if (oldPreview) {
                            oldPreview.remove();
                            console.log(`Preview antiguo [${filenameToReplace}] eliminado.`);
                        } else {
                            console.warn(`No se encontró el preview antiguo para [${filenameToReplace}].`);
                        }

                        const oldHiddenInput = hiddenPhotoInputsContainer.querySelector(`input[name="photo_name[]"][value="${filenameToReplace}"]`);
                        if (oldHiddenInput) {
                            oldHiddenInput.remove();
                            console.log(`Input oculto antiguo [${filenameToReplace}] eliminado.`);
                        } else {
                            console.warn(`No se encontró el input oculto antiguo para [${filenameToReplace}].`);
                        }

                        delete inputElement.dataset.replacingFilename;
                    } else {
                        console.log(`Añadiendo nueva foto: ${newFilename}`);
                    }

                    const previewElement = crearPreviewFoto(photoData.previewHtml, newFilename);
                    listaFotosContainer.appendChild(previewElement);
                    console.log(`Nuevo preview [${newFilename}] añadido.`);

                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'photo_name[]';
                    hiddenInput.value = newFilename;
                    hiddenPhotoInputsContainer.appendChild(hiddenInput);
                    console.log(`Nuevo input oculto [${newFilename}] añadido.`);

                    validarCampo(listaFotosContainer, '#error-fotos', true, '');

                    actualizarPlaceholders();
                } else {
                    mostrarErrorFotos(`Error procesando la respuesta del servidor para "${file.name}". Respuesta: ${html}`);

                    if (inputElement.dataset.replacingFilename) {
                        delete inputElement.dataset.replacingFilename;
                    }
                }

                updateArrowButtonStates();
            })
            .catch(error => {
                loadingIndicator.remove();
                mostrarErrorFotos(`Error subiendo "${file.name}": ${error.message}`);
                console.error('Error en fetch:', error);

                if (inputElement.dataset.replacingFilename) {
                    delete inputElement.dataset.replacingFilename;
                }

                updateArrowButtonStates();
            });
    }

    function parsePhotoUploadResponse(html) {
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        const hiddenInput = tempDiv.querySelector('input[type="hidden"][name="photo_name[]"]');
        const filename = hiddenInput ? hiddenInput.value : null;
        return {filename: filename, previewHtml: html};
    }

    function crearLoadingPreview(filename) {
        const div = document.createElement('div');
        div.classList.add('foto-subida-item', 'loading');

        div.innerHTML = `
            <div class="loading-spinner"></div>
            <span>Subiendo ${filename}...</span>
        `;
        return div;
    }

    function actualizarPlaceholders() {
        if (!listaFotosContainer || !hiddenPhotoInputsContainer) {
            console.error('Error: No se encontraron los contenedores de fotos para actualizar placeholders.');
            return;
        }

        const existingPlaceholders = listaFotosContainer.querySelectorAll('.foto-placeholder');
        existingPlaceholders.forEach(ph => ph.remove());

        const currentPhotosCount = hiddenPhotoInputsContainer.querySelectorAll('input[name="photo_name[]"]').length;

        const placeholdersNeeded = Math.max(0, MAX_PHOTOS - currentPhotosCount);

        for (let i = 0; i < placeholdersNeeded; i++) {
            const placeholderDiv = document.createElement('div');
            placeholderDiv.classList.add('foto-placeholder');
            placeholderDiv.innerHTML = SVG_PLACEHOLDER;

            listaFotosContainer.appendChild(placeholderDiv);
        }
        console.log(`Placeholders actualizados: ${placeholdersNeeded} mostrados.`);
    }

    function crearPreviewFoto(htmlContent, filename) {
        const div = document.createElement('div');
        div.classList.add('foto-subida-item');
        div.dataset.filename = filename;
        div.innerHTML = htmlContent;

        const hiddenInPreview = div.querySelector('input[name="photo_name[]"]');
        hiddenInPreview?.remove();

        const actionsDiv = document.createElement('div');
        actionsDiv.classList.add('preview-actions');

        const moveLeftBtn = document.createElement('button');
        moveLeftBtn.type = 'button';
        moveLeftBtn.classList.add('btn-preview-action', 'btn-move-left', 'btn-toggle-position-select');
        moveLeftBtn.title = 'Elegir posición';
        moveLeftBtn.setAttribute('aria-label', `Elegir posición para foto ${filename}`);
        moveLeftBtn.dataset.filename = filename;
        moveLeftBtn.innerHTML = `<svg data-testid="geist-icon" height="12" stroke-linejoin="round" style="color:currentColor; pointer-events: none;" viewBox="0 0 16 16" width="12" aria-hidden="true"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.46966 13.7803L6.99999 14.3107L8.06065 13.25L7.53032 12.7197L3.56065 8.75001H14.25H15V7.25001H14.25H3.56065L7.53032 3.28034L8.06065 2.75001L6.99999 1.68935L6.46966 2.21968L1.39644 7.2929C1.00592 7.68342 1.00592 8.31659 1.39644 8.70711L6.46966 13.7803Z" fill="currentColor"></path></svg>`;
        moveLeftBtn.addEventListener('click', event => {
            console.log(`FORZADO: Click directo detectado en Botón Izquierda para ${filename}`);
            event.preventDefault();
            event.stopPropagation();
            togglePositionSelect(event);
        });
        actionsDiv.appendChild(moveLeftBtn);

        const moveRightBtn = document.createElement('button');
        moveRightBtn.type = 'button';
        moveRightBtn.classList.add('btn-preview-action', 'btn-move-right', 'btn-toggle-position-select');
        moveRightBtn.title = 'Elegir posición';
        moveRightBtn.setAttribute('aria-label', `Elegir posición para foto ${filename}`);
        moveRightBtn.dataset.filename = filename;
        moveRightBtn.innerHTML = `<svg data-testid="geist-icon" height="12" stroke-linejoin="round" style="color:currentColor; pointer-events: none;" viewBox="0 0 16 16" width="12" aria-hidden="true"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.53033 2.21968L9 1.68935L7.93934 2.75001L8.46967 3.28034L12.4393 7.25001H1.75H1V8.75001H1.75H12.4393L8.46967 12.7197L7.93934 13.25L9 14.3107L9.53033 13.7803L14.6036 8.70711C14.9941 8.31659 14.9941 7.68342 14.6036 7.2929L9.53033 2.21968Z" fill="currentColor"></path></svg>`;
        moveRightBtn.addEventListener('click', event => {
            console.log(`FORZADO: Click directo detectado en Botón Derecha para ${filename}`);
            event.preventDefault();
            event.stopPropagation();
            togglePositionSelect(event);
        });
        actionsDiv.appendChild(moveRightBtn);

        const rotateBtn = document.createElement('button');
        rotateBtn.type = 'button';
        rotateBtn.classList.add('btn-preview-action', 'btn-rotate-foto');
        rotateBtn.title = 'Rotar foto 90°';
        rotateBtn.dataset.filename = filename;
        rotateBtn.innerHTML = `<svg data-testid="geist-icon" height="12" stroke-linejoin="round" style="color:currentColor; pointer-events: none;" viewBox="0 0 16 16" width="12"><path fill-rule="evenodd" clip-rule="evenodd" d="M8.00002 1.25C5.33749 1.25 3.02334 2.73677 1.84047 4.92183L1.48342 5.58138L2.80253 6.29548L3.15958 5.63592C4.09084 3.91566 5.90986 2.75 8.00002 2.75C10.4897 2.75 12.5941 4.40488 13.2713 6.67462H11.8243H11.0743V8.17462H11.8243H15.2489C15.6631 8.17462 15.9989 7.83883 15.9989 7.42462V4V3.25H14.4989V4V5.64468C13.4653 3.06882 10.9456 1.25 8.00002 1.25ZM1.50122 10.8555V12.5V13.25H0.0012207V12.5V9.07538C0.0012207 8.66117 0.337007 8.32538 0.751221 8.32538H4.17584H4.92584V9.82538H4.17584H2.72876C3.40596 12.0951 5.51032 13.75 8.00002 13.75C10.0799 13.75 11.8912 12.5958 12.8266 10.8895L13.1871 10.2318L14.5025 10.9529L14.142 11.6105C12.9539 13.7779 10.6494 15.25 8.00002 15.25C5.05453 15.25 2.53485 13.4313 1.50122 10.8555Z" fill="currentColor"></path></svg>`;
        actionsDiv.appendChild(rotateBtn);

        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.classList.add('btn-preview-action', 'btn-delete-foto');
        deleteBtn.title = 'Eliminar foto';
        deleteBtn.dataset.filename = filename;
        deleteBtn.innerHTML = `<svg data-testid="geist-icon" height="12" stroke-linejoin="round" viewBox="0 0 16 16" width="12" style="color: currentcolor; pointer-events: none;" aria-hidden="true"><path fill-rule="evenodd" clip-rule="evenodd" d="M12.4697 13.5303L13 14.0607L14.0607 13L13.5303 12.4697L9.06065 7.99999L13.5303 3.53032L14.0607 2.99999L13 1.93933L12.4697 2.46966L7.99999 6.93933L3.53032 2.46966L2.99999 1.93933L1.93933 2.99999L2.46966 3.53032L6.93933 7.99999L2.46966 12.4697L1.93933 13L2.99999 14.0607L3.53032 13.5303L7.99999 9.06065L12.4697 13.5303Z" fill="currentColor"></path></svg>`;
        actionsDiv.appendChild(deleteBtn);

        const changeBtn = document.createElement('button');
        changeBtn.type = 'button';
        changeBtn.classList.add('btn-preview-action', 'btn-change-foto');
        changeBtn.style.display = 'none';
        changeBtn.title = 'Cambiar esta foto';
        changeBtn.dataset.filename = filename;
        changeBtn.innerHTML = 'Cambiar';
        actionsDiv.appendChild(changeBtn);

        const optionsContainer = div.querySelector('.photos_options');
        if (optionsContainer) {
            optionsContainer.innerHTML = '';
            optionsContainer.appendChild(actionsDiv);
        } else {
            const img = div.querySelector('img');
            if (img && img.parentNode) {
                img.parentNode.insertBefore(actionsDiv, img.nextSibling);
            } else {
                div.appendChild(actionsDiv);
            }
        }

        const imgElement = div.querySelector('img');
        if (imgElement) {
            imgElement.dataset.filename = filename;
            imgElement.style.cursor = 'pointer';
            imgElement.title = 'Haz clic para cambiar esta imagen';
        }

        console.log(`DEBUG: Botón Izquierda (FORZADO) añadido para ${filename}. ¿Existe? ${!!div.querySelector('.btn-toggle-position-select.btn-move-left')}`);
        console.log(`DEBUG: Botón Derecha (FORZADO) añadido para ${filename}. ¿Existe? ${!!div.querySelector('.btn-toggle-position-select.btn-move-right')}`);

        return div;
    }

    function triggerChangeFotoFromImage(event) {
        const imgElement = event.currentTarget;
        const filenameToReplace = imgElement.dataset.filename;

        if (!filenameToReplace || !fotosInput) {
            console.error('No se pudo obtener el filename de la imagen o el input de fotos no existe.');
            return;
        }

        console.log(`Iniciando reemplazo para: ${filenameToReplace} (click en imagen)`);
        fotosInput.dataset.replacingFilename = filenameToReplace;
        fotosInput.value = null;
        fotosInput.click();
    }

    function togglePositionSelect(event) {
        console.log('--- togglePositionSelect (FORZADO) INICIO ---');
        const button = event.currentTarget;
        const filename = button.dataset.filename;
        console.log('Botón presionado (currentTarget):', button);
        console.log('Filename obtenido del dataset:', filename);
        console.log('Referencia a selectPosicion:', selectPosicion);

        if (!selectPosicion || typeof filename === 'undefined' || filename === null || filename === '') {
            console.error('Error en togglePositionSelect: Falta selectPosicion o filename es inválido/no encontrado en el dataset del botón.');
            console.error(`Detalles: selectPosicion=${selectPosicion}, filename=${filename}`);
            console.log('--- togglePositionSelect FIN (Error inicial) ---');
            return;
        }

        const isVisible = selectPosicion.classList.contains('visible');
        const isForThisButton = selectPosicion.dataset.currentFilename === filename;
        console.log(`Estado actual: visible=${isVisible}, filenameGuardado=${selectPosicion.dataset.currentFilename}, filenameActual=${filename}, ¿Visible para este botón?=${isVisible && isForThisButton}`);

        if (isVisible && isForThisButton) {
            console.log('Ocultando select (ya estaba visible para este botón).');
            selectPosicion.classList.remove('visible');
            selectPosicion.classList.add('oculto');
            delete selectPosicion.dataset.currentFilename;
            document.removeEventListener('click', hideSelectOnClickOutside, true);
            console.log('--- togglePositionSelect FIN (Ocultando) ---');
            return;
        }

        console.log(`Guardando filename '${filename}' en dataset (selectPosicion.dataset.currentFilename)`);
        selectPosicion.dataset.currentFilename = filename;

        try {
            const btnRect = button.getBoundingClientRect();
            const topPos = window.scrollY + btnRect.bottom + 5;
            const leftPos = window.scrollX + btnRect.left;
            console.log('Posición calculada - Top:', topPos, 'Left:', leftPos);
            selectPosicion.style.position = 'absolute';
            selectPosicion.style.top = `${topPos}px`;
            selectPosicion.style.left = `${leftPos}px`;
            selectPosicion.style.zIndex = '100';
        } catch (e) {
            console.error('Error calculando posición del select:', e);
            console.log('--- togglePositionSelect FIN (Error calculando posición) ---');
            return;
        }

        const allPreviews = Array.from(listaFotosContainer.querySelectorAll('.foto-subida-item:not(.loading)'));
        const currentIndex = allPreviews.findIndex(item => item.dataset.filename === filename);
        const valueToSelect = currentIndex !== -1 ? (currentIndex + 1).toString() : '1';
        console.log('Índice actual en DOM:', currentIndex, 'Valor a seleccionar en select:', valueToSelect);
        selectPosicion.value = valueToSelect;

        console.log('Intentando mostrar: Quitando clase "oculto", añadiendo clase "visible"');
        selectPosicion.classList.remove('oculto');
        selectPosicion.classList.add('visible');

        document.removeEventListener('click', hideSelectOnClickOutside, true);
        console.log('Añadiendo listener hideSelectOnClickOutside (con once: true, capture: true)');
        setTimeout(() => {
            document.addEventListener('click', hideSelectOnClickOutside, {once: true, capture: true});
        }, 0);

        console.log('--- togglePositionSelect FIN (Mostrando/Reposicionando) ---');
    }

    function hideSelectOnClickOutside(event) {
        const clickedElement = event.target;
        console.log('hideSelectOnClickOutside - Click detectado en:', clickedElement);

        if (selectPosicion && selectPosicion.classList.contains('visible') && !selectPosicion.contains(clickedElement) && !clickedElement.closest('.btn-toggle-position-select')) {
            console.log('Click fuera detectado por hideSelectOnClickOutside. Ocultando select.');
            selectPosicion.classList.remove('visible');
            selectPosicion.classList.add('oculto');
            delete selectPosicion.dataset.currentFilename;
            console.log('Listener hideSelectOnClickOutside (once:true) consumido.');
        } else {
            console.log('hideSelectOnClickOutside: Click dentro del select o en un botón toggle (o select no visible). No se oculta. Listener (once:true) consumido.');
        }
    }

    function handlePositionChange(event) {
        console.log('--- handlePositionChange INICIO ---');
        const select = event.currentTarget;
        console.log('Select que disparó el evento:', select);
        const selectedValue = select.value;
        console.log('Valor seleccionado:', selectedValue);
        const filename = select.dataset.currentFilename;
        console.log('Filename recuperado del dataset (select.dataset.currentFilename):', filename);

        let newPositionIndex = NaN;
        if (selectedValue) {
            const parsedValue = parseInt(selectedValue, 10);
            if (!isNaN(parsedValue)) {
                newPositionIndex = parsedValue - 1;
            }
        }
        console.log('Filename parseado:', filename);
        console.log('Índice nuevo parseado:', newPositionIndex);

        if (typeof filename === 'undefined' || filename === null || filename === '' || isNaN(newPositionIndex) || newPositionIndex < 0) {
            console.error('Error en handlePositionChange: Datos inválidos para mover la imagen.');
            console.error(`Detalles: filename='${filename}', selectedValue='${selectedValue}', newPositionIndex=${newPositionIndex}`);
            select.classList.remove('visible');
            select.classList.add('oculto');
            delete select.dataset.currentFilename;
            document.removeEventListener('click', hideSelectOnClickOutside, true);
            console.log('--- handlePositionChange FIN (Error de validación de datos) ---');
            return;
        }

        console.log(`Intentando mover filename '${filename}' al índice ${newPositionIndex}`);

        const currentPreviewItem = listaFotosContainer.querySelector(`.foto-subida-item[data-filename="${filename}"]`);
        const currentHiddenInput = hiddenPhotoInputsContainer.querySelector(`input[name="photo_name[]"][value="${filename}"]`);

        if (!currentPreviewItem) {
            console.error(`Error crítico: No se encontró el PREVIEW item para filename '${filename}'. No se puede mover.`);
            select.classList.remove('visible');
            select.classList.add('oculto');
            delete select.dataset.currentFilename;
            document.removeEventListener('click', hideSelectOnClickOutside, true);
            console.log('--- handlePositionChange FIN (Error: Preview no encontrado) ---');
            return;
        }
        if (!currentHiddenInput) {
            console.error(`Error crítico: No se encontró el INPUT OCULTO para filename '${filename}'. La consistencia de datos se perderá si continuamos.`);
            select.classList.remove('visible');
            select.classList.add('oculto');
            delete select.dataset.currentFilename;
            document.removeEventListener('click', hideSelectOnClickOutside, true);
            console.log('--- handlePositionChange FIN (Error: Input oculto no encontrado) ---');
            return;
        }
        console.log('Elementos a mover encontrados:', {preview: currentPreviewItem, input: currentHiddenInput});

        const allPreviewsNodes = listaFotosContainer.querySelectorAll('.foto-subida-item:not(.loading)');
        const allHiddenInputsNodes = hiddenPhotoInputsContainer.querySelectorAll('input[name="photo_name[]"]');
        console.log(`Nodos actuales: ${allPreviewsNodes.length} previews, ${allHiddenInputsNodes.length} inputs.`);

        const targetPreviewSibling = allPreviewsNodes[newPositionIndex] || null;
        const targetInputSibling = allHiddenInputsNodes[newPositionIndex] || null;
        console.log('Nodos de referencia (se insertará ANTES de estos, o al final si son null):', {previewSibling: targetPreviewSibling, inputSibling: targetInputSibling});

        try {
            console.log(`Moviendo preview ${filename} antes de ${targetPreviewSibling ? targetPreviewSibling.dataset.filename : 'final'}`);
            listaFotosContainer.insertBefore(currentPreviewItem, targetPreviewSibling);

            console.log(`Moviendo input ${filename} antes de ${targetInputSibling ? targetInputSibling.value : 'final'}`);
            hiddenPhotoInputsContainer.insertBefore(currentHiddenInput, targetInputSibling);

            console.log('Movimiento DOM completado.');
        } catch (e) {
            console.error('Error durante el movimiento DOM:', e);
            select.classList.remove('visible');
            select.classList.add('oculto');
            delete select.dataset.currentFilename;
            document.removeEventListener('click', hideSelectOnClickOutside, true);
            alert('Ocurrió un error al intentar mover la foto. Por favor, recarga la página.');
            console.log('--- handlePositionChange FIN (Error durante movimiento DOM) ---');
            return;
        }

        console.log('Movimiento intentado. Ocultando select y limpiando dataset.');
        select.classList.remove('visible');
        select.classList.add('oculto');
        delete select.dataset.currentFilename;
        document.removeEventListener('click', hideSelectOnClickOutside, true);

        if (typeof updateArrowButtonStates === 'function') {
            console.log('Llamando a updateArrowButtonStates...');
            updateArrowButtonStates();
        } else {
            console.warn('Función updateArrowButtonStates no encontrada. Estado visual de flechas no actualizado.');
        }

        validarCampo(listaFotosContainer, '#error-fotos', true, '');
        console.log('--- handlePositionChange FIN (Éxito o intento completado) ---');
    }

    function agregarListenersNuevos() {
        console.log('Ejecutando agregarListenersNuevos (CON LISTENERS DELEGADOS PARA ROTAR/ELIMINAR/IMG)...');

        if (selectPosicion) {
            console.log('Añadiendo listener "change" a selectPosicion.');
            selectPosicion.removeEventListener('change', handlePositionChange);
            selectPosicion.addEventListener('change', handlePositionChange);
        } else {
            console.error('Error crítico: selectPosicion (el dropdown de posición) no está definido al añadir listener change.');
        }

        if (listaFotosContainer) {
            console.log('Añadiendo/Asegurando listener delegado a listaFotosContainer para clicks en IMG, ROTATE, DELETE.');

            // Add listener only once logic (example using a flag on the element)
            if (!listaFotosContainer.dataset.delegatedListenerAdded) {
                listaFotosContainer.addEventListener('click', function (event) {
                    const target = event.target;
                    console.log('Click delegado detectado en listaFotosContainer. Target:', target);

                    const rotateButton = target.closest('.btn-rotate-foto');
                    if (rotateButton) {
                        console.log('Delegated click: Botón ROTAR detectado.');
                        event.preventDefault();
                        event.stopPropagation();
                        handleRotateFotoClick({currentTarget: rotateButton});
                        return;
                    }

                    const deleteButton = target.closest('.btn-delete-foto');
                    if (deleteButton) {
                        console.log('Delegated click: Botón ELIMINAR detectado.');
                        event.preventDefault();
                        event.stopPropagation();
                        eliminarFoto({currentTarget: deleteButton});
                        return;
                    }

                    if (target.tagName === 'IMG') {
                        const previewItem = target.closest('.foto-subida-item');
                        if (previewItem && !previewItem.classList.contains('loading') && !previewItem.classList.contains('foto-placeholder') && target.dataset.filename) {
                            console.log(`Delegated click: Imagen con filename ${target.dataset.filename} detectada para cambio.`);
                            event.preventDefault();
                            event.stopPropagation();
                            triggerChangeFotoFromImage({currentTarget: target});
                            return;
                        } else if (previewItem && previewItem.classList.contains('foto-placeholder')) {
                            console.log('Delegated click: Placeholder SVG clickeado.');
                            // fotosInput?.click(); // Optional: trigger file input on placeholder click
                            return;
                        }
                    }

                    const changeButton = target.closest('.btn-change-foto');
                    if (changeButton && changeButton.style.display !== 'none') {
                        console.log('Delegated click: Botón CAMBIAR (visible) detectado.');
                        event.preventDefault();
                        event.stopPropagation();
                        handleChangeFotoClick({currentTarget: changeButton});
                        return;
                    }

                    console.log('Delegated click: El clic no coincidió con IMG, Rotar, Eliminar o Cambiar interactivos.');
                });
                listaFotosContainer.dataset.delegatedListenerAdded = 'true'; // Mark as added
                console.log('Listener delegado añadido a listaFotosContainer.');
            } else {
                console.log('Listener delegado ya existe en listaFotosContainer.');
            }
        } else {
            console.error('Error crítico: listaFotosContainer (el contenedor de previews) no encontrado al intentar añadir listener delegado.');
        }
    }

    function handleRotateFotoClick(event) {
        const button = event.currentTarget;
        const filename = button.dataset.filename;
        if (!filename) {
            console.error('Error Rotar (Visual): No se encontró filename en el botón.');
            return;
        }

        const previewItem = button.closest('.foto-subida-item');
        if (!previewItem) {
            console.error(`Error Rotar (Visual): No se encontró el contenedor preview para ${filename}`);
            return;
        }
        const imgElement = previewItem.querySelector('img');
        if (!imgElement) {
            console.error(`Error Rotar (Visual): No se encontró el elemento <img> para ${filename}`);
            return;
        }

        let currentRotation = parseInt(imgElement.dataset.rotation || '0', 10);
        const newRotation = (currentRotation + 90) % 360;

        imgElement.style.transform = `rotate(${newRotation}deg)`;
        imgElement.style.transition = 'transform 0.3s ease';
        imgElement.dataset.rotation = newRotation;

        if (newRotation === 90 || newRotation === 270) {
            imgElement.style.maxWidth = '80px';
            imgElement.style.maxHeight = '120px';
            console.log(`Aplicado max-width: 80px a ${filename} (Rotación: ${newRotation})`);
        } else {
            imgElement.style.maxWidth = '';
            imgElement.style.maxHeight = '';
            console.log(`Quitado max-width de ${filename} (Rotación: ${newRotation})`);
        }

        console.log(`Rotación visual aplicada a ${filename}: ${newRotation} grados.`);
    }

    function handleMoveFotoClick(event) {
        const button = event.currentTarget;
        const filename = button.dataset.filename;
        const direction = button.classList.contains('btn-move-left') ? 'left' : 'right';

        const currentPreviewItem = button.closest('.foto-subida-item');
        if (!currentPreviewItem) return;

        const currentHiddenInput = hiddenPhotoInputsContainer.querySelector(`input[name="photo_name[]"][value="${filename}"]`);
        if (!currentHiddenInput) {
            console.error(`No se encontró el input oculto para ${filename}`);
            return;
        }

        let siblingPreviewItem = null;
        let siblingHiddenInput = null;

        if (direction === 'left') {
            siblingPreviewItem = currentPreviewItem.previousElementSibling;
        } else {
            siblingPreviewItem = currentPreviewItem.nextElementSibling;
        }

        if (siblingPreviewItem && siblingPreviewItem.classList.contains('foto-subida-item') && !siblingPreviewItem.classList.contains('foto-placeholder')) {
            const siblingFilename = siblingPreviewItem.dataset.filename;
            siblingHiddenInput = hiddenPhotoInputsContainer.querySelector(`input[name="photo_name[]"][value="${siblingFilename}"]`);

            if (siblingHiddenInput) {
                if (direction === 'left') {
                    listaFotosContainer.insertBefore(currentPreviewItem, siblingPreviewItem);
                } else {
                    listaFotosContainer.insertBefore(currentPreviewItem, siblingPreviewItem.nextElementSibling);
                }

                if (direction === 'left') {
                    hiddenPhotoInputsContainer.insertBefore(currentHiddenInput, siblingHiddenInput);
                } else {
                    hiddenPhotoInputsContainer.insertBefore(currentHiddenInput, siblingHiddenInput.nextElementSibling);
                }

                updateArrowButtonStates();
            } else {
                console.error(`No se encontró el input oculto para el hermano ${siblingFilename}`);
            }
        }
    }

    function updateArrowButtonStates() {
        if (!listaFotosContainer) return;
        const previewItems = listaFotosContainer.querySelectorAll('.foto-subida-item:not(.loading)');
        const itemCount = previewItems.length;

        previewItems.forEach((item, index) => {
            const moveLeftBtn = item.querySelector('.btn-move-left');
            const moveRightBtn = item.querySelector('.btn-move-right');

            if (moveLeftBtn) {
                moveLeftBtn.disabled = index === 0;
            }
            if (moveRightBtn) {
                moveRightBtn.disabled = index === itemCount - 1;
            }
        });
    }

    function handleChangeFotoClick(event) {
        const button = event.currentTarget;
        const filenameToReplace = button.dataset.filename;

        if (!filenameToReplace || !fotosInput) {
            console.error('No se pudo obtener el filename o el input de fotos no existe.');
            return;
        }

        console.log(`Iniciando reemplazo para: ${filenameToReplace}`);
        fotosInput.dataset.replacingFilename = filenameToReplace;
        fotosInput.value = null;
        fotosInput.click();
    }

    function eliminarFoto(event) {
        const button = event.currentTarget;
        const filename = button.dataset.filename;
        if (!filename) {
            console.error('No se encontró el filename en el botón de eliminar.');
            return;
        }

        console.log(`Intentando eliminar: ${filename}`);

        const previewItem = button.closest('.foto-subida-item');
        if (previewItem) {
            previewItem.remove();
            console.log(`Preview [${filename}] eliminado.`);
        } else {
            console.warn(`No se encontró el elemento preview para [${filename}] (selector: .foto-subida-item).`);
        }

        const hiddenInput = hiddenPhotoInputsContainer.querySelector(`input[name="photo_name[]"][value="${filename}"]`);
        if (hiddenInput) {
            hiddenInput.remove();
            console.log(`Input oculto [${filename}] eliminado.`);
        } else {
            console.warn(`No se encontró el input oculto para [${filename}].`);
        }

        actualizarPlaceholders(); // Update placeholders *after* removing elements

        const fotosRestantes = hiddenPhotoInputsContainer.querySelectorAll('input[name="photo_name[]"]').length;
        validarCampo(listaFotosContainer, '#error-fotos', fotosRestantes > 0, 'Debes subir al menos una foto.');

        if (fotosInput && fotosInput.dataset.replacingFilename === filename) {
            delete fotosInput.dataset.replacingFilename;
            console.log(`Estado de reemplazo limpiado para [${filename}] porque fue eliminada.`);
        }

        updateArrowButtonStates();
    }

    function mostrarErrorFotos(mensaje) {
        const errorDiv = form.querySelector('#error-fotos');
        if (errorDiv) {
            errorDiv.textContent = mensaje;
            errorDiv.classList.remove('oculto');

            listaFotosContainer?.classList.add('invalido');

            fotosInput?.classList.add('invalido');
        } else {
            console.error('Error fotos (div #error-fotos no encontrado):', mensaje);
            alert(mensaje);
        }
    }

    function manejarEnvioFinal(event) {
        event.preventDefault();

        cargarHorarioDesdeStorage();

        if (!validarFormularioCompleto()) {
            // Alert is shown within validarFormularioCompleto
            // irAPrimeraEtapaConError(); // Already handled by validarFormularioCompleto
            return;
        }

        actualizarSellerTypeOculto();
        actualizarIdiomasOculto();

        console.log('Intentando añadir campos ocultos del horario detallado...');
        const horarioGuardadoString = localStorage.getItem(HORARIO_STORAGE_KEY);
        if (horarioGuardadoString) {
            try {
                const scheduleData = JSON.parse(horarioGuardadoString);
                const dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

                form.querySelectorAll('input[name^="horario_dia["]').forEach(input => input.remove());

                dias.forEach(diaKey => {
                    const diaInfo = scheduleData[diaKey];

                    if (diaInfo) {
                        const activoValue = diaInfo.disponible ? '1' : '0';
                        const inicioValue = diaInfo.inicio || '00:00';
                        const finValue = diaInfo.fin || '23:30';

                        const inputActivo = document.createElement('input');
                        inputActivo.type = 'hidden';
                        inputActivo.name = `horario_dia[${diaKey}][activo]`;
                        inputActivo.value = activoValue;
                        form.appendChild(inputActivo);

                        const inputInicio = document.createElement('input');
                        inputInicio.type = 'hidden';
                        inputInicio.name = `horario_dia[${diaKey}][inicio]`;
                        inputInicio.value = inicioValue;
                        form.appendChild(inputInicio);

                        const inputFin = document.createElement('input');
                        inputFin.type = 'hidden';
                        inputFin.name = `horario_dia[${diaKey}][fin]`;
                        inputFin.value = finValue;
                        form.appendChild(inputFin);
                    } else {
                        const inputActivo = document.createElement('input');
                        inputActivo.type = 'hidden';
                        inputActivo.name = `horario_dia[${diaKey}][activo]`;
                        inputActivo.value = '0';
                        form.appendChild(inputActivo);
                    }
                });
                console.log('Campos ocultos del horario detallado añadidos al formulario.');
            } catch (e) {
                console.error('Error al procesar horario desde localStorage para añadir campos ocultos:', e);
            }
        } else {
            console.log('No se encontró horario detallado en localStorage para añadir campos ocultos.');
            form.querySelectorAll('input[name^="horario_dia["]').forEach(input => input.remove());
        }

        // --- Add Rotation Data ---
        const rotationData = {};
        hiddenPhotoInputsContainer.querySelectorAll('input[name="photo_name[]"]').forEach(hiddenInput => {
            const filename = hiddenInput.value;
            const previewItem = listaFotosContainer.querySelector(`.foto-subida-item[data-filename="${filename}"]`);
            const imgElement = previewItem?.querySelector('img');
            const rotation = imgElement?.dataset.rotation || '0'; // Default to 0 if no rotation data
            if (rotation !== '0') {
                // Only send if rotation is not default
                rotationData[filename] = rotation;
            }
        });

        // Remove previous rotation input if exists
        const oldRotationInput = form.querySelector('input[name="photo_rotations"]');
        oldRotationInput?.remove();

        // Add hidden input for rotations if there's data
        if (Object.keys(rotationData).length > 0) {
            const rotationInput = document.createElement('input');
            rotationInput.type = 'hidden';
            rotationInput.name = 'photo_rotations';
            rotationInput.value = JSON.stringify(rotationData);
            form.appendChild(rotationInput);
            console.log('Rotation data added to form:', rotationInput.value);
        }
        // --- End Add Rotation Data ---

        console.log('Submitting form...');
        form.submit();
    }

    function validarFormularioCompleto() {
        let todoValido = true;
        let primeraEtapaInvalida = -1;
        let primeraEtapaInvalidaElement = null; // Store the element causing the first error

        for (let i = 0; i < etapas.length; i++) {
            const originalIndex = etapaActualIndex;
            etapaActualIndex = i;
            const etapaValida = validarEtapaActual();
            etapaActualIndex = originalIndex;

            if (!etapaValida) {
                todoValido = false;
                if (primeraEtapaInvalida === -1) {
                    primeraEtapaInvalida = i;
                    // Find the first element marked as invalid or the first visible error message in this stage
                    primeraEtapaInvalidaElement = etapas[i].querySelector('.invalido, .error-msg:not(.oculto)');
                }
                // Continue validating all stages to show all errors
            }
        }

        const horarioGuardado = localStorage.getItem(HORARIO_STORAGE_KEY);
        const horarioRequerido = true;

        if (horarioRequerido && !horarioGuardado) {
            if (horarioSubmitErrorDiv) {
                horarioSubmitErrorDiv.textContent = 'Es obligatorio configurar y guardar el horario antes de finalizar.';
                horarioSubmitErrorDiv.classList.remove('oculto');
            }
            todoValido = false;
            if (primeraEtapaInvalida === -1) {
                const etapaHorarioIndex = etapas.findIndex(etapa => etapa.querySelector('#btn-mostrar-horario'));
                if (etapaHorarioIndex !== -1) {
                    primeraEtapaInvalida = etapaHorarioIndex;
                    primeraEtapaInvalidaElement = horarioSubmitErrorDiv; // Point to the horario error div
                }
            }
        } else {
            if (horarioSubmitErrorDiv) horarioSubmitErrorDiv.classList.add('oculto');
        }

        if (!todoValido) {
            alert('Por favor, revisa el formulario. Hay errores o campos incompletos.');
            if (primeraEtapaInvalida !== -1) {
                cambiarEtapa(primeraEtapaInvalida);

                // Scroll to the first error element found
                setTimeout(() => {
                    // Use timeout to ensure stage change is rendered
                    const elementToScroll = primeraEtapaInvalidaElement || form.querySelector('.error-msg:not(.oculto), .invalido');
                    elementToScroll?.scrollIntoView({behavior: 'smooth', block: 'center'});
                    // Attempt to focus the related input if possible
                    const focusableElement = elementToScroll?.closest('.frm-grupo')?.querySelector('input, select, textarea, .custom-select-trigger') || elementToScroll;
                    if (focusableElement && typeof focusableElement.focus === 'function') {
                        focusableElement.focus({preventScroll: true}); // preventScroll avoids conflict with scrollIntoView
                    }
                }, 100); // Small delay
            }
            return false;
        }

        return true;
    }

    function irAPrimeraEtapaConError() {
        for (let i = 0; i < etapas.length; i++) {
            const etapa = etapas[i];
            // No limpiar errores aquí, ya que validarFormularioCompleto lo hace
            const originalIndex = etapaActualIndex;
            etapaActualIndex = i;
            const esValida = validarEtapaActual(); // Re-validate just to find the first invalid one
            etapaActualIndex = originalIndex;

            if (!esValida) {
                cambiarEtapa(i);
                // Scrolling and focus are handled in validarFormularioCompleto now
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
        const isCategoriaSelect = wrapperId === 'custom-categoria-wrapper'; // Flag for category select

        if (!trigger || !dropdown || !searchInput || !optionsList || !valueDisplay || !closeButton || !originalSelect) {
            console.error(`Missing elements within custom select wrapper #${wrapperId}`);
            return;
        }

        let allOptionsData = [];

        function populateOptions() {
            optionsList.innerHTML = '';
            allOptionsData = [];
            const originalOptions = originalSelect.querySelectorAll('option');
            let selectedText = '';
            const currentOriginalValue = originalSelect.value; // Get current value before repopulating

            originalOptions.forEach(option => {
                if (option.value === '') return;

                const li = document.createElement('li');
                li.textContent = option.textContent;
                li.dataset.value = option.value;
                li.setAttribute('role', 'option');
                li.setAttribute('tabindex', '-1');

                // Restore selected state based on original select's current value
                if (option.value === currentOriginalValue) {
                    li.classList.add('selected');
                    li.setAttribute('aria-selected', 'true');
                    selectedText = option.textContent;
                    console.log(`Opción seleccionada (repopulate): ${selectedText}`);
                } else {
                    li.setAttribute('aria-selected', 'false');
                }

                optionsList.appendChild(li);
                allOptionsData.push({value: option.value, text: option.textContent.toLowerCase(), element: li});

                li.addEventListener('click', handleOptionSelect);
                li.addEventListener('keydown', e => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault(); // Prevent space from scrolling
                        handleOptionSelect(e);
                    }
                });
            });

            if (selectedText) {
                valueDisplay.textContent = selectedText;
            } else {
                const placeholderOption = originalSelect.querySelector('option[value=""]');
                valueDisplay.textContent = placeholderOption ? placeholderOption.textContent : 'Seleccionar...';
            }

            console.log(`Options populated for ${wrapperId}`, allOptionsData);
        }

        function toggleDropdown(event, forceClose = false) {
            if (event && dropdown.contains(event.target) && event.target !== closeButton && event.target !== searchInput) {
                // Allow clicks on search and close button, but not elsewhere inside dropdown
                return;
            }

            const isOpen = wrapper.classList.contains('open');
            if (forceClose || isOpen) {
                wrapper.classList.remove('open');
                trigger.setAttribute('aria-expanded', 'false');
                dropdown.hidden = true;
                document.removeEventListener('click', handleClickOutside, true);
            } else {
                // Close other open custom selects first
                document.querySelectorAll('.custom-select-wrapper.open').forEach(openWrapper => {
                    if (openWrapper !== wrapper) {
                        const openTrigger = openWrapper.querySelector('.custom-select-trigger');
                        const openDropdown = openWrapper.querySelector('.custom-select-dropdown');
                        openWrapper.classList.remove('open');
                        openTrigger?.setAttribute('aria-expanded', 'false');
                        if (openDropdown) openDropdown.hidden = true;
                    }
                });

                wrapper.classList.add('open');
                trigger.setAttribute('aria-expanded', 'true');
                dropdown.hidden = false;
                setTimeout(() => {
                    document.addEventListener('click', handleClickOutside, true);
                    searchInput.focus();
                    scrollToSelected();
                }, 0);
            }
        }

        function handleClickOutside(event) {
            if (!wrapper.contains(event.target)) {
                toggleDropdown(null, true);
            }
        }

        function handleOptionSelect(event) {
            const selectedLi = event.currentTarget;
            const newValue = selectedLi.dataset.value;
            const newText = selectedLi.textContent;

            originalSelect.value = newValue;

            console.log(`Valor seleccionado para ${originalSelectId}: ${newValue}`);
            console.log(`Texto seleccionado: ${newText}`);

            // --- Service Filtering Logic (ONLY for Categoria Select) ---
            if (isCategoriaSelect) {
                console.log(`Filtrando servicios para categoría ID: ${newValue}`);
                const checkboxes = document.querySelectorAll('.sc_services'); // Re-query inside handler
                let visibleCount = 0;
                checkboxes.forEach(cb => {
                    const cbCategory = cb.dataset.value; // category ID is in data-value
                    const isVisible = cbCategory === newValue;
                    cb.style.display = isVisible ? 'block' : 'none';
                    // Uncheck checkboxes that become hidden
                    if (!isVisible && cb.querySelector('input[type="checkbox"]')?.checked) {
                        cb.querySelector('input[type="checkbox"]').checked = false;
                        console.log(`Servicio ${cb.textContent.trim()} desmarcado por cambio de categoría.`);
                    }
                    if (isVisible) visibleCount++;
                });
                console.log(`${visibleCount} servicios visibles para categoría ${newValue}`);
                // Re-validate services field after filtering
                const serviciosContainer = form.querySelector('.grupo-checkboxes');
                const serviciosSeleccionados = form.querySelectorAll('input[name="servicios[]"]:checked').length;
                validarCampo(serviciosContainer, '#error-servicios', serviciosSeleccionados > 0 && serviciosSeleccionados <= 12, 'Debes seleccionar de 1 a 12 servicios.');
            }
            // --- End Service Filtering Logic ---

            originalSelect.dispatchEvent(new Event('change', {bubbles: true}));

            valueDisplay.textContent = newText;

            allOptionsData.forEach(optData => {
                const isSelected = optData.value === newValue;
                optData.element.classList.toggle('selected', isSelected);
                optData.element.setAttribute('aria-selected', isSelected.toString());
            });

            toggleDropdown(null, true);
            trigger.focus();

            // Re-validate the original select after change
            validarCampo(wrapper, `#error-${originalSelectId}`, originalSelect.value !== '', `Debes seleccionar un valor.`);
        }

        function filterOptions() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            let firstVisibleOption = null;
            allOptionsData.forEach(optData => {
                const isMatch = optData.text.includes(searchTerm);
                optData.element.classList.toggle('filtered-out', !isMatch);
                if (isMatch && !firstVisibleOption) {
                    firstVisibleOption = optData.element;
                }
            });
            // Optional: Scroll to the first match after filtering
            // if (firstVisibleOption) {
            //     optionsList.scrollTop = firstVisibleOption.offsetTop - optionsList.offsetTop;
            // }
        }

        function scrollToSelected() {
            const selectedOption = optionsList.querySelector('li.selected');
            if (selectedOption) {
                const scrollOptions = {
                    behavior: 'auto',
                    block: 'nearest'
                };
                setTimeout(() => {
                    if (!dropdown.hidden) {
                        // Check if dropdown still open
                        selectedOption.scrollIntoView(scrollOptions);
                    }
                }, 50);
            }
        }

        function handleKeyDown(event) {
            const isOpen = wrapper.classList.contains('open');

            if (event.key === 'Escape') {
                if (isOpen) {
                    toggleDropdown(null, true);
                    trigger.focus();
                }
                return; // Stop propagation even if closed
            }

            if (event.key === 'Tab' && isOpen) {
                toggleDropdown(null, true); // Close on Tab out
                // Allow default Tab behavior
                return;
            }

            if (!isOpen) {
                if (event.key === 'ArrowDown' || event.key === 'ArrowUp' || event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    toggleDropdown(); // Open with navigation keys or Enter/Space
                }
                return;
            }

            const currentFocus = document.activeElement;
            const focusableOptions = Array.from(optionsList.querySelectorAll('li:not(.filtered-out)'));
            if (focusableOptions.length === 0 && event.key !== 'ArrowUp') return; // No options to navigate (except up to search)

            let currentIndex = focusableOptions.findIndex(opt => opt === currentFocus);

            switch (event.key) {
                case 'ArrowDown':
                    event.preventDefault();
                    if (focusableOptions.length === 0) break;
                    if (currentFocus === searchInput || currentIndex === -1 || currentIndex === focusableOptions.length - 1) {
                        focusableOptions[0].focus();
                    } else {
                        focusableOptions[currentIndex + 1].focus();
                    }
                    break;
                case 'ArrowUp':
                    event.preventDefault();
                    if (currentFocus === searchInput) {
                        // Optional: Cycle to last option
                        // focusableOptions[focusableOptions.length - 1]?.focus();
                    } else if (currentIndex === -1 || currentIndex === 0) {
                        searchInput.focus(); // Go back to search input
                    } else if (focusableOptions.length > 0) {
                        focusableOptions[currentIndex - 1].focus();
                    } else {
                        searchInput.focus(); // If no options, focus search
                    }
                    break;
                case 'Home':
                    if (focusableOptions.length > 0) {
                        event.preventDefault();
                        focusableOptions[0]?.focus();
                    }
                    break;
                case 'End':
                    if (focusableOptions.length > 0) {
                        event.preventDefault();
                        focusableOptions[focusableOptions.length - 1]?.focus();
                    }
                    break;
                case 'Enter':
                case ' ':
                    if (currentFocus && currentFocus.tagName === 'LI') {
                        event.preventDefault();
                        handleOptionSelect({currentTarget: currentFocus});
                    } else if (currentFocus === searchInput && focusableOptions.length === 1) {
                        // Optional: Auto-select if only one result after filtering and Enter pressed in search
                        event.preventDefault();
                        handleOptionSelect({currentTarget: focusableOptions[0]});
                    }
                    break;
                default:
                    // If typing letters/numbers when search input NOT focused, maybe focus it?
                    if (currentFocus !== searchInput && event.key.length === 1 && !event.altKey && !event.ctrlKey && !event.metaKey) {
                        // searchInput.focus(); // Let the input handler take care of it
                    }
                    break;
            }
        }

        trigger.addEventListener('click', toggleDropdown);
        trigger.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault(); // Prevent default space scroll / enter submit
                toggleDropdown();
            } else if ((e.key === 'ArrowDown' || e.key === 'ArrowUp') && !wrapper.classList.contains('open')) {
                e.preventDefault();
                toggleDropdown();
            }
        });
        closeButton.addEventListener('click', e => {
            e.stopPropagation(); // Prevent trigger click
            toggleDropdown(null, true);
            trigger.focus();
        });
        searchInput.addEventListener('input', filterOptions);
        searchInput.addEventListener('keydown', e => {
            // Prevent ArrowUp/Down from navigating away from search if needed
            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                handleKeyDown(e); // Let the main handler manage focus movement
            }
            if (e.key === 'Escape') {
                e.stopPropagation(); // Prevent wrapper's escape handler if needed
                toggleDropdown(null, true);
                trigger.focus();
            }
        });
        wrapper.addEventListener('keydown', handleKeyDown);

        populateOptions();

        if (originalSelect.value) {
            const selectedOptionElement = originalSelect.querySelector(`option[value="${originalSelect.value}"]`);
            if (selectedOptionElement) {
                valueDisplay.textContent = selectedOptionElement.textContent;
            }
        }

        // Re-populate if original select changes (e.g., dynamic updates elsewhere)
        const observer = new MutationObserver(mutations => {
            console.log(`Mutation observed on #${originalSelectId}, repopulating options.`);
            populateOptions();
            // Also re-filter services if it's the category select and its value changed externally
            if (isCategoriaSelect) {
                handleOptionSelect({currentTarget: optionsList.querySelector(`li[data-value="${originalSelect.value}"]`) || optionsList.firstElementChild});
            }
        });
        observer.observe(originalSelect, {childList: true, subtree: true}); // Observe options added/removed

        // Observe changes to the 'value' attribute of the original select (might happen programmatically)
        const valueObserver = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                    console.log(`Value attribute changed on #${originalSelectId}, updating display.`);
                    const newValue = originalSelect.value;
                    const newOption = originalSelect.querySelector(`option[value="${newValue}"]`);
                    const newText = newOption ? newOption.textContent : originalSelect.querySelector('option[value=""]') ? originalSelect.querySelector('option[value=""]').textContent : 'Seleccionar...';
                    valueDisplay.textContent = newText;
                    // Update selected state in custom dropdown
                    allOptionsData.forEach(optData => {
                        const isSelected = optData.value === newValue;
                        optData.element.classList.toggle('selected', isSelected);
                        optData.element.setAttribute('aria-selected', isSelected.toString());
                    });
                    // Re-filter services if category select's value changed externally
                    if (isCategoriaSelect) {
                        handleOptionSelect({currentTarget: optionsList.querySelector(`li[data-value="${newValue}"]`) || optionsList.firstElementChild});
                    }
                }
            });
        });
        valueObserver.observe(originalSelect, {attributes: true});
    } // End setupCustomSelect

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.sc_services').forEach(cb => {
                cb.style.display = 'none';
            });
            setupCustomSelect('custom-provincia-wrapper');
            setupCustomSelect('custom-categoria-wrapper');
            // Call inicializar after setting up custom selects
            inicializar();
        });
    } else {
        document.querySelectorAll('.sc_services').forEach(cb => {
            cb.style.display = 'none';
        });
        setupCustomSelect('custom-provincia-wrapper');
        setupCustomSelect('custom-categoria-wrapper');
        // Call inicializar after setting up custom selects
        inicializar();
    }
})();
