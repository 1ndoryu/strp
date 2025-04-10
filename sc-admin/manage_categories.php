<?
loadModule("filter");
$error_div=false;
$exito_div=false;
if(isset($_POST['save_order']))
{
    $data = $_POST['data'];
    $data = json_decode($data, true);
    foreach($data as $id => $order){
        updateSQL("sc_category", array('ord'=>($order + 1)), array('ID_cat'=>$id));
    }
    $exito_div=$language_admin['manage_categories.subcat_edited'];
}

$exito_div = ModFilter::catch();

if(isset($_GET['list'])){
// Borrar categoría hijo
if(isset($_GET['del'])){
	deleteSQL("sc_category", $w=array('ID_cat'=>$_GET['del']));
	$exito_div=$language_admin['manage_categories.subcat_deleted'];
}
// Nueva categoría hijo
if(isset($_POST['new_cat2'])){
	insertSQL("sc_category", $a=array(
        'name'=>$_POST['new_cat2'],
        'name_seo'=>toAscii($_POST['new_cat2']),
        'parent_cat'=>$_GET['list'],
        'seo_title'=>$_POST['new_cat_seo_title'],
        'seo_desc'=>$_POST['new_cat_seo_desc'],
        'seo_keys'=>$_POST['new_cat_seo_keys'],
        'text'=>$_POST['text'],
        'field_0'=> isset($_POST['f0']) ? 1 : 0,
        'field_3'=> isset($_POST['f3']) ? 1 : 0,
        'field_2'=> isset($_POST['f2']) ? 1 : 0,
        'icon' => '', 
        'ord' => 0));
	$exito_div=$language_admin['manage_categories.subcat_created'];
}
$child = selectSQL("sc_category", $b=array('parent_cat'=>$_GET['list']),"name ASC");
if(!isset($_GET['edt'])){?>
<h2><?=$language_admin['manage_categories.title_h1_2']?></h2>
<form action="<? $_SERVER['PHP_SELF']; ?>" method="post" class="param_form white_form">
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
            <div class="row-content">
                <span class="form-separator"></span>
                <div class="form_group">
                    <label>Campos vehículos (Km, año, combustible..)
                   <span class="float-right">
                       <input name="f0" type="checkbox" value="1" <? if($mod[0]['field_0']==1) echo 'checked';?>> Sí
                   </span> 
                    </label>                
                </div>
            </div>
            <div class="clear"></div>
            <div class="row-content">
                <span class="form-separator"></span>
                <div class="form_group">
                    <label>Campos inmobiliaria (Habitaciones, baños)
                        <span class="float-right">
                            <input name="f3" type="checkbox" value="1" <? if($mod[0]['field_3']==1) echo 'checked';?>> Sí    
                        </span>
                    </label>
                </div>
            </div>
            <div class="clear"></div>
            <div class="row-content">
                <span class="form-separator"></span>
                <div class="form_group">
                    <label>Campos inmobiliaria (Superficie m2)
                        <span class="float-right">
                            <input name="f2" type="checkbox" value="1" <? if($mod[0]['field_2']==1) echo 'checked';?>> Sí        
                        </span>
                        
                    </label>
                </div>
            </div>
                       
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
<form action="<? $_SERVER['PHP_SELF']; ?>" method="post" class="param_form white_form">
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
            <div class="row-content">
                <span class="form-separator"></span>
                <div class="form_group">
                    <label>Campos vehículos (Km, año, combustible..)
                   <span class="float-right">
                       <input name="f0" type="checkbox" value="1" <? if($mod[0]['field_0']==1) echo 'checked';?>> Sí
                   </span> 
                    </label>                
                </div>
            </div>
            <div class="clear"></div>
            <div class="row-content">
                <span class="form-separator"></span>
                <div class="form_group">
                    <label>Campos inmobiliaria (Habitaciones, baños)
                        <span class="float-right">
                            <input name="f3" type="checkbox" value="1" <? if($mod[0]['field_3']==1) echo 'checked';?>> Sí    
                        </span>
                    </label>
                </div>
            </div>
            <div class="clear"></div>
            <div class="row-content">
                <span class="form-separator"></span>
                <div class="form_group">
                    <label>Campos inmobiliaria (Superficie m2)
                        <span class="float-right">
                            <input name="f2" type="checkbox" value="1" <? if($mod[0]['field_2']==1) echo 'checked';?>> Sí        
                        </span>
                        
                    </label>
                </div>
            </div>
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
	// categorias fercode: 'ord'=>$_POST['orden']
	insertSQL("sc_category", $a=array('name'=>$_POST['new_cat'],'name_seo'=>toAscii($_POST['new_cat']),'parent_cat'=>"-1",'seo_title'=>$_POST['new_cat_seo_title'],'seo_desc'=>$_POST['new_cat_seo_desc'],'seo_keys'=>$_POST['new_cat_seo_keys'],'icon'=>'',"adult"=> (isset($_POST['adult']) ? 1 : 0),'ord'=>$_POST['orden']));
	$exito_div=$language_admin['manage_categories.cat_created'];
}
if(!isset($_GET['edit'])){
// categorias fercode: ord ASC
$parent = selectSQL("sc_category", $b=array('parent_cat'=>"0<"),"ord ASC");
?>
<h2><?=$language_admin['manage_categories.title_h1']?></h2>
<form action="<? $_SERVER['PHP_SELF']; ?>" method="post" class="param_form white_form">
	<!-- Campo orden fercode //-->
	<label><?=$language_admin['manage_categories.orden']?></label>
    <input name="orden" type="text" placeholder="Ejemplo: 2">
	<div class="clear"></div>
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
    <label for="adult">Adulto</label>
    <input type="checkbox" name="adult" id="adult" >
    <div class="clear"></div>
    <input name="add" type="submit" value="<?=$language_admin['manage_categories.button_add']?>">
</form>
<hr />
<? if($exito_div!==FALSE) {?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$exito_div;?></div>
<? } ?>
<ul class="list_categories" id="catList">
<? for($i=0;$i<count($parent);$i++){?>
	<li data-id="<?=$parent[$i]['ID_cat'];?>" draggable="true">
        <span class="col_left">
            <img draggable="false" src="<?=Images::getImage('drag_indicator.svg', Images::IMG);?>" alt="">
            <b><?=$parent[$i]['name'];?></b>
        </span>
    	<span class="col_right">
        <a href="index.php?id=manage_categories&list=<? echo $parent[$i]['ID_cat']; ?>"><?=$language_admin['manage_categories.view_subcat']?></a>
        <a href="index.php?id=manage_categories&edit=<? echo $parent[$i]['ID_cat']; ?>"><?=$language_admin['manage_categories.edit']?></a>
        <a href="index.php?id=manage_categories&delete=<? echo $parent[$i]['ID_cat']; ?>"><?=$language_admin['manage_categories.delete']?></a>
        </span>
    </li>
<? }?>
</ul>
<div class="text-center">
    <button id="save_order" class="btn btn-primary">Guardar orden</button>
</div>
<form action="" method="post" id="save_order_form">
    <input type="hidden" name="save_order" value="1">
    <input type="hidden" id="save_order_data" name="data" value="">
</form>

<? include('./block/manage_filter.php'); ?>

<? 
}else{
		/// MODIFICAR CATEGORÍA PADRE
		$mod = selectSQL("sc_category", $b=array('ID_cat'=>$_GET['edit']));
		if(isset($_POST['modcat'])){
			// categorias fercode: 'ord'=>$_POST['orden']

            //guardar el nombre de la image
            $img_cat = $img_cat_n = $_POST['image_cat'];

            //comprueba si hay una nueva imagen
            if($_FILES['image_cat_file']['error'] === 0)
            {
                switch ($_FILES['image_cat_file']['type']) {
                    case "image/jpeg":
                            $img_cat_n = str_replace(' ','-',$_POST['modcat']) . ".jpeg";
                        break;
                    case "image/jpg":
                            $img_cat_n = str_replace(' ','-',$_POST['modcat']) . ".jpg";
                        break;
                    case "image/png":
                        $img_cat_n = str_replace(' ','-',$_POST['modcat']) . ".png";
                        break;
                    case "image/webp":
                        $img_cat_n = str_replace(' ','-',$_POST['modcat']) . ".webp";
                        break;
                    case "image/svg+xml":
                        $img_cat_n = str_replace(' ','-',$_POST['modcat']) . ".svg";
                        break;
                    default:
                }
                //subir imagen al directorio
                if(move_uploaded_file($_FILES['image_cat_file']['tmp_name'], ABSPATH. IMG_CATEGORY . $img_cat_n))
                    $img_cat = $img_cat_n;
            }


			updateSQL("sc_category",
            $a=array(
                'name'=>$_POST['modcat'],
                'seo_title'=>$_POST['new_cat_seo_title'],
                'seo_desc'=>$_POST['new_cat_seo_desc'],
                'seo_keys'=>$_POST['new_cat_seo_keys'],
                'adult'=> (isset($_POST['adult']) ? 1 : 0),
                'ord'=>$_POST['orden'], 'image' => $img_cat,
                'text' => $_POST['text'],
                'text_city' => $_POST['text_city'],
                'description' => $_POST['desc'], 
                'seo_title_city' => $_POST['seo_title_city'],
                'seo_desc_city' => $_POST['seo_desc_city'],
                'seo_keys_city' => $_POST['seo_keys_city'],
                'title_html' => $_POST['title_html'],
            ),
                $s=array('ID_cat'=>$mod[0]['ID_cat'])
            );
		$exito_div=$language_admin['manage_categories.cat_edited'];
		}
		$mod = selectSQL("sc_category", $b=array('ID_cat'=>$_GET['edit']));
		?>
        <h2><?=$language_admin['manage_categories.edit_cat']?></h2>
        <? if($exito_div!==FALSE) {?>
        <div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$exito_div;?></div>
        <? } ?>
        <form action="<? $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data" method="post" class="param_form">
			<!-- Campo orden fercode //-->
			<label><?=$language_admin['manage_categories.orden']?></label>
			<input name="orden" type="text" placeholder="Ejemplo: 2" value="<?=$mod[0]['ord'];?>">
			<div class="clear"></div>
            <label><?=$language_admin['manage_categories.edit_cat']?></label>
            <input name="modcat" type="text" value="<?=$mod[0]['name'];?>">
            <div class="clear"></div>
            <label>titulo html</label>
            <input name="title_html" type="text" value="<?=$mod[0]['title_html'];?>">
            <div class="clear"></div>

            <label>Descripcion de categoría (h2)</label>
            <input name="desc" type="text" class="long_text" value="<?=$mod[0]['description'];?>">
            <div class="clear"></div>
            <label for="adult">Adulto</label>
            <input type="checkbox" name="adult" id="adult" <?=$mod[0]['adult'] == 1 ? 'checked' : ''?>>
            <div class="clear"></div>

            <h3 class="px-4 f-1600 pt-5">Pagina General</h3>
            <hr class="mt-0">
            <label><?=$language_admin['manage_categories.seo_title']?></label>
            <input name="new_cat_seo_title" type="text" class="long_text" value="<?=$mod[0]['seo_title'];?>">
            <div class="clear"></div>
            <label><?=$language_admin['manage_categories.seo_desc']?></label>
            <input name="new_cat_seo_desc" type="text" class="long_text" value="<?=$mod[0]['seo_desc'];?>">
            <div class="clear"></div>

            <label><?=$language_admin['manage_categories.seo_keys']?></label>
            <input name="new_cat_seo_keys" type="text" class="long_text" value="<?=$mod[0]['seo_keys'];?>">
            <div class="clear"></div>
            <label for="text">Texto</label>
            <textarea name="text" id="text" class="long_text" placeholder="" cols="30" rows="5"><?=$mod[0]['text']?></textarea>
            <div class="clear"></div>

            <h3 class="px-4 f-1600">Pagina Ciudad</h3>
            <hr class="mt-0">
            <label><?=$language_admin['manage_categories.seo_title']?> </label>
            <input name="seo_title_city" type="text" class="long_text" value="<?=$mod[0]['seo_title_city'];?>">
            <div class="clear"></div>


            <label><?=$language_admin['manage_categories.seo_desc']?> </label>
            <input name="seo_desc_city" type="text" class="long_text" value="<?=$mod[0]['seo_desc_city'];?>">
            <div class="clear"></div>

            <label><?=$language_admin['manage_categories.seo_keys']?> </label>
            <input name="seo_keys_city" type="text" class="long_text" value="<?=$mod[0]['seo_keys_city'];?>">
            <div class="clear"></div>
            

            <label for="text">Texto </label>
            <textarea name="text_city" id="text_city" class="long_text" placeholder="" cols="30" rows="5"><?=$mod[0]['text_city']?></textarea>
            <div class="clear"></div>
		    <label class="withInfo">Imagen de categoría</label>
            <input type="hidden" name="image_cat" value="<?=$mod[0]['image']?>"/>
            <label class="image-cat-fild" title="Cambiar imagen">
                <?php if($mod[0]['image'] !== ""): ?>
                    <img id="image_cat_preview" src="<?=getConfParam('SITE_URL') . IMG_CATEGORY . $mod[0]['image']?>" alt="">
                <?php else: ?>
                    <img id="image_cat_preview" src="<?=getConfParam('SITE_URL') . IMG_PATH . 'back_photo_upload.png' ?>" alt="">
                <?php endif?>
                <input type="file" name="image_cat_file" id="image_cat_file">
            </label>
           
            <div class="clear"></div>
            <input name="Modificar" type="submit" value="<?=$language_admin['manage_categories.button_save']?>" >
		</form>
        <a href="index.php?id=manage_categories" class="back">&laquo; <?=$language_admin['manage_categories.back_cat']?></a>
		<?
}
}
?>

<script type="text/javascript" src="<?=getConfParam('SITE_URL');?>sc-admin/res/manage_categories.js"></script>


	

