<?php
define('IMG_PATH', dirname(__DIR__) . "/src/images/");

class Core
{
    const PATH = __DIR__;
    //const IMG_PATH = dirname(__DIR__) . "/src/images/";
    const IMG_WIDTH = 636;
    const IMG_HEIGHT = 750;
    const IMG_WIDTH_R = 330;
    const IMG_HEIGHT_R = 390;
    const IMG_RATIO = 11/13;
    const COMPRESS = 90;


    static function checkUser($id)
    {
        global $bd_target;
        $sql = "SELECT * FROM sc_user WHERE ID_user = '$id'";
        $resultado = $bd_target->query($sql);
        if ($resultado->num_rows > 0) {
            return true;
        }else
          return false;
    }

    static function mapDis($dis)
    {
        if($dis == 0)
            return 0;
        if($dis == 1)
            return 3;
        if($dis == 2)
            return 4;
        if($dis == 3)
            return 1;
        if($dis == 4)
            return 2;
    }

    static function mapHorario($horario)
    {
        if(str_contains($horario, "-"))
            return explode("-", $horario);
        else
            return ["", ""];
    }

    static function mapCategoria($idcat)
    {
        $parent_cat = 0;
        $cat = 0;
        if($idcat == 7)
        {
            $parent_cat = 105;
            $cat = 328;
        }

        if($idcat == 10)
        {
            $parent_cat = 32;
            $cat = 329;
        }

        if($idcat == 11 || $idcat == 24)
        {
            $parent_cat = 234;
            $cat = 330;
        }

        if($idcat == 4)
        {
            $parent_cat = 61;
            $cat = 331;
        }
        
        return array($parent_cat, $cat);
    }

    static function mapImages($images, $id_ad)
    {
        global $bd_target;
        $target_path = dirname(self::PATH) . "/src/photos/";
        $source_path = dirname(dirname(self::PATH)) . "/httpdocs/images/";
        //$source_path = dirname(dirname(self::PATH)) . "/solomasajistas/images/";
        foreach ($images as $key => $value) 
        {
            if($value != "" && file_exists($source_path . $value))
            {
                $fo = fopen($source_path . $value, "r");
                $image_binary = fread($fo, filesize($source_path . $value));
                $md5_hash = md5($image_binary);
                if(self::existsImage($md5_hash, $id_ad))
                    continue;
                fclose($fo);
                $img = self::uploadImage($source_path . $value, $target_path);
                if($img == "")
                    continue;
                self::createWebp($img, $target_path);
                $query = "INSERT INTO sc_images (position, ID_ad, name_image, date_upload, hash, status) VALUES (0, $id_ad, '$img', '".time()."', '$md5_hash', 1)";
                $bd_target->query($query);
            }else if(VERBOSE && $value != "")
                echo "Imagen $source_path$value no encontrada <br>";
        }
    }

    static function existsImage($hash, $ID_ad)
    {
        global $bd_target;

        $sql = "SELECT * FROM sc_images WHERE hash = '$hash' AND ID_ad = '$ID_ad'";
        $result = $bd_target->query($sql);

        if ($result->num_rows > 0) {
            return true;
        }else
          return false;

    }

    static function uploadImage($img, $target_path)
    {
        $ext = self::getExtension($img);
        $name = md5(date("YmdHis").randomString(2)).".jpg";
        $newW=800; 
        $newH=600;
        $folder=date("Ymd")."/";
		if (!is_dir($target_path.$folder)){
			$r = mkdir($target_path.$folder, 0777, true); 
            if(!$r && VERBOSE)
                echo "Error al crear carpeta $folder";
			chmod($target_path.$folder, 0777);      
		}

        $filename = $folder.$name;
        $ruta = $target_path . $filename;

        list($w,$h)=getimagesize($img);
        if($w >= $h){
            $nw=$newW;
            $nh=($h/$w)*$nw;
        }else{
            $nh=$newH;
            $nw=($w/$h)*$nh;
        }
        try {
            switch($ext){
                    case ".gif":
                        $img_src = imagecreatefromgif($img);
                    break;
                    case ".png":
                    case ".PNG":
                        $img_src = imagecreatefrompng($img);
                    break;
                    case ".jpg":
                    case ".jpeg":
                    case ".JPEG":
                    case ".JPG":
                        $img_src = imagecreatefromjpeg($img);
                    break;
                    case ".webp":
                        $img_src = imagecreatefromwebp($img);
                    break;
                    default:
                        return "";
            }
            $white = imagecreatetruecolor($nw, $nh);
            $bg = imagecolorallocate($white, 255, 255, 255);
            imagefill($white, 0, 0, $bg);
            imagecolortransparent($white, $bg); 
            $resize=imagecopyresampled($white,$img_src,0,0,0,0,$nw,$nh,$w,$h);
            $resultado=imagejpeg($white,$ruta,100);
            imagedestroy($img_src);
            imagedestroy($white);
            if(VERBOSE)
                echo "Imagen $filename creada";
            return $filename;
        } catch (\Throwable $th) {
            //throw $th;
            return "";
        }

    }

