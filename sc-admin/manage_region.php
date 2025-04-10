<?
$error_div=false;
$exito_div=false;
if(isset($_GET['list'])){
// Borrar categoría hijo
if(isset($_GET['del'])){
	deleteSQL("sc_city", $w=array('ID_city'=>$_GET['del']));
	$exito_div=$language_admin['manage_region.city_deleted'];
}
// Nueva categoría hijo
if(isset($_POST['new_cat2'])){
	insertSQL("sc_city", $a=array('name'=>$_POST['new_cat2'],'name_seo'=>toAscii($_POST['new_cat2']),'ID_region'=>$_GET['list']));
	$exito_div=$language_admin['manage_region.city_created'];
}
$child = selectSQL("sc_city", $b=array('ID_region'=>$_GET['list']),"name ASC");
if(!isset($_GET['edt'])){?>
<h2><?=$language_admin['manage_region.title_h1']?></h2>
<form action="<? $_SERVER['PHP_SELF']; ?>" method="post" class="param_form">
	<div>
	<label><?=$language_admin['manage_region.add_city']?></label>
	<input name="new_cat2" type="text">
	</div>
	<input name="Add" type="submit" value="<?=$language_admin['manage_region.button_add']?>">
</form>
<hr />
<? if($exito_div!==FALSE) {?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$exito_div;?></div>
<? } ?>
<a href="index.php?id=manage_region" class="back">&laquo; <?=$language_admin['manage_region.back_region']?></a>
<ul class="list_categories">
	<? for($i=0;$i<count($child);$i++){?>
    <li>
    	<span class="col_left"><?=$child[$i]['name'];?></span>
    	<span class="col_right">
        <a href="index.php?id=manage_region&list=<? echo $_GET['list']; ?>&edt=<? echo $child[$i]['ID_city']; ?>"><?=$language_admin['manage_region.edit']?></a>
        <a href="index.php?id=manage_region&list=<? echo $_GET['list']; ?>&del=<? echo $child[$i]['ID_city']; ?>"><?=$language_admin['manage_region.delete']?></a>
        </span>
    </li>
<? }?>
</ul>
<? }else{
	// Modificar categoría hijo
	if(isset($_POST['modcat2'])){
		updateSQL("sc_city",$a=array('name'=>$_POST['modcat2'],'name_seo'=>toAscii($_POST['modcat2'])), $s=array('ID_city'=>$_GET['edt']));
		$exito_div=$language_admin['manage_region.city_edited'];
	}
	$mod = selectSQL("sc_city", $b=array('ID_city'=>$_GET['edt']));
?>
<h2><?=$language_admin['manage_region.edit_city']?></h2>
<? if($exito_div!==FALSE) {?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$exito_div;?></div>
<? } ?>
<form action="<? $_SERVER['PHP_SELF']; ?>" method="post" class="param_form">
	<div>
		<label><?=$language_admin['manage_region.edit_city']?></label>
        <input name="modcat2" type="text" value="<?=$mod[0]['name'];?>">
	</div>
    <input name="edit_" type="submit" value="<?=$language_admin['manage_region.button_save']?>" >
	</form>
	<a href="index.php?id=manage_region&list=<?=$_GET['list'];?>" class="back">&laquo; <?=$language_admin['manage_region.back_city']?></a>
<?
	}
}else{
// Borrar categoría padre
if(isset($_GET['borrar'])){
	deleteSQL("sc_region", $da=array('ID_region'=>$_GET['borrar']));
	deleteSQL("sc_city", $dsa=array('ID_region'=>$_GET['borrar']));
	$exito_div=$language_admin['manage_region.region_deleted'];
}
// Nueva categoría padre
if(isset($_POST['new_cat'])){
	insertSQL("sc_region", $a=array('name'=>$_POST['new_cat'],'name_seo'=>toAscii($_POST['new_cat'])));
	$exito_div=$language_admin['manage_region.region_created'];
}
?>
<?
if(!isset($_GET['edit'])){
$parent = selectSQL("sc_region", $b=array(),"name ASC");
?>
<h2><?=$language_admin['manage_region.title_h1']?></h2>
<form action="<? $_SERVER['PHP_SELF']; ?>" method="post" class="param_form">
	<div>
	    <label><?=$language_admin['manage_region.add_region']?></label>
	    <input name="new_cat" type="text">
    </div>
    <input name="add" type="submit" value="<?=$language_admin['manage_region.button_add']?>">
</form>
<hr />
<? if($exito_div!==FALSE) {?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$exito_div;?></div>
<? } ?>
<ul class="list_categories">
<? for($i=0;$i<count($parent);$i++){?>
    <li>
    	<span class="col_left"><?=$parent[$i]['name'];?></span>
    	<span class="col_right">
        <a href="index.php?id=manage_region&list=<?=$parent[$i]['ID_region']; ?>"><?=$language_admin['manage_region.view_cities']?></a>
        <a href="index.php?id=manage_region&edit=<?=$parent[$i]['ID_region']; ?>"><?=$language_admin['manage_region.edit']?></a>
        <a href="index.php?id=manage_region&borrar=<?=$parent[$i]['ID_region']; ?>"><?=$language_admin['manage_region.delete']?></a>
        </span>
    </li>
<? }?>
</ul>
<? }else{
	/// MODIFICAR CATEGORÍA PADRE
		$mod = selectSQL("sc_region", $b=array('ID_region'=>$_GET['edit']));
		if(isset($_POST['modcat'])){
			updateSQL("sc_region",$a=array('name'=>$_POST['modcat'],'name_seo'=>toAscii($_POST['modcat'])), $s=array('ID_region'=>$mod[0]['ID_region']));
		$exito_div=$language_admin['manage_region.region_edited'];
		}
		$mod = selectSQL("sc_region", $b=array('ID_region'=>$_GET['edit']));
		?>
        <h2><?=$language_admin['manage_region.edit_region']?></h2>
        <? if($exito_div!==FALSE) {?>
        <div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$exito_div;?></div>
        <? } ?>
        <form action="<? $_SERVER['PHP_SELF']; ?>" method="post" class="param_form">
		<div>
        	<label><?=$language_admin['manage_region.edit_region']?></label>
        	<input name="modcat" type="text" value="<?=$mod[0]['name'];?>">
        </div>
        <input name="Modificar" type="submit" value="<?=$language_admin['manage_region.button_save']?>" >
		</form>
        <a href="index.php?id=manage_region" class="back">&laquo; <?=$language_admin['manage_region.back_region']?></a>
		<?
		}
 }
 ?>