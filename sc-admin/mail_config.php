<?
$config_change=false;
if(isset($_POST['save_config'])){
	$config_save=array(
		'DEFAULT_NAME'=>$_POST['DEFAULT_NAME'],
		'DEFAULT_MAIL'=>$_POST['DEFAULT_MAIL'],
		// SMTP config
		'SMTP'=>$_POST['SMTP'],
		'SMTP_HOST'=>$_POST['SMTP_HOST'],
		'SMTP_PORT'=>$_POST['SMTP_PORT'],
		'SMTP_USER'=>$_POST['SMTP_USER'],
		'SMTP_PASSWORD'=>$_POST['SMTP_PASSWORD'],
	);
	foreach($config_save as $param => $value){
		updateSQL("sc_config",$s=array('value_param'=>$value),$w=array('name_param'=>$param));
	}
	$config_change=$language_admin['param_config.save_changes'];
}
?>
<h2>Configurar Email</h2>
<? if($config_change!==FALSE){?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$config_change?></div>
<? } ?>
<form action="<? $_SERVER['PHP_SELF'];?>" method="post" class="param_form">
<legend>Dirección de email</legend>
<?
$param_config_form=array('DEFAULT_NAME','DEFAULT_MAIL');
for($i=0;$i<count($param_config_form);$i++)
	echo '<div><label>'.getConfText($param_config_form[$i]).'</label>
		  <input type="text" name="'.$param_config_form[$i].'" value="'.getConfParam($param_config_form[$i]).'"></div>
		  ';
?>
<legend>Datos SMTP</legend>
<? formRadioConfigParam('SMTP'); ?>
<?
if(getConfParam('SMTP')==1){
	if(getConfParam('SMTP_HOST')!="" && getConfParam('SMTP_PORT')!="" && getConfParam('SMTP_USER')!="" && getConfParam('SMTP_PASSWORD')!=""){
	$smtpValid=checkSMTP();
	}
	if($smtpValid){ ?>
	<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i> La conexión al servidor SMTP es correcta.</div>
	<?
	}else{ ?>
	<div class="info_invalid"><i class="fa fa-exclamation-circle" aria-hidden="true"></i> La conexión al servidor SMTP no es correcta.</div>
	<? }
	?>
	<?
    $param_config_form=array('SMTP_HOST','SMTP_PORT','SMTP_USER');
    for($i=0;$i<count($param_config_form);$i++)
        echo '<div><label>'.getConfText($param_config_form[$i]).'</label>
              <input type="text" name="'.$param_config_form[$i].'" value="'.getConfParam($param_config_form[$i]).'"></div>
              ';
    ?>
    <div><label><?=getConfText('SMTP_PASSWORD')?></label>
              <input type="password" name="SMTP_PASSWORD" value="<?=getConfParam('SMTP_PASSWORD')?>"></div>
<? } ?>
<input name="save_config" type="submit" id="save_config" value="<?=$language_admin['param_config.button_save']?>">
</form>