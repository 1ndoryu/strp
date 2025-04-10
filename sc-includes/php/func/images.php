<?php


class Images
{
    const CAT_IMG = IMG_CATEGORY . "img/";
    const CAT_ICON = IMG_CATEGORY ."icon/";
    const BANNERS = IMG_BANNERS;
    const DEFAULT_IMAGE = IMG_PATH . 'back_photo_upload.png';
    const IMG = IMG_PATH;
    const ORG_SUB = "org";
    const IMG_WIDTH = 636;
    const IMG_HEIGHT = 750;
    const IMG_WIDTH_R = 330;
    const IMG_HEIGHT_R = 390;
    const MAIN_IMG_WIDTH = 770;
    const MAIN_IMG_HEIGHT = 910;
    const IMG_RATIO = 11/13;
    const COMPRESS = 90;
    /**
     * obtiene la extension de la imagen por el mimetype
     */
    static function getExtension($mimetype)
    {
        $ext = ".jpg";
        switch ($mimetype) {
            case "image/jpeg":
                $ext =".jpeg";
                break;
            case "image/jpg":
                $ext = ".jpg";
                break;
            case "image/png":
                $ext =".png";
                break;
            case "image/webp":
                $ext = ".webp";
                break;
            case "image/svg+xml":
                $ext = ".svg";
                break;
            default:
        }

        return $ext;
    }

    static function uploadImages($path, $img, $tmp)
    {
        $path = ABSPATH . $path . $img;
        if(move_uploaded_file($tmp ,$path))
            return $img;
        else
            return "";
    }

    static function discardChanges($id)
    {
        $images = selectSQL("sc_images", $w = array(
            'ID_ad' => $id
        ) , 'position ASC, ID_image ASC');
        foreach ($images as $key => $value) 
        {
            if($value['status'] == ImageStatus::Delete)
                updateSQL("sc_images",$d=array('status'=>ImageStatus::Active),$w=array('ID_image'=>$value['ID_image']));
            if($value['status'] == ImageStatus::Inactive)
                Images::deleteImage($value['ID_image']);
        }
    }

    static function deleteImage($id)
    {
        $image = selectSQL("sc_images", $w = array(
            'ID_image' => $id
        ));

        if(count($image) > 0)
        {
            $image = $image[0];
            @unlink(ABSPATH . IMG_ADS . $image['name_image']);
            @unlink(ABSPATH . IMG_ADS . min_image($image['name_image']));
            deleteSQL("sc_images", $wm = array(
                'ID_image' => $id
            ));
        }
    }

    static function getImageSize($img)
    {	
        $dir = ABSPATH . IMG_ADS . $img;
        if(!file_exists($dir)){
            return array(0,0);
        }
        $imagezise = getimagesize($dir);
        return $imagezise;
    }

    static function calculateImageSize($img, $height)
    {
        $dir = ABSPATH . IMG_ADS . $img;
        if(!file_exists($dir)){
            return array(0,0);
        }
        list($width_org, $height_org) = getimagesize($dir);
        $width = $width_org * $height / $height_org;
        return array($width, $height);
    }

    static function getImageDefault()
    {
        return getConfParam('SITE_URL') . self::DEFAULT_IMAGE;
    }

    static function getImage($img, $path = IMG_PATH, $webp = false, $t = 0)
    {
        if($t == 0)
            $tstring = "";
        else
            $tstring = "?t=".$t;
        $img_d = ABSPATH . $path . $img;
        if($img != "" && file_exists($img_d))
        {
            if($webp)
            {

                return getConfParam('SITE_URL') . self::getImageWebp($path , $img) . $tstring;
            }
            return getConfParam('SITE_URL') . $path . $img . $tstring;
        }
        else
            return getConfParam('SITE_URL') . self::DEFAULT_IMAGE;
    }

