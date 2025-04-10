<?php

include("../../../settings.inc.php");

$facturas = Service::getSolicitudesFactura();
$map = Service::mapFacturas($facturas);
header('Content-Type: application/json');
die(json_encode($map));



