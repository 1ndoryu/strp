<?php 
    include ("../../../settings.inc.php");

    if(isset($_POST['ID']) && isset($_POST['pass']) && isset($_POST['mail'])){
        
        $datos = array();

        if($_POST['pass'] != ""){
            $datos['pass'] = $_POST['pass'];
        }
        if($_POST['mail'] != ""){
            $datos['mail'] = $_POST['mail'];
        }

        if(isset($_POST['nombre']) && $_POST['nombre'] != ""){
            $datos['name'] = $_POST['nombre'];
        }

        if(isset($_POST['credits']) && $_POST['credits'] != "")
            $datos['credits'] = $_POST['credits'];

        if(isset($_POST['rol']) && $_POST['rol'] != "")
            $datos['rol'] = $_POST['rol'];
        if(isset($_POST['limit']) && $_POST['limit'] != "")
            $datos['anun_limit'] = $_POST['limit'];

        print updateSQL('sc_user', $datos, $w = array('ID_user' => $_POST['ID']));

    }
