<?
loadModule('banner');
$actualizado=false;
$creado=false;
$eliminado=false;
$error='';
$valido = '';
// $banners_data=selectSQL("sc_banners", $w=array('size'=>$_GET['size']));
	if(isset($_POST['banner_position'])){

        console_log($_POST);
        
		$banners_id=array();
		$banners_code=array();
		$banners_url=array();
		$banners_position=array();
		
		foreach($_POST['id'] as $n => $value){
			$banners_id[]=$value;
		}
        // foreach($_POST['banner_code'] as $n => $value){
        // 	$banners_code[]=$value;
        // }
		foreach($_POST['banner_url'] as $n => $value){
			$banners_url[]=$value;
		}
		foreach($_POST['banner_position'] as $n => $value){
			$banners_position[]=$value;
		}
	
		for($i=0;$i<count($banners_id);$i++){
			updateSQL("sc_banners",$das=
			    array(
			        // 'code'=>addslashes($banners_code[$i]),
			        'url'=>addslashes($banners_url[$i]),
			        'position_up'=>$banners_position[$i]
			    ),$w=array('ID_banner'=>$banners_id[$i]));
		}
		
		$actualizado=true;
	}
	
	if(isset($_POST['new_position'])){
	
	    console_log($_POST);
	    console_log($_FILES['new_code']);
	   // console_log($_FILES['new_code']['error'] == 0);
	   // console_log($_SESSION['data']);
	    
	    
	    if(isset($_FILES['new_code']) && $_FILES['new_code']['error'] == 0) {
	        console_log('hey');
		    $resultado=uploadImage($_FILES['new_code'],IMG_BANNERS,-1,false);
		    console_log($resultado);
		    if($resultado!==false) {
		        
		        $url = $_POST['new_url'];
		        
		        if ($_POST['new_ad'] == 0){
		            $error='Error al crear Banner. Debe seleccionar un Anuncio';
		        } elseif ($_POST['new_category'] == 0){
		            $error='Error al crear Banner. Debe seleccionar una categoría';
		        } else {
		            
    		        if ($url == '')
                    {
                        if($_POST['type'] == '1'){
                            $url = "";
                        }else
                        {
                            $url = urlAd($_POST['new_ad']);
                        }
                    }

                    $expire = time() + (int) $_POST['new_duration'];
    		        if($_POST['type'] == '1'){

                        $categories = $_POST['category'];

                        foreach($categories as $key => $cat){
                            $categories[$key] = "[$cat]";
                        }

                        $categories = implode(",", $categories);

                        insertSQL("sc_p_banner",
                            $dsa=array(
                                'cats'=>$categories,
                                'name' => $_POST['size'],
                                'size' => str_replace('x', '', $_POST['size']),
                                'code'=>'src/images/banners/' . $resultado,
                                'url'=>$url,
                                'position'=>$_POST['new_position'],
                            ));

                    }else
                    {
                        insertSQL("sc_banners",
                            $dsa=array(
                                'ID_ad'=>$_POST['new_ad'],
                                'name' => $_POST['size'],
                                'size' => str_replace('x', '', $_POST['size']),
                                'code'=>'src/images/banners/' . $resultado,
                                'url'=>$url,
                                'position_up'=>$_POST['new_position'],
                                'parent_cat'=>$_POST['new_category'],
                                'active_thru'=> 0,
                                'status'=>0,
                                'dias'=>$_POST['new_duration'],
                                'date_start'=> "NOW",
                            ));
                    }

                    $bannerID = lastIdSQL();

                    if($_POST['type'] == '0')
                    {

                        Service::insertService($_POST['new_ad'], 'banner',"", $expire, $bannerID);
    
                        updateSQL('sc_ad', $data = array('ID_banner' => $bannerID), $w=array('ID_ad' => $_POST['new_ad']));

                    }
    			        
    			    $creado=true;
		        }
    	    } else {
    	        $error='Error al crear Banner. No se pudo cargar el archivo, intente de nuevo.';
    	    }
	    } else {
	        $error='Error al crear Banner. Debe cargar un archivo';
	    }
	    
	}

    $valido = Banner::catch();

	if (isset($_POST['to_delete_id'])){
	    deleteSQL('sc_banners', $w = array('ID_banner'=>$_POST['to_delete_id']));
        Service::inactiveByBanner($_POST['to_delete_id']);
	    $eliminado=true;
	}
	
$banners_data=selectSQL("sc_banners", $w=array(), 'size DESC');
?>
<h2>Configurar Banners</h2>

