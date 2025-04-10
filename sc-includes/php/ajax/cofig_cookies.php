<?php 

    if(isset($_POST['preferencias']) && $_POST['preferencias'] == true){
        $cofig = array('preferencias' => true);
    }else{
        $cofig = array('preferencias' => false);
    }
    if(isset($_POST['terceros']) && $_POST['terceros'] == true){
        $cofig['terceros'] = true;
    }else{
        $cofig['terceros'] = false;
    }

    print setcookie('CONFIG', json_encode($cofig) , time() + 3600 * 24 * 30 , "/");