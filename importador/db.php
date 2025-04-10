<?php 

// Conexión a la base de datos
$host = 'localhost';
$user = 'phpmyadmin';
$password = '1234';
$dbname = 'solomasajistas';

$bd_source = new mysqli($host, $user, $password, $dbname);

$host = 'localhost';
$user = 'phpmyadmin';
$password = '1234';
$dbname = 'solomasajistas_p2';

$bd_target = new mysqli($host, $user, $password, $dbname);

// Verifica la conexión
if ($bd_source->connect_error) {
    die("Error de conexión: " . $bd_source->connect_error);
}

if ($bd_target->connect_error) {
    die("Error de conexión: " . $bd_target->connect_error);
}