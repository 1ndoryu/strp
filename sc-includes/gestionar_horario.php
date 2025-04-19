<?php
require_once 'html/iconos.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>

    <!--[if lt IE 9]>
<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title><?= $TITLE_; ?></title>
    <?php if (DEBUG): ?>
        <meta name="robots" content="noindex,nofollow">
    <?php endif ?>
    <meta name="title" content="<?= $TITLE_; ?>" />
    <meta name="description" content="<?= $DESCRIPTION_; ?>" />
    <meta name="keywords" content="<?= $KEYWORDS_; ?>">
    <meta property="og:title" content="<?= $TITLE_; ?>">
    <meta property="og:description" content="<?= $DESCRIPTION_; ?>">
    <meta property="og:site_name" content="<?= getConfParam('SITE_NAME'); ?>">
    <meta property="og:url" content="<?= trim(getConfParam('SITE_URL'), '/'); ?><?= $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:type" content="<?= $TYPE_SITE; ?>">



    <link rel="preload" href="src/css/select2.min.css" rel="stylesheet" as="style" onload="this.onload=null;this.rel='stylesheet'" />
    <noscript>
        <link rel="stylesheet" href="src/css/select2.min.css">
    </noscript>
    <!-- <link rel="stylesheet" href="src/css/all.min.css"> -->
    <link rel="stylesheet" href="src/css/webfonts/fuentes.css">



    <!--- css -->
    <!-- <link rel="preload" href="src/css/bootstrap.min.css" rel="stylesheet" as="style" onload="this.onload=null;this.rel='stylesheet'" />
<noscript><link rel="stylesheet" href="src/css/bootstrap.min.css"></noscript> -->

    <link rel="stylesheet" href="src/css/bootstrap.min.css">

    <!-- <script src="src/js/glide.min.js"></script> -->


    <!-- js -->
    <script defer src="src/js/splide.min.js"></script>
    <!-- <script src="src/js/jquery.min.js"></script> -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script defer src="src/js/jquery-ui.min.js"></script>
    <script defer src="src/js/bootstrap.min.js"></script>

    <!-- <link rel="stylesheet" type="text/css" href="src/css/cookies.css"> -->
    <!-- <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&display=swap"> -->
    <!-- <link rel="alternate" type="application/rss+xml" title="<?= $TITLE_; ?>" href="<?= getConfParam('SITE_URL'); ?>feed/"> -->
    <!--<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>-->

    <link rel="stylesheet" href="src/css/style.css?v=0.6">
    <link rel="stylesheet" href="src/css/main.css?=v=0.5">
    <link rel="stylesheet" type="text/css" href="src/css/w-formPost.css?v=0.4">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <link rel="preload" href="src/css/item.css" rel="stylesheet" as="style" onload="this.onload=null;this.rel='stylesheet'" />
    <noscript>
        <link rel="stylesheet" href="src/css/item.css">
    </noscript>

    <link rel="preload" href="src/css/splide.min.css" rel="stylesheet" as="style" onload="this.onload=null;this.rel='stylesheet'" />
    <noscript>
        <link rel="stylesheet" href="src/css/splide.min.css">
    </noscript>


    <script defer src="src/js/select2.js"></script>



    <script src="src/js/main.js?v=0.2" defer></script>
    <!--<link rel="stylesheet" href="node_modules/@glidejs/glide/dist/css/glide.core.min.css">-->
    <!--<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>-->
    <!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@glidejs/glide">-->
    <style>
        .splide {
            visibility: hidden;
            position: relative;
        }
    </style>

<body>

