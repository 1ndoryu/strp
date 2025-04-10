<?
    require_once 'settings.inc.php';

    function recortarImagen($rutaImagen) {
        $ratio = 11/13;

        // Dimensiones finales deseadas
        if(!isset($_GET['size']) || $_GET['size'] == 'normal')
        {
            $anchoFinal = 400;
            $altoFinal = 473;
        }else if($_GET['size'] == 'small')
        {
            $anchoFinal = 360;
            $altoFinal = 425;
        }else if($_GET['size'] == 'tiny')
        {
            $anchoFinal = 200;
            $altoFinal = 236;
        }
    
        // Cargar la imagen original
        $rutaImagen = ABSPATH . IMG_ADS . $rutaImagen;
        list($anchoOriginal, $altoOriginal) = getimagesize($rutaImagen);
        $ext = Images::getExtension($rutaImagen);
        switch ($ext) 
        {
            case '.jpg':
            case '.jpeg':
                $imagenOriginal = imagecreatefromjpeg($rutaImagen);
                break;
            case '.png':
                $imagenOriginal = imagecreatefrompng($rutaImagen);
                break;

            
            default:
                # code...
                break;
        }

    
        // Calcular las dimensiones de recorte
        $proporcionOriginal = $anchoOriginal / $altoOriginal;
        $proporcionFinal = $anchoFinal / $altoFinal;

        if(round($proporcionOriginal, 2) == round($ratio, 2))
        {
            $imagenFinal = $imagenOriginal;
        }else
        {
            if ($proporcionOriginal > $proporcionFinal) {
                // Imagen más ancha que la proporción final (recortar laterales)
                $nuevoAlto = $altoOriginal;
                $nuevoAncho = $altoOriginal * $proporcionFinal;
                $x = ($anchoOriginal - $nuevoAncho) / 2;
                $y = 0;
            } else {
                // Imagen más alta que la proporción final (recortar solo abajo)
                $nuevoAncho = $anchoOriginal * 0.8;
                $nuevoAlto = $nuevoAncho / $proporcionFinal;
                $x = abs($anchoOriginal - $nuevoAncho) / 2;
                //$x = 0;
                $y = 0; // Mantener la parte superior intacta
            }
        
            // Crear una nueva imagen con las dimensiones finales
            $imagenFinal = imagecreatetruecolor($anchoFinal, $altoFinal);
        
            // Copiar y redimensionar la imagen original al tamaño final
            imagecopyresampled(
                $imagenFinal, $imagenOriginal,
                0, 0, $x, $y,
                $anchoFinal, $altoFinal,
                $nuevoAncho, $nuevoAlto
            );
        }
    
    
        // Enviar encabezado de imagen
        header('Content-Type: image/webp');
    
        // Mostrar la imagen recortada
        if(isset($_GET['size']) && $_GET['size'] == 'tiny')
            imagewebp($imagenFinal, null, 80);
        else
            imagewebp($imagenFinal, null, 100);
    
        // Liberar memoria
        imagedestroy($imagenOriginal);
        imagedestroy($imagenFinal);
    }

    if (isset($_GET['img'])) {
        $rutaImagen = $_GET['img'];
        recortarImagen($rutaImagen);
    } else {
        echo "No se ha proporcionado una imagen.";
    }