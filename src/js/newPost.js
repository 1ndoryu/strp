(function () {
    //newPost.js
    'use strict';

    const HORARIO_STORAGE_KEY = 'userPendingSchedule';

    const form = document.getElementById('form-nuevo-anuncio');
    if (!form) {
        return;
    }

    // >>> NUEVO: Constantes para placeholders <<<
    const MAX_PHOTOS = 3; // Número máximo de imágenes permitidas
    const SVG_PLACEHOLDER = `<?xml version="1.0" encoding="UTF-8"?> <svg id="uuid-0ca005e1-d9fe-4045-a665-2e60e21962d4" data-name="Capa 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 145.83 120.73"> <defs> <style> .uuid-4e0375d0-00b6-45fd-960c-a83a919e3c21 { fill: #383a39; } .uuid-e42eef17-cbac-4bbb-bccc-8dc7fa78a734 { fill: none; stroke: #383a39; stroke-miterlimit: 10; stroke-width: 4.33px; } </style> </defs> <polygon class="uuid-4e0375d0-00b6-45fd-960c-a83a919e3c21" points="19.95 103.93 45.95 72.93 62.9 88.43 87.95 55.93 125.88 103.93 19.95 103.93"/> <circle class="uuid-4e0375d0-00b6-45fd-960c-a83a919e3c21" cx="31.95" cy="34.93" r="12"/> <rect class="uuid-e42eef17-cbac-4bbb-bccc-8dc7fa78a734" x="2.17" y="2.17" width="141.5" height="116.4" rx="18.8" ry="18.8"/> </svg>`;
    // <<< FIN NUEVO >>>

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
    // --- NUEVOS Selectores para el Horario ---
    const btnMostrarHorario = document.getElementById('btn-mostrar-horario');
    const contenedorHorario = document.getElementById('contenedor-horario');
    const ayudaTextoHorario = document.getElementById('ayuda-horario');
    const diaEstadoBotones = form.querySelectorAll('.btn-dia-estado'); // Selector para los NUEVOS botones

    // obtener el input y el contenedor para mostrar el input
    const contenedorInput = document.getElementById('input-url');
    const inputUrl = document.getElementById('url-banner');
    const inputUrlLateral = document.getElementById('input-url-banner-lateral');
    const contenedorInputLateral = document.getElementById('url-banner-lateral');

    contenedorInputLateral.addEventListener('click', () => {
        if (inputUrlLateral.classList.contains('active')) {
            inputUrlLateral.classList.remove('active');
            return;
        }

        inputUrlLateral.classList.toggle('active');
    });

    contenedorInput.addEventListener('click', () => {
        if (inputUrl.classList.contains('active')) {
            inputUrl.classList.remove('active');
            return;
        }

        inputUrl.classList.toggle('active');
    });

    // Obtener los divs que contienen los iconos de relojes para para la propagación
    const checkboxLabels = document.querySelectorAll('label.opcion-checkbox');

    checkboxLabels.forEach(label => {
        label.addEventListener('click', e => {
            // Comprueba si el elemento donde se hizo clic (e.target)
            // o uno de sus padres es el .icono-clock
            if (e.target.closest('.icono-clock')) {
                // Si es el icono, previene la acción por defecto de la etiqueta (marcar/desmarcar el input)
                e.preventDefault();
                console.log('Click en icono-clock, prevenido toggle de checkbox.');
                // Aquí puedes añadir lógica si quieres hacer algo más al clicar el icono (como mostrar el tooltip manualmente si fuera necesario)
            }
            // Si no se hizo clic en el icono, la etiqueta funciona normalmente.
        });
    });

    const horarioFeedbackDiv = document.createElement('div');
    horarioFeedbackDiv.id = 'horario-feedback';
    horarioFeedbackDiv.style.marginTop = '10px'; // Espacio
    if (contenedorHorario) {
        contenedorHorario.parentNode.insertBefore(horarioFeedbackDiv, contenedorHorario.nextSibling);
    }
    // NUEVO: Un div para errores específicos del horario no configurado en el submit final
    const horarioSubmitErrorDiv = document.createElement('div');
    horarioSubmitErrorDiv.id = 'error-horario-submit';
    horarioSubmitErrorDiv.classList.add('error-msg', 'oculto'); // Clase de error, oculto por defecto
    if (horarioFeedbackDiv) {
        // Insertar después del feedback
        horarioFeedbackDiv.parentNode.insertBefore(horarioSubmitErrorDiv, horarioFeedbackDiv.nextSibling);
    }

    // --- SELECTOR ANTIGUO (ya no se usa directamente para listeners o toggle) ---
    // const horarioCheckboxes = form.querySelectorAll('.check-dia input[type="checkbox"]'); // Comentado o eliminado
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
            // Limpiamos CUALQUIER listener de click previo para evitar conflictos
            // Clonando y reemplazando el nodo (forma segura de quitar todos los listeners)
            const btnClone = btnMostrarHorario.cloneNode(true);
            btnMostrarHorario.parentNode.replaceChild(btnClone, btnMostrarHorario);
            // Obtenemos la referencia al nuevo botón clonado
            const newBtnMostrarHorario = document.getElementById('btn-mostrar-horario');
            // Añadimos SOLO el listener correcto al clon
            if (newBtnMostrarHorario) {
                // Verificar que el clon se encontró
                newBtnMostrarHorario.addEventListener('click', abrirGestorHorario);
            } else {
                console.error('No se pudo encontrar el botón de horario clonado.');
            }
        }
        cargarHorarioDesdeStorage();
    }

    // --- NUEVA FUNCIÓN: Abrir la pestaña de gestión ---
    function abrirGestorHorario(event) {
        event.preventDefault();
        // Idealmente, crea una página separada (e.g., gestor_horario.html o .php)
        // Si no, usa la misma página con un parámetro ?gestionar_horario=1
        // y en el JS/PHP de carga, detecta ese parámetro para mostrar solo el horario.
        // Asumamos una página separada por claridad:
        const urlGestor = 'sc-includes/gestionar_horario.php/'; // *** ¡Crea esta página! ***
        window.open(urlGestor, 'gestorHorarioTab', 'width=600,height=700,scrollbars=yes,resizable=yes');

        // Actualizar feedback en la página principal
        actualizarFeedbackHorario('gestionando');
    }

    // --- NUEVA FUNCIÓN: Cargar datos desde localStorage y actualizar campos ocultos ---
    function cargarHorarioDesdeStorage() {
        const savedData = localStorage.getItem(HORARIO_STORAGE_KEY);
        if (horarioSubmitErrorDiv) horarioSubmitErrorDiv.classList.add('oculto'); // Limpiar error de submit

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
                    // Calcular 'dis' value (misma lógica que tenías en actualizarHorarioOculto)
                    let valorDis = '0';
                    if (diasSeleccionados.length === 7) valorDis = '1';
                    else if (diasSeleccionados.length === 5 && primerDia === 0 && ultimoDia === 4) valorDis = '2';
                    else if (diasSeleccionados.length === 6 && primerDia === 0 && ultimoDia === 5) valorDis = '3';
                    else if (diasSeleccionados.length === 2 && primerDia === 5 && ultimoDia === 6) valorDis = '4';
                    else valorDis = '1'; // Ajusta según necesidad del backend si no encaja

                    hiddenDis.value = valorDis;
                    hiddenHorarioInicio.value = horarios.inicio === '23:59' ? '00:00' : horarios.inicio; // Fallback si algo raro pasa
                    hiddenHorarioFinal.value = horarios.fin === '00:00' ? '23:59' : horarios.fin; // Fallback

                    actualizarFeedbackHorario('cargado', {dias: diasSeleccionados.length, inicio: hiddenHorarioInicio.value, fin: hiddenHorarioFinal.value});
                    // Limpiar error de validación si la etapa estaba activa
                    validarCampo(contenedorHorario, '#error-horario', true, ''); // Usa el div original como referencia
                    if (horarioSubmitErrorDiv) horarioSubmitErrorDiv.classList.add('oculto'); // Oculta error específico de submit
                } else {
                    // Datos guardados pero sin días disponibles (raro si la validación funciona)
                    limpiarDatosHorarioOcultosYStorage(false); // Limpia campos y storage sin actualizar feedback principal
                    actualizarFeedbackHorario('error_carga', {message: 'Los datos guardados no tienen días disponibles.'});
                }
            } catch (e) {
                console.error('Error al parsear horario desde localStorage:', e);
                limpiarDatosHorarioOcultosYStorage(false);
                actualizarFeedbackHorario('error_carga', {message: 'Error al leer los datos guardados.'});
            }
        } else {
            // No hay datos guardados
            limpiarDatosHorarioOcultosYStorage(false); // Asegura que los campos ocultos estén vacíos/default
            actualizarFeedbackHorario('no_configurado');
        }
    }

    // --- NUEVA FUNCIÓN: Limpiar campos ocultos y opcionalmente storage ---
    function limpiarDatosHorarioOcultosYStorage(removeFromStorage = true) {
        hiddenDis.value = '0'; // O el valor por defecto que espere tu backend
        hiddenHorarioInicio.value = '00:00';
        hiddenHorarioFinal.value = '00:00';
        if (removeFromStorage) {
            localStorage.removeItem(HORARIO_STORAGE_KEY);
            console.log('Datos del horario borrados de localStorage.');
            actualizarFeedbackHorario('no_configurado'); // Actualiza el mensaje
        }
    }

    // --- NUEVA FUNCIÓN: Actualizar el mensaje de feedback ---
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

        // Añadir listeners a los nuevos botones del feedback
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

                const idioma1Seleccionado = idioma1Select?.value !== '';
                const idioma2Seleccionado = idioma2Select?.value !== '';
                const alMenosUnIdioma = idioma1Seleccionado || idioma2Seleccionado;
                const grupoIdiomasDiv = form.querySelector('.grupo-idiomas');

                if (!validarCampo(grupoIdiomasDiv, '#error-idiomas', alMenosUnIdioma, 'Debes seleccionar al menos un idioma.')) {
                    esValido = false;
                    inputsInvalidos.push(idioma1Select || grupoIdiomasDiv); // Enfocar el primer select o el grupo
                }

                // --- Validación Opcional de Nivel si se selecciona Idioma ---
                if (idioma1Seleccionado && !validarCampo(nivelIdioma1Select, '#error-idiomas', nivelIdioma1Select?.value !== '', 'Debes seleccionar el nivel para el Idioma 1.')) {
                    // Nota: Usamos el mismo div de error, pero podrías tener divs separados si prefieres
                    esValido = false;
                    if (!inputsInvalidos.includes(nivelIdioma1Select)) {
                        inputsInvalidos.push(nivelIdioma1Select);
                    }
                }
                // --- Fin Validación Opcional de Nivel ---

                // --- Validación del Horario (NUEVA LÓGICA) ---
                const horarioGuardadoEtapa = localStorage.getItem(HORARIO_STORAGE_KEY);
                const horarioRequeridoEtapa = true; // Define si el horario es siempre obligatorio en esta etapa

                // Asegúrate de tener un div de error cerca del feedback del horario
                // <div id="error-horario-etapa" class="error-msg oculto"></div>
                const errorHorarioEtapaSelector = '#error-horario-etapa'; // Selector para el mensaje de error

                if (horarioRequeridoEtapa && !horarioGuardadoEtapa) {
                    // Mostrar error cerca del botón/feedback de horario
                    // Usamos horarioFeedbackDiv como 'elemento' para validarCampo, ya que el contenedor original está oculto
                    if (!validarCampo(horarioFeedbackDiv, errorHorarioEtapaSelector, false, 'Debes configurar y guardar tu horario.')) {
                        esValido = false;
                        // Intentamos obtener el botón real para el foco, incluso si fue clonado
                        const currentBtnMostrarHorario = document.getElementById('btn-mostrar-horario');
                        inputsInvalidos.push(currentBtnMostrarHorario || horarioFeedbackDiv); // Enfocar el botón o el feedback
                    }
                    if (horarioSubmitErrorDiv) horarioSubmitErrorDiv.classList.add('oculto'); // Limpiar error de submit final si este aparece antes
                } else {
                    validarCampo(horarioFeedbackDiv, errorHorarioEtapaSelector, true, ''); // Limpiar error si existe
                }
                // --- Fin Validación Horario ---

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
                loadingIndicator.remove(); // Siempre quitar el loading
                const photoData = parsePhotoUploadResponse(html);

                // --- Comprobar si la subida fue exitosa ANTES de hacer nada más ---
                if (photoData.filename) {
                    // DECLARAR newFilename AQUÍ, donde sabemos que photoData.filename existe
                    const newFilename = photoData.filename;
                    const filenameToReplace = inputElement.dataset.replacingFilename; // Comprueba si estábamos reemplazando

                    if (filenameToReplace) {
                        // --- ESTAMOS REEMPLAZANDO ---
                        console.log(`Reemplazando: ${filenameToReplace} con ${newFilename}`);

                        // 1. Eliminar el preview antiguo (MOVER AQUÍ)
                        const oldPreview = listaFotosContainer.querySelector(`.foto-subida-item[data-filename="${filenameToReplace}"]`);
                        if (oldPreview) {
                            oldPreview.remove();
                            console.log(`Preview antiguo [${filenameToReplace}] eliminado.`);
                        } else {
                            console.warn(`No se encontró el preview antiguo para [${filenameToReplace}].`);
                        }

                        // 2. Eliminar el input oculto antiguo (MOVER AQUÍ)
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
                        // --- ESTAMOS AÑADIENDO ---
                        console.log(`Añadiendo nueva foto: ${newFilename}`);
                        // No se elimina nada al añadir
                    }

                    // --- AÑADIR NUEVO PREVIEW E INPUT (MOVER AQUÍ) ---
                    // Solo se crean si la subida fue exitosa y tenemos newFilename
                    const previewElement = crearPreviewFoto(photoData.previewHtml, newFilename);
                    listaFotosContainer.appendChild(previewElement);
                    console.log(`Nuevo preview [${newFilename}] añadido.`);

                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'photo_name[]'; // Nombre esperado por el backend
                    hiddenInput.value = newFilename;
                    hiddenPhotoInputsContainer.appendChild(hiddenInput);
                    console.log(`Nuevo input oculto [${newFilename}] añadido.`);

                    // Validar: limpiar error si se añadió la primera foto
                    validarCampo(listaFotosContainer, '#error-fotos', true, '');

                    actualizarPlaceholders();
                } else {
                    // --- ERROR: El servidor no devolvió un filename válido ---
                    mostrarErrorFotos(`Error procesando la respuesta del servidor para "${file.name}". Respuesta: ${html}`);
                    // Limpiar el estado de reemplazo si falló durante el reemplazo
                    if (inputElement.dataset.replacingFilename) {
                        delete inputElement.dataset.replacingFilename;
                    }
                    // No se crea preview ni input oculto si falla
                }

                // --- Actualizar estado de botones de flecha SIEMPRE ---
                // Se ejecuta después de añadir, reemplazar (que implica eliminar y añadir) o fallar.
                updateArrowButtonStates();
            })
            .catch(error => {
                loadingIndicator.remove(); // Asegurar quitar loading en error de red
                mostrarErrorFotos(`Error subiendo "${file.name}": ${error.message}`);
                console.error('Error en fetch:', error);
                // Limpiar el estado de reemplazo si falló
                if (inputElement.dataset.replacingFilename) {
                    delete inputElement.dataset.replacingFilename;
                }
                // Actualizar botones por si acaso algo cambió antes del error
                updateArrowButtonStates();
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

    function actualizarPlaceholders() {
        if (!listaFotosContainer || !hiddenPhotoInputsContainer) {
            console.error('Error: No se encontraron los contenedores de fotos para actualizar placeholders.');
            return;
        }

        // 1. Eliminar TODOS los placeholders existentes primero
        const existingPlaceholders = listaFotosContainer.querySelectorAll('.foto-placeholder');
        existingPlaceholders.forEach(ph => ph.remove());

        // 2. Contar cuántas fotos reales (previews) hay
        //    Usamos los inputs ocultos como fuente fiable de la cantidad de fotos subidas
        const currentPhotosCount = hiddenPhotoInputsContainer.querySelectorAll('input[name="photo_name[]"]').length;

        // 3. Calcular cuántos placeholders se necesitan
        const placeholdersNeeded = Math.max(0, MAX_PHOTOS - currentPhotosCount);

        // 4. Crear y añadir los placeholders necesarios
        for (let i = 0; i < placeholdersNeeded; i++) {
            const placeholderDiv = document.createElement('div');
            placeholderDiv.classList.add('foto-placeholder'); // Clase para identificar y estilizar
            placeholderDiv.innerHTML = SVG_PLACEHOLDER; // Insertar el SVG
            // Añadir el placeholder al final del contenedor de la lista
            listaFotosContainer.appendChild(placeholderDiv);
        }
        console.log(`Placeholders actualizados: ${placeholdersNeeded} mostrados.`);
    }

    function crearPreviewFoto(htmlContent, filename) {
        const div = document.createElement('div');
        div.classList.add('foto-subida-item');
        div.dataset.filename = filename;
        div.innerHTML = htmlContent; // Asume HTML base

        const hiddenInPreview = div.querySelector('input[name="photo_name[]"]');
        hiddenInPreview?.remove();

        const actionsDiv = document.createElement('div');
        actionsDiv.classList.add('preview-actions');

        // --- Botón Mover Izquierda (Toggle Select) ---
        const moveLeftBtn = document.createElement('button');
        moveLeftBtn.type = 'button';
        moveLeftBtn.classList.add('btn-preview-action', 'btn-move-left', 'btn-toggle-position-select');
        moveLeftBtn.title = 'Elegir posición';
        moveLeftBtn.setAttribute('aria-label', `Elegir posición para foto ${filename}`);
        moveLeftBtn.dataset.filename = filename;
        moveLeftBtn.innerHTML = `
            <svg data-testid="geist-icon" height="12" stroke-linejoin="round" style="color:currentColor; pointer-events: none;" viewBox="0 0 16 16" width="12" aria-hidden="true">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.46966 13.7803L6.99999 14.3107L8.06065 13.25L7.53032 12.7197L3.56065 8.75001H14.25H15V7.25001H14.25H3.56065L7.53032 3.28034L8.06065 2.75001L6.99999 1.68935L6.46966 2.21968L1.39644 7.2929C1.00592 7.68342 1.00592 8.31659 1.39644 8.70711L6.46966 13.7803Z" fill="currentColor"></path>
            </svg>`;
        // *** INICIO: Listener Directo FORZADO ***
        moveLeftBtn.addEventListener('click', event => {
            console.log(`FORZADO: Click directo detectado en Botón Izquierda para ${filename}`);
            event.preventDefault(); // Prevenir comportamiento default
            event.stopPropagation(); // Detener burbujeo aquí para evitar conflictos
            togglePositionSelect(event); // Llama a la función pasando el evento
        });
        // *** FIN: Listener Directo FORZADO ***
        actionsDiv.appendChild(moveLeftBtn);

        // --- Botón Mover Derecha (Toggle Select) ---
        const moveRightBtn = document.createElement('button');
        moveRightBtn.type = 'button';
        moveRightBtn.classList.add('btn-preview-action', 'btn-move-right', 'btn-toggle-position-select');
        moveRightBtn.title = 'Elegir posición';
        moveRightBtn.setAttribute('aria-label', `Elegir posición para foto ${filename}`);
        moveRightBtn.dataset.filename = filename;
        moveRightBtn.innerHTML = `
            <svg data-testid="geist-icon" height="12" stroke-linejoin="round" style="color:currentColor; pointer-events: none;" viewBox="0 0 16 16" width="12" aria-hidden="true">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.53033 2.21968L9 1.68935L7.93934 2.75001L8.46967 3.28034L12.4393 7.25001H1.75H1V8.75001H1.75H12.4393L8.46967 12.7197L7.93934 13.25L9 14.3107L9.53033 13.7803L14.6036 8.70711C14.9941 8.31659 14.9941 7.68342 14.6036 7.2929L9.53033 2.21968Z" fill="currentColor"></path>
            </svg>`;
        // *** INICIO: Listener Directo FORZADO ***
        moveRightBtn.addEventListener('click', event => {
            console.log(`FORZADO: Click directo detectado en Botón Derecha para ${filename}`);
            event.preventDefault(); // Prevenir comportamiento default
            event.stopPropagation(); // Detener burbujeo aquí
            togglePositionSelect(event); // Llama a la función pasando el evento
        });
        // *** FIN: Listener Directo FORZADO ***
        actionsDiv.appendChild(moveRightBtn);

        // --- Botón Rotar --- (Sin listener directo aquí, lo maneja la delegación)
        const rotateBtn = document.createElement('button');
        rotateBtn.type = 'button';
        rotateBtn.classList.add('btn-preview-action', 'btn-rotate-foto');
        rotateBtn.title = 'Rotar foto 90°';
        rotateBtn.dataset.filename = filename;
        rotateBtn.innerHTML = `
        <svg data-testid="geist-icon" height="12" stroke-linejoin="round" style="color:currentColor; pointer-events: none;" viewBox="0 0 16 16" width="12"><path fill-rule="evenodd" clip-rule="evenodd" d="M8.00002 1.25C5.33749 1.25 3.02334 2.73677 1.84047 4.92183L1.48342 5.58138L2.80253 6.29548L3.15958 5.63592C4.09084 3.91566 5.90986 2.75 8.00002 2.75C10.4897 2.75 12.5941 4.40488 13.2713 6.67462H11.8243H11.0743V8.17462H11.8243H15.2489C15.6631 8.17462 15.9989 7.83883 15.9989 7.42462V4V3.25H14.4989V4V5.64468C13.4653 3.06882 10.9456 1.25 8.00002 1.25ZM1.50122 10.8555V12.5V13.25H0.0012207V12.5V9.07538C0.0012207 8.66117 0.337007 8.32538 0.751221 8.32538H4.17584H4.92584V9.82538H4.17584H2.72876C3.40596 12.0951 5.51032 13.75 8.00002 13.75C10.0799 13.75 11.8912 12.5958 12.8266 10.8895L13.1871 10.2318L14.5025 10.9529L14.142 11.6105C12.9539 13.7779 10.6494 15.25 8.00002 15.25C5.05453 15.25 2.53485 13.4313 1.50122 10.8555Z" fill="currentColor"></path></svg>`;
        // rotateBtn.addEventListener('click', handleRotateFotoClick); // ¡¡¡ ASEGÚRATE QUE ESTÁ COMENTADO O BORRADO !!!
        actionsDiv.appendChild(rotateBtn);

        // --- Botón Eliminar --- (Sin listener directo aquí, lo maneja la delegación)
        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.classList.add('btn-preview-action', 'btn-delete-foto');
        deleteBtn.title = 'Eliminar foto';
        deleteBtn.dataset.filename = filename;
        deleteBtn.innerHTML = `
            <svg data-testid="geist-icon" height="12" stroke-linejoin="round" viewBox="0 0 16 16" width="12" style="color: currentcolor; pointer-events: none;" aria-hidden="true">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12.4697 13.5303L13 14.0607L14.0607 13L13.5303 12.4697L9.06065 7.99999L13.5303 3.53032L14.0607 2.99999L13 1.93933L12.4697 2.46966L7.99999 6.93933L3.53032 2.46966L2.99999 1.93933L1.93933 2.99999L2.46966 3.53032L6.93933 7.99999L2.46966 12.4697L1.93933 13L2.99999 14.0607L3.53032 13.5303L7.99999 9.06065L12.4697 13.5303Z" fill="currentColor"></path>
            </svg>`;
        // deleteBtn.addEventListener('click', eliminarFoto); // ¡¡¡ ASEGÚRATE QUE ESTÁ COMENTADO O BORRADO !!!
        actionsDiv.appendChild(deleteBtn);

        // --- Botón Cambiar (oculto) --- (Sin listener directo aquí)
        const changeBtn = document.createElement('button');
        changeBtn.type = 'button';
        changeBtn.classList.add('btn-preview-action', 'btn-change-foto');
        changeBtn.style.display = 'none';
        changeBtn.title = 'Cambiar esta foto';
        changeBtn.dataset.filename = filename;
        changeBtn.innerHTML = 'Cambiar';
        actionsDiv.appendChild(changeBtn);

        // --- Adjuntar Contenedor de Acciones ---
        const optionsContainer = div.querySelector('.photos_options');
        if (optionsContainer) {
            optionsContainer.innerHTML = '';
            optionsContainer.appendChild(actionsDiv);
        } else {
            // Fallback
            const img = div.querySelector('img');
            if (img && img.parentNode) {
                img.parentNode.insertBefore(actionsDiv, img.nextSibling);
            } else {
                div.appendChild(actionsDiv);
            }
        }

        // --- Listener en la imagen (para cambiarla) --- (Sin listener directo aquí)
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
        // Acepta el objeto evento directamente
        console.log('--- togglePositionSelect (FORZADO) INICIO ---');
        // *** CAMBIO CLAVE: Obtener el botón del evento ***
        const button = event.currentTarget;
        // ************************************************
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
        selectPosicion.dataset.currentFilename = filename; // <<< IMPORTANTE

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
    // >>> NUEVO: Helper para ocultar el select si se hace click fuera <<<
    function hideSelectOnClickOutside(event) {
        // Verifica si el select existe, es visible Y si el click ocurrió FUERA de él Y FUERA de un botón que lo abre
        // Usar event.target para saber dónde ocurrió el click original
        const clickedElement = event.target;
        console.log('hideSelectOnClickOutside - Click detectado en:', clickedElement);

        if (
            selectPosicion &&
            selectPosicion.classList.contains('visible') && // ¿Está visible?
            !selectPosicion.contains(clickedElement) && // ¿Click fuera del select?
            !clickedElement.closest('.btn-toggle-position-select')
        ) {
            // ¿Click fuera de un botón toggle?
            console.log('Click fuera detectado por hideSelectOnClickOutside. Ocultando select.');
            selectPosicion.classList.remove('visible');
            selectPosicion.classList.add('oculto');
            delete selectPosicion.dataset.currentFilename; // Limpiar filename guardado
            // No necesitamos quitar el listener manualmente aquí porque se añadió con { once: true }
            console.log('Listener hideSelectOnClickOutside (once:true) consumido.');
        } else {
            // El listener {once:true} se consume igual si el click fue dentro o en el botón, pero no ocultamos.
            console.log('hideSelectOnClickOutside: Click dentro del select o en un botón toggle (o select no visible). No se oculta. Listener (once:true) consumido.');
        }
    }

    // >>> NUEVO: Función para manejar el cambio en el select de posición <<<
    function handlePositionChange(event) {
        console.log('--- handlePositionChange INICIO ---'); // LOG 16: ¿Se dispara al cambiar?
        const select = event.currentTarget;
        console.log('Select que disparó el evento:', select); // LOG 17
        const selectedValue = select.value;
        console.log('Valor seleccionado:', selectedValue); // LOG 18
        // Obtenemos el filename que DEBERÍA haber sido guardado por togglePositionSelect
        const filename = select.dataset.currentFilename; // <<< El posible punto de fallo
        console.log('Filename recuperado del dataset (select.dataset.currentFilename):', filename); // LOG 19: ¡Este es el valor crucial!

        // Validar datos antes de proceder
        let newPositionIndex = NaN;
        if (selectedValue) {
            // Asegurarse de que sea un número antes de restar 1
            const parsedValue = parseInt(selectedValue, 10);
            if (!isNaN(parsedValue)) {
                newPositionIndex = parsedValue - 1; // value es 1, 2, 3 -> index 0, 1, 2
            }
        }
        console.log('Filename parseado:', filename); // LOG 20
        console.log('Índice nuevo parseado:', newPositionIndex); // LOG 21

        // Comprobación más robusta de los datos necesarios
        if (typeof filename === 'undefined' || filename === null || filename === '' || isNaN(newPositionIndex) || newPositionIndex < 0) {
            console.error('Error en handlePositionChange: Datos inválidos para mover la imagen.'); // LOG 22: El error que ves
            console.error(`Detalles: filename='${filename}', selectedValue='${selectedValue}', newPositionIndex=${newPositionIndex}`); // Más detalles
            // Ocultar select y limpiar por seguridad, aunque los datos fueran malos
            select.classList.remove('visible');
            select.classList.add('oculto');
            delete select.dataset.currentFilename;
            document.removeEventListener('click', hideSelectOnClickOutside, true); // Limpiar listener
            console.log('--- handlePositionChange FIN (Error de validación de datos) ---');
            return; // Detener ejecución
        }

        console.log(`Intentando mover filename '${filename}' al índice ${newPositionIndex}`); // LOG 23

        // Encontrar los elementos DOM a mover
        const currentPreviewItem = listaFotosContainer.querySelector(`.foto-subida-item[data-filename="${filename}"]`);
        const currentHiddenInput = hiddenPhotoInputsContainer.querySelector(`input[name="photo_name[]"][value="${filename}"]`);

        // Validar que encontramos ambos elementos
        if (!currentPreviewItem) {
            console.error(`Error crítico: No se encontró el PREVIEW item para filename '${filename}'. No se puede mover.`);
            // Ocultar y limpiar
            select.classList.remove('visible');
            select.classList.add('oculto');
            delete select.dataset.currentFilename;
            document.removeEventListener('click', hideSelectOnClickOutside, true);
            console.log('--- handlePositionChange FIN (Error: Preview no encontrado) ---');
            return;
        }
        if (!currentHiddenInput) {
            console.error(`Error crítico: No se encontró el INPUT OCULTO para filename '${filename}'. La consistencia de datos se perderá si continuamos.`);
            // Es mejor detenerse
            select.classList.remove('visible');
            select.classList.add('oculto');
            delete select.dataset.currentFilename;
            document.removeEventListener('click', hideSelectOnClickOutside, true);
            console.log('--- handlePositionChange FIN (Error: Input oculto no encontrado) ---');
            return;
        }
        console.log('Elementos a mover encontrados:', {preview: currentPreviewItem, input: currentHiddenInput});

        // Obtener lista actual de nodos (previews e inputs) para determinar la referencia
        const allPreviewsNodes = listaFotosContainer.querySelectorAll('.foto-subida-item:not(.loading)');
        const allHiddenInputsNodes = hiddenPhotoInputsContainer.querySelectorAll('input[name="photo_name[]"]');
        console.log(`Nodos actuales: ${allPreviewsNodes.length} previews, ${allHiddenInputsNodes.length} inputs.`);

        // Determinar el nodo de referencia ANTES del cual insertar
        // Si newPositionIndex es 0, targetSibling será el primer nodo actual.
        // Si newPositionIndex es N, targetSibling será el nodo en el índice N.
        // Si newPositionIndex es >= longitud, targetSibling será null (insertBefore actúa como appendChild).
        const targetPreviewSibling = allPreviewsNodes[newPositionIndex] || null;
        const targetInputSibling = allHiddenInputsNodes[newPositionIndex] || null;
        console.log('Nodos de referencia (se insertará ANTES de estos, o al final si son null):', {previewSibling: targetPreviewSibling, inputSibling: targetInputSibling});

        // Mover los elementos DOM (con try-catch por si algo falla)
        try {
            // Mover Preview: insertBefore(elementoAMover, elementoDeReferencia)
            console.log(`Moviendo preview ${filename} antes de ${targetPreviewSibling ? targetPreviewSibling.dataset.filename : 'final'}`);
            listaFotosContainer.insertBefore(currentPreviewItem, targetPreviewSibling);

            // Mover Input Oculto
            console.log(`Moviendo input ${filename} antes de ${targetInputSibling ? targetInputSibling.value : 'final'}`);
            hiddenPhotoInputsContainer.insertBefore(currentHiddenInput, targetInputSibling);

            console.log('Movimiento DOM completado.');
        } catch (e) {
            console.error('Error durante el movimiento DOM:', e);
            // Si falla el movimiento, podría dejar el DOM en estado inconsistente.
            // Ocultar el select de todas formas para evitar más interacciones erróneas.
            select.classList.remove('visible');
            select.classList.add('oculto');
            delete select.dataset.currentFilename;
            document.removeEventListener('click', hideSelectOnClickOutside, true);
            alert('Ocurrió un error al intentar mover la foto. Por favor, recarga la página.'); // Informar al usuario
            console.log('--- handlePositionChange FIN (Error durante movimiento DOM) ---');
            return; // Salir si falla el movimiento
        }

        // Ocultar el select y limpiar estado SIEMPRE después de un intento exitoso o fallido de mover
        console.log('Movimiento intentado. Ocultando select y limpiando dataset.'); // LOG 24
        select.classList.remove('visible');
        select.classList.add('oculto');
        delete select.dataset.currentFilename;
        document.removeEventListener('click', hideSelectOnClickOutside, true); // Asegurar limpieza

        // Actualizar estado de botones de flecha (si existe la función)
        if (typeof updateArrowButtonStates === 'function') {
            console.log('Llamando a updateArrowButtonStates...'); // LOG 25
            updateArrowButtonStates();
        } else {
            console.warn('Función updateArrowButtonStates no encontrada. Estado visual de flechas no actualizado.');
        }
        // Limpiar cualquier error previo de validación de fotos
        validarCampo(listaFotosContainer, '#error-fotos', true, '');
        console.log('--- handlePositionChange FIN (Éxito o intento completado) ---');
    }

    // >>> ESTA ES LA FUNCIÓN agregarListenersNuevos COMPLETA Y CORREGIDA <<<
    function agregarListenersNuevos() {
        console.log('Ejecutando agregarListenersNuevos (CON LISTENERS DELEGADOS PARA ROTAR/ELIMINAR/IMG)...');

        // --- Listener para el cambio en el select de posición ---
        // (Esta parte maneja el dropdown para mover fotos a una posición específica)
        if (selectPosicion) {
            console.log('Añadiendo listener "change" a selectPosicion.');
            // Remover listener previo para evitar duplicados si esta función se llama más de una vez
            selectPosicion.removeEventListener('change', handlePositionChange);
            selectPosicion.addEventListener('change', handlePositionChange);
        } else {
            console.error('Error crítico: selectPosicion (el dropdown de posición) no está definido al añadir listener change.');
        }

        // --- Listener Delegado ÚNICO para Clics dentro de listaFotosContainer ---
        // (Este único listener manejará clics en imágenes, botón rotar, botón eliminar, etc.)
        if (listaFotosContainer) {
            console.log('Añadiendo/Asegurando listener delegado a listaFotosContainer para clicks en IMG, ROTATE, DELETE.');

            // NOTA: Idealmente, se añadiría este listener una sola vez.
            // Si hay riesgo de llamarlo múltiples veces, deberías guardar una referencia
            // a la función callback y usar removeEventListener antes de añadirlo de nuevo,
            // o usar una bandera para saber si ya se añadió.
            // Por simplicidad aquí, asumimos que se configura correctamente una vez.

            listaFotosContainer.addEventListener('click', function (event) {
                // 'event.target' es el elemento exacto donde se hizo clic (puede ser un SVG, un SPAN, etc.)
                const target = event.target;

                console.log('Click delegado detectado en listaFotosContainer. Target:', target);

                // --- 1. Comprobar si se hizo clic DENTRO del botón ROTAR ---
                // Usamos closest() para encontrar el botón aunque se haya hecho clic en su icono SVG
                const rotateButton = target.closest('.btn-rotate-foto');
                if (rotateButton) {
                    console.log('Delegated click: Botón ROTAR detectado.');
                    event.preventDefault(); // Prevenir comportamiento por defecto si lo hubiera
                    event.stopPropagation(); // Detener la propagación para no activar otros listeners (si los hubiera)
                    // Llamamos a la función original, pasándole un objeto que simula
                    // el 'currentTarget' que esperaría si el listener estuviera directo en el botón.
                    handleRotateFotoClick({currentTarget: rotateButton});
                    return; // Acción realizada, no necesitamos seguir comprobando otros elementos
                }

                // --- 2. Comprobar si se hizo clic DENTRO del botón ELIMINAR ---
                const deleteButton = target.closest('.btn-delete-foto');
                if (deleteButton) {
                    console.log('Delegated click: Botón ELIMINAR detectado.');
                    event.preventDefault();
                    event.stopPropagation();
                    // Llamamos a la función de eliminar, simulando el evento
                    eliminarFoto({currentTarget: deleteButton});
                    return; // Acción realizada
                }

                // --- 3. Comprobar si se hizo clic en una IMAGEN (para cambiarla) ---
                // (Esta lógica ya estaba, la mantenemos y aseguramos que funciona con la delegación)
                if (target.tagName === 'IMG') {
                    const previewItem = target.closest('.foto-subida-item');
                    // Asegurarse que es una imagen de un preview real (no loading, no placeholder) y tiene filename
                    if (previewItem && !previewItem.classList.contains('loading') && !previewItem.classList.contains('foto-placeholder') && target.dataset.filename) {
                        console.log(`Delegated click: Imagen con filename ${target.dataset.filename} detectada para cambio.`);
                        event.preventDefault(); // Podría ser útil si la imagen estuviera dentro de un enlace
                        event.stopPropagation();
                        // Llamamos a la función que dispara el input de archivo para reemplazar
                        triggerChangeFotoFromImage({currentTarget: target}); // Pasamos la imagen como currentTarget
                        return; // Acción realizada
                    } else if (previewItem && previewItem.classList.contains('foto-placeholder')) {
                        // El usuario hizo clic en un placeholder SVG
                        console.log('Delegated click: Placeholder SVG clickeado.');
                        // Podrías decidir abrir el selector de archivos aquí si quieres esa funcionalidad
                        // fotosInput.click();
                        return; // Acción (o inacción intencionada) realizada
                    }
                }

                // --- 4. (Opcional) Comprobar clic en botón 'Cambiar' si se hiciera visible ---
                // (Este botón está oculto por defecto en tu código, pero por si acaso)
                const changeButton = target.closest('.btn-change-foto');
                if (changeButton && changeButton.style.display !== 'none') {
                    console.log('Delegated click: Botón CAMBIAR (visible) detectado.');
                    event.preventDefault();
                    event.stopPropagation();
                    handleChangeFotoClick({currentTarget: changeButton});
                    return; // Acción realizada
                }

                // Si el clic no fue en ninguno de los elementos interactivos que nos interesan,
                // el código llega hasta aquí y simplemente no hace nada, permitiendo el comportamiento normal.
                console.log('Delegated click: El clic no coincidió con IMG, Rotar, Eliminar o Cambiar interactivos.');
            }); // Fin del addEventListener 'click' en listaFotosContainer
        } else {
            console.error('Error crítico: listaFotosContainer (el contenedor de previews) no encontrado al intentar añadir listener delegado.');
        }
    
    } 

    function handleRotateFotoClick(event) {
        const button = event.currentTarget;
        const filename = button.dataset.filename; // Lo mantenemos por si acaso, aunque no se use para llamar al servidor
        if (!filename) {
            console.error('Error Rotar (Visual): No se encontró filename en el botón.');
            // No mostramos alerta al usuario, es un problema interno menor en este caso
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

        // --- Lógica de Rotación Visual ---

        // 1. Obtener la rotación actual (desde un atributo data o asumir 0)
        let currentRotation = parseInt(imgElement.dataset.rotation || '0', 10);

        // 2. Calcular la nueva rotación (sumar 90 grados, ciclo 0 -> 90 -> 180 -> 270 -> 0)
        const newRotation = (currentRotation + 90) % 360;

        // 3. Aplicar la rotación CSS al elemento <img>
        imgElement.style.transform = `rotate(${newRotation}deg)`;
        // Opcional: Añadir transición para suavizar
        imgElement.style.transition = 'transform 0.3s ease'; // Mantenemos la transición si ya estaba

        // 4. Guardar el nuevo estado de rotación en el atributo data
        imgElement.dataset.rotation = newRotation;

        // --- INICIO: Lógica para aplicar max-width en 90/270 grados ---
        if (newRotation === 90 || newRotation === 270) {
            // Si la rotación es 90 o 270 (orientación vertical), aplicamos max-width
            imgElement.style.maxWidth = '80px';
            imgElement.style.maxHeight = '120px';
            console.log(`Aplicado max-width: 80px a ${filename} (Rotación: ${newRotation})`);
        } else {
            // Si la rotación es 0 o 180 (orientación horizontal), quitamos el max-width
            // Establecer a '' elimina el estilo inline específico de max-width
            imgElement.style.maxWidth = '';
            imgElement.style.maxHeight = '';
            console.log(`Quitado max-width de ${filename} (Rotación: ${newRotation})`);
        }
        // --- FIN: Lógica para aplicar max-width ---

        console.log(`Rotación visual aplicada a ${filename}: ${newRotation} grados.`);
    }

    function handleMoveFotoClick(event) {
        const button = event.currentTarget;
        const filename = button.dataset.filename;
        const direction = button.classList.contains('btn-move-left') ? 'left' : 'right';

        const currentPreviewItem = button.closest('.foto-subida-item');
        if (!currentPreviewItem) return;

        // Encuentra el input oculto correspondiente al preview actual
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
            // direction === 'right'
            siblingPreviewItem = currentPreviewItem.nextElementSibling;
        }

        // Verifica que el hermano sea también un item de foto (y no el botón de subir, por ejemplo)
        if (siblingPreviewItem && siblingPreviewItem.classList.contains('foto-subida-item')) {
            const siblingFilename = siblingPreviewItem.dataset.filename;
            siblingHiddenInput = hiddenPhotoInputsContainer.querySelector(`input[name="photo_name[]"][value="${siblingFilename}"]`);

            if (siblingHiddenInput) {
                // Mover los elementos visuales (previews)
                if (direction === 'left') {
                    listaFotosContainer.insertBefore(currentPreviewItem, siblingPreviewItem);
                } else {
                    // right
                    listaFotosContainer.insertBefore(currentPreviewItem, siblingPreviewItem.nextElementSibling); // Insertar después del hermano
                }

                // Mover los inputs ocultos
                if (direction === 'left') {
                    hiddenPhotoInputsContainer.insertBefore(currentHiddenInput, siblingHiddenInput);
                } else {
                    // right
                    hiddenPhotoInputsContainer.insertBefore(currentHiddenInput, siblingHiddenInput.nextElementSibling); // Insertar después del hermano
                }

                // Actualizar el estado de los botones de flecha
                updateArrowButtonStates();
            } else {
                console.error(`No se encontró el input oculto para el hermano ${siblingFilename}`);
            }
        }
    }

    function updateArrowButtonStates() {
        const previewItems = listaFotosContainer.querySelectorAll('.foto-subida-item');
        const itemCount = previewItems.length;

        previewItems.forEach((item, index) => {
            const moveLeftBtn = item.querySelector('.btn-move-left');
            const moveRightBtn = item.querySelector('.btn-move-right');

            if (moveLeftBtn) {
                moveLeftBtn.disabled = index === 0; // Deshabilitar izquierda si es el primero
            }
            if (moveRightBtn) {
                moveRightBtn.disabled = index === itemCount - 1; // Deshabilitar derecha si es el último
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

        actualizarPlaceholders();

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

        updateArrowButtonStates();
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
        event.preventDefault();

        cargarHorarioDesdeStorage();

        if (!validarFormularioCompleto()) {
            alert('Por favor, revisa el formulario. Hay errores o campos incompletos en alguna de las etapas.');
            irAPrimeraEtapaConError();
            return;
        }

        actualizarSellerTypeOculto();

        actualizarIdiomasOculto();

        // --- INICIO: Añadir campos ocultos para el horario detallado ---
        console.log('Intentando añadir campos ocultos del horario detallado...');
        const horarioGuardadoString = localStorage.getItem(HORARIO_STORAGE_KEY);
        if (horarioGuardadoString) {
            try {
                const scheduleData = JSON.parse(horarioGuardadoString);
                const dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

                // Primero, eliminamos campos de horario_dia previos si existen (para evitar duplicados si algo sale mal)
                form.querySelectorAll('input[name^="horario_dia["]').forEach(input => input.remove());

                dias.forEach(diaKey => {
                    const diaInfo = scheduleData[diaKey];

                    if (diaInfo) {
                        // Si hay información para este día
                        const activoValue = diaInfo.disponible ? '1' : '0';
                        const inicioValue = diaInfo.inicio || '00:00'; // Valor por defecto si no existe
                        const finValue = diaInfo.fin || '23:30'; // Valor por defecto si no existe

                        // Crear input para 'activo'
                        const inputActivo = document.createElement('input');
                        inputActivo.type = 'hidden';
                        inputActivo.name = `horario_dia[${diaKey}][activo]`;
                        inputActivo.value = activoValue;
                        form.appendChild(inputActivo);

                        // Crear input para 'inicio' (solo si está activo, aunque el PHP lo maneja)
                        const inputInicio = document.createElement('input');
                        inputInicio.type = 'hidden';
                        inputInicio.name = `horario_dia[${diaKey}][inicio]`;
                        inputInicio.value = inicioValue;
                        form.appendChild(inputInicio);

                        // Crear input para 'fin' (solo si está activo, aunque el PHP lo maneja)
                        const inputFin = document.createElement('input');
                        inputFin.type = 'hidden';
                        inputFin.name = `horario_dia[${diaKey}][fin]`;
                        inputFin.value = finValue;
                        form.appendChild(inputFin);

                        // console.log(`Añadido hidden para ${diaKey}: activo=${activoValue}, inicio=${inicioValue}, fin=${finValue}`);
                    } else {
                        // Si no hay info para el día, podemos añadir 'activo=0' para ser explícitos
                        const inputActivo = document.createElement('input');
                        inputActivo.type = 'hidden';
                        inputActivo.name = `horario_dia[${diaKey}][activo]`;
                        inputActivo.value = '0';
                        form.appendChild(inputActivo);
                        // No necesitamos añadir inicio/fin si no está activo
                        // console.log(`Añadido hidden para ${diaKey}: activo=0 (no configurado en localStorage)`);
                    }
                });
                console.log('Campos ocultos del horario detallado añadidos al formulario.');
            } catch (e) {
                console.error('Error al procesar horario desde localStorage para añadir campos ocultos:', e);
                // Opcional: Mostrar un error al usuario antes de enviar?
                // alert("Hubo un problema al procesar el horario guardado. No se enviará la información detallada.");
            }
        } else {
            console.log('No se encontró horario detallado en localStorage para añadir campos ocultos.');
            // Opcional: Asegurarse de que no queden campos viejos si se borró el storage
            form.querySelectorAll('input[name^="horario_dia["]').forEach(input => input.remove());
        }
        // --- FIN: Añadir campos ocultos para el horario detallado ---

        form.submit();
    }

    function validarFormularioCompleto() {
        let todoValido = true;
        let primeraEtapaInvalida = -1;

        for (let i = 0; i < etapas.length; i++) {
            const originalIndex = etapaActualIndex;
            etapaActualIndex = i; // Temporalmente cambia a la etapa para validar
            const etapaValida = validarEtapaActual();
            etapaActualIndex = originalIndex; // Restaura el índice actual

            if (!etapaValida && primeraEtapaInvalida === -1) {
                primeraEtapaInvalida = i;
                todoValido = false;
                // No rompemos el bucle, para mostrar todos los errores iniciales
            }
        }

        // --- Validación específica del horario al final ---
        const horarioGuardado = localStorage.getItem(HORARIO_STORAGE_KEY);
        const horarioRequerido = true; // Define si es obligatorio

        if (horarioRequerido && !horarioGuardado) {
            if (horarioSubmitErrorDiv) {
                horarioSubmitErrorDiv.textContent = 'Es obligatorio configurar y guardar el horario antes de finalizar.';
                horarioSubmitErrorDiv.classList.remove('oculto');
            }
            todoValido = false;
            // Si no se encontró otra etapa inválida antes, marca la del horario
            if (primeraEtapaInvalida === -1) {
                // Encuentra el índice de la etapa que contiene el horario
                const etapaHorarioIndex = etapas.findIndex(etapa => etapa.querySelector('#btn-mostrar-horario'));
                if (etapaHorarioIndex !== -1) {
                    primeraEtapaInvalida = etapaHorarioIndex;
                }
            }
        } else {
            if (horarioSubmitErrorDiv) horarioSubmitErrorDiv.classList.add('oculto'); // Ocultar si es válido
        }

        if (!todoValido) {
            alert('Por favor, revisa el formulario. Hay errores o campos incompletos.');
            if (primeraEtapaInvalida !== -1) {
                cambiarEtapa(primeraEtapaInvalida); // Ir a la primera etapa con error
                // Intentar hacer scroll al primer elemento inválido O al error del horario
                const primerErrorVisible = form.querySelector('.error-msg:not(.oculto), .invalido');
                primerErrorVisible?.scrollIntoView({behavior: 'smooth', block: 'center'});
            }
            return false; // Detiene el envío
        }

        return true; // Todo válido
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
