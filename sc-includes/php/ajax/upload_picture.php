<?php
///*     ScriptClasificados v8.0                     *///
///*     www.scriptclasificados.com                  *///
///*     Created by cyrweb.com. All rights reserved. *///
///*     Copyright 2009-2017                         *///

include("../../../settings.inc.php");


function compressImage($filePath, $quality = 90) {
	//var_dump($filePath);
	//die('===');
    $info = getimagesize($filePath);
    $image = null;
    
    if ($info['mime'] == 'image/jpeg') {
        $image = imagecreatefromjpeg($filePath);
        imagejpeg($image, $filePath, $quality);
    } elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($filePath);
        imagepng($image, $filePath, 9);
    } else {
        return false; // Unsupported file type
    }
    
    if ($image) {
        imagedestroy($image);
    }
    return true;
}


	if(isset($_FILES['userImage']) && $_FILES['userImage']['error'] == 0) {
		$ac_w = getConfParam('AC_WATERMARK');
		$fo = fopen($_FILES["userImage"]["tmp_name"], "r");
		$image_binary = fread($fo, filesize($_FILES["userImage"]["tmp_name"]));
		$md5_hash = md5($image_binary);
		$resultado=uploadImage($_FILES['userImage'],IMG_ADS,-1, 0);
		$status = ImageStatus::Inactive;
		if(isset($_POST['status']))
			$status = $_POST['status'];
		if($resultado!==false)
		{
			
			$imagePath = '/var/www/vhosts/41121521.servicio-online.net/httpdocs/'.IMG_ADS.$resultado;
			//			var_dump(IMG_ADS ,$resultado , '===', '/var/www/vhosts/41121521.servicio-online.net/httpdocs/'.IMG_ADS.$resultado);
			//die();
			if ($imagePath && file_exists($imagePath)) {
            	compressImage($imagePath);
        	}
 
			Images::createWebp($resultado);
			insertSQL("sc_images",$dsa=array('name_image'=>$resultado,'date_upload'=>time(), 'hash' => $md5_hash, 'status' => $status));
		
	?>
	<div class="removeImg"><i class="fa fa-times" aria-hidden="true"></i></div>
	<a href="javascript:void(0);" class="edit-photo-icon" onclick="editImage(2)">
		<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h357l-80 80H200v560h560v-278l80-80v358q0 33-23.5 56.5T760-120H200Zm280-360ZM360-360v-170l367-367q12-12 27-18t30-6q16 0 30.5 6t26.5 18l56 57q11 12 17 26.5t6 29.5q0 15-5.5 29.5T897-728L530-360H360Zm481-424-56-56 56 56ZM440-440h56l232-232-28-28-29-28-231 231v57Zm260-260-29-28 29 28 28 28-28-28Z"></path></svg>
	</a>
	<span class="helper"></span>
	<img class="<?= getImgOrientation($resultado) ?>" src="<?=getConfParam('SITE_URL')?><?=IMG_ADS?><?=$resultado?>"/>
    <input type="hidden" name="photo_name[]" value="<?=$resultado;?>">
	<?php
    	}
}


?>