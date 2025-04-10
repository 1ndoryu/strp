<?php 
    require_once "inc/mrocever_pass.php";
?>

<div class="container-fluid my-5">
    <div class="row py-5">
        <div class="col-4"></div>
        <div class="col-4 bg-light text-center shadow-sm border-light border-1 py-4">
            <?php if( comprovarToken($_GET['token']) ): ?>
                <h3>recuperar contraseña</h3>
                <form  method="post" class="py-2">
                    <div class="form-group">
                        <label for="">Nueva Contraseña</label>
                        <input type="password" name="pass" id="newpassadmin" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Confirmar Contraseña</label>
                        <input type="password" name="confirm-pass" id="pass-confirm" class="form-control">
                        <div class="invalid-feedback">
                            las contraseñas no coinsiden
                        </div>
                    </div>
                    <input type="submit" value="Cambiar Contraseña" class="btn btn-primary" id="newpassadminb">
                </form>
            <?php else: ?>
                <div class="bg-danger p-4">
                    Error Al Validar
                </div>
                <a class="pt-3 alert-link d-block" href="<?=getConfParam('SITE_URL') ?>">volver</a>
            <?php endif ?>
        </div>
        <div class="col-4"></div>
    </div>
</div>