<?php 
    $exito_div = false;
    if(isset($_POST['to-email']) && isset($_POST['email'])){
        $msg = toHtml($_POST['msg']);
        if(CompartirMail($_POST['to-email'], $_POST['to-name'], $_POST['email'], $_POST['name'], $msg, $_POST['id_ad']))
            $exito_div = 'Mensaje Enviado';
    }

?>


<div class="col_single">
    <h1>Compartir</h1>
    <?php if( $exito_div !== false ): ?>
    <div class="info_valid"><?=$exito_div?></div>
    <?php endif ?>
    <form id="form_compartir_mail" method="post" class="fm">
        <div class="row">
            <div class="col_lft"><label>Nombre del Destinatario</label></div>
            <div class="col_rgt">
                <input type="text" name="to-name" id="compartir_to_name">
                <div class="error_msg" id="error_compartir_to_name">Falta el nombre</div>
            </div>
        </div>
        <div class="row">
            <div class="col_lft"><label>Email del Destinatario *</label></div>
            <div class="col_rgt">
                <input type="email" name="to-email" id="compartir_to_email">
                <div class="error_msg" id="error_compartir_to_email">Falta el Email</div>
            </div>
        </div>
        <div class="row">
            <div class="col_lft"><label>Tu nombre</label></div>
            <div class="col_rgt">
                <input type="text" value="<?=$_SESSION['data']['name'];?>" name="name" id="compartir_name">
                <div class="error_msg" id="error_compartir_name">Falta el Nombre</div>
            </div>
        </div>
        <div class="row">
            <div class="col_lft"><label >Tu Email *</label></div>
            <div class="col_rgt">
                <input type="email" value="<?=$_SESSION['data']['mail'];?>" name="email" id="compartir_email">
                <div class="error_msg" id="error_compartir_email">Falta el email</div>
            </div>
        </div>
        <div class="row">
            <div class="col_lft"><label>Mensaje *</label></div>
            <div class="col_rgt">
                <textarea name="msg" id="compartir_msg" cols="30" rows="10"></textarea>
                <div class="error_msg" id="error_compartir_msg">Escriba un mensaje</div>
            </div>
        </div>
        
        <input type="hidden" name="id_ad" value="<?php if( isset($_GET['id_ad'])) echo urlAd($_GET['id_ad']); else print '';?>">

        <div class="row">
            <div class="col_rgt">
                <label class="text-left">
                    <input type="checkbox" name="privacidad" id="cprivacidad" value="true" >
                    He leído y acepto la <a class="link-gray" href="proteccion-de-datos" target="_blank">política de privacidad, <a/><a class="link-gray" href ="aviso-legal/" target="_blank">aviso y </a><a class="link-gray" href="'.$urlfriendly['url.terms'].'" target="_blank">condiciones de uso.</a>
                </label>
                <div class="error_msg" id="error_privacity">Debes aceptar nuestra politica de privacidad</div>
            </div>
        </div>

        <input type="button" value="Compartir" id="btn_compartir_email">
    </form>
</div>