
<?php if(!$_COOKIE['ageadv']): ?>
  
    <div <?=!isset($_COOKIE['cookie_advice']) ? 'style="display:none;"' : '' ?> class="adv-container">
        <div class="adv-msg">
            <!-- <h2>Advertencia para menores de edad</h2> -->
            <!-- <p>Soy mayor de edad y soy conciente que en esta sección se puede mostrar <span>contenido para adultos.</span></p>
            <span>No mostraré este contenido para menores de edad.</span>
            <span>Al acceder aceptas las condiciones de uso.</span> -->
            <h2>Soy mayor de edad</h2>
            <p>Soy consciente de que en esta sección se puede mostrar <span>contenido para adultos.</span></p>
            <p>Al aceptar aceptas las condiciones de uso.</p>
            <button class="btn btn-blue" onclick="closeadv()">ENTRAR</button>
        </div>
    </div>
    
    <script>
        function closeadv(){
            $('.adv-container').hide(0);
            setCookie('ageadv','1',365);
        }
    </script>
       
<?php endif ?>
