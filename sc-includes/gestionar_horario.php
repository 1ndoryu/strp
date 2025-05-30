<?php
function svgs1()
{
    $GLOBALS['sol'] = '<?xml version="1.0" encoding="UTF-8"?>
    <svg id="uuid-35a452b5-d528-4366-9dad-dfdc567d22a4" data-name="Capa 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 38.74 38.03">
    <defs>
        <style>
        .uuid-652aaa07-3685-42c1-856f-cfe5ef8c2f8a {
            fill: #fff;
        }
        </style>
    </defs>
    <path class="uuid-652aaa07-3685-42c1-856f-cfe5ef8c2f8a" d="m31.79,24.07l6.49-3.8c.6-.35.62-1.2.04-1.58l-6.73-4.45c-.33-.22-.48-.61-.39-.99l1.56-6.59c.16-.66-.43-1.26-1.09-1.13l-6.83,1.38c-.38.08-.76-.09-.97-.41L19.95.43c-.37-.57-1.19-.57-1.56,0l-4.22,6.38c-.23.35-.66.5-1.06.38l-6.52-2.05c-.69-.22-1.36.4-1.19,1.11l1.73,7.18c.1.41-.09.83-.46,1.04L.48,17.86c-.64.35-.64,1.27,0,1.63l5.66,3.18c.34.19.52.57.47.95l-1.26,8.32c-.1.65.49,1.2,1.14,1.05l7.26-1.7c.39-.09.78.07,1,.41l3.78,5.9c.37.57,1.2.57,1.57,0l3.77-5.89c.22-.34.63-.5,1.01-.4l7.27,1.84c.69.18,1.32-.46,1.13-1.15l-1.9-6.88c-.11-.41.06-.84.43-1.05Zm-12.42,4.78c-5.43,0-9.83-4.4-9.83-9.83s4.4-9.83,9.83-9.83,9.83,4.4,9.83,9.83-4.4,9.83-9.83,9.83Z"/>
    <circle class="uuid-652aaa07-3685-42c1-856f-cfe5ef8c2f8a" cx="19.37" cy="19.02" r="7.6"/>
    </svg>';

    $GLOBALS['luna'] = '<?xml version="1.0" encoding="UTF-8"?>
    <svg id="uuid-1bee79ce-470d-437f-98e4-766498baed20" data-name="Capa 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 38.22 40.32">
    <defs>
        <style>
        .uuid-b8caf4e5-edf7-468d-b87f-21ac40c1e8c8 {
            fill: #fff;
        }
        </style>
    </defs>
    <path class="uuid-b8caf4e5-edf7-468d-b87f-21ac40c1e8c8" d="m29.45,31.68c-8.88,0-16.09-7.2-16.09-16.09,0-7.04,4.53-13.01,10.82-15.19-1.3-.26-2.65-.4-4.03-.4C9.03,0,0,9.03,0,20.16s9.03,20.16,20.16,20.16c7.93,0,14.77-4.59,18.06-11.25-2.52,1.65-5.53,2.61-8.77,2.61Z"/>
    </svg>';
}

svgs1();


?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Horario</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

</head>

