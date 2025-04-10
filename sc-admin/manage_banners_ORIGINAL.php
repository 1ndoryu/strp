<?
$actualizado=false;
$banners_data=selectSQL("sc_banners", $w=array('size'=>$_GET['size']));
	if(isset($_POST['banner_code'])){

			$banners_id=array();
			$banners_code=array();
			foreach($_POST['id'] as $n => $value){
				$banners_id[]=$value;
			}
			foreach($_POST['banner_code'] as $n => $value){
				$banners_code[]=$value;
			}
		
			for($i=0;$i<count($banners_id);$i++){
				updateSQL("sc_banners",$das=array('code'=>addslashes($banners_code[$i])),$w=array('ID_banner'=>$banners_id[$i]));
			}
			
			$actualizado=true;
	}
$banners_data=selectSQL("sc_banners", $w=array());
?>
<h2>Configurar Banners</h2>
<form action="<? $_SERVER['PHP_SELF'];?>" method="post" class="param_form">
<? if($actualizado){?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i>Banners actualizados correctamente.</div>
<? } ?>
<div class="info_banners">Puedes añadir códigos de banners adaptables para que se adapten correctamente a cualquier dispositivo.</div>
<? for($i=0;$i<count($banners_data);$i++){?>
<div>
<label>Banner <?=$banners_data[$i]['name'];?></label>
<textarea name="banner_code[]"><?=stripslashes($banners_data[$i]['code']);?></textarea>
<input type="hidden" name="id[]" value="<?=$banners_data[$i]['ID_banner'];?>">
</div>
<? } ?>
<input name="enviar" type="submit" id="enviar" value="<?=$language_admin['banner.button_save']?>">
</form>