<h3 style="text-align:center; font-size: 1.5em">Crear Nuevo Banner</h3>

<? if($creado){?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i>Banner creado correctamente.</div>
<? } ?>
<? if($valido != ''){?> 
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$valido?></div>
<? } ?>

<? if($error != ''){?>
<div class="info_error"><i class="fa fa-exclamation-circle" aria-hidden="true"></i><?=$error?></div>
<? } ?>

<form enctype="multipart/form-data" action="<? $_SERVER['PHP_SELF'];?>" method="post" class="param_form" >
    
    <div class="text_container">
        <label>Tipo</label>
        <select name="type" id="banenr_type">
            <option value='0'>Banner de usuario</option>
            <option value='1'>Banner publicidad</option>

        </select>
    </div>
    <div class="text_container">
        <label>Tamaño</label>
        <select name="size">
            <option value='1220x180'>1220x180</option>
            <option value='850x170'>850x170</option>
            <option selected value='740x120'>740x120</option>
            <option value='728x90'>728x90</option>
            <option value='300x250'>300x250</option>
        </select>
    </div>
    
    <div class="text_container">
        <label>Enlace Banner (imagen)</label>
        <input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
        <input type="file" name="new_code" accept="image/*" id="banner_img">
    </div>
    
    <div class="text_container">
        <label>URL</label>
        <textarea name="new_url"></textarea>
    </div>
    
    <div class="text_container">
        <label>Posición</label>
        <select name="new_position">
            <option selected value='1'>Superior</option>
            <option value='0'>Inferior</option>
            <option value='2'>Medio</option>
        </select>
    </div>
    
    <div class="text_container" id="new_duration">
        <label>Duración</label>
        <select name="new_duration">
            <option selected value='15'>15 días</option>
            <option value='30'>30 días</option>
        </select>
    </div>
    
    <input type="hidden" name="new_category" id="category">
    <input type="hidden" name="new_ad" id="idad">
    <div class="text_container" id="new_ad" >
        <label>Anuncio</label>
        <div class="search_ad_container">
            <input type="text" autocomplete="off" id="search_ad">
            <div class="search_options" style="display: none;">
    
            </div>
            
        </div>
        

    </div>
    <?php 
        $parent_cats = selectSQL("sc_category", $w = array('parent_cat'=> "-1"));
    ?>
    <div class="text_container banner_search" id="new_category" style="display: none;">
        <label>Categoría</label>
        <?php loadBlock('search-cats', array('id'=>'filter_category')); ?>
    </div>
    
    <input type="submit" id="save" value="Crear">
</form>

<hr>

<!--<h3 style="text-align:center; font-size: 1.5em">Editar Banners</h3>-->

<? if($actualizado){?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i>Banners actualizados correctamente.</div>
<? } ?>

<? if($eliminado){?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i>Banner eliminado correctamente.</div>
<? } ?>

<!--<div class="info_banners">Puedes añadir códigos de banners adaptables para que se adapten correctamente a cualquier dispositivo.</div>-->

<h4 class="f-1600">Banner Superior</h4>
<table class="banner_table table table-responsive-md">
    <thead>
        <tr>
            <th>Banner</th>
            <th>Categorías</th>
            <th>Días</th>
            <th>link</th>
            <th>Inicio</th>
            <th>Expiración</th>
            <th>Opcciones</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($banners_data as $i => $banner){ 
            $ad = selectSQL("sc_ad", $w = array('ID_ad'=>$banners_data[$i]['ID_ad']));
            $cat = getCat($banners_data[$i]['parent_cat']);
            if (!($banners_data[$i]['position_up'] == 1))
                continue;   
        ?>
        <tr>
            <td>
                <a class="banner_list_image_link" href="/<?=$banner['code']?>" target="_blank">
                    <img src="/<?=$banner['code']?>" alt="" class="banner_list_image">
                </a>
            </td>
            <td>
                <label><?=$cat['name']?></label>
            </td>
            <td><?=$banner['dias']?></td>
            <td>
               <a href="<?=$banner['url']?>" target="_blank">enlace</a>
            </td>
            
            <td><?=$banner['date_start']?></td>
            <td><?=
                $banners_data[$i]['active_thru'] != 0 ? date('d-m-Y h:i:s', $banners_data[$i]['active_thru']) : 'indefinido'
                ?>
            </td>
            <td>
                <a href="<?=getConfParam('SITE_URL')?>sc-admin/index.php?id=manage_banners&action=delete_banner&banner=<?=$banner['ID_banner']?>">Eliminar</a>
                <a href="javascript:setExtender(<?=$banner['ID_banner']?>)">Extender</a>
                <?php if($banner['status'] == 1): ?>
                    <a href="<?=getConfParam('SITE_URL')?>sc-admin/index.php?id=manage_banners&action=deactivate_banner&banner=<?=$banner['ID_banner']?>">Desactivar</a>
                <?php else: ?>
                    <a href="<?=getConfParam('SITE_URL')?>sc-admin/index.php?id=manage_banners&action=activate_banner&banner=<?=$banner['ID_banner']?>">Activar</a>
                  
                <?php endif ?>
            </td>
            <td>
               <?php if($banner['status'] == 1): ?>
                    <span class="status status-expired">Activo</span>
               <?php elseif($banner['status'] == 0): ?>
                    <span class="status status-active">Inactivo</span>
               <?php endif ?>
            </td>
        </tr>

        <?php }?>
    </tbody>