<style>
    /* ======================================== */
    /*          VARIABLES GLOBALES             */
    /* ======================================== */

    body {
        font-family: Poppins;
    }

    :root {
        --color-primario: #007bff;
        /* Azul ejemplo */
        --color-secundario: #6c757d;
        /* Gris ejemplo */
        --color-exito: #28a745;
        /* Verde ejemplo */
        --color-error: #dc3545;
        /* Rojo ejemplo */
        --color-info: #17a2b8;
        /* Azul claro ejemplo */
        --color-fondo: #f8f9fa;
        --color-borde: #dee2e6;
        --color-texto: #1a1a1a;
        --color-texto-claro: #fff;
        --color-texto-secundario: #1a1a1a;
        --radio-borde: 4px;
        --espaciado-base: 1rem;
        /* 16px por defecto */
        --espaciado-pequeno: calc(var(--espaciado-base) * 0.5);
        --espaciado-mediano: calc(var(--espaciado-base) * 1.5);
        --espaciado-grande: calc(var(--espaciado-base) * 2);
    }

    /* ======================================== */
    /*          ESTILOS GENERALES              */
    /* ======================================== */

    .lista-opciones {
        display: grid;
        grid-template-columns: 1fr;
        gap: var(--espaciado-base);
    }

    @media (min-width: 576px) {
        .lista-opciones {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 992px) {
        .lista-opciones {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    .titulo-etapa-tipo-usuario {
        color: #ff339a;
        font-size: 21px;
        margin-bottom: 20px;
    }

    #boton-subir-foto {
        position: relative;
        cursor: pointer;
        overflow: hidden;
    }

    * {
        box-sizing: border-box;
    }

    a {
        color: var(--color-primario);
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    /* ======================================== */
    /*      CONTENEDOR Y FORMULARIO BASE       */
    /* ======================================== */

    .opcion-contenido {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .beneficio-tipo-usuario {
        display: flex;
        gap: 10px;
        align-items: center;
        align-content: center;
        /* margin-bottom: 2px; */
        padding: 2px;
    }

    .separador-opcion-perfil {
        border-bottom: 2px solid #ff339a;
        margin-top: -5px;
        margin-bottom: 5px;
    }

    .boton-selecionar-perfil {
        display: flex;
        align-items: center;
        justify-content: center;
        align-content: center;
        background: #ff339a;
        border-radius: 5px;
        padding: 7px;
        color: white;
        margin-top: 10px;
        font-size: 14px;
        font-weight: 600;
    }

    .contenedor-formulario {
        max-width: 800px;
        /* Ajusta según necesites */
        margin: var(--espaciado-mediano) auto;
        padding: var(--espaciado-mediano);
        background-color: #fff;
        border-radius: var(--radio-borde);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .formulario-multi-etapa {
        /* Estilos generales para el form si son necesarios */
    }

    .titulo-principal {
        text-align: center;
        color: var(--color-primario);
        margin-bottom: var(--espaciado-mediano);
    }

    /* ======================================== */
    /*                ETAPAS                   */
    /* ======================================== */
    .etapa {
        display: none;
        /* Ocultas por defecto */
        padding: var(--espaciado-base);
        border-radius: var(--radio-borde);
        margin-bottom: var(--espaciado-mediano);
        background-color: #fff;
        /* Fondo blanco para cada etapa */
    }

    .etapa.activa {
        display: block;
        /* Muestra la etapa activa */
    }

    /* Clase de utilidad para ocultar elementos con JS */
    .oculto {
        display: none !important;
    }

    /* ======================================== */
    /*      GRUPOS DE CAMPOS Y ETIQUETAS       */
    /* ======================================== */
    .frm-seccion {
        margin-bottom: var(--espaciado-mediano);
        border-radius: var(--radio-borde);
    }

    .frm-seccion legend {
        font-weight: bold;
        color: var(--color-primario);
        padding: 0 var(--espaciado-pequeno);
    }

    .frm-grupo {
        margin-bottom: var(--espaciado-base);
    }

    .frm-etiqueta {
        display: block;
        margin-bottom: var(--espaciado-pequeno);
        font-weight: bold;
        color: var(--color-texto-secundario);
    }

    /* ======================================== */
    /*      CAMPOS DE FORMULARIO (Inputs)      */
    /* ======================================== */
    .frm-campo,
    .frm-select,
    .frm-textarea {
        width: 100%;
        padding: var(--espaciado-pequeno) var(--espaciado-base);
        border: 1px solid var(--color-borde);
        border-radius: var(--radio-borde);
        font-size: 1rem;
        transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .frm-campo:focus,
    .frm-select:focus,
    .frm-textarea:focus {
        outline: none;
        border-color: var(--color-primario);
        box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
    }

    .frm-textarea {
        min-height: 120px;
        resize: vertical;
        /* Permite redimensionar verticalmente */
    }

    .frm-select.corto {
        width: auto;
        /* Para selectores de hora */
        min-width: 80px;
    }

    /* ======================================== */
    /*      CHECKBOXES Y RADIOS PERSONALIZADOS */
    /* ======================================== */

    /* Ajustar columnas para diferentes listas si es necesario */
    .grupo-radios-plan {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        /* 3 columnas en desktop */
    }

    .grupo-checkboxes-extra {
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        /* 2 o 3 columnas */
    }

    .opcion-checkbox.opcion-extra svg {
        height: 36px;
        width: 36px;
    }

    .opcion-radio,
    .opcion-checkbox {
        display: block;
        border: 2px solid var(--color-borde);
        padding: var(--espaciado-base);
        border-radius: var(--radio-borde);
        cursor: pointer;
        transition: border-color 0.2s ease, background-color 0.2s ease;
        width: 100%;
        position: relative;
    }

    .opcion-radio:hover,
    .opcion-checkbox:hover,
    .opcion-radio.marcado {
        border-color: var(--color-primario);
        background-color: #f0f8ff;
    }

    label.opcion-checkbox.opcion-extra {
        display: flex !important;
        gap: 20px;
        align-items: center;
    }

    .precio-y-tiempo {
        margin-left: auto;
        display: flex;
        gap: 15px;
    }

    /* Estilo cuando el input dentro está seleccionado */
    .opcion-radio input:checked+.opcion-contenido,
    .opcion-checkbox input:checked+.opcion-contenido {
        /* Puedes añadir algún indicador visual si quieres */
    }

    .opcion-radio input:checked,
    .opcion-checkbox input:checked {
        /* Estilo para el borde de la opción seleccionada */
        /* Necesitas seleccionar el padre, lo hacemos con JS o ajustando estructura. */
        /* Alternativa: Estilar la etiqueta contenedora cuando el input está checked */
    }

    .opcion-radio input[type='radio'],
    .opcion-checkbox input[type='checkbox'] {
        /* Ocultar el input por defecto si quieres un look totalmente custom */
        /* appearance: none; margin-right: 10px; */
        /* Por ahora, lo dejamos visible por simplicidad */
        margin-right: var(--espaciado-pequeno);
        vertical-align: middle;
        display: none;
    }

    .imagen-anuncio img {
        max-width: 160px;
        max-height: 100%;
        object-fit: cover;
        aspect-ratio: 1 / 7;
    }

    .opcion-contenido strong {
        display: block;
        color: var(--color-texto);
        font-size: 16px;
    }

    .opcion-contenido span {
        display: block;
        font-size: 14px;
        color: var(--color-texto-secundario);
        margin-top: -5px;
    }

    .precio-y-tiempo p {
        margin: 0;
        font-weight: 700;
        font-size: 18px;
        /* width: 50px; */
        white-space: nowrap;
        line-height: 34px;
    }

    input[type='radio'] {
        display: none;
    }

    .frm-grupo.opcion-gratis-extra {
        margin-top: 12px;
    }

    .frm-checkbox a {
        margin-left: 4px;
        margin-right: 4px;
    }

    .precio-y-tiempo {
        margin-left: auto;
    }

    .precio-plan {
        font-weight: bold;
        color: var(--color-exito);
        margin-top: var(--espaciado-pequeno);
        font-size: 34px !important;
    }

    /* Checkboxes normales (no en lista de opciones) */
    .frm-checkbox {
        display: inline-flex;
        /* O block si prefieres uno por línea */
        align-items: center;
        margin-right: var(--espaciado-base);
        /* Espacio entre checkboxes en línea */
        margin-bottom: var(--espaciado-pequeno);
        cursor: pointer;
    }

    .frm-checkbox input[type='checkbox'] {
        margin-right: var(--espaciado-pequeno);
    }

    .grupo-checkboxes {
        display: flex;
        flex-wrap: wrap;
        gap: var(--espaciado-pequeno) var(--espaciado-base);
    }

    .formulario-multi-etapa {
        font-family: Poppins, Nunito, Roboto, Arial, sans-serif;
    }

    /* ======================================== */
    /*         COMPONENTES ESPECÍFICOS         */
    /* ======================================== */

    /* --- Ayuda y Contadores --- */
    .ayuda-texto {
        font-size: 0.85em;
        color: var(--color-texto-secundario);
        margin-top: var(--espaciado-pequeno);
        margin-bottom: var(--espaciado-pequeno);
    }

    .botones-horario-pestana {
        color: black;
        font-weight: 400;
        font-size: 14px;
        background: unset;
        border: 1px solid #d3d3d3;
        padding: var(--espaciado-pequeno) var(--espaciado-mediano);
        border-radius: 5px;
    }

    .contador-caracteres {
        font-size: 0.8em;
        color: var(--color-texto-secundario);
        text-align: right;
        margin-top: var(--espaciado-pequeno);
    }

    /* --- Subida de Fotos --- */
    .subida-fotos-contenedor {
        border: 2px dashed var(--color-borde);
        padding: var(--espaciado-mediano);
        text-align: center;
        border-radius: var(--radio-borde);
        margin-bottom: var(--espaciado-base);
    }

    .boton-subir {
        cursor: pointer;
        color: var(--color-primario);
        font-weight: bold;
        padding: var(--espaciado-base);
    }

    .boton-subir:hover {
        background-color: #f0f8ff;
    }

    .lista-fotos {
        margin-top: var(--espaciado-base);
        gap: var(--espaciado-base);
        display: flex;
        flex-direction: row;
    }

    .foto-subida-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        position: relative;
    }

    svg#uuid-67eca691-fad9-4dbb-8a42-6bf39e0830b8 {
        height: 24px;
        width: 24px;
    }

    .foto-subida-item img {
        max-width: 200px;
        max-height: 200px;
        aspect-ratio: 4 / 6;
        object-fit: cover;
    }

    .foto-subida-item .removeImg {
        display: none;
    }

    .foto-subida-item a.edit-photo-icon {
        display: none;
    }

    .preview-actions {
        display: flex;
        justify-content: space-evenly;
        width: 100%;
    }

    .preview-actions button {
        all: unset;
        cursor: pointer;
        padding: 5px 10px;
    }

    button.btn-dia-estado.disponible {
        border: unset;
        padding: 10px;
        font-weight: 600;
        background-color: #f1a1cb;
        height: 80px;
        min-width: 140px;
        cursor: pointer;
    }

    button.btn-dia-estado.no-disponible {
        border: unset;
        padding: 10px;
        font-weight: 600;

        background-color: #de5962;
        height: 80px;
        width: 140px;
        cursor: pointer;
    }

    span.nombre-dia {
        width: 140px;
        padding: 34px 40px;
    }

    button#btn-guardar-horario {
        color: black;
        font-weight: 400;
        font-size: 14px;
        background: unset;
        border: 1px solid #d3d3d3;
        padding: var(--espaciado-pequeno) var(--espaciado-mediano);
        border-radius: 5px;
        margin-top: 30px;
        margin-bottom: 40px;
    }

    button#btn-modificar-horario {
        color: black;
        font-weight: 400;
        font-size: 14px;
        background: unset;
        border: 1px solid #d3d3d3;
        padding: var(--espaciado-pequeno) var(--espaciado-mediano);
        border-radius: 5px;
    }



    /* Estilo para las previsualizaciones (necesitarás añadir elementos img/div aquí con JS) */
    .lista-fotos .foto-preview {
        position: relative;
        border: 1px solid var(--color-borde);
        border-radius: var(--radio-borde);
        overflow: hidden;
        cursor: pointer;
        /* Para seleccionar principal */
        aspect-ratio: 1 / 1;
        /* Mantiene cuadrada la previsualización */
    }

    .lista-fotos .foto-preview img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* Escala y recorta la imagen */
    }

    .lista-fotos .foto-preview.principal {
        border: 3px solid var(--color-primario);
    }

    .lista-fotos .foto-preview .eliminar-foto {
        /* Botón para eliminar (opcional) */
        position: absolute;
        top: 5px;
        right: 5px;
        background-color: rgba(220, 53, 69, 0.8);
        color: white;
        border: none;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        font-size: 12px;
        line-height: 20px;
        text-align: center;
        cursor: pointer;
    }

    /* --- Horario Semanal --- */
    .horario-semanal {
        /* Contenedor general */
    }

    .dia-horario {
        display: flex;
        align-items: center;
        gap: var(--espaciado-base);
        padding: var(--espaciado-pequeno) 0;
        border-bottom: 1px dotted var(--color-borde);
    }

    .dia-horario:last-child {
        border-bottom: none;
    }

    .dia-horario .check-dia {
        flex-basis: 150px;
        /* Ancho fijo para el nombre del día */
        flex-shrink: 0;
        margin: 0;
        /* Resetear margen de frm-checkbox */
    }

    .horas-dia {
        display: flex;
        align-items: flex-start;
        gap: var(--espaciado-pequeno);
        flex-grow: 1;
        flex-direction: column;
    }

    .horas-dia label {
        font-weight: normal;
        /* Sobrescribir frm-etiqueta si hereda */
        margin-bottom: 0;
        color: var(--color-texto-secundario);
    }

    /* --- Grupo Teléfono --- */
    .grupo-telefono {
        display: flex;
        align-items: center;
        gap: var(--espaciado-base);
    }

    .grupo-telefono .frm-campo {
        flex-grow: 1;
        max-width: 300px;
        width: 100%;
    }

    .grupo-telefono .check-whatsapp {
        margin: 0;
        /* Resetear margen de frm-checkbox */
        white-space: nowrap;
        /* Evita que se parta el texto */
    }

    button#btn-mostrar-horario {
        color: black;
        font-weight: 400;
        font-size: 14px;
        background: unset;
        border: 1px solid #d3d3d3;
    }

    /* --- Grupo Idiomas --- */
    .grupo-idiomas {
        display: grid;
        gap: var(--espaciado-base);
    }

    .par-idioma {
        display: flex;
        gap: var(--espaciado-base);
    }

    .par-idioma .frm-select {
        flex: 1;
        /* Ocupan espacio equitativo */
    }

    /* ======================================== */
    /*             NAVEGACIÓN ETAPAS           */
    /* ======================================== */
    .navegacion-etapa {
        display: flex;
        justify-content: space-between;
        /* Separa botones anterior/siguiente */
        margin-top: var(--espaciado-mediano);
        padding-top: var(--espaciado-base);
    }

    .frm-boton {
        padding: var(--espaciado-pequeno) var(--espaciado-mediano);
        border: none;
        border-radius: var(--radio-borde);
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.2s ease, transform 0.1s ease;
        color: var(--color-texto-claro);
    }

    .frm-boton:hover {
        opacity: 0.9;
    }

    .frm-boton:active {
        transform: scale(0.98);
    }

    .btn-siguiente,
    .btn-publicar {
        /* background-color: var(--color-primario); */
        color: black;
        font-weight: 400;
        font-size: 14px;
        background: unset;
        border: 1px solid #d3d3d3;
    }

    .btn-anterior {
        color: black;
        font-weight: 400;
        font-size: 14px;
        background: unset;
        border: 1px solid #d3d3d3;
    }

    .btn-pago {
        background-color: var(--color-exito);
        /* Verde para pago */
    }

    /* ======================================== */
    /*           MENSAJES DE ERROR             */
    /* ======================================== */
    .error-msg {
        color: var(--color-error);
        font-size: 0.85em;
        margin-top: var(--espaciado-pequeno);
        /* display: none; */
        /* Se gestiona con la clase .oculto desde JS */
    }

    /* ======================================== */
    /*                ALERTAS PHP              */
    /* ======================================== */
    .alerta {
        padding: var(--espaciado-base);
        margin-bottom: var(--espaciado-mediano);
        border: 1px solid transparent;
        border-radius: var(--radio-borde);
    }

    .alerta.error {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }

    .alerta.info {
        color: #0c5460;
        background-color: #d1ecf1;
        border-color: #bee5eb;
    }

    /* ======================================== */
    /*                OTROS ESTILOS            */
    /* ======================================== */
    .ayuda-post {
        list-style: none;
        padding: 0;
        margin: 0 0 var(--espaciado-base) 0;
        text-align: center;
    }

    .ayuda-post-item {
        display: inline-block;
        margin: 0 var(--espaciado-pequeno);
        font-size: 0.9em;
    }

    .terminos-finales {
        margin-top: var(--espaciado-grande);
    }

    .texto-fijo {
        padding: var(--espaciado-pequeno) 0;
        display: inline-block;
        color: var(--color-texto-secundario);
    }

    /* ======================================== */
    /*             RESPONSIVIDAD               */
    /* ======================================== */
    @media (max-width: 768px) {
        .contenedor-formulario {
            margin: var(--espaciado-base) auto;
            padding: var(--espaciado-base);
        }

        .grupo-radios-plan,
        .grupo-checkboxes-extra {
            grid-template-columns: 1fr;
            /* Una columna en móvil */
        }

        .dia-horario {
            align-items: flex-start;
            gap: var(--espaciado-pequeno);
        }

        .dia-horario .check-dia {
            flex-basis: auto;
            margin-bottom: var(--espaciado-pequeno);
        }

        .horas-dia {
            flex-wrap: wrap;
            /* Permitir que los selects se envuelvan */
            width: 100%;
        }

        .horas-dia .frm-select.corto {
            min-width: 100px;
            /* Ajustar ancho mínimo */
        }

        .par-idioma {
            flex-direction: column;
        }

        .navegacion-etapa {
            flex-direction: column-reverse;
            /* Poner siguiente arriba */
            gap: var(--espaciado-base);
        }

        .navegacion-etapa .frm-boton {
            width: 100%;
            /* Botones ocupan todo el ancho */
        }
    }

    @media (max-width: 480px) {
        .grupo-telefono {
            flex-direction: column;
            align-items: flex-start;
        }

        .grupo-telefono .frm-campo {
            width: 100%;
        }

        .lista-fotos {
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: var(--espaciado-pequeno);
        }
    }

    .poppins-thin {
        font-family: 'Poppins', sans-serif;
        font-weight: 100;
        font-style: normal;
    }

    .poppins-extralight {
        font-family: 'Poppins', sans-serif;
        font-weight: 200;
        font-style: normal;
    }

    .poppins-light {
        font-family: 'Poppins', sans-serif;
        font-weight: 300;
        font-style: normal;
    }

    .poppins-regular {
        font-family: 'Poppins', sans-serif;
        font-weight: 400;
        font-style: normal;
    }

    .poppins-medium {
        font-family: 'Poppins', sans-serif;
        font-weight: 500;
        font-style: normal;
    }

    .poppins-semibold {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        font-style: normal;
    }

    .poppins-bold {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        font-style: normal;
    }

    .poppins-extrabold {
        font-family: 'Poppins', sans-serif;
        font-weight: 800;
        font-style: normal;
    }

    .poppins-black {
        font-family: 'Poppins', sans-serif;
        font-weight: 900;
        font-style: normal;
    }

    .poppins-thin-italic {
        font-family: 'Poppins', sans-serif;
        font-weight: 100;
        font-style: italic;
    }

    .poppins-extralight-italic {
        font-family: 'Poppins', sans-serif;
        font-weight: 200;
        font-style: italic;
    }

    .poppins-light-italic {
        font-family: 'Poppins', sans-serif;
        font-weight: 300;
        font-style: italic;
    }

    .poppins-regular-italic {
        font-family: 'Poppins', sans-serif;
        font-weight: 400;
        font-style: italic;
    }

    .poppins-medium-italic {
        font-family: 'Poppins', sans-serif;
        font-weight: 500;
        font-style: italic;
    }

    .poppins-semibold-italic {
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        font-style: italic;
    }

    .poppins-bold-italic {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        font-style: italic;
    }

    .poppins-extrabold-italic {
        font-family: 'Poppins', sans-serif;
        font-weight: 800;
        font-style: italic;
    }

    .poppins-black-italic {
        font-family: 'Poppins', sans-serif;
        font-weight: 900;
        font-style: italic;
    }

    .lista-opciones.grupo-radios-plan {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .planes-primer-div p {
        margin: 0px;
    }

    .planes-primer-div {
        padding: 15px 35px;
        border: 1px solid #d3d3d3;
        border-radius: 5px;
        justify-items: anchor-center;
        font-weight: 700;
        font-size: 17px;
    }

    .planes-segundo-div {
        display: flex;
        flex-direction: row;
        border: 1px solid #d3d3d3;
        padding: 15px 25px;
        border-radius: 5px;
        align-items: center;
        cursor: pointer;
        width: 100%;
        justify-content: space-between;
    }

    .contenido-planes-segundo-div p {
        margin: 0px;
    }

    .segundo-div-plan {
        width: 100%;
        max-width: 900px;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    p.titulosegundodiv {
        font-size: 16px;
    }

    .contenido-planes-segundo-div {
        font-weight: 700;
    }

    .tiempo-plan {
        position: absolute;
        background: #fbc300;
        padding: 5px 10px;
        border-radius: 40px;
        color: white;
        font-weight: 700;
        top: -15px;
        right: 10px;
        font-size: 13px;
    }

    button.btn-seleccionar-plan {
        display: flex;
        align-items: center;
        justify-content: center;
        align-content: center;
        background: #fbc300;
        border-radius: 5px;
        padding: 7px 14px;
        color: white;
        font-size: 14px;
        font-weight: 600;
        border: unset;
    }

    .tercer-div-plan {
        margin-top: 20px;
    }

    p.titulotercerdiv {
        background: #ececec;
        width: 100%;
        padding: 15px;
        text-align-last: center;
        font-weight: 700;
        font-size: 15px;
    }

    .primer-div-plan {
        display: flex;
        gap: 15px;
    }

    table.comparar-caracteristicas-tabla {
        padding: 40px;
        padding-top: 0px;
    }

    h2.titulo-etapa-plan {
        justify-self: center;
        padding: 20px;
        color: #1a1a1a;
    }

    .tercer-div-plan {
        width: 100%;
        max-width: 750px;
    }

    td {
        padding-bottom: 10px;
    }

    .divisor-anuncio {
        max-width: 700px;
        width: 100%;
    }

    .divisor-anuncio-principal {
        display: flex;
        gap: 20px;
        margin: auto;
        flex-direction: row;
        justify-content: center;
    }

    .titulo-etapa-anuncio-div {
        display: flex;
        align-items: center;
        align-content: center;
        gap: 10px;
        padding: 18px 0px;
    }

    p.numero-etapa {
        margin: 0px;
        background: #fbc300;
        border-radius: 130px;
        /* padding: 13px 13px; */
        height: 30px;
        width: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        align-content: center;
        padding-top: 3px;
        font-weight: 700;
        color: white;
    }

    .titulo-etapa {
        color: var(--color-secundario);
    }

    .progresos-etapa {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 30px;
    }

    .numero-etapa-progreso p {
        margin: 0px;
        /* background: #fbc300; */
        border-radius: 130px;
        /* padding: 13px 13px; */
        height: 30px;
        width: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        align-content: center;
        padding-top: 3px;
        font-weight: 700;
        /* color: white; */
        background: #eaeaea;
    }

    .numero-etapa-progreso.etapa-actual-progreso p {
        background: #fbc300;
        color: white;
    }

    .linea-etapa-progreso {
        border-bottom: 1px solid #3e3e3e;
        width: 200px;
        opacity: 0.2;
    }

    @media (max-width: 992px) {
        .imagen-anuncio {
            display: none;
        }
    }

    /* --- Estilos Base Selector Personalizado --- */
    .custom-select-wrapper {
        position: relative;
        /* Necesario para posicionar el dropdown */
        width: 100%;
    }

    .custom-select-trigger {
        display: flex;
        /* Para alinear texto y flecha */
        justify-content: space-between;
        /* Empuja la flecha a la derecha */
        align-items: center;
        width: 100%;
        text-align: left;
        cursor: pointer;
        /* Hereda estilos de .frm-campo o añade los tuyos */
        padding: 10px 15px;
        border: 1px solid #ccc;
        background-color: #fff;
        min-height: 40px;
        /* Asegura altura mínima como otros campos */
        box-sizing: border-box;
        /* Importante */
    }

    .custom-select-trigger:focus {
        outline: 2px solid dodgerblue;
        /* O el color de foco de tu tema */
        outline-offset: 2px;
    }

    .custom-select-value {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        flex-grow: 1;
        /* Ocupa el espacio disponible */
        padding-right: 10px;
        /* Espacio antes de la flecha */
    }

    .custom-select-arrow {
        flex-shrink: 0;
        /* Evita que la flecha se encoja */
        transition: transform 0.2s ease;
    }

    .custom-select-wrapper.open .custom-select-arrow {
        transform: rotate(180deg);
    }

    .custom-select-dropdown {
        position: absolute;
        /* Posición por defecto (desktop) */
        top: calc(100% + 5px);
        /* Debajo del trigger */
        left: 0;
        width: 100%;
        background-color: #fff;
        border: 1px solid #ccc;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        /* Asegura que esté por encima de otros elementos */
        max-height: 250px;
        /* Altura máxima antes de scroll (desktop) */
        display: flex;
        flex-direction: column;
        /* Estructura vertical */
        opacity: 0;
        /* Para transición suave */
        visibility: hidden;
        transform: translateY(-10px);
        transition: opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease;
    }

    .custom-select-wrapper.open .custom-select-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .custom-select-header {
        padding: 10px;
        border-bottom: 1px solid #eee;
        display: flex;
        /* Alinea buscador y botón cerrar */
        align-items: center;
    }

    .custom-select-search {
        width: 100%;
        /* Ocupa el espacio */
        padding: 8px 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        margin-right: 10px;
        /* Espacio con el botón cerrar */
        box-sizing: border-box;
    }

    .custom-select-close {
        background: none;
        border: none;
        font-size: 1.5em;
        line-height: 1;
        padding: 0 5px;
        cursor: pointer;
        color: #888;
        display: none;
        /* Oculto por defecto (visible en móvil) */
        flex-shrink: 0;
    }

    .custom-select-options {
        list-style: none;
        margin: 0;
        padding: 0;
        overflow-y: auto;
        /* Scroll si hay muchas opciones */
        flex-grow: 1;
        /* Ocupa el espacio restante en el dropdown */
    }

    .custom-select-options li {
        padding: 10px 15px;
        cursor: pointer;
        transition: background-color 0.15s ease;
    }

    .custom-select-options li:hover,
    .custom-select-options li:focus {
        background-color: #f0f0f0;
        outline: none;
    }

    .custom-select-options li.selected {
        background-color: #e0e0e0;
        /* O un color que indique selección */
        font-weight: bold;
    }

    .custom-select-options li.filtered-out {
        display: none;
        /* Oculta opciones filtradas */
    }

    /* --- Estilo para Ocultar el Select Original Accesiblemente --- */
    .visually-hidden {
        position: absolute !important;
        height: 1px;
        width: 1px;
        overflow: hidden;
        clip: rect(1px, 1px, 1px, 1px);
        white-space: nowrap;
        /* Evita que el contenido afecte al layout */
        border: 0;
        /* Asegura que no tenga borde visible */
        padding: 0;
        /* Asegura que no tenga padding visible */
        margin: -1px;
        /* Evita posible espacio extra */
    }

    /* --- Estilos Específicos para Móvil (Pantalla Completa) --- */
    @media (max-width: 768px) {

        /* Ajusta este breakpoint si es necesario */
        .custom-select-wrapper.open .custom-select-dropdown {
            position: fixed;
            /* Fija a la ventana */
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            max-height: 100%;
            background-color: rgba(255, 255, 255, 0.98);
            /* Fondo semi-transparente o sólido */
            z-index: 1001;
            /* Por encima de otros elementos fijos */
            border: none;
            box-shadow: none;
            /* Animación de entrada (opcional) */
            transform: translateY(0);
            animation: slideInUp 0.3s ease forwards;
        }

        .custom-select-header {
            background-color: #f8f8f8;
            /* Un fondo para el header */
            padding: 15px;
        }

        .custom-select-close {
            display: block;
            /* Muestra el botón de cerrar en móvil */
        }

        .custom-select-options {
            padding: 10px 0;
            /* Espacio arriba/abajo en la lista */
        }

        .custom-select-options li {
            padding: 15px 20px;
            /* Más espacio táctil */
            font-size: 1.1em;
        }

        /* Animación (Opcional) */
        @keyframes slideInUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    }

    .icono-clock {
        position: relative;
        /* Necesario para posicionar el tooltip relativo al icono */
        cursor: pointer;
        /* Indica que se puede interactuar */
    }

    .clock-tooltip {
        display: none;
        position: absolute;
        bottom: 110%;
        left: 50%;
        background-color: #f4f4f4;
        color: #1b1b1b;
        padding: 5px 10px;
        height: 30px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 10;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    /* Opcional: Pequeño triángulo/flecha para el tooltip */
    .clock-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        /* En la parte inferior del tooltip */
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #333 transparent transparent transparent;
    }

    .subir-imagen-div-div {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
    }

    .inputfalso {
        display: flex;
        align-items: center;
        align-content: center;
        flex-direction: row;
        color: black;
        FONT-WEIGHT: 600;
        opacity: 70%;
    }

    .inputfalsodiv {
        border: 1px solid #929292;
        border-right: unset;
        height: 38px;
        padding: 0px 10px;
        width: 270px;
        border-radius: 5px;
        border-top-right-radius: 0px;
        border-bottom-right-radius: 0px;
        display: flex;
        align-items: center;
    }

    .inputfalso button {
        height: 38px;
        background: #000000;
        border: unset;
        padding: 9px 20px;
        color: white;
        FONT-WEIGHT: 600;
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
        max-height: 38px;
        /* border: 1px solid; */
    }

    svg#uuid-a38627c9-109d-413f-b7ed-005836ef688e {
        max-width: 50px;
    }

    button.btn-preview-action.btn-change-foto {
        position: absolute;
        top: 9px;
        right: -4px;
    }

    p.textoayudafal,
    .tamañotextodiv {
        color: black;
        opacity: 70%;
        FONT-WEIGHT: 400;
        margin-top: 5px;
        margin-bottom: 0px;
    }

    .inputfalsodiv p {
        margin: 0px;
    }

    .foto-subida-item.rotating {
        opacity: 0.7;
        /* Podrías añadir un overlay con un spinner aquí si quieres */
        position: relative;
        /* Necesario si usas un spinner absoluto */
        cursor: progress;
    }

    /* Ejemplo básico de spinner (necesitarías ajustar estilos) */
    .foto-subida-item.rotating::after {
        content: '';
        display: block;
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin-top: -10px;
        margin-left: -10px;
        border: 2px solid rgba(255, 255, 255, 0.5);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Deshabilitar acciones mientras rota */
    .foto-subida-item.rotating .btn-preview-action {
        pointer-events: none;
    }

    select.frm-campo.frm-select.corto {
        border-radius: 0px;
        height: 42px;
    }

    .inputhorahorario {
        display: flex;
        align-items: center;
        flex-direction: row;
    }

    .iconohorario {
        background: #3166cc;
        height: 36px;
        width: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .iconohorario svg {
        height: 15px;
        width: 15px;
    }

    .inputhorahorario input.frm-campo.corto {
        border-radius: 0px;
        width: 100px;
        height: 36px;
    }
</style>

<body>

    <button type="button" id="btn-guardar-horario" class="boton-guardar">Guardar Horario y Cerrar</button>
    <div id="mensaje-estado" class="mensaje-estado oculto"></div>
    <div class="contenedor-principal">
        <div class="horario-semanal" id="contenedor-horario">
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
                            <input type="text"
                                name="horario_dia[<?= $key ?>][inicio]"
                                class="frm-campo corto"
                                value="09:00"
                                placeholder="HH:MM"
                                pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]"
                                maxlength="5"
                                title="Introduce la hora de inicio en formato HH:MM (ej. 09:00)">
                            <?php /* ¡Atributo 'disabled' eliminado de aquí! */ ?>
                        </div>
                        <div class="inputhorahorario">
                            <label class="iconohorario"><?php echo $GLOBALS['luna']; ?></label>
                            <input type="text"
                                name="horario_dia[<?= $key ?>][fin]"
                                class="frm-campo corto"
                                value="18:30"
                                placeholder="HH:MM"
                                pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]"
                                maxlength="5"
                                title="Introduce la hora de fin en formato HH:MM (ej. 17:30)">
                            <?php /* ¡Atributo 'disabled' eliminado de aquí! */ ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="error-msg oculto" id="error-horario-guardar">Debes marcar al menos un día como disponible.</div>
        </div>
    </div>





    </div>

    <script>
        (function() {
            'use strict';

            const contenedorHorario = document.getElementById('contenedor-horario');
            const diaEstadoBotones = contenedorHorario.querySelectorAll('.btn-dia-estado');
            const btnGuardar = document.getElementById('btn-guardar-horario');
            const errorMsgDiv = document.getElementById('error-horario-guardar');
            const mensajeEstadoDiv = document.getElementById('mensaje-estado');
            const HORARIO_STORAGE_KEY = 'userPendingSchedule';

            // --- Función para cambiar estado del día (CORREGIDA) ---
            function toggleDiaEstado(event) {
                const boton = event.currentTarget;
                const diaHorarioDiv = boton.closest('.dia-horario');
                const horasDiv = diaHorarioDiv.querySelector('.horas-dia');
                // CORREGIDO: Buscar inputs en lugar de selects
                const inputsHora = horasDiv.querySelectorAll('input[type="text"]');
                const esDisponibleAhora = boton.classList.contains('disponible');

                if (esDisponibleAhora) {
                    boton.textContent = 'No disponible';
                    boton.classList.remove('disponible');
                    boton.classList.add('no-disponible');
                    horasDiv.classList.add('oculto');
                    // CORREGIDO: Deshabilitar inputs
                    inputsHora.forEach(input => (input.disabled = true));
                    diaHorarioDiv.classList.remove('dia-activo');
                } else {
                    boton.textContent = 'Disponible';
                    boton.classList.remove('no-disponible');
                    boton.classList.add('disponible');
                    horasDiv.classList.remove('oculto');
                    // CORREGIDO: Habilitar inputs
                    inputsHora.forEach(input => (input.disabled = false));
                    diaHorarioDiv.classList.add('dia-activo');
                }
                // Limpiar error al interactuar
                errorMsgDiv.classList.add('oculto');
                mensajeEstadoDiv.classList.add('oculto');
                btnGuardar.disabled = false;
            }

            // --- Función para cargar estado inicial desde localStorage (CORREGIDA) ---
            function cargarEstadoInicial() {
                const savedData = localStorage.getItem(HORARIO_STORAGE_KEY);
                if (savedData) {
                    try {
                        const schedule = JSON.parse(savedData);
                        diaEstadoBotones.forEach(boton => {
                            const diaKey = boton.dataset.dia;
                            const diaInfo = schedule[diaKey];
                            const diaHorarioDiv = boton.closest('.dia-horario'); // Mover fuera del if/else
                            const horasDiv = diaHorarioDiv.querySelector('.horas-dia'); // Mover fuera del if/else
                            // CORREGIDO: Buscar inputs específicos
                            const inicioInput = horasDiv.querySelector('input[name$="[inicio]"]');
                            const finInput = horasDiv.querySelector('input[name$="[fin]"]');
                            // CORREGIDO: Buscar todos los inputs para habilitar/deshabilitar
                            const inputsHora = horasDiv.querySelectorAll('input[type="text"]');

                            if (diaInfo && diaInfo.disponible) {
                                boton.textContent = 'Disponible';
                                boton.classList.remove('no-disponible');
                                boton.classList.add('disponible');
                                horasDiv.classList.remove('oculto');
                                // CORREGIDO: Habilitar inputs
                                inputsHora.forEach(input => (input.disabled = false));
                                diaHorarioDiv.classList.add('dia-activo');

                                // Establecer valores guardados (asegurarse que los inputs existen)
                                if (inicioInput) inicioInput.value = diaInfo.inicio || '09:00';
                                if (finInput) finInput.value = diaInfo.fin || '18:30'; // Usar el default del HTML
                            } else {
                                boton.textContent = 'No disponible';
                                boton.classList.remove('disponible');
                                boton.classList.add('no-disponible');
                                horasDiv.classList.add('oculto');
                                // CORREGIDO: Deshabilitar inputs
                                inputsHora.forEach(input => (input.disabled = true));
                                diaHorarioDiv.classList.remove('dia-activo');
                            }
                        });
                    } catch (e) {
                        console.error("Error al cargar horario desde localStorage:", e);
                        mostrarMensaje('error', 'No se pudo cargar el horario guardado previamente.');
                    }
                } else {
                    // Asegurarse de que todos los inputs estén deshabilitados al inicio si no hay datos guardados
                    contenedorHorario.querySelectorAll('.horas-dia input[type="text"]').forEach(input => input.disabled = true);
                }
            }

            // --- Función para mostrar mensajes de estado ---
            function mostrarMensaje(tipo, texto) {
                mensajeEstadoDiv.textContent = texto;
                mensajeEstadoDiv.className = 'mensaje-estado'; // Reset clases
                mensajeEstadoDiv.classList.add(tipo === 'exito' ? 'exito' : 'error');
                mensajeEstadoDiv.classList.remove('oculto');
            }

            // --- Lógica de Guardado (CORREGIDA) ---
            function guardarHorario() {
                errorMsgDiv.classList.add('oculto');
                mensajeEstadoDiv.classList.add('oculto');

                const diasDisponibles = contenedorHorario.querySelectorAll('.btn-dia-estado.disponible');

                if (diasDisponibles.length === 0) {
                    errorMsgDiv.textContent = 'Debes marcar al menos un día como disponible.';
                    errorMsgDiv.classList.remove('oculto');
                    return;
                }

                const scheduleData = {};
                const dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
                let errorEnHoras = false; // Flag para detener si hay error

                dias.forEach(key => {
                    const diaDiv = contenedorHorario.querySelector(`#horario-${key}`);
                    const botonEstado = diaDiv.querySelector('.btn-dia-estado');
                    if (botonEstado.classList.contains('disponible')) {
                        // CORREGIDO: Buscar inputs
                        const inicioInput = diaDiv.querySelector('input[name$="[inicio]"]');
                        const finInput = diaDiv.querySelector('input[name$="[fin]"]');

                        // Asegurarse de que los inputs existen antes de leer 'value'
                        if (inicioInput && finInput) {
                            // Validación simple de horas (fin > inicio)
                            // Convertir a minutos o usar comparación de strings directa (HH:MM funciona)
                            if (finInput.value <= inicioInput.value) {
                                mostrarMensaje('error', `La hora de fin debe ser mayor que la de inicio para el ${key.charAt(0).toUpperCase() + key.slice(1)}.`);
                                errorEnHoras = true; // Marcar que hubo un error
                                // No añadir este día a los datos si hay error
                            } else {
                                // Añadir solo si las horas son válidas
                                scheduleData[key] = {
                                    disponible: true,
                                    inicio: inicioInput.value,
                                    fin: finInput.value
                                };
                            }
                        } else {
                            // Esto no debería pasar si el HTML es correcto, pero por seguridad
                            console.error(`Error: No se encontraron inputs de hora para ${key}`);
                            mostrarMensaje('error', `Error interno al procesar el horario de ${key}.`);
                            errorEnHoras = true; // Marcar que hubo un error
                        }
                    } else {
                        scheduleData[key] = {
                            disponible: false
                        };
                    }
                });

                // Detener si hubo error en la validación de horas
                if (errorEnHoras) {
                    return;
                }

                // --- Guardado en localStorage (sin cambios aquí, pero ahora recibe datos correctos) ---
                try {
                    localStorage.setItem(HORARIO_STORAGE_KEY, JSON.stringify(scheduleData));
                    mostrarMensaje('exito', '¡Horario guardado con éxito! Puedes cerrar esta pestaña.');
                    btnGuardar.disabled = true;

                    if (window.opener && !window.opener.closed) {
                        // Intenta notificar a la ventana principal si es posible
                        // Esto puede fallar por restricciones de seguridad o si la función no existe
                        try {
                            if (typeof window.opener.horarioActualizado === 'function') {
                                window.opener.horarioActualizado();
                            }
                        } catch (e) {
                            console.warn("No se pudo notificar a la ventana principal (puede ser normal).");
                        }
                    }

                    setTimeout(() => {
                        window.close();
                    }, 1500);

                } catch (e) {
                    console.error("Error al guardar en localStorage:", e);
                    mostrarMensaje('error', 'Ocurrió un error al intentar guardar el horario. Verifica el espacio de almacenamiento o permisos.');
                    btnGuardar.disabled = false;
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