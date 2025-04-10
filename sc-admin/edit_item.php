<?

function compressImage($filePath, $quality = 90) {
	//die($filePath);
    $info = getimagesize($filePath);
    $image = null;
    
    if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($filePath);
        imagejpeg($image, $filePath, $quality);
    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($filePath);
        imagepng($image, $filePath, 9);
    } else {
       // return false; // Unsupported file type
    }
	//var_dump($filePath , $image , $info);
	//die();
    
    if ($image) {
        imagedestroy($image);
    }
    return true;
}

$edited=false;
$DATAJSON = array();
$DATAJSON['max_photos'] = getConfParam('MAX_PHOTOS_AD');

if(isset($_GET['a'])){
  $check=selectSQL("sc_ad",$w=array('ID_ad'=>$_GET['a']));
  if(count($check)!=0){
    $ad=getDataAd($check[0]['ID_ad']);
    $error_insert=false;
    if(isset($_POST['tit'])){
      $datos_ad=array();
		
      $datos_ad['ID_cat']=$_POST['category'];
      
      $cat = selectSQL('sc_category', $w = array('ID_cat' => $_POST['category']));
      console_log($cat);
      $datos_ad['parent_cat'] = $cat[0]['parent_cat'];
      
      $datos_ad['ID_region']=$_POST['region'];

      //$datos_ad['ID_city']=$_POST['city'];

      $datos_ad['location']=$_POST['city'];

      $datos_ad['ad_type']=$_POST['ad_type'];

      $datos_ad['title']=$_POST['tit'];

      $datos_ad['title_seo']=toAscii($datos_ad['title']);

      $datos_ad['texto']=htmlspecialchars($_POST['text']);

      $datos_ad['phone']=trim($_POST['telf']);

      $datos_ad['price']=$_POST['precio'];

      $datos_ad['mileage']=$_POST['km_car'];

      $datos_ad['fuel']=$_POST['fuel_car'];

      $datos_ad['date_car']=$_POST['date_car'];

      $datos_ad['area']=$_POST['area'];

      $datos_ad['room']=$_POST['room'];

      $datos_ad['broom']=$_POST['bathroom'];

      $datos_ad['name']=formatName($_POST['name']);

      $datos_ad['phone']=$_POST['phone'];

      $datos_ad['hor_start']=$_POST['horario-inicio'];

      $datos_ad['hor_end']=$_POST['horario-final'];
  
      $datos_ad['dis']=$_POST['dis'];

      $datos_ad['whatsapp']= isset($_POST['whatsapp']) ? 1 : 0;
      
      //$datos_ad['phone1']=$_POST['phone1'];

      $datos_ad['whatsapp1']= isset($_POST['whatsapp1']) ? 1 : 0;

      $datos_ad['seller_type']=$_POST['seller_type'];
      //rotar iamgenes
      foreach($_POST['photo_name'] as $photo => $name){
        Images::rotateImage($name, $_POST['optImgage'][$photo]['rotation']);
      }

      //if($datos_ad['ID_region']!=0 && $datos_ad['ID_city']!=0 && strlen($datos_ad['title'])>9 && strlen($datos_ad['texto'])>9 && is_numeric($datos_ad['price'])){
      if($datos_ad['ID_region']!=0 && $datos_ad['ad_type']!=0 && $datos_ad['seller_type']!=0  && is_numeric($datos_ad['price'])){

    		$insert=updateSQL("sc_ad",$datos_ad,$w=array('ID_ad'=>$ad['ad']['ID_ad']));
    		// Method alternative
    		if(isset($_FILES['photo'])){
    				$tot = count($_FILES['photo']['name']);
    				for($i=0; $i<$tot; $i++){
    					$resultado=uploadImage($_FILES['photo'],IMG_ADS,$i,true);
    					if($resultado!==false){
							
							 $imagePath = '/var/www/vhosts/41121521.servicio-online.net/httpdocs/' . IMG_ADS . $resultado;
					//var_dump(__DIR__ . '/' . IMG_ADS . $resultado , __DIR__ ,'/' . IMG_ADS . $resultado);
							
							// **Compress image immediately after upload**
							compressImage($imagePath);
							insertSQL("sc_images",$data=array('ID_ad'=>$ad['ad']['ID_ad'],'name_image'=>$resultado,'date_upload'=>time()));
							
						}
    				}
    		// Method main
    		}elseif(isset($_POST['photo_name'])){
  				foreach($_POST['photo_name'] as $photo => $name){
            updateSQL("sc_images",$data=array('ID_ad'=>$ad['ad']['ID_ad'], 'position'=>$photo),$wa=array('name_image'=>$name));
					
					 $imagePath = '/var/www/vhosts/41121521.servicio-online.net/httpdocs/' . IMG_ADS . $name;
					//var_dump(__DIR__ . '/' . IMG_ADS . $name ,'======','/var/www/vhosts/41121521.servicio-online.net/httpdocs');

					// **Compress image when updating an existing image**
					$res = compressImage($imagePath);
					//die($res, '=====');
					
  				}
    		}
        $images = selectSQL("sc_images", $w = array(
          'ID_ad' => $ad['ad']['ID_ad']
        ) , 'position ASC, ID_image ASC');
        foreach ($images as $key => $value) 
        {
            if($value['status'] == ImageStatus::Delete)
                Images::deleteImage($value['ID_image']);
            if($value['status'] == ImageStatus::Inactive)
                updateSQL("sc_images",$d=array('status'=>ImageStatus::Active),$w=array('ID_image'=>$value['ID_image']));
        }
    		if($insert){
    			$edited=true;
    		}
    	}
    }
    $ad=getDataAd($check[0]['ID_ad'], false);
    $ad=parseChanges($ad);
?>

<div class="col_single">
  <h2><?=$language['edit.title_h1']?></h2>
  <? if($edited){?>
    <script>
      window.close();
    </script>
    <div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i> Anuncio modificado correctamente!</div>
    <div class="text-center">
      <a class="btn btn-primary" href="javascript:void(0);" onclick="window.close()"><i class="fa fa-arrow-left" aria-hidden="true"></i> Volver</a>
    </div>
  <? }else{?>

  <form id="new_item_post" class="fm" method="post" action="<? $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
    <fieldset>
    	<div class="row">
<div class="col_lft"><label><?=$language['post.label_category']?> *</label></div>
<div class="col_rgt"><select name="category" id="category">
    <option value="0"><?=$language['post.select_category']?></option>
    	<?
		$parent=selectSQL("sc_category",$where=array('parent_cat' => -1),"ord ASC");
		for($i=0;$i<count($parent);$i++){
      $child=selectSQL("sc_category",$where=array('parent_cat' => $parent[$i]['ID_cat']),"name ASC");
      if(count($child) > 0){
        $child = $child[0];
        ?>
            <option value="<?=$child['ID_cat'];?>" <? if($child['parent_cat']==$ad['ad']['parent_cat']) echo ' selected'?>>&nbsp;&nbsp;<?=$child['name']; ?></option>
          <? 
        }else{ ?>
            <option value="<?=$parent[$i]['ID_cat'];?>" <? if($parent[$i]['ID_cat']==$ad['ad']['parent_cat']) echo ' selected'?>>&nbsp;&nbsp;<?=$parent[$i]['name']; ?></option>
      <?php  }
			}
			?>
			
			<? 
		 
		?>
</select>
<div class="error_msg" id="error_category"><?=$language['post.error_category']?></div>
</div>
</div>

      <div class="row">
        <div class="col_lft">
        <label><?=$language['edit.label_region']?> *</label></div>
        <div class="col_rgt">
          <select name="region" size="1" id="region">
            <option value="0"><?=$language['edit.select_region']?></option>
            <?php $cat = selectSQL("sc_region",$arr=array(),"name ASC");
  	        for($i=0;$i<count($cat);$i++){ ?>
              <option value="<?php echo $cat[$i]['ID_region']; ?>"<? if($cat[$i]['ID_region']==$ad['ad']['ID_region']) echo ' selected'?>>
                <?php echo $cat[$i]['name']; ?>
              </option>
        <? } ?>
          </select>
          <div class="error_msg" id="error_region"><?=$language['edit.error_region']?></div>
        </div>
      </div>

      <div class="row">
        <div class="col_lft">
          <label><?=$language['edit.label_city']?> </label>
        </div>
        <div class="col_rgt">
         <? /* <select name="city" size="1" id="city">
            <option value="0"><?=$language['edit.select_city']?></option>
            <?
            $loc = selectSQL('sc_city',$w=array('ID_region'=>$ad['ad']['ID_region']),'name ASC');
            for($i=0;$i<count($loc);$i++){?>
      	      <option value="<? echo $loc[$i]['ID_city']; ?>"<? if($loc[$i]['ID_city']==$ad['ad']['ID_city']) echo ' selected'?>>
                <? echo $loc[$i]['name']; ?>                
              </option>
         <? } ?>
          </select>*/ ?>
          <input name="city" type="text" id="city" maxlength="250"   value="<?=stripslashes($ad['ad']['location']);?>" />
          <div class="error_msg" id="error_city"><?=$language['edit.error_city']?></div>
        </div>
      </div>

      <div class="row">
        <div class="col_lft">
          <label><?=$language['edit.label_ad_type']?> *</label>
        </div>
        <div class="col_rgt">   
          <select name="ad_type" size="1" id="ad_type">
              <option value="0" <?php echo $ad['ad']['ad_type']==0 ? 'selected':''?> ><?=$language['edit.select_ad_type']?></option>
              <option value="1" <?php echo $ad['ad']['ad_type']==1 ? 'selected':''?> ><?=$language['edit.ad_type_option_1']?></option>
              <option value="2" <?php echo $ad['ad']['ad_type']==2 ? 'selected':''?> ><?=$language['edit.ad_type_option_2']?></option>
          </select>
          <div class="error_msg" id="error_ad_type"><?=$language['edit.error_ad_type']?></div>
        </div>
      </div>
    </fieldset>
    <fieldset>
      <div class="row">
        <div class="col_lft"><label><?=$language['edit.label_title']?> *</label></div>
        <div class="col_rgt">
          <input name="tit" type="text" id="tit" value="<?=stripslashes($ad['ad']['title']);?>"/>
          <div class="input_count">Caracteres <span id="nro-car-tit"><?= strlen($ad['ad']['title']) ?></span> (min 10/máx 50)</div>
          <div class="error_msg" id="error_tit"><?=$language['edit.error_title']?></div>
        </div>
      </div>

      <div class="row">
        <div class="col_lft"><label><?=$language['edit.label_description']?> *</label></div>
        <div class="col_rgt">
          <textarea name="text" rows="5" id="text" maxlength="1200"><?=stripslashes($ad['ad']['texto']);?></textarea>
          <div class="input_count">Caracteres <span id="nro-car-text"><?= strlen($ad['ad']['texto']) ?></span> (min 30/máx 500)</div>
          <div class="error_msg" id="error_text"><?=$language['edit.error_description']?></div>
        </div>
      </div>

<div id="extra_fields" style="display: none;">
<? if($ad['category']['field_0']==1){ ?>
<div class="row">
<div class="col_lft"><label>Kilómetros</label></div>
<div class="col_rgt"><input name="km_car" type="tel" id="km_car" size="10" maxlength="10" value="<?=$ad['ad']['mileage']?>" />
</div>
</div>
<div class="row">
<div class="col_lft"><label>Año</label></div>
<div class="col_rgt"><select name="date_car" id="date_car">
        <option value="">Año</option>
        <? for($i=date("Y",time());$i>1970;$i--){?>
        <option value="<?=$i?>" <? if($i==$ad['ad']['date_car']) echo 'selected';?>><?=$i?></option>
        <? } ?>
        </select>
</div>
</div>
<div class="row">
<div class="col_lft"><label>Combustible</label></div>
<div class="col_rgt"><select name="fuel_car" id="fuel_car">
        <option value="">Combustible</option>
		<? $type_fuel=selectSQL("sc_type_fuel",$w=array(),'ID_fuel ASC');
		for($i=0;$i<count($type_fuel);$i++){?>
        <option value="<?=$type_fuel[$i]['ID_fuel']?>" <? if($ad['ad']['fuel']==$type_fuel[$i]['ID_fuel']) echo 'selected';?>><?=$type_fuel[$i]['name']?></option>
       	<? } ?>
        </select>
</div>
</div>
<?
}
?>
<? if($ad['category']['field_3']==1){ ?>
<div class="row">
<div class="col_lft"><label>Habitaciones</label></div>
<div class="col_rgt"><input type="tel" id="room" name="room" maxlength="4" value="<?=$ad['ad']['room']?>">
</div>
</div>
<div class="row">
<div class="col_lft"><label>Baños</label></div>
<div class="col_rgt"><input type="tel" id="bathroom" name="bathroom" maxlength="4" value="<?=$ad['ad']['broom']?>">
</div>
</div>
<?	} ?>
<?	if($ad['category']['field_2']==1){ ?>
<div class="row">
<div class="col_lft"><label>Superficie (m<sup>2</sup>)</label></div>
<div class="col_rgt"><input type="tel" id="area" name="area" maxlength="10" value="<?=$ad['ad']['area']?>"><span class="decimal_price">.00 <b>m<sup>2</sup></b></span>
</div>
</div>
<?	} ?>
</div>

<div class="row" style="display: none;">
  <div class="col_lft"><label><?=$language['edit.label_price']?></label></div>
  <div class="col_rgt"><input class="numeric" name="precio" type="text" id="precio" size="8" maxlength="9" value="<?=$ad['ad']['price'];?>" step="0.01"/><span class="decimal_price"><b><?=COUNTRY_CURRENCY_CODE;?></b></span>
  <div class="error_msg" id="error_price">Indica un precio para tu anuncio</div></div>
</div>
<div class="row">
	<div class="col_lft"><label>Disponibilidad *</label></div>
	<div class="col_rgt">
		<div class="form-row">
			<div class="form-col">
				<select name="dis" id="dis" size="1" >
					<option value="0">Seleccionar</option>
					<option <?=$ad['ad']['dis'] == 1 ? 'selected' : ''?> value="1">Todos los días</option>
					<option <?=$ad['ad']['dis'] == 2 ? 'selected' : ''?> value="2">Lunes a Viernes</option>
					<option <?=$ad['ad']['dis'] == 3 ? 'selected' : ''?> value="3">Lunes a Sábado</option>
					<option <?=$ad['ad']['dis'] == 4 ? 'selected' : ''?> value="4">Sábados y Domingos</option>
				</select>
			</div>
			
		</div>
		<div class="error_msg" id="error_dis">Selecciona tu disponibilidad</div>
	</div>
</div>
<div class="row">
	<div class="col_lft"><label>Horario *</label></div>
	<div class="col_rgt">
		<div class="form-row">
			<div class="form-col">
				<select name="horario-inicio" size="1" id="horario-inicio">
					<?php 
						for($i = 0; $i < 24; $i++){ 
							$h = $i >= 10 ? $i : "0".$i;
              if($ad['ad']['hor_start']=="$h:00")
							  print "<option selected value='$h:00'>$h:00</option>";
              else
							  print "<option value='$h:00'>$h:00</option>";
							if($i !== 24)
							{
                if($ad['ad']['hor_start']=="$h:30")
                  print "<option selected value='$h:30' >$h:30</option>";
                else
								  print "<option value='$h:30' >$h:30</option>";
							}
						}
                    ?>
				</select>
			</div>
			<div class="form-col">
				<select name="horario-final" size="1" id="horario-final">
					<?php 
						for($i = 0; $i < 24; $i++){ 
							$h = $i >= 10 ? $i : "0".$i;
              if($ad['ad']['hor_end']=="$h:00")
							  print "<option selected value='$h:00'>$h:00</option>";
              else
							  print "<option value='$h:00'>$h:00</option>";
							if($i !== 24)
							{
                if($ad['ad']['hor_end']=="$h:30")
                  print "<option selected value='$h:30' >$h:30</option>";
                else
								  print "<option value='$h:30' >$h:30</option>";
							}
						}
            ?>
				</select>
			</div>
		</div>
		<div class="error_msg" id="error_horario">Indica un horario para tu anuncio</div>
	</div>
</div>

</fieldset>
<fieldset>
<div class="title_photos_list" id="title_photos_list">¡Sube fotos a tu anuncio!</div>
<div class="error_msg" id="error_photo">Sube al menos una foto para tu anuncio!</div>
<div class="photos_list sortable">
<? 
// Current Photos
$current_photos = count($ad['images']);
$upload_photos = (getConfParam('MAX_PHOTOS_AD')-$current_photos);
$photo_id=1;
?>
<? for($i=0;$i<$current_photos;$i++){?>
    <div class="photo_box">
      <div id="photo_container-<?=$photo_id;?>" class="photo_list">
          <div class="removeImg"><i class="fa fa-times" aria-hidden="true"></i></div>
          <a href="javascript:void(0);" class="edit-photo-icon" onclick="editImage(<?=$photo_id?>)">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h357l-80 80H200v560h560v-278l80-80v358q0 33-23.5 56.5T760-120H200Zm280-360ZM360-360v-170l367-367q12-12 27-18t30-6q16 0 30.5 6t26.5 18l56 57q11 12 17 26.5t6 29.5q0 15-5.5 29.5T897-728L530-360H360Zm481-424-56-56 56 56ZM440-440h56l232-232-28-28-29-28-231 231v57Zm260-260-29-28 29 28 28 28-28-28Z"/></svg>
        </a>
          <span class="helper"></span>
          <img class="<?=getImgOrientation($ad['images'][$i]['name_image'])?>" src="<?=getConfParam('SITE_URL')?>src/photos/<?=$ad['images'][$i]['name_image']?>"/>
          <input type="hidden" name="photo_name[]" value="<?=$ad['images'][$i]['name_image'];?>">
      </div>
      <div class="photos_options">
			<?php if($photo_id-1 >= 1): ?>
			  
				<a href="javascript:void(0);" onclick="transferPhoto(<?=$photo_id?>,<?=$photo_id-1?>)">
					<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/></svg>
				</a>	
			<?php endif ?>
			<a href="javascript:void(0);" onclick="rotateRight(<?=$photo_id?>)">
				<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M522-80v-82q34-5 66.5-18t61.5-34l56 58q-42 32-88 51.5T522-80Zm-80 0Q304-98 213-199.5T122-438q0-75 28.5-140.5t77-114q48.5-48.5 114-77T482-798h6l-62-62 56-58 160 160-160 160-56-56 64-64h-8q-117 0-198.5 81.5T202-438q0 104 68 182.5T442-162v82Zm322-134-58-56q21-29 34-61.5t18-66.5h82q-5 50-24.5 96T764-214Zm76-264h-82q-5-34-18-66.5T706-606l58-56q32 39 51 86t25 98Z"/></svg>
			</a>
    
			<?php if($photo_id+1 <= getConfParam('MAX_PHOTOS_AD')): ?>
			  
				<a href="javascript:void(0);" onclick="transferPhoto(<?=$photo_id?>,<?=$photo_id+1?>)">
					<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z"/></svg>
				</a>
			<?php endif ?>
		</div>
      <input type="hidden" name="optImgage[][rotation]" id="rotation-<?=$photo_id;?>" value="0">
    </div>
    <? $photo_id++;
	}?>
	<? for($i=1;$i<=$upload_photos;$i++){?>
    <div class="photo_box">
		<div id="photo_container-<?=$photo_id;?>" class="photo_list free">
			<input name="userImage[]" id="photo-<?=$photo_id;?>" type="file" class="photoFile" />

		</div>
		<div class="photos_options">
			<?php if($photo_id-1 >= 1): ?>
			  
				<a href="javascript:void(0);" onclick="transferPhoto(<?=$photo_id?>,<?=$photo_id-1?>)">
					<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80H313Z"/></svg>
				</a>	
			<?php endif ?>
			<a href="javascript:void(0);" onclick="rotateRight(<?=$photo_id?>)">
				<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M522-80v-82q34-5 66.5-18t61.5-34l56 58q-42 32-88 51.5T522-80Zm-80 0Q304-98 213-199.5T122-438q0-75 28.5-140.5t77-114q48.5-48.5 114-77T482-798h6l-62-62 56-58 160 160-160 160-56-56 64-64h-8q-117 0-198.5 81.5T202-438q0 104 68 182.5T442-162v82Zm322-134-58-56q21-29 34-61.5t18-66.5h82q-5 50-24.5 96T764-214Zm76-264h-82q-5-34-18-66.5T706-606l58-56q32 39 51 86t25 98Z"/></svg>
			</a>

			
			<?php if($photo_id+1 <= getConfParam('MAX_PHOTOS_AD')): ?>
			  
				<a href="javascript:void(0);" onclick="transferPhoto(<?=$photo_id?>,<?=$photo_id+1?>)">
					<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M647-440H160v-80h487L423-744l57-56 320 320-320 320-57-56 224-224Z"/></svg>
				</a>
			<?php endif ?>
		</div>
		<input type="hidden" name="optImgage[][rotation]" id="rotation-<?=$photo_id;?>" value="0">
	</div>
    <? $photo_id++;
	} ?>
    <div class="error_msg" id="error_photos"></div>
</div>

</fieldset>
<fieldset>

<div class="row">
<div class="col_lft"><label><?=$language['post.label_name']?> *</label></div>
<div class="col_rgt"><input name="name" type="text" id="name" size="30" maxlength="25" value="<? echo $ad['ad']['name']; ?>"/><div class="error_msg" id="error_name"><?=$language['post.error_name']?></div>
</div>
</div>

<div class="row">
  <div class="col_lft">
    <label><?=$language['edit.label_seller_type']?> *</label>
  </div>
  <div class="col_rgt">   
    <select id="sellerType" name="seller_type" size="1">
        <option value="0" <?php echo $ad['ad']['seller_type']==0 ? 'selected':''?>><?=$language['edit.select_seller_type']?></option>
        <option value="1" <?php echo $ad['ad']['seller_type']==1 ? 'selected':''?>><?=$language['edit.seller_type_option_1']?></option>
        <option value="2" <?php echo $ad['ad']['seller_type']==2 ? 'selected':''?>><?=$language['edit.seller_type_option_2']?></option>
    </select>
    <div class="error_msg" id="error_sellerType"><?=$language['edit.error_seller_type']?></div>
  </div>
</div>


<div class="row">
	<div class="col_lft"><label><?=$language['post.label_phone']?> *</label></div>
	<div class="col_rgt">
    <div class="phone_container">
				<input name="phone"  type="tel" id="phone" size="20" maxlength="20" value="<?=$ad['ad']['phone']; ?>"/>
				<label ><input type="checkbox" value="1" name="whatsapp" <?= $ad['ad']['whatsapp'] == 1 ? 'checked' : '' ?> > Whatsapp </label>
			</div>
    <!-- <div class="phone_container">
				<input name="phone1" readonly  type="tel" id="phone1" size="20" maxlength="20" value="<?=$ad['ad']['phone1']; ?>"/>
				<label >Whatsapp <input type="checkbox" value="1" name="whatsapp1" <?= $ad['ad']['whatsapp1'] == 1 ? 'checked' : '' ?>  ></label>
			</div> -->
    <div class="error_msg" id="error_phone"><?=$language['post.error_phone']?></div>
  </div>
</div>

</fieldset>
<? /*
<div class="row">
<label class="radio">
    <input name="terminos" type="checkbox" id="terminos" value="1"/>
    <?=$language['edit.label_terms']?></label><div class="error_msg" id="error_terminos"><?=$language['edit.error_terms']?></div>
</div>
*/?>
<div class="row">
<input type="button" class="button" id="editPub" value="<?=$language['edit.button_update']?>"/>
</div>
</form>
<? } 

loadBlock("editor");
?>
</div>
<script src="<?=getConfParam('SITE_URL')?>src/js/filter.js"></script>
<script src="<?=getConfParam('SITE_URL')?>src/js/post.js"></script>

<script type="text/javascript">
  $(document).ready(function() {
    function sellerTypeTemplate (state) {
      if (!state.id) { return state.text; }
      if (state.element.value == 0) { return state.text; }
        var $state = $('<span class="select-icon"><img src="../src/images/'+  state.element.text.toLowerCase() +'.png" class="select-icon" /> ' + state.text +'</span>');
      return $state;
    };
    function sellerTypeTemplateSelection (state) {
      if (!state.id) {
        return state.text;
      }
    if (state.element.value == 0) { return state.text; }
      var baseUrl = "/user/pages/images/flags";
      var $state = $('<span class="select-icon"><img src="../src/images/'+  state.text.toLowerCase() +'.png" class="select-icon" /> ' + state.text +'</span>');
      return $state;
    };

    $('.sortable').sortable({
        helper: "clone",
        forcePlaceholderSize: true,
      forceHelperSize: true,
      grid: [ 10, 10 ]
    });
      $('#content select:not(#sellerType)').select2({
        minimumResultsForSearch: Infinity
    });
    $('#sellerType').select2({
        templateResult: sellerTypeTemplate,
        templateSelection: sellerTypeTemplateSelection,
        minimumResultsForSearch: Infinity
    });
    $('#content select').on('select2:open', function (e) {
      setTimeout(function(){ 
        $('.select2-results__group').each(function() {
            if($(this).siblings().find('.select2-results__option--selected').length > 0){
              $(this).toggleClass('openedGroup');
            }
        });
      }, 100);
    });
  });
</script>
<?
}else echo "<script>window.history.go(-1)</script>";
}else echo "<script>window.history.go(-1)</script>";
?>

