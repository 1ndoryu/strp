<?php
include("../../../settings.inc.php");

if(isset($_GET['id']))
{
    $id = $_GET['id'];
    $premium = ($_GET['premium'] == 'true');
    $renovation = User::renovation($id, $premium);
    $r = array("status" => true ,"data" => $renovation);
     die(json_encode($r));
}
die(json_encode(array("status" => false)));