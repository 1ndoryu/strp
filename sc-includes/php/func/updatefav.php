<?php 

    if(isset($_SESSION['data'])){
        $favs = selectSQL('sc_favorites', array('ID_user' => $_SESSION['data']['ID_user']));
        $cfavs = $_COOKIE['fav'];

        if(!isset($_COOKIE['fav']) && count($favs) >0)
        {
            $_COOKIE['fav'] = array();
        }
        foreach ($favs as $fav) {
            if(!isset($cfavs[$fav['ID_ad']]))
            {
                setcookie('fav['.$fav['ID_ad'].']',$fav['ID_ad'], time()+24*3600*15, '/');
                $_COOKIE['fav'][$fav['ID_ad']] = $fav['ID_ad'];
            }
        }
    }