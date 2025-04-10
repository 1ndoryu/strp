<?php

class Banner
{
    static function catch()
    {

        if(isset($_GET['action']))
        {
            if($_GET['action'] == 'delete_banner')
            {
                if(isset($_GET['p_banner']))
                {
                    $true = deleteSQL('sc_p_banner', $w = array('ID_banner'=>$_GET['banner']));
                }else
                {
                    $true = deleteSQL('sc_banners', $w = array('ID_banner'=>$_GET['banner']));
                }
                if($true)
                    return'Banner eliminado correctamente';
            }

            if($_GET['action'] == 'activate_banner')
            {
                if(isset($_GET['p_banner']))
                    updateSQL('sc_p_banner', $data = array('status'=>1), $w = array('ID_banner'=>$_GET['banner']));
                else
                {
                    $select = selectSQL('sc_banners', $w = array('ID_banner'=>$_GET['banner']));
                    if($select[0]['active_thru'] == 0)
                    {
                        $time = time() + (int) $select[0]['dias'] * 24 * 3600;
                        updateSQL('sc_banners', $data = array('status'=>1, 'active_thru'=>$time), $w = array('ID_banner'=>$_GET['banner']));
                    }else
                        updateSQL('sc_banners', $data = array('status'=>1), $w = array('ID_banner'=>$_GET['banner']));

                }
                return 'Banner activado correctamente';
            }

            if($_GET['action'] == 'deactivate_banner')
            {
                if(isset($_GET['p_banner']))
                    updateSQL('sc_p_banner', $data = array('status'=>0), $w = array('ID_banner'=>$_GET['banner']));
                else
                    updateSQL('sc_banners', $data = array('status'=>0), $w = array('ID_banner'=>$_GET['banner']));
                return 'Banner desactivado correctamente';
            }
        }

        if(isset($_POST['action']))
        {
            if($_POST['action'] == 'extend_banner')
            {
                $select = selectSQL('sc_banners', $w = array('ID_banner'=>$_POST['banner']));
                if(count($select) > 0)
                {
                    $time = $select[0]['active_thru'];
                    $time += $_POST['days'] * 24 * 3600;
                    updateSQL('sc_banners', $data = array('active_thru'=>$time), $w = array('ID_banner'=>$_POST['banner']));
                    return 'Banner extendido correctamente';
                }

            }
        }

        return '';
    }
}