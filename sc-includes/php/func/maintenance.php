<?php

class Maintenance
{
    public static function init()
    {
        if(check_login_admin())
            return;


        $mode = getConfParam('MAINTENANCE_MODE');
        if($mode == 1)
        {
            header('Location: mantenimiento.php');
            die();
        }
    }
}