<?php
$sql = "SELECT * FROM bd_usuario";
$resultado = $bd_source->query($sql);

ob_start();

// Verifica si hay datos
if ($resultado->num_rows > 0) {
    // Genera las sentencias de inserción
    while ($fila = $resultado->fetch_assoc()) {
        $id = $fila['id'];
        $nombre = $fila['nombre'];
        $email = $fila['email'];
        $password = $fila['password'];
        $ip = $fila['ip'];
        $rol = $fila['rol'];
        $sesion = $fila['sesion'];
        $bloqueoip = $fila['bloqueoip'];
        $bloqueoemail = $fila['bloqueoemail'];
        $bloqueotelf = $fila['bloqueotelf'];
        $motivo = $fila['motivo'];
        $fecha = $fila['fecha'];
        $fecha_publicacion = $fila['fecha_publicacion'];
        $creditos = $fila['creditos'];
        $limites = $fila['limites'];
        $enviado = $fila['enviado'];
        $renovaciones = $fila['renovaciones'];
        $fecha_renovacion = $fila['fecha_renovacion'];
        $ediciones = $fila['ediciones'];
        $extras = $fila['extras'];
        // Aquí puedes usar las variables mapeadas como necesites
        $query = "SELECT telefono FROM bd_anuncio WHERE idusuario = '$id' AND motivo = 0 AND publicado = 1";
        $resultado2 = $bd_source->query($query);
        // "fecha = $fecha\n";
        //echo "anuncios: $resultado2->num_rows\n";
        if($fecha == "0000-00-00 00:00:00" || $fecha == "")
            $fecha = 0;
        $fecha = strtotime($fecha);
        if($fecha < 0)
            $fecha = 0;
        $time = time();
        $fecha_publicacion = strtotime($fecha_publicacion);
        if ($resultado2->num_rows > 0) {
            $telefono = $resultado2->fetch_assoc()['telefono'];
            echo "INSERT INTO `sc_user`(`ID_user`, `name`, `pass`, `mail`, `phone`, `banner_img`, `date_reg`, `credits`, `credits_last`, `active`, `IP_user`, `sesion`, `bloqueo`, `bloqueo_date`, `notify`, `rol`, `confirm`, `renovations`, `ren_date`, `date_credits`, `anun_limit`, `extras`) VALUES ('$id','$nombre','$password','$email','$telefono','','$fecha','$creditos','0','1','$ip','0','0',NULL,'1','1', NULL,'0','0000-00-00]',$time,'1','0');\n";
        }
    }
} else {
    echo "No se encontraron registros en sc_user.";
}


$query = ob_get_clean();
echo $query;

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
