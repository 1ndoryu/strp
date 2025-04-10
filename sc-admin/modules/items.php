<?php 
class Items
{
    public static function catch()
    {
        if(isset($_GET['veren'])){
            $ad = getDataAd($_GET['veren']);
            $cat = $ad['ad']['parent_cat'];
            $date = $ad['ad']['date_ad'];
            $rows = rawQuerySQL("SELECT ID_ad  FROM sc_ad WHERE date_ad>='$date' AND active=1 AND review!=1 AND parent_cat = '$cat' AND trash = 0");
            $num = count($rows);
            $pages = ceil($num / getConfParam('ITEM_PER_PAGE'));
            $url = getConfParam('SITE_URL') . $ad['parent_cat']['name_seo'] . "/?pag=$pages";
            header("Location: $url");
            exit;
        }

        if(isset($_GET['autorenueva_id']))
        {
            $duration = Service::DAYS($_GET['tiempo']);
            Service::autorenueva($_GET['autorenueva_id'], $duration);
            return "Anuncio subido autorenueva";
        }

        if(isset($_GET['del_autorenueva']))
        {
            $id = $_GET['del_autorenueva'];
            updateSQL("sc_ad", array('renovable' => 0, 'renovable_limit' => 0), array('ID_ad' => $id));
            return "Autorenueva eliminado";
        }

        if(isset($_GET['destacar_ad']))
        {
            Service::premium3($_GET['destacar_ad'], $_GET['tiempo']);
            return "Anuncio destacado";
        }

        if(isset($_GET['del_destacar']))
        {
            $id = $_GET['del_destacar'];
            updateSQL("sc_ad", array('date_premium3' => null, 'premium3' => 0), array('ID_ad' => $id));
            Service::inactiveByAd($id, 'premium3');
            return "Anuncio destacado eliminado";
        }

        if(isset($_GET['descartar']))
        {
            $id = $_GET['descartar'];
            $ad_del = array('trash' => 0);
            $ad_del['delay'] = 0;
            $ad_del['review'] = 0;
            $ad_del['changelog'] = null;
            $ad_del['discard'] = 1;
            Images::discardChanges($id);
            updateSQL('sc_ad', $ad_del, $w = array('ID_ad' => $id));
            return "Cambios descartados";
        }

        if(isset($_POST['discard']))
        {
            $id = $_POST['discard'];
            Images::discardChanges($id);
            $ad_del = array('trash' => 0);
            $ad_del['delay'] = 0;
            $ad_del['review'] = 0;
            $ad_del['changelog'] = null;
            $ad_del['discard'] = 1;
            $ad_del['trash_comment'] = $_POST['comment'];
            updateSQL('sc_ad', $ad_del, $w = array('ID_ad' => $id));
            return "Cambios descartados";
        }

        if(isset($_POST['desactivar']))
        {
            $id = $_POST['desactivar'];
            $ad_del = array('trash' => 1);
            $ad_del['motivo'] = Motivo::Desactivado;
            $ad_del['date_trash'] = time();
            $ad_del['trash_comment'] = $_POST['comment'];
            $ad_del['changelog'] = null;
            updateSQL('sc_ad', $ad_del, $w = array('ID_ad' => $id));
            return "Anuncio desactivado";
        }

        if(isset($_POST['idad-extend']) && isset($_POST['time']) && isset($_POST['service']))
        {
            $time = $_POST['time'] * 24 * 3600;
            $ad = getDataAd($_POST['idad-extend']);
            $service = $_POST['service'];
            switch ($service) {
                case 'Top':
                    $time = $ad['ad']['date_premium1'] + $time;
                    updateSQL("sc_ad", array('date_premium1' => $time), array('ID_ad' => $_POST['idad-extend']));
                    return "Top extendido";
                    break;
                case 'Destacado':
                    $time = $ad['ad']['date_premium3'] + $time;
                    updateSQL("sc_ad", array('date_premium3' => $time), array('ID_ad' => $_POST['idad-extend']));
                    return "Destacado extendido";
                    break;
                case 'Autorenueva':
                    $time = $ad['ad']['renovable_limit'] + $time;
                    updateSQL("sc_ad", array('renovable_limit' => $time), array('ID_ad' => $_POST['idad-extend']));
                    return "Autorenueva extendido";
                    break;
                
                default:
                    # code...
                    break;
            }
        }

        return false;
    }
}