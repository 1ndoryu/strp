<?php 

if(!isset($_SESSION['data'])){
    if(isset($_COOKIE['PMSESSION'])){
        if($_COOKIE['PMSESSION'] != 'null'){     
          infraLogin($_COOKIE['PMSESSION']);
        }
    }
}

if(!isset($_COOKIE['CONFIG'])){
    setcookie('CONFIG', json_encode(array('preferencias' => true, 'terceros' => true)) , time() + 3600 * 24 * 30 , "/");
}
 