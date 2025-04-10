<?php
class User 
{
    static function checkEdits()
    {
        if(User::checkLogin())
        {
            $edit_time = getConfParam('EDIT_TIME');
            $edit_limit = getConfParam('EDIT_LIMIT');
         
            $edit_time = time() - ($edit_time * 3600);
        
            $id_user = $_SESSION['data']['ID_user'];
            $edited_ads = countSQL("sc_ad", $a = array(
                'ID_user' => $id_user,
                'date_edit' => $edit_time .">",
                'trash' => 0
            ));

                return $edited_ads < $edit_limit;

        }
        return false;
    }

    static function newUser($name, $mail, $pass, $rol, $phone, $banner = '', $ip = '')
    {
        $res = insertSQL("sc_user", 
        $d = array('name' => $name, 'mail' => $mail,
        'pass' => $pass, 'rol' => $rol, 'phone' => $phone,
        'date_reg' => time(), 'active' => 1, 'credits' => 0,
        'IP_user' => $ip, 'banner_img' => $banner, 'date_credits' => 0)
    );
        return $res;
    }

    static function checkEmail($mail)
    {
        $user = rawQuerySQL("SELECT * FROM sc_user WHERE mail = '$mail'");
        if(count($user) > 0)
            return true;
        return false;
    }

    static function check_registered($ip, $phone)
    {
        return false;
        $user = rawQuerySQL("SELECT * FROM sc_user WHERE IP_user = '$ip' OR phone = '$phone'");
        if(count($user) > 0)
            return true;
        return false;
    }

    static function resetRenovations()
    {
        if(self::checkLogin())
        {
            $id_user = $_SESSION['data']['ID_user'];
            rawQuerySQL("UPDATE sc_user SET renovations = 0, ren_date = NOW() WHERE ID_user = $id_user AND ren_date < date_format(NOW(), '%Y-%m-%d')");
            return true;
        }
    }
    /**
     * renueva un anuncio
     * @param int $id_ad id de anuncio
     * @param bool $premium si la renovacion es por premium
     * @return int 1 si se renuevo correctamente, 0 si no se pudo renovar, 2 si supero el limite de renovaciones, 3 creditos insuficientes
     */
    static function renovation($id_ad, $premium = false)
    {
        if(self::checkLogin())
        {
            $id_user = $_SESSION['data']['ID_user'];
            if(self::checkRenovation($id_user))
            {
                self::addRenovation($id_user);
                Statistic::addAnuncioRenovado();
                renoveAd($id_ad);
                return 1;
            }else
            {
                if($premium)
                {
                    if(self::deductCredits($id_user, getConfParam('RE_COST')))
                    {
                        self::addRenovation($id_user);
                        renoveAd($id_ad);
                        return 1;
                    }else
                    {
                        return 3;
                    }
                }else
                {
                    if(self::checkRenPremium($id_ad))
                    {
                        renoveAd($id_ad);
                        return 1;
                    }
                    return 2;
                }
            }


        }

        return 0;

    }

    static function checkRenPremium($id_ad)
    {
        $ad = getDataAd($id_ad);
        if($ad['ad']['renovable'] == renovationType::Diario)
        {
            $time = time() - $ad['ad']['date_ad'];
            return ($time > 3600 * 12);
        }

        return false;
    }

    static function getLimitsByRol($rol)
    {
        if($rol == UserRole::Particular)
            return getConfParam('ITEM_LIMIT');
        else if($rol == UserRole::Centro)
            return getConfParam('ITEM_LIMIT_1');
        else if($rol == UserRole::Publicista)
            return getConfParam('ITEM_LIMIT_2');
    }
    
