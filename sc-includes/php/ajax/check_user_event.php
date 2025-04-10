<?php
    include("../../../settings.inc.php");
    $r = array("status" => 0);
    if(isset($_GET['id']))
    {
        if(user::checkEvent($_GET['id']))
            $r = array("status" => 1);
    }

    die(json_encode($r));