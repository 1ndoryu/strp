<?
$registrado=false;
$ya_existe=false;
?>
<div class="login_div" style="display: none;" div>
<span id="close"><i class="far fa-times-circle" aria-hidden="true"></i></span>
<h2><?=$language['access.title_access']?></h2>
<div id="response_login"></div>
<form id="formLogin" name="formLogin">
<div class="access-group">
  <i class="fa fa-envelope"></i>
  <input name="mail_login" placeholder="Email" type="email" id="mail_login">
</div>
<div class="field-pass">
  <i class="fa fa-lock"></i>
	<input name="pass_login" placeholder="Contraseña" type="password" id="pass_login">
	<span toggle="#pass_login" class="fa fa-fw fa-eye field-icon toggle-password"></span>
</div>

<!--<input name="do_reg" id="do_reg" type="button" value="¡Crea tu cuenta ahora!" />-->
<div class="camp-inline text-center">
  <a href="javascript:void(0);" id="recover_pass">
    <p><?=$language['access.txt_recover']?></p>
  </a>
</div>
<input name="do_login" id="do_login" type="button" value="<?=$language['access.button_login']?>" onclick="login()" />

</form>
<div id="forgot" class="hidden">
<div id="response_recover"></div>
<form id="formRecover" name="formRecover">
<div class="access-group">
  <i class="fa fa-envelope"></i>
  <input name="email_rec" placeholder="Email" type="email" id="email_rec"/>
</div>
<input type="button" name="recuperar" value="<?=$language['access.button_recover_2']?>" id="recPass"/>
</form>
</div>
</div>

<script type="text/javascript">
	
$(".toggle-password").click(function() {

  $(this).toggleClass("fa-eye fa-eye-slash");
  var input = $($(this).attr("toggle"));
  if (input.attr("type") == "password") {
    input.attr("type", "text");
  } else {
    input.attr("type", "password");
  }
});
</script>