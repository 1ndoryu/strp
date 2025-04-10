<?php

class Users
{
    public static function catch()
    {
    
        if(isset($_POST['BAN_ID']))
        {
            $w = array("ID_user" => $_POST['BAN_ID']);

            
            if($_POST['BAN_ACTION'] == "1")
            {
                $bloqueo = 0;
                $active = 1;
                $date = null;
            }
            else
            {
                $bloqueo =  $_POST['bloqueo'];
                $active = 0;
                $date = "NOW";
            }

            
            $d = array("bloqueo" => $bloqueo, "active" => $active, "bloqueo_date" => $date);

            updateSQL("sc_user", $d, $w);

            if($bloqueo == 0)
                return self::return("Usuario desbloqueado");
            else
                return self::return("Usuario bloqueado");
        }

        if(isset($_POST['add-credit-id']))
        {
            if(User::addCredits($_POST['add-credit-id'], $_POST['credit']))
                return self::return("Creditos agregados");
            else
                return self::return(false, "Error al agregar creditos");
        }

        if(isset($_POST['newuser']))
        {
            if(isset($_POST['nombre']) && isset($_POST['mail']) && isset($_POST['pass']) && isset($_POST['rol']) && isset($_POST['phone']))
            {
                if(User::checkEmail($_POST['mail']))
                    return self::return(false, "El mail ya esta en uso");

                if(User::newUser($_POST['nombre'], $_POST['mail'], $_POST['pass'], $_POST['rol'], $_POST['phone']))
                    return self::return("Usuario creado");
                else
                    return self::return(false, "Error al crear usuario");
            }else
                return self::return(false, "Datos incompletos");
        }
        
        return self::return();
    }

    private static function return($succ = false, $err = false)
    {
        $return = array($succ, $err);
        return $return;
    }

    
}
