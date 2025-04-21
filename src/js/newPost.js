(function () {
    //newPost.js
    'use strict';

    const HORARIO_STORAGE_KEY = 'userPendingSchedule';

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
        agregarListenersNuevos(); 

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

    function crearPreviewFoto(htmlContent, filename) {
        const div = document.createElement('div');
        div.classList.add('foto-subida-item');
        div.dataset.filename = filename;
        div.innerHTML = htmlContent; // El HTML base que viene del servidor

        // Limpiar input oculto si viene en el HTML (como en el original)
        const hiddenInPreview = div.querySelector('input[name="photo_name[]"]');
        hiddenInPreview?.remove();

        // Contenedor para los botones de acción
        const actionsDiv = document.createElement('div');
        actionsDiv.classList.add('preview-actions');

        // --- Botón Mover Izquierda (Existente) ---
        const moveLeftBtn = document.createElement('button');
        moveLeftBtn.type = 'button';
        // >>> CAMBIO 1: Añade clase para identificarlo para el select <<<
        moveLeftBtn.classList.add('btn-preview-action', 'btn-move-left', 'btn-toggle-position-select');
        moveLeftBtn.title = 'Elegir posición'; // Cambia el title si quieres
        moveLeftBtn.setAttribute('aria-label', `Elegir posición para foto ${filename}`);
        moveLeftBtn.dataset.filename = filename;
        moveLeftBtn.innerHTML = `
            <svg data-testid="geist-icon" height="12" stroke-linejoin="round" style="color:currentColor" viewBox="0 0 16 16" width="12" aria-hidden="true">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M6.46966 13.7803L6.99999 14.3107L8.06065 13.25L7.53032 12.7197L3.56065 8.75001H14.25H15V7.25001H14.25H3.56065L7.53032 3.28034L8.06065 2.75001L6.99999 1.68935L6.46966 2.21968L1.39644 7.2929C1.00592 7.68342 1.00592 8.31659 1.39644 8.70711L6.46966 13.7803Z" fill="currentColor"></path>
            </svg>`;
        // >>> CAMBIO 1: Quita el listener original de mover <<<
        // moveLeftBtn.addEventListener('click', handleMoveFotoClick); // COMENTADO o ELIMINADO
        actionsDiv.appendChild(moveLeftBtn); // Se mantiene el botón visualmente

        // --- Botón Mover Derecha ---
        const moveRightBtn = document.createElement('button');
        moveRightBtn.type = 'button';
        // >>> CAMBIO 1: Añade clase para identificarlo para el select <<<
        moveRightBtn.classList.add('btn-preview-action', 'btn-move-right', 'btn-toggle-position-select');
        moveRightBtn.title = 'Elegir posición'; // Cambia el title si quieres
        moveRightBtn.setAttribute('aria-label', `Elegir posición para foto ${filename}`);
        moveRightBtn.dataset.filename = filename;
        moveRightBtn.innerHTML = `
            <svg data-testid="geist-icon" height="12" stroke-linejoin="round" style="color:currentColor" viewBox="0 0 16 16" width="12" aria-hidden="true">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.53033 2.21968L9 1.68935L7.93934 2.75001L8.46967 3.28034L12.4393 7.25001H1.75H1V8.75001H1.75H12.4393L8.46967 12.7197L7.93934 13.25L9 14.3107L9.53033 13.7803L14.6036 8.70711C14.9941 8.31659 14.9941 7.68342 14.6036 7.2929L9.53033 2.21968Z" fill="currentColor"></path>
            </svg>`;
        // >>> CAMBIO 1: Quita el listener original de mover <<<
        // moveRightBtn.addEventListener('click', handleMoveFotoClick); // COMENTADO o ELIMINADO
        actionsDiv.appendChild(moveRightBtn); // Se mantiene el botón visualmente

        // --- Botón Cambiar Foto (Existente) ---
        const changeBtn = document.createElement('button');
        changeBtn.type = 'button';
        changeBtn.classList.add('btn-preview-action', 'btn-change-foto');
        changeBtn.title = 'Cambiar foto';
        changeBtn.setAttribute('aria-label', `Cambiar la foto ${filename}`);
        changeBtn.dataset.filename = filename;
        changeBtn.innerHTML = `
            <?xml version="1.0" encoding="UTF-8"?>
            <svg id="uuid-67eca691-fad9-4dbb-8a42-6bf39e0830b8" data-name="Capa 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 28 28">
            <defs><style>.uuid-7cc435b7-ba6c-40a3-af33-f3f74dcd5e16, .uuid-94213a7e-b161-4609-ad8f-10b2f930d26d {fill: #fff;}.uuid-94213a7e-b161-4609-ad8f-10b2f930d26d {fill-rule: evenodd;}.uuid-b5d097c8-88a9-4c72-9377-d504ca5b4c63 {fill: #d84740;}</style></defs>
            <circle class="uuid-b5d097c8-88a9-4c72-9377-d504ca5b4c63" cx="14" cy="14" r="14"/>
            <g><path class="uuid-94213a7e-b161-4609-ad8f-10b2f930d26d" d="m20.8,5.01l-.38-.37-.38.37-7.18,7.09c-.5.49-.78,1.16-.78,1.86v1.76h1.78c.71,0,1.38-.28,1.88-.77l7.18-7.09.38-.37-.38-.37-2.13-2.1Zm-1.75,2.47l1.38-1.36,1.38,1.36-1.38,1.36-1.38-1.36Zm-.75.74l-4.68,4.62c-.3.3-.47.7-.47,1.11v.71h.71c.42,0,.83-.17,1.13-.46l4.68-4.62-1.38-1.36Z"/>
            <polygon class="uuid-7cc435b7-ba6c-40a3-af33-f3f74dcd5e16" points="21.12 21.37 6.54 21.37 6.54 7.74 13.11 7.74 13.11 8.65 7.46 8.65 7.46 20.46 20.21 20.46 20.21 12.11 21.12 12.11 21.12 21.37"/></g>
            </svg>`;
        // >>> CAMBIO 2: Quita el listener del botón de cambiar <<<
        // changeBtn.addEventListener('click', handleChangeFotoClick); // COMENTADO o ELIMINADO
        actionsDiv.appendChild(changeBtn); // Se mantiene el botón visualmente

        // --- ***NUEVO: Botón Rotar Foto*** ---
        const rotateBtn = document.createElement('button');
        rotateBtn.type = 'button';
        rotateBtn.classList.add('btn-preview-action', 'btn-rotate-foto'); // Clase específica
        rotateBtn.title = 'Rotar foto 90°';
        rotateBtn.setAttribute('aria-label', `Rotar la foto ${filename} 90 grados`);
        rotateBtn.dataset.filename = filename;
        // Usamos el icono SVG proporcionado por el usuario
        rotateBtn.innerHTML = `
            <svg data-testid="geist-icon" height="12" stroke-linejoin="round" style="color:currentColor" viewBox="0 0 16 16" width="12"><path fill-rule="evenodd" clip-rule="evenodd" d="M8.00002 1.25C5.33749 1.25 3.02334 2.73677 1.84047 4.92183L1.48342 5.58138L2.80253 6.29548L3.15958 5.63592C4.09084 3.91566 5.90986 2.75 8.00002 2.75C10.4897 2.75 12.5941 4.40488 13.2713 6.67462H11.8243H11.0743V8.17462H11.8243H15.2489C15.6631 8.17462 15.9989 7.83883 15.9989 7.42462V4V3.25H14.4989V4V5.64468C13.4653 3.06882 10.9456 1.25 8.00002 1.25ZM1.50122 10.8555V12.5V13.25H0.0012207V12.5V9.07538C0.0012207 8.66117 0.337007 8.32538 0.751221 8.32538H4.17584H4.92584V9.82538H4.17584H2.72876C3.40596 12.0951 5.51032 13.75 8.00002 13.75C10.0799 13.75 11.8912 12.5958 12.8266 10.8895L13.1871 10.2318L14.5025 10.9529L14.142 11.6105C12.9539 13.7779 10.6494 15.25 8.00002 15.25C5.05453 15.25 2.53485 13.4313 1.50122 10.8555Z" fill="currentColor"></path></svg>`;
        rotateBtn.addEventListener('click', handleRotateFotoClick); // ¡Nuevo handler!
        actionsDiv.appendChild(rotateBtn);

        // --- Botón Eliminar Foto (Existente) ---
        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.classList.add('btn-preview-action', 'btn-delete-foto');
        deleteBtn.title = 'Eliminar foto';
        deleteBtn.setAttribute('aria-label', `Eliminar la foto ${filename}`);
        deleteBtn.dataset.filename = filename;
        deleteBtn.innerHTML = `
            <svg data-testid="geist-icon" height="12" stroke-linejoin="round" viewBox="0 0 16 16" width="12" style="color: currentcolor;" aria-hidden="true">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M12.4697 13.5303L13 14.0607L14.0607 13L13.5303 12.4697L9.06065 7.99999L13.5303 3.53032L14.0607 2.99999L13 1.93933L12.4697 2.46966L7.99999 6.93933L3.53032 2.46966L2.99999 1.93933L1.93933 2.99999L2.46966 3.53032L6.93933 7.99999L2.46966 12.4697L1.93933 13L2.99999 14.0607L3.53032 13.5303L7.99999 9.06065L12.4697 13.5303Z" fill="currentColor"></path>
            </svg>`;
        deleteBtn.addEventListener('click', eliminarFoto);
        actionsDiv.appendChild(deleteBtn);

        // --- Adjuntar Contenedor de Acciones ---
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

        // >>> CAMBIO 2: Añade listener a la imagen <<<
        const imgElement = div.querySelector('img');
        if (imgElement) {
            imgElement.dataset.filename = filename; // Guardamos filename en la imagen
            imgElement.style.cursor = 'pointer'; // Indicador visual
            imgElement.title = 'Haz clic para cambiar esta imagen'; // Tooltip
            imgElement.addEventListener('click', triggerChangeFotoFromImage); // Nuevo handler
        } else {
            console.warn('No se encontró <img> dentro del preview para añadir listener de cambio.');
        }
        // <<< FIN CAMBIO 2 >>>

        return div;
    }
    
    const selectPosicion = document.getElementById('select-posicion-foto');

    // >>> NUEVO: Función para manejar click en la imagen para cambiarla <<<
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

    // >>> NUEVO: Función para mostrar/ocultar y posicionar el select <<<
    function togglePositionSelect(event) {
        const button = event.currentTarget;
        const filename = button.dataset.filename;
        if (!selectPosicion || !filename) return;

        // Guardar el filename en el select para saber qué imagen mover luego
        selectPosicion.dataset.filename = filename;

        // Calcular posición (ejemplo básico: debajo del botón)
        const btnRect = button.getBoundingClientRect();
        const containerRect = listaFotosContainer.getBoundingClientRect(); // O el contenedor relativo

        // Ajusta estas coordenadas según tu layout y CSS
        selectPosicion.style.top = `${btnRect.bottom - containerRect.top + window.scrollY}px`;
        selectPosicion.style.left = `${btnRect.left - containerRect.left + window.scrollX}px`;

        // Obtener índice actual para preseleccionar (opcional pero útil)
        const allPreviews = Array.from(listaFotosContainer.querySelectorAll('.foto-subida-item'));
        const currentIndex = allPreviews.findIndex(item => item.dataset.filename === filename);
        if (currentIndex !== -1) {
            // Los índices son 0, 1, 2... los valores del select son 1, 2, 3
            selectPosicion.value = (currentIndex + 1).toString();
        }

        // Mostrar/ocultar
        selectPosicion.classList.toggle('visible'); // Usa tu clase para mostrar/ocultar

        // Ocultar si se hace clic fuera
        if (selectPosicion.classList.contains('visible')) {
            // Pequeño delay para evitar que el mismo click que abre, cierre.
            setTimeout(() => {
                document.addEventListener('click', hideSelectOnClickOutside, {once: true});
            }, 0);
        }
    }

    // >>> NUEVO: Helper para ocultar el select si se hace click fuera <<<
    function hideSelectOnClickOutside(event) {
        if (selectPosicion && !selectPosicion.contains(event.target) && !event.target.classList.contains('btn-toggle-position-select')) {
            selectPosicion.classList.remove('visible');
            delete selectPosicion.dataset.filename; // Limpiar filename guardado
        } else if (selectPosicion && selectPosicion.classList.contains('visible')) {
            // Si se hizo clic dentro del select o en otro botón, volver a añadir el listener
            // para el *próximo* click fuera.
            setTimeout(() => {
                document.addEventListener('click', hideSelectOnClickOutside, {once: true});
            }, 0);
        }
    }

    // >>> NUEVO: Función para manejar el cambio en el select de posición <<<
    function handlePositionChange(event) {
        const select = event.currentTarget;
        const filename = select.dataset.filename;
        const newPositionIndex = parseInt(select.value, 10) - 1; // value es 1, 2, 3 -> index 0, 1, 2

        if (filename === undefined || isNaN(newPositionIndex) || newPositionIndex < 0 || newPositionIndex >= 3) {
            console.error('Error al obtener datos para mover la imagen desde el select.');
            select.classList.remove('visible');
            delete select.dataset.filename;
            return;
        }

        const currentPreviewItem = listaFotosContainer.querySelector(`.foto-subida-item[data-filename="${filename}"]`);
        const currentHiddenInput = hiddenPhotoInputsContainer.querySelector(`input[name="photo_name[]"][value="${filename}"]`);

        if (!currentPreviewItem || !currentHiddenInput) {
            console.error(`No se encontró el preview o input oculto para ${filename}`);
            select.classList.remove('visible');
            delete select.dataset.filename;
            return;
        }

        // Obtener todos los items actuales (previews e inputs)
        const allPreviews = Array.from(listaFotosContainer.querySelectorAll('.foto-subida-item'));
        const allHiddenInputs = Array.from(hiddenPhotoInputsContainer.querySelectorAll('input[name="photo_name[]"]'));

        // Encontrar el preview/input que está actualmente en la posición de destino (si existe)
        const targetPreviewSibling = allPreviews[newPositionIndex];
        const targetInputSibling = allHiddenInputs[newPositionIndex];

        // Mover el elemento
        // 1. Mover Preview
        if (targetPreviewSibling && targetPreviewSibling !== currentPreviewItem) {
            // Si hay un elemento en la posición destino, insertar ANTES de él
            listaFotosContainer.insertBefore(currentPreviewItem, targetPreviewSibling);
        } else if (!targetPreviewSibling) {
            // Si no hay elemento (o estamos moviendo al final), simplemente añadir al final
            listaFotosContainer.appendChild(currentPreviewItem);
            // Nota: Si mueves el último a una posición anterior, insertBefore se encarga.
            // Este append asegura que si mueves a una posición más allá del final actual, quede al final.
        }
        // 2. Mover Input Oculto (de forma similar)
        if (targetInputSibling && targetInputSibling !== currentHiddenInput) {
            hiddenPhotoInputsContainer.insertBefore(currentHiddenInput, targetInputSibling);
        } else if (!targetInputSibling) {
            hiddenPhotoInputsContainer.appendChild(currentHiddenInput);
        }

        // Ocultar el select y limpiar
        select.classList.remove('visible');
        delete select.dataset.filename;

        // Actualizar estado de botones (especialmente si moviste el primero o último)
        updateArrowButtonStates(); // Asegúrate que esta función sigue existiendo y funciona con 3 elementos max.
    }

    // >>> NUEVO: Añadir listeners (preferiblemente en la función inicializar o agregarListeners) <<<
    function agregarListenersNuevos() {
        // Listener DELEGADO para los botones de mover/seleccionar posición
        if (listaFotosContainer) {
            listaFotosContainer.addEventListener('click', function (event) {
                const toggleButton = event.target.closest('.btn-toggle-position-select');
                if (toggleButton) {
                    event.preventDefault(); // Prevenir cualquier acción default del botón
                    togglePositionSelect({currentTarget: toggleButton}); // Llama a la función que muestra el select
                }
            });
        }

        // Listener para el cambio en el select de posición
        if (selectPosicion) {
            selectPosicion.addEventListener('change', handlePositionChange);
        }
    }

    // aqui necesito que cuando este a 90 grados y 270 la imagen tenga max-width: 80px;, eso es todo
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
            console.log(`Aplicado max-width: 80px a ${filename} (Rotación: ${newRotation})`);
        } else {
            // Si la rotación es 0 o 180 (orientación horizontal), quitamos el max-width
            // Establecer a '' elimina el estilo inline específico de max-width
            imgElement.style.maxWidth = '';
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
