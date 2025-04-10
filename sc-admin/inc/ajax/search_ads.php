<?php 

    include("../../../settings.inc.php");

    if(isset($_GET['s'])){
        $anuns = selectSQL('sc_ad', array(), '' , array('ID_ad' => $_GET['s'], 'name' => $_GET['s'], 'title' => $_GET['s'])); 
        $anuns = array_map( function($val){
            $cat = selectSQL('sc_category', array('ID_cat' => $val['parent_cat']));
            return array('ID_ad' => $val['ID_ad'], 'title' => $val['title'], 'parent_cat' => $val['parent_cat'], 'cat' => $cat[0]['name']);
        } ,$anuns);
        print json_encode($anuns);
    }