<div class="contenedor-principal">
    <h2>Configura tu Disponibilidad</h2>
    <p>Marca los días que estás disponible y selecciona las horas. Pulsa Guardar cuando termines.</p>

    <div class="horario-semanal" id="contenedor-horario"> <?php /* Ya no necesita la clase oculto aquí */ ?>
        <?php
        $dias = ['lunes' => 'Lunes', 'martes' => 'Martes', 'miercoles' => 'Miércoles', 'jueves' => 'Jueves', 'viernes' => 'Viernes', 'sabado' => 'Sábado', 'domingo' => 'Domingo'];
        foreach ($dias as $key => $nombre) {
        ?>
            <div class="dia-horario" id="horario-<?= $key ?>" data-dia="<?= $key ?>">
                <span class="nombre-dia"><?= $nombre ?>:</span>
                <button type="button" class="btn-dia-estado no-disponible" data-dia="<?= $key ?>">No disponible</button>
                <div class="horas-dia oculto">
                    <div class="inputhorahorario">
                        <label class="iconohorario"><?php echo $GLOBALS['sol']; ?></label>
                        <select name="horario_dia[<?= $key ?>][inicio]" class="frm-campo frm-select corto" disabled>
                            <?php for ($h = 0; $h < 24; $h++) {
                                $hora = sprintf('%02d', $h);
                                echo "<option value='{$hora}:00'>{$hora}:00</option><option value='{$hora}:30'>{$hora}:30</option>";
                            } ?>
                        </select>
                    </div>
                    <div class="inputhorahorario">
                        <label class="iconohorario"><?php echo $GLOBALS['luna']; ?></label>
                        <select name="horario_dia[<?= $key ?>][fin]" class="frm-campo frm-select corto" disabled>
                            <?php for ($h = 0; $h < 24; $h++) {
                                $hora = sprintf('%02d', $h);
                                $selected_fin = ($h == 18) ? 'selected' : ''; // Mantener el default
                                echo "<option value='{$hora}:00' " . (($h == 18 && !$selected_fin) ? 'selected' : '') . ">{$hora}:00</option><option value='{$hora}:30' " . (($h == 18) ? 'selected' : '') . ">{$hora}:30</option>";
                            } ?>
                        </select>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="error-msg oculto" id="error-horario-guardar">Debes marcar al menos un día como disponible.</div>
    </div>

    <div id="mensaje-estado" class="mensaje-estado oculto"></div>

    <button type="button" id="btn-guardar-horario" class="boton-guardar">Guardar Horario y Cerrar</button>

</div>

<script>
(function() {
    'use strict';

    const contenedorHorario = document.getElementById('contenedor-horario');
    const diaEstadoBotones = contenedorHorario.querySelectorAll('.btn-dia-estado');
    const btnGuardar = document.getElementById('btn-guardar-horario');
    const errorMsgDiv = document.getElementById('error-horario-guardar');
    const mensajeEstadoDiv = document.getElementById('mensaje-estado');
    const HORARIO_STORAGE_KEY = 'userPendingSchedule'; // Misma clave que en la página principal

    // --- Función para cambiar estado del día (Copiada y pegada, o refactorizada) ---
    function toggleDiaEstado(event) {
        const boton = event.currentTarget;
        const diaHorarioDiv = boton.closest('.dia-horario');
        const horasDiv = diaHorarioDiv.querySelector('.horas-dia');
        const selectsHora = horasDiv.querySelectorAll('select');
        const esDisponibleAhora = boton.classList.contains('disponible');

        if (esDisponibleAhora) {
            boton.textContent = 'No disponible';
            boton.classList.remove('disponible');
            boton.classList.add('no-disponible');
            horasDiv.classList.add('oculto');
            selectsHora.forEach(select => (select.disabled = true));
            diaHorarioDiv.classList.remove('dia-activo');
        } else {
            boton.textContent = 'Disponible';
            boton.classList.remove('no-disponible');
            boton.classList.add('disponible');
            horasDiv.classList.remove('oculto');
            selectsHora.forEach(select => (select.disabled = false));
             // Pre-seleccionar horas por defecto si se desea al activar
             // const inicioSelect = horasDiv.querySelector('select[name$="[inicio]"]');
             // const finSelect = horasDiv.querySelector('select[name$="[fin]"]');
             // if (!inicioSelect.value) inicioSelect.value = '09:00'; // Ejemplo
             // if (!finSelect.value || finSelect.value < inicioSelect.value) finSelect.value = '18:00'; // Ejemplo
            diaHorarioDiv.classList.add('dia-activo');
        }
        // Limpiar error al interactuar
        errorMsgDiv.classList.add('oculto');
        mensajeEstadoDiv.classList.add('oculto'); // Ocultar mensajes anteriores
        btnGuardar.disabled = false; // Habilitar botón si estaba deshabilitado
    }

    // --- Función para cargar estado inicial desde localStorage ---
    function cargarEstadoInicial() {
        const savedData = localStorage.getItem(HORARIO_STORAGE_KEY);
        if (savedData) {
            try {
                const schedule = JSON.parse(savedData);
                diaEstadoBotones.forEach(boton => {
                    const diaKey = boton.dataset.dia;
                    const diaInfo = schedule[diaKey];
                    if (diaInfo && diaInfo.disponible) {
                        // Simular un click para ponerlo en estado disponible
                        // O establecer clases y valores directamente
                        boton.textContent = 'Disponible';
                        boton.classList.remove('no-disponible');
                        boton.classList.add('disponible');

                        const diaHorarioDiv = boton.closest('.dia-horario');
                        const horasDiv = diaHorarioDiv.querySelector('.horas-dia');
                        const selectsHora = horasDiv.querySelectorAll('select');
                        const inicioSelect = horasDiv.querySelector('select[name$="[inicio]"]');
                        const finSelect = horasDiv.querySelector('select[name$="[fin]"]');

                        horasDiv.classList.remove('oculto');
                        selectsHora.forEach(select => (select.disabled = false));
                        diaHorarioDiv.classList.add('dia-activo');

                        // Establecer valores guardados
                        if (inicioSelect) inicioSelect.value = diaInfo.inicio || '09:00'; // Valor por defecto si falta
                        if (finSelect) finSelect.value = diaInfo.fin || '18:00'; // Valor por defecto si falta
                    } else {
                        // Asegurar que está en estado no disponible (ya es el default, pero por si acaso)
                         boton.textContent = 'No disponible';
                         boton.classList.remove('disponible');
                         boton.classList.add('no-disponible');
                         const diaHorarioDiv = boton.closest('.dia-horario');
                         const horasDiv = diaHorarioDiv.querySelector('.horas-dia');
                         const selectsHora = horasDiv.querySelectorAll('select');
                         horasDiv.classList.add('oculto');
                         selectsHora.forEach(select => (select.disabled = true));
                         diaHorarioDiv.classList.remove('dia-activo');
                    }
                });
            } catch (e) {
                console.error("Error al cargar horario desde localStorage:", e);
                mostrarMensaje('error', 'No se pudo cargar el horario guardado previamente.');
            }
        }
    }

     // --- Función para mostrar mensajes de estado ---
     function mostrarMensaje(tipo, texto) {
         mensajeEstadoDiv.textContent = texto;
         mensajeEstadoDiv.className = 'mensaje-estado'; // Reset clases
         mensajeEstadoDiv.classList.add(tipo === 'exito' ? 'exito' : 'error');
         mensajeEstadoDiv.classList.remove('oculto');
     }

    // --- Lógica de Guardado ---
    function guardarHorario() {
        errorMsgDiv.classList.add('oculto'); // Limpiar error previo
        mensajeEstadoDiv.classList.add('oculto');

        const diasDisponibles = contenedorHorario.querySelectorAll('.btn-dia-estado.disponible');

        // *** Validación Obligatoria ***
        if (diasDisponibles.length === 0) {
            errorMsgDiv.textContent = 'Debes marcar al menos un día como disponible.';
            errorMsgDiv.classList.remove('oculto');
            return; // Detener guardado
        }

        // Recopilar datos
        const scheduleData = {};
        const dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
        dias.forEach(key => {
            const diaDiv = contenedorHorario.querySelector(`#horario-${key}`);
            const botonEstado = diaDiv.querySelector('.btn-dia-estado');
            if (botonEstado.classList.contains('disponible')) {
                const inicioSelect = diaDiv.querySelector('select[name$="[inicio]"]');
                const finSelect = diaDiv.querySelector('select[name$="[fin]"]');
                // Validación simple de horas (fin > inicio) - Opcional pero recomendable
                 if (finSelect.value <= inicioSelect.value) {
                     mostrarMensaje('error', `La hora de fin debe ser mayor que la de inicio para el ${key.charAt(0).toUpperCase() + key.slice(1)}.`);
                     // Podríamos detener el guardado aquí o simplemente advertir
                     // return; // Descomentar para detener si la hora es inválida
                 }

                scheduleData[key] = {
                    disponible: true,
                    inicio: inicioSelect.value,
                    fin: finSelect.value
                };
            } else {
                scheduleData[key] = { disponible: false };
            }
        });

        // Guardar en localStorage
        try {
            localStorage.setItem(HORARIO_STORAGE_KEY, JSON.stringify(scheduleData));
            mostrarMensaje('exito', '¡Horario guardado con éxito! Puedes cerrar esta pestaña.');
            btnGuardar.disabled = true; // Deshabilitar después de guardar

            // Informar a la ventana original que se guardó (Opcional, 'focus' listener es más robusto)
             if (window.opener && !window.opener.closed) {
                 // Podrías llamar a una función específica si existe, pero puede fallar
                 // window.opener.cargarHorarioDesdeStorage();
                 // O simplemente confiar en el listener 'focus' de la pestaña original
             }

            // Cerrar la ventana después de un breve retraso
            setTimeout(() => {
                window.close();
            }, 1500); // 1.5 segundos para que el usuario lea el mensaje

        } catch (e) {
            console.error("Error al guardar en localStorage:", e);
            mostrarMensaje('error', 'Ocurrió un error al intentar guardar el horario. Verifica el espacio de almacenamiento o permisos.');
            btnGuardar.disabled = false; // Re-habilitar si falla
        }
    }

    // --- Añadir Listeners ---
    diaEstadoBotones.forEach(boton => {
        boton.addEventListener('click', toggleDiaEstado);
    });

    btnGuardar.addEventListener('click', guardarHorario);

    // Cargar estado al iniciar la página
    cargarEstadoInicial();

})();
</script>

</body>
</html>