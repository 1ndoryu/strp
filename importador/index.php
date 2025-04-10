<?php
include_once("./db.php");
include_once("./core.php");

error_reporting(E_ALL | E_STRICT);
if(isset($_GET['verbose']))
{
    ini_set('display_errors','on');
    define('VERBOSE', true);
}else
{
    ini_set('display_errors','off');
    define('VERBOSE', false);
}

//parametros
$limite = 10;
if(isset($_GET['limite']))
{
    $limite = $_GET['limite'];
}

if(isset($_GET['cat']))
    $cat = $_GET['cat'];

if(isset($_GET['etapa']))
{
    $etapa = ($_GET['etapa'] * $limite);
}else
    $etapa = 0;

$tipo = "anuncios";
if(isset($_GET['tipo']))
{
    $tipo = $_GET['tipo'];
}

include "./$tipo.php";


// Cierra la conexión
$bd_source->close();
$bd_target->close();