    static function getImageWebp($path, $img)
    {
        global $IS_MOBILE;

        $pathWebp = str_replace(['.jpg', '.jpeg', '.png'], '.webp', $path . $img);
        
        if(!$IS_MOBILE)
        {
            if(file_exists(ABSPATH .$pathWebp))
                return $pathWebp;
        }else
        {
            $pathWebpR = str_replace(['.jpg', '.jpeg', '.png'], '.webp', $path . min_image($img));
            if(file_exists(ABSPATH .$pathWebpR))
                return $pathWebpR;
            if(file_exists(ABSPATH .$pathWebp))
                return $pathWebp;
        }

        return $path . $img;
    }
    static function getImageAd($id)
    {
        $imgs = selectSQL("sc_images", $w = array(
            'ID_ad' => $id
        ) , 'position ASC, ID_image ASC');
        
        if(count($imgs) > 0)
            return self::getImage($imgs[0]["name_image"], IMG_ADS);
        else
            return self::getImageDefault();
    }

    static function getImageData($id, $preview = false)
    {
        if($preview)
        {
            $imgs = selectSQL("sc_images", $w = array(
                'ID_ad' => $id,
                'status' => ImageStatus::Delete . "!="
            ) , 'position ASC, ID_image ASC');
        }else
        {
            $imgs = selectSQL("sc_images", $w = array(
                'ID_ad' => $id,
                'status' => ImageStatus::Inactive . "!="
            ) , 'position ASC, ID_image ASC');
        }
        
        if(count($imgs) > 0)
            return $imgs;
        else
            return array();
    }
    
    static function checkImageChanges($id_ad)
    {
        $ads = countSQL("sc_images", $w = array(
            'ID_ad' => $id_ad,
            'status' => ImageStatus::Active . "!="
        ));

        return ($ads > 0);
    }

    static function getLogo($size = "normal")
    {
        if($size == "normal")
            $logo = getConfParam('SITE_URL') . IMG_PATH . "logo.png";
        if($size == "compressed")
            $logo = getConfParam('SITE_URL') . IMG_PATH . "logo.webp";
        if($size == "mini")
            $logo = getConfParam('SITE_URL') . IMG_PATH . "logo_r.webp";
        return $logo;
    }

    static function webp($filename)
    {
        $filename = str_replace(['.jpg', '.jpeg', '.png'], '.webp',  $filename);
        return $filename;
    }

    static function putDataImage($path, $filename, $base64String)
    {
        $main = $path . self::main_image($filename);
        $normal = $path . $filename;
        $min = $path . min_image($filename);
        $normal = self::webp($normal);
        $min = self::webp($min);

        // Dividir el string en los datos base64
        list($type, $data) = explode(';', $base64String);
        list(, $data)      = explode(',', $data);

        // Decodificar los datos base64
        $decodedData = base64_decode($data);

        $image = imagecreatefromstring($decodedData);

        if(getConfParam('AC_WATERMARK') == 1)
            $image = self::printStampWebp($image);

        //imagejpeg($image, $main, 20);
		

        if(imagewebp($image, $normal, 100))
        {
            if($min != null)
            {
                imagewebp($image, $min, 100);
            }
            imagedestroy($image);
            return true;
        }else
        {
            imagedestroy($image);
            return false;
        }

    }

    static function is_vertical($img, $path = IMG_ADS)
    {
        $image = getimagesize( ABSPATH . $path . $img);
        if($image[1] > $image[0])
            return true;
        return false;
    }


    static function originalImage($img, $path = IMG_ADS)
    {
        $root=ABSPATH.$path;
           // Obtener la extensión del archivo
        $ext = pathinfo($img, PATHINFO_EXTENSION);
        // Obtener el nombre del archivo sin extensión
        $name = pathinfo($img, PATHINFO_FILENAME);
        // Obtener el directorio del archivo
        $dir = pathinfo($img, PATHINFO_DIRNAME);
        
        $sufijo = self::ORG_SUB;
        // Construir la nueva ruta con el sufijo
        $n_path = $dir . '/' . $sufijo . '_' . $name . '.' . $ext;
        
        return $n_path;

    }

    static function imageEdited($img)
    {
        $select = selectSQL("sc_images", $w = array(
            'name_image' => $img
        ));
        if(count($select) > 0)
        {
            $id = $select[0]['ID_image'];
            $time = time();
            updateSQL("sc_images", $d = array(
                'edit' => $time
            ), $w = array(
                'ID_image' => $id
            ));
            
            return true;
        }
        return false;
    }

