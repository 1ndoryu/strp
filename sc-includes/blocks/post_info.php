<div class="post-info">
    <a href="javascript:void(0)" class="post-info-button" id="post_info_btn" >
        <i class="fa fa-info-circle"></i>
    </a>
    <dialog class="post-info-dialog" id="post_info">
        <div class="post-info-container">
            <p>No te olvides</p>
            <ul>
                <li>
                    <i class="fa fa-check-circle"></i>
                    <span>Si subes una imagen en lenceria decuerpo entero será recortada.</span>
                </li>
                <li>
                    <i class="fa fa-check-circle"></i>
                    <span>Los servicios deben ser publicados de acuerdo con la categoría.</span>
                </li>
                <li>
                    <i class="fa fa-check-circle"></i>
                    <span>No repita imágenes y textos. Si no tu anuncio será eliminado.</span>
                </li>
                <li>
                    <i class="fa fa-check-circle"></i>
                    <span>No suba imágenes con números de teléfono o url, ni giradas o capturas de pantalla.</span>
                </li>
                <li>
                    <i class="fa fa-check-circle"></i>
                    <span>No publique en mas de 1 categoria.</span>
                </li>
                <li>
                    <i class="fa fa-check-circle"></i>
                    <span>Poner los números de teléfono, precio y horario en el campo destinado para ellos.</span>
                </li>
                <li>
                    <i class="fa fa-check-circle"></i>
                    <span>No pongas enlaces a otras web.</span>
                </li>
                <li>
                    <i class="fa fa-check-circle"></i>
                    <span>Si eres un centro no subas varios anuncios a la vez.</span>
                </li>
                <li>
                    <i class="fa fa-check-circle"></i>
                    <span>No publique textos en MAYÚSCULAS.</span>
                </li>
                <li>
                    <i class="fa fa-check-circle"></i>
                    <span>No suba imágenes con números de teléfono, url, textos, marca de agua, giradas o capturas de pantalla. Solo se permite el nombre del centro de la masajista.</span>
                </li>
                <li>
                    <i class="fa fa-check-circle"></i>
                    <span>No ponga número de teléfono, disponibilidad, horario, precio en el título o descripción del anuncio.</span>
                </li>
            </ul>
            <a target="_blank" href="<?=getConfParam("SITE_URL")?>preguntas-frecuentes" class="btn post-info-link-main">
                Preguntas frecuentes
            </a>
            <a target="_blank" href="<?=getConfParam("SITE_URL")?>aviso-legal" class="post-info-link-secondary">
                Visita nuestro blog
            </a>
        </div>
        <a href="javascript:void(0)" onclick="$('.post-info-container').toggleClass('open')" class="post-info-open">
            Ver más
        </a>
    </dialog>
</div>