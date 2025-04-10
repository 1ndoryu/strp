<?php
    include("../../../settings.inc.php");
    if(isset($_GET['mail']))
    {
        $mail = $_GET['mail'];
        $user = selectSQL("sc_user", array("mail" => $mail));
        if(count($user) > 0)
        {
            $user = $user[0];
            if(check_item_limit($user['ID_user']))
                die(json_encode(array('status' => 1)));;
        }
    }

    die(json_encode(array('status' => 0)));