    static function formatearTexto($descripcion) {
        global $bd_source;
        $sql = "SELECT * FROM bd_ciudad where nombre!='Melilla' and nombre!='Ceuta' and nombre!='Jaén' order by nombre asc";

        $result = mysqli_query($bd_source, $sql);

        $provincias = array();
    
        while($assoc = mysqli_fetch_assoc($result)){
    
            array_push($provincias, 
    
                array(
    
                    "id" => $assoc['id'],
    
                    "nombre" => $assoc['nombre'],
    
                    "link" => $assoc['name_seo']
    
                )
    
            );
    
        }
    
        //unset($provincias[0]);
    
        //$provincias = array_values($provincias);
        // Convierte toda la cadena a minúsculas
        $descripcion = mb_strtolower($descripcion, 'UTF-8');
        
        // Reemplaza múltiples signos de interrogación por uno solo
        $descripcion = preg_replace('/\?+/', '?', $descripcion);
        
        // Reemplaza múltiples signos de exclamación por uno solo
        $descripcion = preg_replace('/\!+/', '!', $descripcion);
        
        // Reemplazar dos o más signos de interrogación invertidos por un solo signo de interrogación invertido
        $descripcion = preg_replace('/(¿){2,}/u', '¿', $descripcion);
        
        // Reemplazar dos o más signos de exclamación invertidos por un solo signo de exclamación invertido
        $descripcion = preg_replace('/(¡){2,}/u', '¡', $descripcion);
    
        // Si hay cuatro o más puntos seguidos, reemplácelos por tres puntos suspensivos
        if (preg_match('/\.{4,}/', $descripcion)) {
            $descripcion = preg_replace('/\.{4,}/', '...', $descripcion);
        }
    
        // Agrega un espacio después de cualquier signo de puntuación y convierte la siguiente letra en mayúscula
        $descripcion = preg_replace_callback('/([.?!])\s*(\w)/', function($matches) {
            return $matches[1] . ' ' . strtoupper($matches[2]);
        }, $descripcion);
        
        $descripcion = mb_ereg_replace_callback('([¿¡])\s*(\p{Ll})', function($matches) {
        return $matches[1] . mb_convert_case($matches[2], MB_CASE_UPPER, 'UTF-8');
    }, $descripcion);
        
        // Elimina los espacios en blanco al principio y al final de la cadena
        $descripcion = trim($descripcion);
    
        // Convierte la primera letra de la cadena en mayúscula
        $descripcion = ucfirst($descripcion);
    
        //Convierte los nombre de las provincias en minusculas a mayúsculas
        if(isset($provincias) && is_array($provincias)){
    
            foreach($provincias as $val)
            {
                $name = $val['nombre'];
                $descripcion = preg_replace("/$name/i", $name, $descripcion);
            }
        }
        
        // Devuelve la descripción formateada
        return $descripcion;
    }
    

