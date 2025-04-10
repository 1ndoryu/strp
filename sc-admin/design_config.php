<?
$config_change=false;
if(isset($_POST['save_config'])){
	$ac_w = 0;
	if(isset($_POST['AC_WATEMARK']) && $_POST['AC_WATEMARK'] == 1)
		$ac_w = 1;

	$config_save=array(
		'BODY_COLOR'=>$_POST['BODY_COLOR'],
		'TOPBAR_COLOR'=>$_POST['TOPBAR_COLOR'],
		'HEADER_COLOR'=>$_POST['HEADER_COLOR'],
		'FOOTER_COLOR'=>$_POST['FOOTER_COLOR'],
		'SEARCHBAR_COLOR'=>$_POST['SEARCHBAR_COLOR'],
		'AC_WATERMARK' => $ac_w
	);
	
	if(isset($_FILES['LOGO'])){
		$ext = pathinfo($_FILES['LOGO']['name'], PATHINFO_EXTENSION);
		$na_img= "logo.".$ext;
		$dest = ABSPATH . IMG_PATH . $na_img;
	
		if (move_uploaded_file($_FILES['LOGO']['tmp_name'], $dest)) {
			$config_save['LOGO_IMAGE']=$na_img;
		}
	}
	
	if(isset($_FILES['WATEMARK'])){
		$ext = pathinfo($_FILES['WATEMARK']['name'], PATHINFO_EXTENSION);
		$na_img= "estampa.".$ext;
		$dest = ABSPATH . IMG_PATH . $na_img;
	
		if (move_uploaded_file($_FILES['WATEMARK']['tmp_name'], $dest)) {
			$config_save['WATEMARK_IMAGE']=$na_img;
		}
	}
		
	foreach($config_save as $param => $value){
		updateSQL("sc_config",$s=array('value_param'=>$value),$w=array('name_param'=>$param));
	}
	$config_change=$language_admin['param_config.save_changes'];
}
?>
<h2>Ajustes de diseño</h2>
<? if($config_change!==FALSE){?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$config_change?></div>
<? } ?>
<form action="<? $_SERVER['PHP_SELF'];?>" method="post" class="param_form" enctype="multipart/form-data">
<legend>Imágenes de la web</legend>
<div>
	<label>Logo de la página (Medidas recomendadas: 250x60)</label>
	<input type="file" name="LOGO">
    <div class="preview_content">
    	<div class="preview logo"><img src="<?=getConfParam('SITE_URL')?><?=IMG_PATH?><?=getConfParam('LOGO_IMAGE')?>?<?=randomString(3, true)?>"/></div>
    </div>    
</div>
<div>
	<label>Marca de agua para las fotos (.png)</label>
	<input type="file" name="WATEMARK">
    <div class="preview_content">
	    <div class="preview watemark"><img src="<?=getConfParam('SITE_URL')?><?=IMG_PATH?><?=getConfParam('WATEMARK_IMAGE')?>?<?=randomString(3, true)?>"/></div>
    </div>    
</div>
<div>
	<?php $ac_w = getConfParam('AC_WATERMARK'); ?>
	<label>Activar Marca de agua <input type="checkbox" <?php if($ac_w) print 'checked' ?> value="1" name="AC_WATEMARK"> </label>
	
</div>
<legend>Colores de la web</legend>
<?
$param_config_form=array('BODY_COLOR','TOPBAR_COLOR','HEADER_COLOR','FOOTER_COLOR','SEARCHBAR_COLOR');
for($i=0;$i<count($param_config_form);$i++)
	echo '<div><label>'.getConfText($param_config_form[$i]).'</label>
		  <input class="jscolor" type="text" name="'.$param_config_form[$i].'" value="'.getConfParam($param_config_form[$i]).'"></div>
		  ';
?>
<input name="save_config" type="submit" id="save_config" value="<?=$language_admin['param_config.button_save']?>">
</form>