    static function addCredits($id_user, $amount)
    {
        $credits = selectSQL("sc_user", $a = array(
            'ID_user' => $id_user
        ));
        if(count($credits) == 0)
            return false;
        
        $credits = $credits[0]['credits'];
        $date = time() + 3600*24 * getConfParam('CREDITS_EXP');
        try {
            rawQuerySQL("UPDATE sc_user
            SET credits = credits + $amount, date_credits = '$date'
            WHERE ID_user = $id_user");
        
            return true;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
        
    }

    static function deductCredits($id_user, $amount)
    {
        $credits = selectSQL("sc_user", $a = array(
            'ID_user' => $id_user
        ));
        $credits = $credits[0]['credits'];
        if($credits >= $amount)
        {
            rawQuerySQL("UPDATE sc_user
                SET credits = credits - $amount
                WHERE ID_user = $id_user");
            return true;
        }
        return false;
    }

    static function checkRenovation($id_user)
    {
        $renovations = selectSQL("sc_user", $a = array(
            'ID_user' => $id_user
        ));
        $renovations = $renovations[0]['renovations'];
        

        return ($renovations < getConfParam('RE_LIMIT'));
    }

    static function addRenovation($id_user)
    {
        rawQuerySQL("UPDATE sc_user
            SET renovations = renovations + 1
            WHERE ID_user = $id_user");
    }

    static function updateSesion()
    {
        if(self::checkLogin())
        {
            $id_user = $_SESSION['data']['ID_user'];
            $user = selectSQL("sc_user", array("ID_user" => $id_user));
            $_SESSION['data'] = $user[0];
        }
    }

    static function checkLogin()
    {
        if(isset($_SESSION['data']['ID_user']))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    static function checkEvent($id_user)
    {
        $events = self::getEvents($id_user);
        if(count($events) > 0)
        {
            foreach ($events as $key => $value) 
            {
                if($value['status'] == 0)
                {
                    $value['status'] = 1;
                    updateSQL("sc_user_event", $value, $w = array('ID_user_event' => $value['ID_user_event']));
                }
            }
            return true;
        }
        return false;
    }

    static function getEvents($id_user)
    {
        $events = rawQuerySQL("SELECT * FROM `sc_user_event` WHERE date > NOW() - INTERVAL 10 MINUTE AND status = 0 AND ID_user = $id_user");
        return $events;
    }

    static function insertEvent($id_user)
    {
        $d = array('ID_user' => $id_user, "status" => 0);
        insertSQL("sc_user_event", $d);
    }

    static function getUserByIdad($id_ad)
    {
        $user = selectSQL("sc_ad", array("ID_ad" => $id_ad));
        if(count($user) == 0)
            return 0;
        $user = $user[0]['ID_user'];
        return $user;
    }

    static function getUserByID($id)
    {
        $user = selectSQL("sc_user", array("ID_user" => $id));
        if(count($user) == 0)
            return array();
        $user = $user[0];
        return $user;
    }

    static function updateExtras($id_user)
    {
        $user = selectSQL('sc_user', array("ID_user" => $id_user))[0];
        $extras = $user["extras"];
        $limit = $user["extras_limit"];
        if($extras == 0)
            return array(false, $limit);

        $extras--; 
        updateSQL("sc_user", $d = array("extras" => $extras), array("ID_user" => $id_user));
        return array(true, $limit);
    }
    
    static function autoLogin()
    {
        if(isset($_GET['autologin']) && check_login_admin())
        {
            $result = selectSQL("sc_user", $a = array(
                'ID_user' => $_GET['autologin']
            ));
        
            if (count($result) != 0)
            {
        
                $_SESSION['data'] = $result[0];
        
            }
        }
    }

    static function updateFavorites()
    {
        if(checkSession())
        {
            $id_user = $_SESSION['data']['ID_user'];
            $favs = selectSQL("sc_favorites", $a = array(
                'ID_user' => $id_user,
            ));

            $favs = array_map(function($value){
                return $value['ID_ad'];
            }, $favs);

        
            if(count($favs) > 0)
            {
                if (isset($_COOKIE['fav']) && is_array($_COOKIE['fav'])) {
                    foreach ($_COOKIE['fav'] as $index => $value) {
                        // Si el índice actual no está en la lista de verificados, elimínalo
                        if (!in_array($index, $favs)) {
                            setcookie("fav[$index]", "", time() - 3600, "/");
                        }
                    }
                }

                foreach ($favs as $key => $value) {
                    $w = array(
                        'ID_ad' => $value,
                        'active' => 1,
                        'trash' => 0
                    );
                    if(countSQL("sc_ad", $w) == 0)
                    {
                        if(isset($_COOKIE['fav']) && in_array($value, $_COOKIE['fav']))
                            setcookie('fav['.$value.']',"", time()-3600, '/');
                        continue;
                    }
                    setcookie('fav['.$value.']',$value, time()+24*3600*15, '/');
                }
            }else
            {
                self::clearFavorites();
            }

        }
    }

    static function clearFavorites()
    {
        if (isset($_COOKIE['fav']) && is_array($_COOKIE['fav'])) {
            foreach ($_COOKIE['fav'] as $index => $value) {
                setcookie("fav[$index]", "", time() - 3600, "/");
            }
        }
    }



}