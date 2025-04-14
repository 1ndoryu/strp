<?

if (isset($_GET['confirm'])) {
    confirmEmail($_GET['confirm']);
}

check_no_login();

$registrado = false;

$ya_existe = false;



if (isset($_POST['mail_register'])) {

    if (verifyFormToken('registerToken', $_POST['token'])) {

        $ip = get_client_ip();

        $token = md5(uniqid(rand(), true));

        $datos_u = array(
            'name' => formatName($_POST['name_register']),
            'mail' => $_POST['mail_register'],
            'pass' => $_POST['pass1_register'],
            'phone' => "",
            'date_reg' => time(),
            'banner_img' => "",
            'IP_user' => $ip,
            "rol" => $_POST['rol_register'],
            "confirm" => $token
        );

        if (countSQL("sc_user", $a = array('mail' => $_POST['mail_register'])) == 0) {

            $result = insertSQL("sc_user", $datos_u);
        } else $ya_existe = true;

        if ($result) {

            mailRegister(
                formatName($_POST['name_register']),
                $_POST['mail_register'],
                $token
            );

            $registrado = true;
        }
    }
}

?>

<?php if (check_ip()): ?>
    <script>
        alert("No puedes registrarte desde tu IP");
        window.location.href = "index.php";
    </script>
<?php endif ?>

<div class="col_single">

    <h2 class="title"><?= $language['register.title_h1'] ?></h2>

    <form id="register_form" class="fm reg" method="post" action="<? $_SERVER['PHP_SELF']; ?>">

        <? if ($registrado) { ?><div class="info_valid"><?= formatName($_POST['name_register']) ?><?= $language['register.info_valid'] ?></div><? } ?>

        <? if ($ya_existe) { ?><div class="info_invalid"><?= $language['register.info_invalid'] ?></div><? } ?>

        <fieldset>

            <div class="row">

                <div class="col_lft"><label><?= $language['register.label_name'] ?> <i class="required_field">*</i></label></div>

                <div class="col_rgt"><input name="name_register" type="text" id="name_register" size="150" maxlength="150" />
                    <div class="error_msg" id="error_name"><?= $language['register.error_name'] ?></div>

                </div>

            </div>

            <div class="row">

                <div class="col_lft"><label><?= $language['register.label_mail'] ?> <i class="required_field">*</i></label></div>

                <div class="col_rgt">
                    <input name="mail_register" type="email" id="mail_register" size="150" maxlength="150" />
                    <div class="error_msg" id="error_mail"><?= $language['register.error_mail'] ?></div>
                    <div class="error_msg" id="error_mail2">El email esta ya está registrado</div>

                </div>

            </div>



            <div class="row">

                <div class="col_lft"><label><?= $language['register.label_pass'] ?> <i class="required_field">*</i></label></div>

                <div class="col_rgt">
                    <div class="register-pass">
                        <input name="pass1_register" type="password" id="pass1_register" size="20" maxlength="20" />
                        <i class="fa fa-eye"></i>
                    </div>

                    <span onclick="newPassword()" class="register-generate">Generar contraseña</span>

                    <div class="error_msg" id="error_pass1"><?= $language['register.error_pass'] ?></div>

                </div>

            </div>

            <div class="row">

                <div class="col_lft"><label>Eres un</label></div>

                <div class="col_rgt">
                    <select name="rol_register" id="rol_register">
                        <option value="<?= UserRole::Particular ?>"><?= UserRole::NAME(UserRole::Particular) ?></option>
                        <option value="<?= UserRole::Profesional ?>"><?= UserRole::NAME(UserRole::Profesional) ?></option>
                        <option value="<?= UserRole::Visitante ?>"><?= UserRole::NAME(UserRole::Visitante) ?></option>
                    </select>
                </div>

            </div>
            <div class="row">

                <div class="col_lft"></div>

                <div class="col_rgt">
                    <p id="rol_register_info">Para publicar anuncios y encontrar clientes</p>
                    <p style="display: none;" id="rol_visit_info">Para ver anuncios y contactar a anunciantes</p>
                </div>

            </div>


            <div class="row">

                <label class="radio">

                    <input name="terminos" type="checkbox" id="terminos" value="1" />

                    <?= $language['register.label_terms'] ?></label>

                <div class="error_msg" id="error_terminos"><?= $language['register.error_terms'] ?></div>

            </div>

        </fieldset>

        <div class="row">

            <input type="button" class="button" id="butReg" value="<?= $language['register.button_sigin'] ?>" />

        </div>

        <? $token_q = generateFormToken('registerToken'); ?>

        <input type="hidden" name="token" id="token" value="<?= $token_q; ?>">

    </form>

</div>

<script src="src/js/register.js"></script>