</table>



<h4 class="f-1600">Banner Inferior</h4>

<table class="banner_table table table-responsive-md">
    <thead>
        <tr>
            <th>Banner</th>
            <th>Categorías</th>
            <th>Días</th>
            <th>link</th>
            <th>Inicio</th>
            <th>Expiración</th>
            <th>Opcciones</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($banners_data as $i => $banner){ 
            $ad = selectSQL("sc_ad", $w = array('ID_ad'=>$banners_data[$i]['ID_ad']));
            $cat = getCat($banners_data[$i]['parent_cat']);
            if (!($banners_data[$i]['position_up'] == 0))
                continue;   
        ?>
        <tr>
            <td>
                <a class="banner_list_image_link" href="/<?=$banner['code']?>" target="_blank">
                    <img src="/<?=$banner['code']?>" alt="" class="banner_list_image">
                </a>
            </td>
            <td>
                <label><?=$cat['name']?></label>
            </td>
            <td><?=$banner['dias']?></td>
            <td>
               <a href="<?=$banner['url']?>" target="_blank">enlace</a>
            </td>
            
            <td><?=$banner['date_start']?></td>
            <td><?=date('d-m-Y h:i:s', $banners_data[$i]['active_thru'])?></td>
            <td>
                <a href="<?=getConfParam('SITE_URL')?>sc-admin/index.php?id=manage_banners&action=delete_banner&banner=<?=$banner['ID_banner']?>">Eliminar</a>
                <a href="<?=getConfParam('SITE_URL')?>sc-admin/index.php?id=manage_banners&action=delete_banner&banner=<?=$banner['ID_banner']?>">Extender</a>
                <?php if($banner['status'] == 1): ?>
                    <a href="<?=getConfParam('SITE_URL')?>sc-admin/index.php?id=manage_banners&action=delete_banner&banner=<?=$banner['ID_banner']?>">Desactivar</a>
                <?php else: ?>
                    <a href="<?=getConfParam('SITE_URL')?>sc-admin/index.php?id=manage_banners&action=delete_banner&banner=<?=$banner['ID_banner']?>">Activar</a>
                  
                <?php endif ?>
            </td>
            <td>
               <?php if($banner['status'] == 1): ?>
                    <span class="status status-expired">Activo</span>
               <?php elseif($banner['status'] == 0): ?>
                    <span class="status status-active">Inactivo</span>
               <?php endif ?>
            </td>
        </tr>

        <?php }?>
    </tbody>
</table>

<h4 class="f-1600">Banner medio</h4>

