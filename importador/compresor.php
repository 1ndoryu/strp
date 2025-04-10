<?php

$sql = "SELECT * FROM sc_ad WHERE parent_cat = '$cat' ORDER BY date_ad DESC LIMIT $etapa, $limite";

$resultado = $bd_source->query($sql);

foreach ($resultado as $fila) {
    $ID_ad = $fila['ID_ad'];

}