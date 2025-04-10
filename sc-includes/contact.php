<?
$contacted=false;
$contacted_fail=false;
// Report item
if(isset($_GET['report'])){
	$ad=getDataAd($_GET['report']);
	$message=$language['contact.report_ad']." - ".$ad['ad']['title']." - ".urlAd($ad['ad']['ID_ad']);
}

if(isset($_GET['ad'])){
	$message = "ID del anuncio: ". $_GET['ad'] . "\n";

}

if(isset($_POST['g-recaptcha-response'])){
	
	$Return = getCaptcha($_POST['g-recaptcha-response']);
	if($Return->success == true && $Return->score  > 0.5){
		if(isset($_POST['contact_mail']) && isset($_POST['contact_name']) && isset($_POST['mens'])){
			if(verifyFormToken('contactFrm',$_POST['token'])){
				if(mailContactWeb(formatName($_POST['contact_name']),$_POST['contact_mail'],$_POST['mens'],$_POST['contact_sub']))
					$contacted=true;
				else $contacted_fail=true;
			}
		}
	}else
		$recaptcha_invalid = "Captcha invalido";
}
?>
<? if($contacted){ ?><div class="info_valid"><?=$language['contact.send_valid']?></div><? } ?>
<? if($contacted_fail){ ?><div class="info_invalid"><?=$language['contact.send_invalid']?></div><? } ?>
<?php if( isset($recaptcha_invalid) ): ?>
	<div class="info_invalid"><?=$recaptcha_invalid?></div>
<?php endif ?>

<script src='https://www.google.com/recaptcha/api.js?render=<?php echo SITE_KEY; ?>'></script>

<div class="col_single">
<h1><?=$language['contact.title_h1']?><?=getConfParam('SITE_NAME');?></h1>
<div class="contactar">
<form class="fm" method="post"  id="form_contact_web">
<fieldset>
<div class="row">
	<div class="col_lft"><label><?=$language['contact.label_name']?></label></div>
	<div class="col_rgt">
		<input name="contact_name" type="text" id="contact_name" placeholder="<?=$language['contact.placeholder_name']?>" size="35" />
		<div class="error_msg" id="error_contact_name"><?=$language['contact.error_name']?></div>
	</div>
</div>
<div class="row">
	<div class="col_lft"><label>Asunto</label></div>
	<div class="col_rgt">
		<input name="contact_sub" type="text" id="contact_sub" size="35" value=""/>
		<div class="error_msg" id="error_contact_sub">Escriba un asunto</div>
	</div>
</div>
<div class="row">
<div class="col_lft"><label><?=$language['contact.label_mail']?></label></div>
<div class="col_rgt">
	<input name="contact_mail" type="text" id="contact_mail" placeholder="<?=$language['contact.placeholder_mail']?>" size="35" value="<?=$_SESSION['data']['mail'];?>"/>
	<div class="error_msg" id="error_contact_mail"><?=$language['contact.error_mail']?></div>
	<?php if( !isset($_GET['report']) ): ?>
		<div class=" mail-desc">Si eres un usuario envíanos la Ref. de tu anuncio.</div>
	<?php endif ?>
</div>
</div>
<div class="row">
<div class="col_lft">
	<label class="txtarea"><?=$language['contact.label_txt']?></label>
</div>
<div class="col_rgt">
	<textarea name="mens" cols="30" rows="4" id="mens"><?=$message;?></textarea>
	<div class="error_msg" id="error_mens"><?=$language['contact.error_txt']?></div>
</div>
</div>
</fieldset>
<div class="row">
	<div class="col_rgt">
		<label class="text-left">
			<input type="checkbox" name="privacidad" id="cprivacidad" value="true" >
			He leído y acepto la <a class="link-gray" href="proteccion-de-datos" target="_blank">política de privacidad, <a/><a class="link-gray" href ="aviso-legal/" target="_blank">aviso y </a><a class="link-gray" href="'.$urlfriendly['url.terms'].'" target="_blank">condiciones de uso.</a>
		</label>
		<div class="error_msg" id="error_privacity">Debes aceptar nuestra politica de privacidad</div>
	</div>
</div>
<? $token_q = generateFormToken('contactFrm'); ?>
<input type="hidden" name="token" id="token" value="<?=$token_q;?>">
<input type="button" name="cont_web" id="cont_web" value="CONTACTAR" />
<input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" />
</form>
</div>
</div>
<script>
	function submitFormCaptchaContact () {
    	grecaptcha.ready(function() {
			grecaptcha.execute('<?=SITE_KEY ?>', {action: 'submit'}).then(function(token) 
			{
		        document.getElementById('g-recaptcha-response').value=token;
				//console.log(token);
		        $("#form_contact_web").submit();
		    });
	    });
	}
</script>