    static function cropImage($img)
    {
        $width = imagesx($img);
        $height = imagesy($img);

        $final_width = self::IMG_WIDTH;
        $final_height = self::IMG_HEIGHT;

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

    static function createWebp($img, $target_path)
    {
        $ext = self::getExtension($img);
        $webp = str_replace($ext, ".webp", $img);
        $webp_min = min_image($webp);
        if(file_exists($target_path . $img))
        {
            switch ($ext) 
            {
                case '.jpg':
                case '.jpeg':
                    $orgImg = imagecreatefromjpeg($target_path . $img);
                    break;
                case '.png':
                    $orgImg = imagecreatefrompng($target_path. $img);
                    break;
                case '.webp':
                    $orgImg = imagecreatefromwebp($target_path. $img);
                    break;

                
                default:
                    # code...
                    break;
            }
            if(isset($orgImg))
            {
                 $orgImg = self::cropImage($orgImg);
                 $orgImg = self::printStamp($orgImg);
                 //if(getConfParam('AC_WATERMARK') == 1)
                 $r = imagewebp($orgImg, $target_path . $webp, self::COMPRESS);
                 $min_image = self::scaleImage($orgImg, self::IMG_WIDTH_R, self::IMG_HEIGHT_R);
                 $r = imagewebp($min_image, $target_path  . $webp_min, self::COMPRESS);
                return true;
            }

        }
    }

    static function printStamp($img)
    {
        $estampa = imagecreatefrompng(IMG_PATH . "estampa.png");
        $sx = imagesx($estampa);
        $sy = imagesy($estampa);
        //$width = 600;
        //$height = intval($width * ($sy / $sx));
        $width = $sx;
        $height = $sy;
        $x = (self::IMG_WIDTH - $width) / 2;
        $y = (self::IMG_HEIGHT - $height) / 2;
        $r = imagecopy($img, $estampa, $x, $y, 0, 0, $width, $height);
        return $img;
    }

    static function getExtension($archivo)
    {
        try {
            $extension = pathinfo($archivo, PATHINFO_EXTENSION);
            $extension = "." . $extension;

            return $extension;
        } catch (\Throwable $th) {
            //throw $th;
            return "";
        }
    }

    static function functionOldUrl($id, $tit)
    {
        $special_chars=array("Ã","À","Á","Ä","Â","È","É","Ë","Ê","Ì","Í","Ï","Î","Ò","Ó","Ö","Ô","Ù","Ú","Ü","Û","ã","à","á","ä","â","è","é","ë","ê","ì","í","ï","î","ò","ó","ö","ô","ù","ú","ü","û","Ñ","ñ","Ç","ç","-","/",",",".","*","_","@","!","?","¿","(",")",'\\',"º","ª","#","€","+","'",'"',"&","·","]","[","´","`","%",":",'&','amp;', ' ', '&nbsp;');
        $replacement_chars=array('A','A','A','A','A','E','E','E','E','I','I','I','I','O','O','O','O','U','U','U','U','a','a','a','a','a','e','e','e','e','i','i','i','i','o','o','o','o','u','u','u','u','n','n','c','c',"","","","","","","","","","","","","","","","","","","","","","","","","","","","","",'','','');
        $enlace = filter_var($tit, FILTER_SANITIZE_STRING); 

        $enlace=str_replace($special_chars, $replacement_chars, $enlace);
        $enlace = strtolower($enlace);
        $b_enlace = "detalle/";
        $anunlink = "/{$b_enlace}{$enlace}-{$id}.html";

        return addslashes($anunlink);
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

function randomString($length, $num = false)
{

    $key = "";

    if ($num) $pattern = "1234567890";

    else $pattern = "1234567890abcdefghijklmnopqrstuvwxyz";

    for ($i = 0;$i < $length;$i++)
    {

        $key .= $pattern[rand(0, strlen($pattern) - 1)];

    }

    return $key;

}

class RegionMap {
    private $bd_source;
    private $bd_target;
    private $mapping = [];

    public function __construct($bd_source, $bd_target) {
        $this->bd_source = $bd_source;
        $this->bd_target = $bd_target;

        // Realizamos el mapeo en el constructor y lo guardamos en el atributo
        $sql_source = "SELECT id, name_seo FROM bd_ciudad";
        $result_source = $this->bd_source->query($sql_source);

        while ($row_source = $result_source->fetch_assoc()) {
            $sql_target = "SELECT ID_region FROM sc_region WHERE name_seo = ?";
            $stmt_target = $this->bd_target->prepare($sql_target);
            $stmt_target->bind_param("s", $row_source['name_seo']);
            $stmt_target->execute();
            $result_target = $stmt_target->get_result();

            if ($row_target = $result_target->fetch_assoc()) {
                $this->mapping[$row_source['id']] = $row_target['ID_region'];
            }
        }
    }

    public function getID($id_source) {
        return $this->mapping[$id_source] ?? 0; // Devuelve 0 si no encuentra la clave
    }
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