<table class="banner_table table table-responsive-md">
    <thead>
        <tr>
            <th>Banner</th>
            <th>Categorías</th>
            <th>Días</th>
            <th>link</th>
            <th>Inicio</th>
            <th>Expiración</th>
            <th>Opcciones</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($banners_data as $i => $banner){ 
            $ad = selectSQL("sc_ad", $w = array('ID_ad'=>$banners_data[$i]['ID_ad']));
            $cat = getCat($banners_data[$i]['parent_cat']);
            if (!($banners_data[$i]['position_up'] == 2))
                continue;   
        ?>
        <tr>
            <td>
                <a class="banner_list_image_link" href="/<?=$banner['code']?>" target="_blank">
                    <img src="/<?=$banner['code']?>" alt="" class="banner_list_image">
                </a>
            </td>
            <td>
                <label><?=$cat['name']?></label>
            </td>
            <td><?=$banner['dias']?></td>
            <td>
               <a href="<?=$banner['url']?>" target="_blank">enlace</a>
            </td>
            
            <td><?=$banner['date_start']?></td>
            <td><?=date('d-m-Y h:i:s', $banners_data[$i]['active_thru'])?></td>
            <td>
                <a href="<?=getConfParam('SITE_URL')?>sc-admin/index.php?id=manage_banners&action=delete_banner&banner=<?=$banner['ID_banner']?>">Eliminar</a>
                <a href="<?=getConfParam('SITE_URL')?>sc-admin/index.php?id=manage_banners&action=delete_banner&banner=<?=$banner['ID_banner']?>">Extender</a>
                <?php if($banner['status'] == 1): ?>
                    <a href="<?=getConfParam('SITE_URL')?>sc-admin/index.php?id=manage_banners&action=delete_banner&banner=<?=$banner['ID_banner']?>">Desactivar</a>
                <?php else: ?>
                    <a href="<?=getConfParam('SITE_URL')?>sc-admin/index.php?id=manage_banners&action=delete_banner&banner=<?=$banner['ID_banner']?>">Activar</a>
                  
                <?php endif ?>
            </td>
            <td>
                <?php if($banner['status'] == 1): ?>
                    <span class="status status-expired">Activo</span>
               <?php elseif($banner['status'] == 0): ?>
                    <span class="status status-active">Inactivo</span>
               <?php endif ?>
            </td>
        </tr>

        <?php }?>
    </tbody>
</table>

<h4 class="f-1600 mt-5 pt-5">Banner publicitarios </h4>
<?php 
    $banners_data=selectSQL("sc_p_banner");
?>
<table class="banner_table table table-responsive-md">
    <thead>
        <tr>
            <th>Banner</th>
            <th>Categorías</th>
            <th>Tamaño</th>
            <th>Posicion</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($banners_data as $banner){ 
            $banner['cats'] = explode(",", $banner['cats']);
            $banner['cats'] = array_map(function($cat){
                $cat = str_replace(array("[", "]"), "", $cat);
                if($cat == '0') return array('name' => 'Todos');
                return getCat($cat);
            }, $banner['cats']);
        ?>
        <tr>
            <td>
                <a class="banner_list_image_link" href="/<?=$banner['code']?>" target="_blank">
                    <img src="/<?=$banner['code']?>" alt="" class="banner_list_image">
                </a>
            </td>
            <td>
                <div class="banner_cats">
                    <?php foreach($banner['cats'] as $cat): ?>
                        <label><?=$cat['name']?></label>
                    <?php endforeach ?>
                </div>
            </td>
            <td><?=$banner['name']?></td>
            <td>
                <?php if($banner['position'] == 1): ?>
                    Superior
                <?php elseif($banner['position'] == 0): ?>
                    Inferior
                <?php elseif($banner['position'] == 2): ?>
                    Medio
                <?php endif ?>
            </td>
            <td>
                <a href="<?=getConfParam('SITE_URL')?>sc-admin/index.php?id=manage_banners&action=delete_banner&banner=<?=$banner['ID_banner']?>&p_banner=1">Eliminar</a>
                <?php if($banner['status'] == 0): ?>
                    <a href="<?=getConfParam('SITE_URL')?>sc-admin/index.php?id=manage_banners&action=activate_banner&banner=<?=$banner['ID_banner']?>&p_banner=1">Activar</a>   
                <?php else: ?>
                    <a href="<?=getConfParam('SITE_URL')?>sc-admin/index.php?id=manage_banners&action=deactivate_banner&banner=<?=$banner['ID_banner']?>&p_banner=1">Desactivar</a>
                <?php endif ?>
            </td>
        </tr>

        <?php }?>
    </tbody>
</table>
<hr>

<div class="premium_contianer" style="display: none;" id="banner_preview">
	<div>
		<span class="close" onclick="$('#banner_preview').hide();">
			<fa class="fa fa-times"></fa>
		</span>
		<img src="" class="banner-preview">
		<img src="" class="banner-preview-r">
	</div>	
</div>

