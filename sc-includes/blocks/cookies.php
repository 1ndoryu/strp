
<div class="config_cookies_content" >
    <div id="cookie_config" style="display: none;" class="cookies_config cookies_dialog" >
        <p class="f-1150">Configuración de cookies</p>
        <div>
            <?php
            if ($_COOKIE['CONFIG'])
                $config =  json_decode($_COOKIE['CONFIG']);
            else
                $config = false;
            ?>
            <form class="cookie_form mt-2" method="get">
                <label><input type="checkbox" <?php if (!$config || $config->preferencias == true) print "checked" ?> name="preferencias" value="true"> Preferencias</label>
                <label><input type="checkbox" <?php if (!$config || $config->terceros == true) print "checked" ?> name="terceros" value="true"> Terceros</label>
                <label><input type="checkbox" checked name="necesarias" disabled value="true"> Tecnicas (necesárias)</label>
                <div class="d-flex cookies_btns justify-content-between py-4 align-items-center">
                    <button id="Cookies_btn" class="cookie-btn " type="submit">Guardar</button>
                    <button id="Cookies_volver" type="button" class="cookie-btn ">Volver</button>
                </div>
            </form>
        </div>
    </div>
    <div id="cookie_main" class="cookie_info cookies_dialog">
        <h2>Uso de cookies</h2>
        <p style="font-family: sans-serif; text-align: left;">
            En <?=getConfParam('SITE_NAME')?> usamos cookies propias y de terceros. <br>
            Las cookies propias sirven para recordar tus preferencias de navegación y para mantenerte conectado.
        </p>

        <p class="text-center">
            Visita nuestra
            <a href="terminos-y-condiciones-de-uso#cookies" target="_blank" id="more_info_cookies" rel="nofollow">Politica de cookies</a>.
        </p>

        <div>
            <button  id="agree_cookies" class="cookie-btn"><b>Aceptar</b></button>
            <button id="reject_cookies" class="cookie-btn cookie-btn-dark"><b>Salir</b></button>
            <p class="options_cookies">
                <a href="#configuracion" id="confi_cookies">Configuración de cookies</a>
            </p>
        </div>


    </div>
</div>