    static function copyOriginalImage($img, $path = IMG_ADS)
    {
        $root=ABSPATH.$path;
        
        $new = $root . self::originalImage($img, $path);
        $old = $root . $img;
        if(file_exists($old))
            return copy($old, $new);

    }

    static function copyImage($img, $npath = IMG_ADS, $spath = IMG_ADS , $webp = false)
    {
        $root=ABSPATH.$npath;
        $ext = self::getExtension($img);
        $folder=date("Ymd")."/";
		if (!is_dir($root.$folder)){
			mkdir($root.$folder, 0777, true);   
			chmod($root.$folder, 0777);      
		}
        if(!$webp)
            $newFile = md5(date("YmdHis").randomString(2)).$ext;
        else
            $newFile = md5(date("YmdHis").randomString(2)).".webp";
        $newFileRoot = $folder.$newFile;
        $ruta = $root.$folder.$newFile;
        if(!$webp)
            copy(ABSPATH . $spath . $img, $ruta);
        else
        {
            switch ($ext) 
            {
                case '.jpg':
                case '.jpeg':
                    $orgImg = imagecreatefromjpeg(ABSPATH . $spath . $img);
                    break;
                case '.png':
                    $orgImg = imagecreatefrompng(ABSPATH . $spath . $img);
                    break;

                case '.gif':
                    $orgImg = imagecreatefromgif(ABSPATH . $spath . $img);
                    break;
                default:
                    return "";
                    break;
            }
            imagewebp($orgImg, $ruta , 100);

        }
        return $newFileRoot;
    }

    static function getMaxScore($images)
    {
        $max_score = 0;
        foreach ($images as $key => $value) {
            $score = intval($value['score']);
            if($score > $max_score)
                $max_score = $score;
        }
        return $max_score;
    }

    static function getBolsaImg()
    {
        return self::getImage("bolsa.png");
    }

    static function rotateImage($img, $rotation)
    {
        if($rotation == 0)
            return;
        $rotation = 360 - $rotation;
        $ext = self::getExtension($img);
        $min_name = min_image($img);
        switch ($ext) 
        {
            case '.jpg':
            case '.jpeg':
                $orgImg = imagecreatefromjpeg(ABSPATH . IMG_ADS . $img);
                $orgImg_min = imagecreatefromjpeg(ABSPATH . IMG_ADS . $min_name);
                break;
            case '.png':
                $orgImg = imagecreatefrompng(ABSPATH . IMG_ADS . $img);
                $orgImg_min = imagecreatefrompng(ABSPATH . IMG_ADS . $min_name);
                break;
            default:
                return;
                break;

        }
            
        $imageRotated = imagerotate($orgImg, $rotation, 0);
        $imageRotated_min = imagerotate($orgImg_min, $rotation, 0);
        $webp = str_replace($ext, ".webp", $img);
        imagewebp($imageRotated, ABSPATH . IMG_ADS . $webp, 100);
        //save image
        switch ($ext) 
        {
            case '.jpg':
            case '.jpeg':
                imagejpeg($imageRotated, ABSPATH . IMG_ADS . $img);
                imagejpeg($imageRotated_min, ABSPATH . IMG_ADS . $min_name);
                break;
            case '.png':
                imagepng($imageRotated, ABSPATH . IMG_ADS . $img);
                imagepng($imageRotated_min, ABSPATH . IMG_ADS . $min_name);
                break;
            default:
                return;
                break;
        }
        imagedestroy($orgImg);
        imagedestroy($imageRotated);
        imagedestroy($imageRotated_min);
    }

