<?php
// Consulta para obtener los datos de bd_anuncio
$sql = "SELECT a.* FROM bd_anuncio a, bd_usuario u,bd_ciudad c WHERE (a.publicado=1 OR a.publicado = 2) and a.motivo != 8 and a.listado = 0 and a.idusuario = u.id AND a.idprovincia=c.id GROUP BY a.id ORDER BY a.fecha DESC LIMIT $etapa, $limite";
$resultado = $bd_source->query($sql);
$reg_map = new RegionMap($bd_source, $bd_target);

ob_start();

// Verifica si hay datos
if ($resultado->num_rows > 0) {
    // Genera las sentencias de inserción
    while ($fila = $resultado->fetch_assoc()) {
        $ID_ad = $fila['id'];
        $ID_user = $fila['idusuario'];
        $ad_type = 1; // Valor fijo, por ejemplo: 1 para 'Oferta'
        $seller_type = 1; // Valor fijo, por ejemplo: 1 para 'Particular'
        $title = addslashes(Core::formatearTexto($fila['titulo']));
        $title_seo = addslashes($fila['titulo']);
        $name = addslashes($fila['nombre']);
        $phone = $fila['telefono'];
        $whatsapp = $fila['whatsapp'];
        $phone1 = $fila['telefono2'];
        $whatsapp1 = $fila['whatsapp2'];
        $texto = addslashes(Core::formatearTexto($fila['descripcion']));
        $parent_cat = "NULL"; // Sin mapeo directo
        $ID_cat = $fila['idcat'];
        $ID_region = $fila['idprovincia'];
        $date_ad = strtotime($fila['fecha']); // Convierte a timestamp UNIX
        $price = is_numeric($fila['precio']) ? $fila['precio'] : 0; // Conversión a número
        $visit = $fila['visitas'];
        $contact_times = 0; // Sin mapeo directo
        $area = "NULL"; // Sin mapeo directo
        $room = "NULL"; // Sin mapeo directo
        $broom = "NULL"; // Sin mapeo directo
        $ID_city = "NULL"; // Sin mapeo directo
        $location = $fila['localidad'];
        $address = "NULL"; // Sin mapeo directo
        $mileage = "NULL"; // Sin mapeo directo
        $fuel = "NULL"; // Sin mapeo directo
        $date_car = "NULL"; // Sin mapeo directo
        $premium1 = $fila['premium'];
        $date_premium1 = strtotime($fila['limite_premium']);
        if($date_premium1 == "" OR $date_premium1 == NULL OR $date_premium1 < 0)
            $date_premium1 = "0";
        $premium2 = "0"; // Sin mapeo directo
        $date_premium2 = "0"; // Sin mapeo directo
        $premium2_frecuency = "0"; // Sin mapeo directo
        $premium2_night = "0"; // Sin mapeo directo
        $review = 0; // Sin mapeo directo
        $active = 1; // Valor por defecto
        $notifications = 1; // Valor por defecto
        $trash = 0; // Valor por defecto
        $date_trash = "0"; // Sin mapeo directo
        $motivo = 0;
        $renovate = $fila['renovate'];
        $ID_banner = 0;
        $delay = 0; // Sin mapeo directo
        $changelog = "NULL"; // Sin mapeo directo
        $repeat = 0; // Valor por defecto
        $trash_comment = ""; // Sin mapeo directo
        $premium3 = $fila['listado'];
        $date_premium3 = strtotime($fila['listado_date']); // Sin mapeo directo
        if($date_premium3 == "" OR $date_premium3 == NULL OR $date_premium3 < 0)
            $date_premium3 = "0";
        $ref = $fila['id']; // Sin mapeo directo
        $hor_start = "NULL"; // Sin mapeo directo
        $hor_end = "NULL"; // Sin mapeo directo
        $out = ($fila['salidas'] == 1) ? 1 : 0; 
        $lang1 = 0; // Sin mapeo directo
        $lang2 = 0; // Sin mapeo directo
        $payment = ""; // Sin mapeo directo
        $dis = Core::mapDis($fila['disponibilidad']); // Sin mapeo directo
        $date_edit = 0; // Sin mapeo directo
        $discard = 0; // Valor por defecto
        $renovable = 0; // Valor por defecto
        $renovable_limit = 0; // Sin mapeo directo
        $ID_order = 0; // Valor por defecto

        $ID_region = $reg_map->getID($ID_region);

        if($ID_region == 0)
            continue;


        if($fila['autorenueva'] != 0)
        {
            $renovable = 3;
            $renovable_limit = strtotime($fila['autorenueva_limite']);
        }

        if($fila['enlace'] != "")
            $enlace = addslashes($fila['enlace']);
        else
            $enlace = Core::functionOldUrl($ID_ad, $title);

        $images = array();
        $images[] = $fila['img1'];
        $images[] = $fila['img2'];
        $images[] = $fila['img3'];
        $images[] = $fila['img4'];
        if(VERBOSE)
        {
            echo "ID: $ID_ad, IMGS: <br>";
            print_r($images);
            echo "<br>";
        }

        if(!Core::checkUser($ID_user))
            continue;

        Core::mapImages($images,$ID_ad);

        list($hor_start, $hor_end) = Core::mapHorario($fila['horario']);

        list($parent_cat, $ID_cat) = Core::mapCategoria($fila['idcat']);
        


        // Genera la sentencia SQL de inserción
        echo "INSERT INTO sc_ad (`ID_ad`, `ID_user`, `ad_type`, `seller_type`, `title`, `title_seo`, `name`, `phone`, `whatsapp`, `phone1`, `whatsapp1`, `texto`, `parent_cat`, `ID_cat`, `ID_region`, `date_ad`, `price`, `visit`, `contact_times`, `area`, `room`, `broom`, `ID_city`, `location`, `address`, `mileage`, `fuel`, `date_car`, `premium1`, `date_premium1`, `premium2`, `date_premium2`, `premium2_frecuency`, `premium2_night`, `review`, `active`, `notifications`, `trash`, `date_trash`, `motivo`, `renovate`, `ID_banner`, `delay`, `changelog`, `repeat`, `trash_comment`, `premium3`, `date_premium3`, `premium3_limit`, `ref`, `hor_start`, `hor_end`, `out`, `lang1`, `lang2`, `payment`, `dis`, `date_edit`, `discard`, `renovable`, `renovable_limit`, `ID_order`, `url`) 
            VALUES ('$ID_ad', '$ID_user', '$ad_type', '$seller_type', '$title', '$title_seo', '$name', '$phone', '$whatsapp', '$phone1', '$whatsapp1', '$texto', '$parent_cat', '$ID_cat', '$ID_region', '$date_ad', '$price', '$visit', '$contact_times', '$area', '$room', '$broom', NULL, '$location', '$address', NULL, NULL, NULL, '$premium1', '$date_premium1', '$premium2', '$date_premium2', '$premium2_frecuency', '$premium2_night', '$review', '$active', '$notifications', '$trash', '$date_trash', '$motivo', '$renovate', '$ID_banner', '$delay', NULL, '$repeat', '$trash_comment', '$premium3', '$date_premium3', NULL, '$ref', '$hor_start', '$hor_end', '$out', '$lang1', '$lang2', '$payment', '$dis', '$date_edit', '$discard', '$renovable', '$renovable_limit', 0, '$enlace');\n";

    }
} else {
    echo "No se encontraron registros en bd_anuncio.";
}

$query = ob_get_clean();
echo $query;

if(!VERBOSE)
{

    // Ejecuta la consulta
    try {
        if($bd_target->multi_query($query)){
            do {
                // Verificar si hubo un error en alguna de las consultas
                if ($bd_target->error) {
                    echo "Error en consulta: " . $bd_target->error . "<br>";
                }
            } while ($bd_target->next_result());
            echo "Todas las consultas ejecutadas correctamente.";
        }else{
            echo "Error al ejecutar la consulta: " . $bd_target->error;
        }
    } catch (Exception $e) {
        echo "Error al ejecutar la consulta: " . $e->getMessage();
    }
}