<form action="<? $_SERVER['PHP_SELF'];?>" method="post" class="param_form"  >
<? for($i=0;$i<count($banners_data);$i++){ $ad = selectSQL("sc_ad", $w = array('ID_ad'=>$banners_data[$i]['ID_ad']))?>
<div class="data_container" id="edit_<?=$banners_data[$i]['ID_banner'];?>" style="display: none">
    <label id="b<?=$banners_data[$i]['ID_banner'];?>" class="banner_title"><b>Banner <?=$banners_data[$i]['name'];?> - Anuncio Ref. <?=$ad[0]['ID_ad'];?> - Título: <?=$ad[0]['title'];?></b></label>
    <label>Activo hasta:<b> <? if($banners_data[$i]['active_thru'] == 0) echo 'indefinido'; 
                                else echo date('d-m-Y h:i:s', $banners_data[$i]['active_thru']);?></b></label>
    <div class="text_container">
        <label>Enlace Banner (imagen)</label>
        <!--<textarea name="banner_code[]"><?=stripslashes($banners_data[$i]['code']);?></textarea>-->
        <img src="../<?=stripslashes($banners_data[$i]['code']);?>" alt="<?=stripslashes($banners_data[$i]['code']);?>">
    </div>
    
    <div class="text_container">
        <label>URL</label>
        <textarea name="banner_url[]"><?=stripslashes($banners_data[$i]['url']);?></textarea>
    </div>
    
    <div class="text_container">
        <label>Posicion</label>
        <select name="banner_position[]">
            <option <?if ($banners_data[$i]['position_up'] == 1) echo 'selected';?> value='1'>Superior</option>
            <option <?if ($banners_data[$i]['position_up'] == 0) echo 'selected';?> value='0'>Inferior</option>
        </select>
    </div>
    
    <input type="hidden" name="id[]" value="<?=$banners_data[$i]['ID_banner'];?>">
</div>
<? } ?>
<input name="enviar" type="submit" id="enviar" style="display: none" value="<?=$language_admin['banner.button_save']?>">
</form>


<h4>Banner de la cabecera</h4>
<?php $banner = getConfParam("HEADER_BANNER") ?>
<div class="img-input-container">
    <label for="header-banner">Banner pagina</label>
    <div class="input-banner">
        <div class="img-input-box">
            <?php if($banner != ""): 
                $time = time();
            ?>
                <img src="<?=Images::getImage($banner, IMG_BANNERS, true)?>?t=<?=$time?>" alt="banner">
            <?php else: ?>
                <img src="<?=Images::getImageDefault()?>" alt="banner">
            <?php endif ?>
        
            <input class="img-input" type="file" name="header-banner" id="header_banner">
        </div>
    </div>
</div>

<?php $banner_r = getConfParam("HEADER_BANNER_R") ?>

<div class="img-input-container">
    <label for="header-banner">Banner responsive</label>
    <div class="input-banner responsive">
        <div class="img-input-box">
            <?php if($banner_r != ""): ?>
                <img src="<?=Images::getImage($banner_r, IMG_BANNERS, true)?>?t=<?=$time?>" alt="banner">
            <?php else: ?>
                <img src="<?=Images::getImageDefault()?>" alt="banner">
            <?php endif ?>
        
            <input class="img-input" type="file" name="header-banner" id="header_banner_r">
        </div>
    </div>
</div>

<div class="text-center">
    <button class="btn btn-primary mt-5" onclick="updateHeaderBanner()">Actualizar Banners</button>
</div>

<div class="modal" id="modal-extender" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"></i> Extender banner</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="" id="form-extender" method="post">
            <div class="modal-body">
                    <div class="form-group row">
                        <label for="form-mailer-para" class="col-form-label text-center col-2">Días</label>
                        <div class="col-9">
                            <input type="text" name="days" class="form-control">
                        </div>
                    </div>
                    <input type="hidden" name="action" value="extend_banner">
                    <input type="hidden" name="banner" id="form-extender-banner">
            
            </div>
            <div class="modal-footer">
            <button type="submit" id="modal-extender-btn" class="btn btn-primary">Aplicar</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
		</form>
      </div>
    </div>
</div>

<script>
    function go_to_banner(id){
        var my_element = document.getElementById("edit_"+id);
        
        // my_element.scrollIntoView({
        //   behavior: "smooth",
        //   block: "start",
        //   inline: "nearest"
        // });

        var x = document.getElementsByClassName("data_container");
        var i;
        for (i = 0; i < x.length; i++) {
          x[i].style.display = "none";
        }
        
        my_element.style.display = "block";
        
        document.getElementById("enviar").style.display= "block";
    }
    
    function delete_banner(id) {
        document.getElementById("delete_form_" + id).submit();
    }
    
    $('#banner_img').change(function() {
        
        var file = document.getElementById('banner_img').files[0];
        var filesize = file.size;
        console.log(file);
        if (filesize > 3000000){
            alert('Tamaño máximo de imagen es de 3MB');
            $('#banner_img').val('');
        }    
        console.log(document.getElementById('banner_img').files[0]);
    });
    
</script>

<script src="res/banners.js"></script>
