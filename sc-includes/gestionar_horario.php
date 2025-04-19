<?php
// Puedes incluir cabeceras básicas de HTML, CSS necesario, etc.
// O simplemente empezar con el div del horario si usas un layout mínimo.

// Incluir variables $GLOBALS['sol'] y $GLOBALS['luna'] si las necesitas aquí también.
// Ejemplo simplificado (asegúrate de definir estas variables globalmente o pasarlas):
$GLOBALS['sol'] = '☀️'; // Ejemplo
$GLOBALS['luna'] = '🌙'; // Ejemplo

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Horario</title>
    <!-- Incluye aquí los mismos estilos CSS que afectan al horario en tu página principal -->
    <link rel="stylesheet" href="ruta/a/tu/estilo_formulario.css">
    <style>
        /* Estilos adicionales específicos para esta página si son necesarios */
        body { padding: 20px; font-family: sans-serif; }
        .contenedor-principal { max-width: 550px; margin: auto; border: 1px solid #ccc; padding: 20px; border-radius: 8px; }
        .boton-guardar {
            display: block; /* Ocupa todo el ancho */
            width: 100%; /* Ocupa todo el ancho */
            padding: 12px 20px;
            margin-top: 25px;
            background-color: #4CAF50; /* Verde */
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s ease;
        }
        .boton-guardar:hover { background-color: #45a049; }
        .boton-guardar:disabled { background-color: #cccccc; cursor: not-allowed; }
        .mensaje-estado { margin-top: 15px; padding: 10px; border-radius: 4px; text-align: center; }
        .mensaje-estado.exito { background-color: #e7f4e7; color: #0d6a0d; border: 1px solid #b7d8b7;}
        .mensaje-estado.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;}
        .oculto { display: none; } /* Asegúrate que la clase oculto funciona */

         /* Copia aquí TODOS los estilos CSS relevantes para .horario-semanal y sus hijos */
        .horario-semanal { /* ... tus estilos ... */ }
        .dia-horario { display: flex; align-items: center; margin-bottom: 10px; flex-wrap: wrap; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .nombre-dia { font-weight: bold; min-width: 80px; margin-right: 10px; }
        .btn-dia-estado { /* ... tus estilos ... */ padding: 5px 10px; cursor: pointer; border-radius: 4px; border: 1px solid; margin-right: 10px; }
        .btn-dia-estado.disponible { background-color: #d4edda; border-color: #c3e6cb; color: #155724; }
        .btn-dia-estado.no-disponible { background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .horas-dia { display: flex; align-items: center; margin-left: auto; /* Empuja a la derecha */ }
        .inputhorahorario { display: flex; align-items: center; margin-left: 15px; }
        .iconohorario { margin-right: 5px; }
        .frm-campo.frm-select.corto { width: 90px; /* Ajusta según necesites */ padding: 5px; }
        .horas-dia.oculto { display: none; }
        .dia-activo { background-color: #f0f9f4; /* Un ligero fondo para días activos */ }
        .error-msg { color: #dc3545; font-size: 0.9em; margin-top: 5px; width: 100%; /* Para que ocupe el ancho */}

    </style>
</head>
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