    static function  cropImage($img, $main = false)
    {
        $width = imagesx($img);
        $height = imagesy($img);
        if($main)
        {
            $final_width = self::MAIN_IMG_WIDTH;
            $final_height = self::MAIN_IMG_HEIGHT;
        }else
        {
            $final_width = self::IMG_WIDTH;
            $final_height = self::IMG_HEIGHT;
        }

        $ratio = $width / $height;
        if($ratio <= self::IMG_RATIO)
        {
            $n_width = $width * 1;
            $n_height = $n_width / self::IMG_RATIO;
            $x = abs($width - $n_width) / 2;
            //$x = 0;
            $y = 0; // Mantener la parte superior intacta
        }else
        {
            $n_height = $height;
            $n_width = $height * self::IMG_RATIO;
            $x = ($width - $n_width) / 2;
            $y = 0;
        }

        $fimage = imagecreatetruecolor($final_width, $final_height);
        imagecopyresampled(
            $fimage, $img,
            0, 0,
            $x, $y,
            $final_width, $final_height,
            $n_width, $n_height
        );

        imagedestroy($img);
        return $fimage;

    }

    static function printStampWebp($img)
    {
        $ancho_original = imagesx($img);
        $alto_original = imagesy($img);
  
        
        $image = imagecreatetruecolor(self::IMG_WIDTH, self::IMG_HEIGHT);
        
        $r = imagecopyresampled($image, $img, 0, 0, 0, 0, self::IMG_WIDTH, self::IMG_HEIGHT, $ancho_original, $alto_original);
        
        $image = self::printStamp($image);

        return $image;
    }

    static function printStamp($img)
    {
        $estampa = imagecreatefrompng(ABSPATH . IMG_PATH . getConfParam('WATEMARK_IMAGE'));
        $sx = imagesx($estampa);
        $sy = imagesy($estampa);
        //$width = 600;
        //$height = intval($width * ($sy / $sx));
        $width = $sx;
        $height = $sy;
        $source_width = imagesx($img);
        $source_height = imagesy($img);
        $x = ($source_width - $width) / 2;
        $y = ($source_height - $height) / 2;
        $r = imagecopy($img, $estampa, $x, $y, 0, 0, $width, $height);
        return $img;
    }
    

    static function createWebp($img)
    {
        $ext = self::getExtension($img);
        $webp = str_replace($ext, ".webp", $img);
        $webp_min = min_image($webp);
        
        if(file_exists(ABSPATH .IMG_ADS . $img))
        {
            switch ($ext) 
            {
                case '.jpg':
                case '.jpeg':
                    $orgImg = imagecreatefromjpeg(ABSPATH . IMG_ADS . $img);
                    break;
                case '.png':
                    $orgImg = imagecreatefrompng(ABSPATH . IMG_ADS . $img);
                    break;

                
                default:
                    # code...
                    break;
            }
            if(isset($orgImg))
            {
                 $orgImg = self::cropImage($orgImg);
                 //if(getConfParam('AC_WATERMARK') == 1)
                 //   $orgImg = self::printStamp($orgImg);
                 $r = imagewebp($orgImg, ABSPATH . IMG_ADS . $webp, 99);
                 $min_image = self::scaleImage($orgImg, self::IMG_WIDTH_R, self::IMG_HEIGHT_R);
                 $r = imagewebp($min_image, ABSPATH . IMG_ADS . $webp_min, self::COMPRESS);
                 return true;
            }

        }
    
    }

    static function main_image($img)
    {
        if(strpos($img,"/") === false)
        $name_min = "main_".$img;
        else{
            $imm=explode("/",$img);
            $name_min=$imm[0]."/main_".$imm[1];
        }
        return $name_min;
    }

    static function getMainImg($img)
    {
        $filename = IMG_ADS . self::main_image($img);
        if(!file_exists(ABSPATH . $filename)){
            return self::getImage($img, IMG_ADS, true);
        }

        return getConfParam('SITE_URL') . $filename;

    }

    static function createMainImage($img, $filename)
    {
        $img = self::cropImage($img, true);
        $img = self::printStamp($img);
        $r = imagejpeg($img, $filename, 100);
    }

    static function scaleImage($image, $new_width, $new_height) {
        // Obtenemos las dimensiones originales de la imagen
        $old_width = imagesx($image);
        $old_height = imagesy($image);
    
        // Creamos una nueva imagen con las nuevas dimensiones
        $new_image = imagecreatetruecolor($new_width, $new_height);
    
        // Copiamos la imagen original a la nueva, redimensionando
        imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $old_width, $old_height);
    
        return $new_image;
    }
}