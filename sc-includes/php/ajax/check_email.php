<?php

include("../../../settings.inc.php");

if(isset($_GET['mail'])){

    $mail = $_GET['mail'];
    if(checkRegisteredEmail($mail))
        die(json_encode(array('status' => 0)));
    else
        die(json_encode(array('status' => 1)));
   

}else 
    echo json_encode(array('status' => 0));


