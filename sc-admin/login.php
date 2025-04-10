<?php
if(isset($_POST['user'])){
    $resultado = login_admin($_POST['user'], md5($_POST['pass']));
}else
    $resultado = null;

?>
<div class="adminform">
    <form method="post" action="index.php" id="admin_login_form">
        <h2>Acceder al panel</h2>
    <? if($resultado==2){?>
    <div class="info_invalid">Usuario o contraseña incorrecto.</div>
    <? }?>
    <label>Usuario</label>
    <input type="text" name="user" id="user">
    <label>Contraseña</label>
    <div class="group-admin">
        <input type="password" name="pass" id="pass">
        <i onclick="togglePass(this)" class="fa fa-eye"></i>
    </div>
    <input name="login" type="submit" id="login" value="Acceder">
    <div class="text-center">
        <a href="javascript:void(0);" id="recovery_admin_pass"  ><?=$language['access.txt_recover']?></a>
    </div>
    </form>
</div>

<div class="recoverAdmin" style="display: none;">
    <h5 class="text-center">Recuperar Contraseña</h5>
    <form method="post">
        <div class="pt-2 px-5 form-group">
            <input type="email" name="mail" require class="form-control" id="recoverAdminMail">
            <div class="invalid-feedback">

            </div>
            <div class="valid-feedback f-1150">
                <?=$language['javascript.recover_pass_admin'] ?>
            </div>
        </div>
        <input name="login" type="submit" class="mt-1" id="recoverPassAdmin" value="Recuperar">
    </form>
    <div class="text-center">
        <a href="javascript:void(0);" id="backtoadminlogin" style="color: black;" >Volver</a>
    </div>
</div>