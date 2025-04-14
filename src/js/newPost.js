(function () {
    'use strict';

    // --- Configuración y Cache de DOM ---
    const form = document.getElementById('form-nuevo-anuncio');
    if (!form) {
        console.error('Formulario #form-nuevo-anuncio no encontrado.');
        return;
    }

    const etapas = Array.from(form.querySelectorAll('.etapa'));
    const btnSiguiente = form.querySelectorAll('.btn-siguiente');
    const btnAnterior = form.querySelectorAll('.btn-anterior');
    const btnFinalizar = document.getElementById('btn-finalizar');

    // Inputs/Selects importantes para validar y mapear
    const tipoUsuarioRadios = form.querySelectorAll('input[name="tipo_usuario"]');
    const planRadios = form.querySelectorAll('input[name="plan"]');
    const nombreInput = form.querySelector('#nombre'); // ID del nuevo form
    const categoriaSelect = form.querySelector('#categoria'); // ID del nuevo form
    const provinciaSelect = form.querySelector('#provincia'); // ID del nuevo form
    const tituloInput = form.querySelector('#titulo_anuncio'); // ID del nuevo form
    const descripcionTextarea = form.querySelector('#descripcion'); // ID del nuevo form
    const serviciosCheckboxes = form.querySelectorAll('input[name="servicios[]"]');
    const fotosInput = form.querySelector('#campo-subir-foto');
    const listaFotosContainer = form.querySelector('#lista-fotos-subidas');
    const horarioCheckboxes = form.querySelectorAll('.check-dia input[type="checkbox"]');
    const telefonoInput = form.querySelector('#telefono'); // ID del nuevo form
    const whatsappCheckbox = form.querySelector('input[name="whatsapp"]'); // name mapeado
    const idioma1Select = form.querySelector('#idioma_1');
    const idioma2Select = form.querySelector('#idioma_2');
    const salidasSelect = form.querySelector('#realiza_salidas'); // ID del nuevo form (name='out')
    const emailInput = form.querySelector('#email'); // ID del nuevo form
    const terminosCheckbox = form.querySelector('#terminos'); // ID del nuevo form (name='terminos')
    const notificacionesCheckbox = form.querySelector('#notifications'); // name mapeado

    // Contadores
    const contTitulo = document.getElementById('cont-titulo');
    const contDesc = document.getElementById('cont-desc');

    // Campos ocultos para mapeo
    const hiddenSellerType = form.querySelector('#hidden_seller_type');
    const hiddenDis = form.querySelector('#hidden_dis');
    const hiddenHorarioInicio = form.querySelector('#hidden_horario_inicio');
    const hiddenHorarioFinal = form.querySelector('#hidden_horario_final');
    const hiddenLang1 = form.querySelector('#hidden_lang_1');
    const hiddenLang2 = form.querySelector('#hidden_lang_2');
    const hiddenPhotoInputsContainer = form.querySelector('#hidden-photo-inputs');
    // ELIMINADO: Ya no necesitamos referenciar el input de recaptcha response aquí
    // const recaptchaResponseInput = form.querySelector('#g-recaptcha-response');

    let etapaActualIndex = 0;

    // --- Variables Globales (Asegúrate que estén definidas en tu PHP si las necesitas) ---
    // Estas variables se usan en la subida de fotos. Deben definirse ANTES de que este script se ejecute.
    // Ejemplo en PHP: echo "<script>const maxPhotosAllowed = ".($DATAJSON['max_photos'] ?? 3).";</script>";
    // Ejemplo en PHP: echo "<script>const uploadUrl = '/sc-includes/php/ajax/upload_picture.php';</script>";
    // QUITAR: Ya no se necesita siteKey para recaptcha
    // Ejemplo en PHP: echo "<script>const siteKey = '".SITE_KEY."';</script>";

    // --- Inicialización ---
    function inicializar() {
        etapas.forEach((etapa, index) => {
            if (etapa.classList.contains('activa')) {
                etapaActualIndex = index;
            } else {
                etapa.classList.add('oculto');
            }
        });
        agregarListeners();
        actualizarContadores(); // Inicializa al cargar por si hay datos repoblados
        inicializarHorarioVisual();
        actualizarMarcadoVisualRadios(tipoUsuarioRadios);
        actualizarMarcadoVisualRadios(planRadios);
        console.log('Formulario multi-etapa inicializado.');
    }

    // --- Listeners ---
    function agregarListeners() {
        btnSiguiente.forEach(btn => btn.addEventListener('click', irASiguienteEtapa));
        btnAnterior.forEach(btn => btn.addEventListener('click', irAEtapaAnterior));

        // Listeners para validación en tiempo real o actualizaciones
        if (tituloInput && contTitulo) tituloInput.addEventListener('input', actualizarContadores);
        if (descripcionTextarea && contDesc) descripcionTextarea.addEventListener('input', actualizarContadores);
        if (fotosInput) fotosInput.addEventListener('change', manejarSeleccionFotos);

        horarioCheckboxes.forEach(check => {
            check.addEventListener('change', toggleHorasDia);
        });

        // Listener para el envío final (YA NO maneja reCAPTCHA directamente aquí)
        form.addEventListener('submit', manejarEnvioFinal);


        tipoUsuarioRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                // 1. Actualiza el aspecto visual (como antes)
                actualizarMarcadoVisualRadios(tipoUsuarioRadios);

                // 2. Verifica si estamos en la etapa correcta y avanza
                const etapaActual = etapas[etapaActualIndex];
                if (etapaActual && etapaActual.id === 'etapa-tipo-usuario' && radio.checked) {
                    console.log('Tipo de usuario seleccionado en la etapa correcta, intentando avanzar...');
                    // Llama a la función refactorizada para validar y cambiar de etapa
                    avanzarSiValido();
                }
            });
        });

        planRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                actualizarMarcadoVisualRadios(planRadios);
            });
        });
    }

    // --- Navegación ---

    
    function avanzarSiValido() {
        // Esta función contiene la lógica principal que estaba en irASiguienteEtapa
        if (validarEtapaActual()) {
            actualizarCamposOcultosEtapaActual(); // Actualiza antes de cambiar
            if (etapaActualIndex < etapas.length - 1) {
                cambiarEtapa(etapaActualIndex + 1);
            } else {
                // Esto no debería pasar desde la primera etapa, pero es bueno tenerlo
                console.warn('Se intentó ir más allá de la última etapa.');
            }
        } else {
            console.warn(`Validación fallida en etapa ${etapaActualIndex}`);
            // Mantenemos el scroll por si acaso, aunque aquí la validación debería pasar siempre
            window.scrollTo({top: form.offsetTop - 20, behavior: 'smooth'});
        }
    }

    function irASiguienteEtapa(event) {
        // Solo previene el default y llama a la función refactorizada
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
            console.log(`Cambiado a etapa ${etapaActualIndex}`);
        }
    }

    // --- Validación ---
    function validarEtapaActual() {
        const etapaActual = etapas[etapaActualIndex];
        limpiarErroresEtapa(etapaActual);
        let esValido = true;
        const inputsInvalidos = []; // Para foco

        console.log(`Validando etapa: ${etapaActual.id}`);

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
                if (!validarCampo(planRadios[0]?.closest('.lista-opciones'), '#error-plan', planSeleccionado, 'Debes seleccionar un plan.')) {
                    esValido = false;
                    inputsInvalidos.push(planRadios[0]);
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
                // TODO: Añadir validación palabras prohibidas para título si es necesario

                const descVal = descripcionTextarea?.value.trim() || '';
                if (!validarCampo(descripcionTextarea, '#error-descripcion', descVal && descVal.length >= 30 && descVal.length <= 500, `La descripción es obligatoria (entre 30 y 500 caracteres). Actual: ${descVal.length}`)) {
                    esValido = false;
                    inputsInvalidos.push(descripcionTextarea);
                }
                // TODO: Añadir validación palabras prohibidas para descripción si es necesario

                const serviciosSeleccionados = form.querySelectorAll('input[name="servicios[]"]:checked').length;
                if (!validarCampo(serviciosCheckboxes[0]?.closest('.grupo-checkboxes'), '#error-servicios', serviciosSeleccionados > 0, 'Debes seleccionar al menos un servicio.')) {
                    esValido = false;
                    inputsInvalidos.push(serviciosCheckboxes[0]?.closest('.grupo-checkboxes'));
                }
                break

            case 'etapa-anuncio':
                

                const fotosSubidas = hiddenPhotoInputsContainer.querySelectorAll('input[name="photo_name[]"]').length;
                if (!validarCampo(listaFotosContainer, '#error-fotos', fotosSubidas > 0, 'Debes subir al menos una foto.')) {
                    esValido = false;
                    inputsInvalidos.push(fotosInput);
                }

                if (!validarHorarioSeleccionado()) {
                    esValido = false;
                    inputsInvalidos.push(form.querySelector('.horario-semanal'));
                } // La función validarHorarioSeleccionado muestra el error específico

                const telefonoVal = telefonoInput?.value.replace(/\D/g, '') || ''; // Solo números
                if (!validarCampo(telefonoInput, '#error-telefono', /^[0-9]{9,15}$/.test(telefonoVal), 'Introduce un teléfono válido (9-15 dígitos).')) {
                    esValido = false;
                    inputsInvalidos.push(telefonoInput);
                }

                if (!validarCampo(salidasSelect, '#error-salidas', salidasSelect?.value !== '', 'Debes indicar si realizas salidas.')) {
                    esValido = false;
                    inputsInvalidos.push(salidasSelect);
                }

                // Validar email solo si está visible (usuario no logueado)
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
            inputsInvalidos[0]?.focus(); // Intenta poner el foco en el primer campo inválido
        }

        return esValido;
    }

    function validarCampo(elemento, errorSelector, condition, message) {
        const errorMsgElement = form.querySelector(errorSelector);
        if (!errorMsgElement) {
            console.warn(`Elemento de error no encontrado: ${errorSelector}`);
            return condition; // No podemos mostrar error, pero respetamos la condición
        }

        const campo = elemento?.nodeName === 'INPUT' || elemento?.nodeName === 'SELECT' || elemento?.nodeName === 'TEXTAREA' ? elemento : elemento?.querySelector('input, select, textarea'); // Busca dentro del contenedor si se pasó un div

        if (!condition) {
            errorMsgElement.textContent = message;
            errorMsgElement.classList.remove('oculto');
            campo?.classList.add('invalido');
            elemento?.classList.add('invalido'); // Añade al contenedor también si existe
            return false;
        } else {
            errorMsgElement.classList.add('oculto');
            campo?.classList.remove('invalido');
            elemento?.classList.remove('invalido');
            return true;
        }
    }

    function validarHorarioSeleccionado() {
        const diasActivos = form.querySelectorAll('.check-dia input[type="checkbox"]:checked');
        if (diasActivos.length === 0) {
            return validarCampo(form.querySelector('.horario-semanal'), '#error-horario', false, 'Debes marcar al menos un día de disponibilidad.');
        }
        // Podría añadirse validación de que las horas inicio/fin sean lógicas si se seleccionan
        return validarCampo(form.querySelector('.horario-semanal'), '#error-horario', true, ''); // Limpia error si hay días
    }

    function limpiarErroresEtapa(etapa) {
        etapa.querySelectorAll('.error-msg').forEach(msg => msg.classList.add('oculto'));
        etapa.querySelectorAll('.invalido').forEach(el => el.classList.remove('invalido'));
    }

    // --- Actualización de Campos Ocultos (Mapeo) ---
    function actualizarCamposOcultosEtapaActual() {
        const etapaId = etapas[etapaActualIndex]?.id;
        console.log(`Actualizando campos ocultos para etapa: ${etapaId}`);

        switch (etapaId) {
            case 'etapa-tipo-usuario':
                actualizarSellerTypeOculto();
                break;
            case 'etapa-anuncio':
                actualizarHorarioOculto();
                actualizarIdiomasOculto();
                // Whatsapp y Out se actualizan directamente por su 'name'
                break;
            // No hay mapeos específicos en otras etapas por ahora
        }
    }

    function actualizarSellerTypeOculto() {
        const seleccionado = form.querySelector('input[name="tipo_usuario"]:checked');
        if (seleccionado && hiddenSellerType) {
            // Asegúrate que el 'value' del radio coincida con lo esperado por el backend (1, 2, 3)
            hiddenSellerType.value = seleccionado.value === 'visitante' ? '' : seleccionado.value;
            console.log(`hidden_seller_type actualizado a: ${hiddenSellerType.value}`);
        }
    }

    function actualizarHorarioOculto() {
        let diasSeleccionados = [];
        let horarios = {inicio: '23:59', fin: '00:00'};
        let primerDia = -1,
            ultimoDia = -1;
        const diasOrden = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];

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
            hiddenDis.value = '0'; // Ninguno seleccionado
            hiddenHorarioInicio.value = '00:00';
            hiddenHorarioFinal.value = '00:00';
            console.log('Horario oculto: Ningún día seleccionado.');
            return;
        }

        // Mapeo simplificado para 'dis' (Puede necesitar ajuste fino)
        let valorDis = '0';
        if (diasSeleccionados.length === 7) valorDis = '1'; // Todos los días
        else if (diasSeleccionados.length === 5 && primerDia === 0 && ultimoDia === 4) valorDis = '2'; // L-V
        else if (diasSeleccionados.length === 6 && primerDia === 0 && ultimoDia === 5) valorDis = '3'; // L-S
        else if (diasSeleccionados.length === 2 && primerDia === 5 && ultimoDia === 6) valorDis = '4'; // S-D
        // Otros casos podrían mapear a '1' (Todos los días) o requerir un valor 'Otro' si existiera
        // AJUSTE IMPORTANTE: Si no coincide con L-V, L-S, S-D o Todos, podríamos asignar '1' (Todos) o un valor específico como '5' (Otro/Personalizado) si el backend lo soporta. Usaremos '1' como fallback conservador.
        else valorDis = '1'; // Fallback a 'Todos los días' si no es un patrón reconocido

        hiddenDis.value = valorDis;
        hiddenHorarioInicio.value = horarios.inicio;
        hiddenHorarioFinal.value = horarios.fin;

        console.log(`hidden_dis actualizado a: ${hiddenDis.value}`);
        console.log(`hidden_horario_inicio actualizado a: ${hiddenHorarioInicio.value}`);
        console.log(`hidden_horario_final actualizado a: ${hiddenHorarioFinal.value}`);
    }

    function actualizarIdiomasOculto() {
        if (idioma1Select && hiddenLang1) {
            // Asume que el 'value' del select es el código esperado (e.g., 'es', 'en')
            // El backend antiguo esperaba números (1, 2, 3...). Se necesita un mapeo aquí o ajustar el backend/selects.
            // Usaremos el valor del select por ahora. ¡REVISAR ESTO!
            hiddenLang1.value = idioma1Select.value;
            console.log(`hidden_lang_1 actualizado a: ${hiddenLang1.value}`);
        }
        if (idioma2Select && hiddenLang2) {
            hiddenLang2.value = idioma2Select.value;
            console.log(`hidden_lang_2 actualizado a: ${hiddenLang2.value}`);
        }
    }

    // --- Funciones Auxiliares ---
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
            toggleHorasDia({target: check}); // Llama a la función para establecer el estado inicial
        });
    }

    function toggleHorasDia(event) {
        const checkbox = event.target;
        const horasDiv = checkbox.closest('.dia-horario').querySelector('.horas-dia');
        if (horasDiv) {
            horasDiv.classList.toggle('oculto', !checkbox.checked);
        }
    }

    // --- Lógica de Subida de Fotos ---
    function manejarSeleccionFotos(event) {
        const files = event.target.files;
        // Usa la variable global definida desde PHP
        const maxPhotos = typeof maxPhotosAllowed !== 'undefined' ? maxPhotosAllowed : 3;
        const currentPhotosCount = hiddenPhotoInputsContainer.querySelectorAll('input[name="photo_name[]"]').length;
        let addedCount = 0;

        limpiarErroresEtapa(etapas[etapaActualIndex]); // Limpia errores previos de fotos

        if (currentPhotosCount >= maxPhotos) {
            mostrarErrorFotos(`Ya has alcanzado el límite de ${maxPhotos} fotos.`);
            event.target.value = null; // Limpia selección
            return;
        }

        for (let i = 0; i < files.length; i++) {
            if (currentPhotosCount + addedCount >= maxPhotos) {
                mostrarErrorFotos(`Solo puedes añadir ${maxPhotos - currentPhotosCount} foto(s) más.`);
                break;
            }
            const file = files[i];

            // Validación básica de tipo y tamaño (ejemplo 2MB)
            if (!file.type.match('image/jpeg') && !file.type.match('image/png')) {
                mostrarErrorFotos(`Archivo "${file.name}" no es JPG o PNG.`);
                continue;
            }
            if (file.size > 2 * 1024 * 1024) {
                // 2 MB
                mostrarErrorFotos(`Archivo "${file.name}" excede los 2MB.`);
                continue;
            }

            subirFotoAjax(file);
            addedCount++;
        }
        event.target.value = null; // Limpia selección para permitir subir el mismo archivo si se borra
    }

    function subirFotoAjax(file) {
        const loadingIndicator = crearLoadingPreview(file.name);
        listaFotosContainer.appendChild(loadingIndicator);

        const formData = new FormData();
        formData.append('userImage', file); // Nombre esperado por upload_picture.php

        // Usa la URL global definida desde PHP
        const urlSubida = typeof uploadUrl !== 'undefined' ? uploadUrl : 'sc-includes/php/ajax/upload_picture.php';

        fetch(urlSubida, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP ${response.status}`);
                }
                return response.text(); // Asume respuesta HTML como en script antiguo
            })
            .then(html => {
                loadingIndicator.remove(); // Quita el indicador de carga
                const photoData = parsePhotoUploadResponse(html);

                if (photoData.filename) {
                    const previewElement = crearPreviewFoto(photoData.previewHtml, photoData.filename);
                    listaFotosContainer.appendChild(previewElement);

                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'photo_name[]';
                    hiddenInput.value = photoData.filename;
                    hiddenPhotoInputsContainer.appendChild(hiddenInput);

                    // Opcional: Inicializar sortable aquí si se usa jQuery UI
                    // $(listaFotosContainer).sortable('refresh');

                    console.log(`Foto subida: ${photoData.filename}`);
                    // Limpia error genérico si todo va bien
                    validarCampo(listaFotosContainer, '#error-fotos', true, '');
                } else {
                    mostrarErrorFotos(`Error procesando la respuesta del servidor para "${file.name}".`);
                    console.error('Respuesta inválida:', html);
                }
            })
            .catch(error => {
                loadingIndicator.remove();
                mostrarErrorFotos(`Error subiendo "${file.name}": ${error.message}`);
                console.error('Error en fetch:', error);
            });
    }

    function parsePhotoUploadResponse(html) {
        // Intenta extraer el nombre del archivo del input oculto dentro del HTML devuelto
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        // Busca un input con name="photo_name[]" y obtiene su value
        const hiddenInput = tempDiv.querySelector('input[name="photo_name[]"]');
        const filename = hiddenInput ? hiddenInput.value : null;

        // Devuelve el nombre y el HTML original para la preview
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
        // Inserta el HTML recibido, que puede contener la img y otros elementos
        div.innerHTML = htmlContent;

        // Asegúrate de que no haya un input 'photo_name[]' duplicado en la preview
        const hiddenInPreview = div.querySelector('input[name="photo_name[]"]');
        hiddenInPreview?.remove();

        // Añadir botón de eliminar
        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.textContent = 'Eliminar'; // O un icono 'X'
        deleteBtn.classList.add('btn-eliminar-foto');
        deleteBtn.dataset.filename = filename;
        deleteBtn.addEventListener('click', eliminarFoto);

        // Intenta añadir el botón dentro de alguna estructura existente o al final
        const optionsDiv = div.querySelector('.photos_options'); // Si existe del HTML antiguo
        if (optionsDiv) {
            optionsDiv.appendChild(deleteBtn);
        } else {
            div.appendChild(deleteBtn);
        }

        // TODO: Re-añadir lógica de rotación si es necesaria, adaptando los botones/listeners

        return div;
    }

    function eliminarFoto(event) {
        const filename = event.target.dataset.filename;
        if (!filename) return;

        // 1. Eliminar preview
        event.target.closest('.foto-subida-item')?.remove();

        // 2. Eliminar input oculto correspondiente
        const hiddenInput = hiddenPhotoInputsContainer.querySelector(`input[name="photo_name[]"][value="${filename}"]`);
        hiddenInput?.remove();

        console.log(`Foto eliminada (cliente): ${filename}`);

        // 3. Opcional: Llamada AJAX para eliminar en servidor
        // fetch(`ruta/a/delete_picture.php?filename=${filename}`)...
    }

    function mostrarErrorFotos(mensaje) {
        const errorDiv = form.querySelector('#error-fotos');
        if (errorDiv) {
            errorDiv.textContent = mensaje;
            errorDiv.classList.remove('oculto');
        } else {
            alert(mensaje); // Fallback
        }
    }

    // --- Envío Final (Modificado para quitar reCAPTCHA) ---
    function manejarEnvioFinal(event) {
        event.preventDefault(); // Detiene el envío normal siempre

        console.log('Intentando enviar formulario...');

        // 1. Validar TODAS las etapas
        if (!validarFormularioCompleto()) {
            alert('Por favor, revisa el formulario. Hay errores o campos incompletos en alguna de las etapas.');
            irAPrimeraEtapaConError();
            console.warn('Validación fallida. Envío cancelado.'); // Log añadido
            return; // Detiene el proceso si hay errores
        }

        // 2. Asegurarse que todos los mapeos ocultos estén actualizados
        console.log('Actualizando campos ocultos finales...');
        actualizarSellerTypeOculto();
        actualizarHorarioOculto();
        actualizarIdiomasOculto();

        // <<<--- AÑADIR LOGS AQUÍ --->>>
        console.log('--- Verificando datos antes de submit ---');
        console.log('Token:', form.querySelector('#token')?.value);
        console.log('Order:', form.querySelector('#new_order')?.value);
        console.log('Seller Type (Hidden):', form.querySelector('#hidden_seller_type')?.value);
        console.log('Dis (Hidden):', form.querySelector('#hidden_dis')?.value);
        console.log('Horario Inicio (Hidden):', form.querySelector('#hidden_horario_inicio')?.value);
        console.log('Horario Final (Hidden):', form.querySelector('#hidden_horario_final')?.value);
        console.log('Lang 1 (Hidden):', form.querySelector('#hidden_lang_1')?.value);
        console.log('Lang 2 (Hidden):', form.querySelector('#hidden_lang_2')?.value);
        console.log(
            'Fotos (Hidden Inputs):',
            Array.from(hiddenPhotoInputsContainer.querySelectorAll('input[name="photo_name[]"]')).map(input => input.value)
        );
        // Puedes añadir más campos que consideres críticos (category, region, tit, text, phone, email, out, terminos, notifications...)
        console.log('Categoría:', form.querySelector('#categoria')?.value);
        console.log('Título:', form.querySelector('#titulo_anuncio')?.value);
        console.log('Teléfono:', form.querySelector('#telefono')?.value);
        console.log('Email:', form.querySelector('#email')?.value);
        console.log('Términos:', form.querySelector('#terminos')?.checked);
        console.log('---------------------------------------');
        // Para ver TODOS los datos que se enviarían:
        const formDataForLog = new FormData(form);
        console.log('FormData a enviar:');
        for (let [key, value] of formDataForLog.entries()) {
            console.log(`${key}: ${value}`);
        }
        console.log('---------------------------------------');

        console.log('Validación completa OK. Enviando formulario directamente vía form.submit()...');

        // 4. Enviar el formulario directamente
        form.submit(); // ESTA LÍNEA CAUSA EL REINICIO DE PÁGINA
    }
    function validarFormularioCompleto() {
        let todoValido = true;
        for (let i = 0; i < etapas.length; i++) {
            // Guarda el índice actual y lo restaura después de validar cada etapa
            const originalIndex = etapaActualIndex;
            etapaActualIndex = i; // Establece temporalmente para validar la etapa correcta
            if (!validarEtapaActual()) {
                todoValido = false;
                // Podrías detenerte en el primer error o seguir para mostrarlos todos
                // break;
            }
            etapaActualIndex = originalIndex; // Restaura el índice visual
        }
        // Restablece el estado visual correcto de los errores para la etapa actual (si no es válida)
        if (!todoValido) {
            validarEtapaActual(); // Re-valida la etapa actual para mostrar sus errores si es la que falló
        }
        return todoValido;
    }


    function irAPrimeraEtapaConError() {
        for (let i = 0; i < etapas.length; i++) {
            const etapa = etapas[i];
            limpiarErroresEtapa(etapa); // Limpia para re-validar
            const originalIndex = etapaActualIndex;
            etapaActualIndex = i;
            const esValida = validarEtapaActual(); // Re-valida y muestra errores
            etapaActualIndex = originalIndex; // Restaura antes de cambiar

            if (!esValida) {
                console.log(`Encontrado error en etapa ${i}, cambiando visualización.`);
                cambiarEtapa(i); // Cambia a la primera etapa con error
                break;
            }
        }
    }

    function actualizarMarcadoVisualRadios(radiosNodeList) {
        radiosNodeList.forEach(radio => {
            // Encuentra el label padre para este radio específico
            const labelPadre = radio.closest('label.opcion-radio');
            if (labelPadre) {
                // Añade o quita la clase 'marcado' basado en si el radio está chequeado
                if (radio.checked) {
                    labelPadre.classList.add('marcado');
                } else {
                    labelPadre.classList.remove('marcado');
                }
            } else {
                // Opcional: Advertencia si no se encuentra el label esperado
                console.warn('No se encontró label.opcion-radio para el radio:', radio);
            }
        });
    }

    // --- Ejecutar Inicialización ---
    // Asegúrate que el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializar);
    } else {
        inicializar();
    }
})();
