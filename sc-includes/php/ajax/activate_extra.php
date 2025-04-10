<?php

include("../../../settings.inc.php");

if(isset($_GET['id'])){

    $id = $_GET['id'];
    if(!user::checkLogin())
        die(json_encode(array('status' => 0)));
    
    $user = user::getUserByID($_SESSION['data']['ID_user']);
    $extras = $user['extras'];
    $limit = $user['extras_limit'];
    if($extras <= 0)
        die(json_encode(array('status' => 0)));
    
    $extras--;
    updateSQL("sc_user", $d = array('extras' => $extras), $w = array('ID_user' => $_SESSION['data']['ID_user']));
    $d = array('renovable' => renovationType::Diario, 'renovable_limit' => $limit);
    updateSQL("sc_ad", $d, $w = array('ID_ad' => $id));
    
    die(json_encode(array('status' => 1)));

}else 
    echo json_encode(array('status' => 0));


