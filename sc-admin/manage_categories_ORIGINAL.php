<?
$error_div=false;
$exito_div=false;
if(isset($_GET['list'])){
// Borrar categoría hijo
if(isset($_GET['del'])){
	deleteSQL("sc_category", $w=array('ID_cat'=>$_GET['del']));
	$exito_div=$language_admin['manage_categories.subcat_deleted'];
}
// Nueva categoría hijo
if(isset($_POST['new_cat2'])){
	insertSQL("sc_category", $a=array('name'=>$_POST['new_cat2'],'name_seo'=>toAscii($_POST['new_cat2']),'parent_cat'=>$_GET['list'],'seo_title'=>$_POST['new_cat_seo_title'],'seo_desc'=>$_POST['new_cat_seo_desc'],'seo_keys'=>$_POST['new_cat_seo_keys'],'field_0'=>$_POST['f0'],'field_3'=>$_POST['f3'],'field_2'=>$_POST['f2']));
	$exito_div=$language_admin['manage_categories.subcat_created'];
}
$child = selectSQL("sc_category", $b=array('parent_cat'=>$_GET['list']),"name ASC");
if(!isset($_GET['edt'])){?>
<h2><?=$language_admin['manage_categories.title_h1_2']?></h2>
<form action="<? $_SERVER['PHP_SELF']; ?>" method="post" class="param_form">
            <label><?=$language_admin['manage_categories.add_subcat']?></label>
            <input name="new_cat2" type="text">
            <div class="clear"></div>
            <label><?=$language_admin['manage_categories.seo_title']?></label>
            <input name="new_cat_seo_title" type="text" class="long_text">
            <div class="clear"></div>
            <label><?=$language_admin['manage_categories.seo_desc']?></label>
            <input name="new_cat_seo_desc" type="text" class="long_text">
            <div class="clear"></div>
            <label><?=$language_admin['manage_categories.seo_keys']?></label>
            <input name="new_cat_seo_keys" type="text" class="long_text">
            <div class="clear"></div>
            <label>Campos vehículos (Km, año, combustible..)</label>
            <input name="f0" type="checkbox" value="1"> Sí
            <div class="clear"></div>
            <label>Campos inmobiliaria (Habitaciones, baños)</label>
            <input name="f3" type="checkbox" value="1"> Sí
            <div class="clear"></div>
            <label>Campos inmobiliaria (Superficie m2)</label>
            <input name="f2" type="checkbox" value="1"> Sí
            <div class="clear"></div>
	<input name="add" type="submit" value="<?=$language_admin['manage_categories.button_add']?>">
</form>
<hr />
<? if($exito_div!==FALSE) {?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$exito_div;?></div>
<? } ?>
<ul class="list_categories">
<? for($i=0;$i<count($child);$i++){?>
	<li>
    	<span class="col_left"><?=$child[$i]['name'];?></span>
    	<span class="col_right">
        <a href="index.php?id=manage_categories&list=<?=$_GET['list']; ?>&edt=<?=$child[$i]['ID_cat']; ?>"><?=$language_admin['manage_categories.edit']?></a>
        <a href="index.php?id=manage_categories&list=<?=$_GET['list']; ?>&del=<?=$child[$i]['ID_cat']; ?>"><?=$language_admin['manage_categories.delete']?></a>
        </span>
    </li>
<? }?>
</ul>
<a href="index.php?id=manage_categories" class="back">&laquo; <?=$language_admin['manage_categories.back_cat']?></a>
<? 
}else{
// Modificar categoría hijo
	if(isset($_POST['modcat2'])){
		updateSQL("sc_category",$a=array('name'=>$_POST['modcat2'],'seo_title'=>$_POST['new_cat_seo_title'],'seo_desc'=>$_POST['new_cat_seo_desc'],'seo_keys'=>$_POST['new_cat_seo_keys'],'field_0'=>$_POST['f0'],'field_3'=>$_POST['f3'],'field_2'=>$_POST['f2']), $s=array('ID_cat'=>$_GET['edt']));
		$exito_div=$language_admin['manage_categories.subcat_edited'];
	}
	$mod = selectSQL("sc_category", $b=array('ID_cat'=>$_GET['edt']));
?>
<h2><?=$language_admin['manage_categories.edit_subcat']?></h2>
<? if($exito_div!==FALSE) {?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$exito_div;?></div>
<? } ?>
<form action="<? $_SERVER['PHP_SELF']; ?>" method="post" class="param_form">
            <label><?=$language_admin['manage_categories.edit_subcat']?></label>
            <input name="modcat2" type="text" value="<?=$mod[0]['name'];?>">
            <div class="clear"></div>
            <label><?=$language_admin['manage_categories.seo_title']?></label>
            <input name="new_cat_seo_title" type="text" class="long_text" value="<?=$mod[0]['seo_title'];?>">
            <div class="clear"></div>
            <label><?=$language_admin['manage_categories.seo_desc']?></label>
            <input name="new_cat_seo_desc" type="text" class="long_text" value="<?=$mod[0]['seo_desc'];?>">
            <div class="clear"></div>
            <label><?=$language_admin['manage_categories.seo_keys']?></label>
            <input name="new_cat_seo_keys" type="text" class="long_text" value="<?=$mod[0]['seo_keys'];?>">
            <div class="clear"></div>
            <label>Campos vehículos (Km, año, combustible..)</label>
            <input name="f0" type="checkbox" value="1" <? if($mod[0]['field_0']==1) echo 'checked';?>> Sí
            <div class="clear"></div>
            <label>Campos inmobiliaria (Habitaciones, baños)</label>
            <input name="f3" type="checkbox" value="1" <? if($mod[0]['field_3']==1) echo 'checked';?>> Sí
            <div class="clear"></div>
            <label>Campos inmobiliaria (Superficie m2)</label>
            <input name="f2" type="checkbox" value="1" <? if($mod[0]['field_2']==1) echo 'checked';?>> Sí
            <div class="clear"></div>
    		<input name="Modificar" type="submit" value="<?=$language_admin['manage_categories.button_save']?>" >
</form>
<a href="index.php?id=manage_categories&list=<?=$_GET['list'];?>" class="back">&laquo; <?=$language_admin['manage_categories.back_subcat']?></a>
<?
}
?>
<?
}else{
// Borrar categoría padre
if(isset($_GET['delete'])){
	deleteSQL("sc_category", $da=array('ID_cat'=>$_GET['delete']));
	deleteSQL("sc_category", $dsa=array('parent_cat'=>$_GET['delete']));
	$exito_div=$language_admin['manage_categories.cat_deleted'];
}
// Nueva categoría padre
if(isset($_POST['new_cat'])){
	$name_img="";
	insertSQL("sc_category", $a=array('name'=>$_POST['new_cat'],'name_seo'=>toAscii($_POST['new_cat']),'parent_cat'=>"-1",'seo_title'=>$_POST['new_cat_seo_title'],'seo_desc'=>$_POST['new_cat_seo_desc'],'seo_keys'=>$_POST['new_cat_seo_keys'],'icon'=>$_POST['icon_cat']));
	$exito_div=$language_admin['manage_categories.cat_created'];
}
if(!isset($_GET['edit'])){
$parent = selectSQL("sc_category", $b=array('parent_cat'=>"0<"),"ID_cat DESC");
?>
<h2><?=$language_admin['manage_categories.title_h1']?></h2>
<form action="<? $_SERVER['PHP_SELF']; ?>" method="post" class="param_form">
    <label><?=$language_admin['manage_categories.add_cat']?></label>
    <input name="new_cat" type="text">
    <div class="clear"></div>
    <label><?=$language_admin['manage_categories.seo_title']?></label>
    <input name="new_cat_seo_title" type="text" class="long_text">
    <div class="clear"></div>
    <label><?=$language_admin['manage_categories.seo_desc']?></label>
    <input name="new_cat_seo_desc" type="text" class="long_text">
    <div class="clear"></div>
    <label><?=$language_admin['manage_categories.seo_keys']?></label>
    <input name="new_cat_seo_keys" type="text" class="long_text">
    <div class="clear"></div>
    <label class="withInfo">Icono de categoría <span>El listado de iconos disponibles esta en <a href="//fontawesome.io/icons/" target="_blank">fontawesome.io/icons/</a></span></label>
    <input type="text" name="icon_cat"/>
    <div class="clear"></div>
    <input name="add" type="submit" value="<?=$language_admin['manage_categories.button_add']?>">
</form>
<hr />
<? if($exito_div!==FALSE) {?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$exito_div;?></div>
<? } ?>
<ul class="list_categories">
<? for($i=0;$i<count($parent);$i++){?>
	<li>
    	<span class="col_left">(Orden: <?=$parent[$i]['ord'];?>) <b><?=$parent[$i]['name'];?></b></span>
    	<span class="col_right">
        <a href="index.php?id=manage_categories&list=<? echo $parent[$i]['ID_cat']; ?>"><?=$language_admin['manage_categories.view_subcat']?></a>
        <a href="index.php?id=manage_categories&edit=<? echo $parent[$i]['ID_cat']; ?>"><?=$language_admin['manage_categories.edit']?></a>
        <a href="index.php?id=manage_categories&delete=<? echo $parent[$i]['ID_cat']; ?>"><?=$language_admin['manage_categories.delete']?></a>
        </span>
    </li>
<? }?>
</ul>* A mayor valor de Orden, m&aacute;s prioridad en aparici&oacute;n.
<? 
}else{
		/// MODIFICAR CATEGORÍA PADRE
		$mod = selectSQL("sc_category", $b=array('ID_cat'=>$_GET['edit']));
		if(isset($_POST['modcat'])){
			updateSQL("sc_category",$a=array('name'=>$_POST['modcat'],'seo_title'=>$_POST['new_cat_seo_title'],'seo_desc'=>$_POST['new_cat_seo_desc'],'seo_keys'=>$_POST['new_cat_seo_keys'],'icon'=>$_POST['icon_cat']), $s=array('ID_cat'=>$mod[0]['ID_cat']));
		$exito_div=$language_admin['manage_categories.cat_edited'];
		}
		$mod = selectSQL("sc_category", $b=array('ID_cat'=>$_GET['edit']));
		?>
        <h2><?=$language_admin['manage_categories.edit_cat']?></h2>
        <? if($exito_div!==FALSE) {?>
        <div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$exito_div;?></div>
        <? } ?>
        <form action="<? $_SERVER['PHP_SELF']; ?>" method="post" class="param_form">
            <label><?=$language_admin['manage_categories.edit_cat']?></label>
            <input name="modcat" type="text" value="<?=$mod[0]['name'];?>">
            <div class="clear"></div>
            <label><?=$language_admin['manage_categories.seo_title']?></label>
            <input name="new_cat_seo_title" type="text" class="long_text" value="<?=$mod[0]['seo_title'];?>">
            <div class="clear"></div>
            <label><?=$language_admin['manage_categories.seo_desc']?></label>
            <input name="new_cat_seo_desc" type="text" class="long_text" value="<?=$mod[0]['seo_desc'];?>">
            <div class="clear"></div>
            <label><?=$language_admin['manage_categories.seo_keys']?></label>
            <input name="new_cat_seo_keys" type="text" class="long_text" value="<?=$mod[0]['seo_keys'];?>">
            <div class="clear"></div>
		    <label class="withInfo">Icono de categoría <span>El listado de iconos disponibles esta en <a href="//fontawesome.io/icons/" target="_blank">fontawesome.io/icons/</a></span></label>
            <input type="text" name="icon_cat" value="<?=$mod[0]['icon']?>"/><i class="icon_category fa <?=$mod[0]['icon']?>"></i>
            <div class="clear"></div>
            <input name="Modificar" type="submit" value="<?=$language_admin['manage_categories.button_save']?>" >
		</form>
        <a href="index.php?id=manage_categories" class="back">&laquo; <?=$language_admin['manage_categories.back_cat']?></a>
		<?
}
}
?>
