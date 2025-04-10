<?
$exito_div=false;
if(isset($_POST['ADMIN_USER'])){
	$config_save=array(
		'ADMIN_MAIL'=>$_POST['ADMIN_MAIL'],
		'ADMIN_USER'=>$_POST['ADMIN_USER'],
		'ADMIN_PASS'=>md5($_POST['ADMIN_PASS']),
	);
	foreach($config_save as $param => $value){
		updateSQL("sc_config",$s=array('value_param'=>$value),$w=array('name_param'=>$param));
	}
	$exito_div=$language_admin['admin_user.saved'];
}
?>
<h2><?=$language_admin['admin_user.title_h1']?></h2>
<? if($exito_div!==FALSE) {?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$exito_div;?></div>
<? } ?>
<form action="<? $_SERVER['PHP_SELF'];?>" method="post" class="param_form">
<div>
	<label>Usuario administrador:</label>
	<input type="text" name="ADMIN_USER" value="<?=getConfParam('ADMIN_USER')?>">
</div>
<div>
	<label>Dirección de email del administrador:</label>
	<input type="text" name="ADMIN_MAIL" value="<?=getConfParam('ADMIN_MAIL')?>">
</div>
<div>
	<label>Contraseña del administrador:</label>
	<input type="password" name="ADMIN_PASS" >
</div>
    <input name="update_data" type="submit" id="update_data" value="<?=$language_admin['admin_user.button_save']?>">
</form>