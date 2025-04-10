<?php
function uploadImage($fileArray, $ruta_img, $position, $stamp, $newW=600, $newH=600){
	//$newW=1500, $newH=1500
	$root=ABSPATH.$ruta_img;
	$max_file_size = 10; // MB
	$ext_allowed = array('jpg', 'jpeg', 'gif', 'png');  // Type allowed
	$resultado = false;
	if($position<0){
		$size=$fileArray['size'];
		$type=$fileArray['type'];
		$name_temp=$fileArray['tmp_name'];
		$name_file=$fileArray['name'];
		$position=0;
		$sep=explode('image/',$type);
		$tipo=end($sep);
	}else{
		$size=$fileArray['size'][$position];
		$type=$fileArray['type'][$position];
		$name_temp=$fileArray['tmp_name'][$position];
		$name_file=$fileArray['name'][$position];
		$sep=explode('image/',$type);
		$tipo=end($sep);
	}
	if($size <=  ($max_file_size * 1024 * 1024) && $size > 1){
		$file_ext = strtolower(substr(strrchr($name_file, '.') ,1) );
        if (in_array($file_ext, $ext_allowed)){
		switch ($tipo){
				case "gif":	$ext = ".gif"; break;
				case "png":	$ext = ".png"; break;
				case "pjpeg": case "jpeg": $ext = ".jpg"; break;
		}
		$folder=date("Ymd")."/";
		if (!is_dir($root.$folder)){
			mkdir($root.$folder, 0777, true);   
			chmod($root.$folder, 0777);      
		}
		$newFile = md5(date("YmdHis").randomString(2)).$position.".jpg";
		$newFile_min = "min_".$newFile;
		$newFileRoot = $folder.$newFile;
		$ruta = $root.$folder.$newFile;
		$ruta_min = $root.$folder.$newFile_min;
		$ruta_main = $root.$folder. "main_".$newFile;
		move_uploaded_file($name_temp, $ruta);
		list($w,$h)=getimagesize($ruta);
			//var_dump($w,$h);
			//die();
		if($w >= $h){
			$nw=$newW;
			if($w < $newW)			
			$nw=$w;
			
			$nh=($h/$w)*$nw;
		}else{
			$nh=$newH;
			if($w < $newH)
				$nh=$h;
			
			$nw=($w/$h)*$nh;
		}
		switch($ext){
				case ".gif":
					$img_src = imagecreatefromgif($ruta);
				break;
				case ".png":
					$img_src = imagecreatefrompng($ruta);
				break;
				case ".jpg":
					$img_src = imagecreatefromjpeg($ruta);
				break;
		}
		$white = imagecreatetruecolor($nw, $nh);
		$bg = imagecolorallocate($white, 255, 255, 255);
		imagefill($white, 0, 0, $bg);
		imagecolortransparent($white, $bg); 
		$resize=imagecopyresampled($white,$img_src,0,0,0,0,$nw,$nh,$w,$h);
		$resultado=imagejpeg($white,$ruta,100);
		Images::createMainImage($img_src, $ruta_main);
		imagedestroy($img_src);
		imagedestroy($white);
		
		if($stamp && file_exists(ABSPATH . IMG_PATH .'estampa.png')){
			$estampa = imagecreatefrompng(ABSPATH . IMG_PATH .'estampa.png');
			$im = imagecreatefromjpeg($ruta);
			$margen_dcho = 10;
			$margen_inf = 10;
			$sx = imagesx($estampa);
			$sy = imagesy($estampa);
			imagecopy($im, $estampa, imagesx($im) - $sx - $margen_dcho, imagesy($im) - $sy - $margen_inf, 0, 0, imagesx($estampa), imagesy($estampa));
			$resultado = imagejpeg($im, $ruta, 100);
		}
		list($w,$h)=getimagesize($ruta);
		if($w >= $h){
			$nw=205;
			$nh=($h/$w)*$nw;
		}else{
			$nh=160;
			$nw=($w/$h)*$nh;
		}

		
		// $im_new_min = imagecreatetruecolor($nw, $nh);
		// $im_save = imagecreatefromjpeg($ruta);
		// $resize=imagecopyresampled($im_new_min,$im_save,0,0,0,0,$nw,$nh,$w,$h);
		// $resultado1=imagejpeg($im_new_min,$ruta_min);
		
	}else $resultado=false;
	}else $resultado=false;
	if($resultado) return $newFileRoot; else return false;
}
function min_image($img){
	if(strpos($img,"/") === false)
	$name_min = "min_".$img;
	else{
		$imm=explode("/",$img);
		$name_min=$imm[0]."/min_".$imm[1];
	}
	return $name_min;
}
function clean_images_db(){
	$hour_ago=time()-24*60*60;
	$images=selectSQL("sc_images",$w=array('ID_ad'=>0,'date_upload'=>$hour_ago."<"));
	for($i=0;$i<count($images);$i++){
			@unlink(ABSPATH.IMG_ADS.$images[$i]['name_image']);
			@unlink(ABSPATH.IMG_ADS.min_image($images[$i]['name_image']));
			deleteSQL("sc_images",$wm=array('ID_image'=>$images[$i]['ID_image']));
	}
}

function getImgOrientation($img){
	$dir = ABSPATH . IMG_ADS . $img;
	if(!file_exists($dir)){
		return false;
	}
	$orientation = 'horizontal';
	$imagezise = getimagesize($dir) ;
	if($imagezise[0] < $imagezise[1])
		$orientation = 'vertical';

	//return 'vertical';
	return $orientation;
}