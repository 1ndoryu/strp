<?php 
class Service
{
	static function getOption($service, $name)
    {
        $w = array(
            "name" => $name,
            "service" => $service
        );
        $rows = selectSQL("sc_service_options", $w);

        if (count($rows) > 0) {
            return $rows[0]['value'];
        }

        return null;
    }

    static function setOption($service, $name, $value)
    {
        $w = array(
            "name" => $name,
            "service" => $service
        );
        $d = array(
            "value" => $value
        );
        $r = updateSQL("sc_service_options", $d, $w);
        return $r;
    }

    static function getServices( $id_user = null, $start = 0, $perpage = 20 )
    {
        $w = $services = array();
        $query = "";

        if(isset($_GET['q']) && $_GET['q'] != "" && isset($_GET['field']))
        {
            switch ($_GET['field']) {
                case 'mail':
                    $query = "SELECT sc_service.*
                            FROM sc_service
                            JOIN sc_user ON sc_service.ID_user = sc_user.ID_user
                            WHERE sc_user.mail LIKE '%".trim($_GET['q'])."%';";
                    break;
                case 'ref':
                    $query = "SELECT sc_service.*
                            FROM sc_service
                            JOIN sc_ad ON sc_service.ID_ad = sc_ad.ID_ad
                            WHERE sc_ad.ref = ".trim($_GET['q']);
                    break;
                default:
                    # code...
                    break;
            }
        }
        
        if( $id_user != null )
            $w['ID_user'] = $id_user;

        if($query == "")
            $rows = selectSQL("sc_service", $w, "date DESC LIMIT $start, $perpage");
        else
            $rows = rawQuerySQL($query);

        foreach ($rows as $row) {

            $cat = selectSQL("sc_category", $w = array(
                'ID_cat' => $row['ID_cat']
            ));
            $user = selectSQL("sc_user", $w = array(
                'ID_user' => $row['ID_user']
            ));
            $ad = selectSQL("sc_ad", $w = array(
                'ID_ad' => $row['ID_ad']
            ));

            $expired = ($row['expire'] < time());

            if($expired && $row['active'] == 1 && $row['type'] != "premium2")
            {
                Service::inactive($row['ID_service']);
                $row['active'] = 0;
            }
               

            $services[] = array(
                "service" => $row,
                "category" => $cat[0],
                "user" => $user[0],
                "ad" => $ad[0]
            );
            
        }

        return $services;
 
    }

    static function inactive($id_service)
    {
        $d = array(
            "active" => 0
        );
        $r = updateSQL("sc_service", $d, $w = array('ID_service' => $id_service));
        return $r;
    }
    static function inactiveByAd($id_ad, $type)
    {
        $d = array(
            "active" => 0
        );
        $w = array(
            'ID_ad' => $id_ad,
            'active' => 1,
            'type' => $type
            );
        $r = updateSQL("sc_service", $d, $w);
        return $r;
    }
    static function inactiveByBanner($id_banner)
    {
        $d = array(
            "active" => 0
        );
        $w = array(
            'ID_banner' => $id_banner,
             'active' => 1,
             'type' => 'banner'
            );
        $r = updateSQL("sc_service", $d, $w);
        return $r;
    }


    static function insertService($ad, $type, $method = "créditos", $expire = 0, $banner = 0)
    {
        $ad = getDataAd($ad);
        $d = array(
            "ID_ad" => $ad['ad']['ID_ad'],
            "ID_cat" => $ad['ad']['parent_cat'],
            "ID_user" => $ad['ad']['ID_user'],
            "ID_banner" => $banner,
            "expire" => $expire,
            "date" => "NOW",
            "type" => $type,
            "method" => $method
        );
        $r = insertSQL("sc_service", $d);

        // $msg = "El anuncio de ".'"'. $ad['ad']['title'] .'"' . " ha sido ";

        // switch ($type) {
        //     case "premium1":
        //         $msg .= "destacado TOP";
        //         break;
        //     case "premium2":
        //         $msg .= "subir a listado";
        //         break;
        //     case "premium3":
        //         $msg .= "Destacado";
        //         break;
        //     case "banner":
        //         $msg = "Su banner ha sido activado";
        //         break;
        // }

        $msg = "Nuevo servicio";

        Notice::addNotice($ad['ad']['ID_user'],"Nueva notificación" ,  $msg, urlAd($ad['ad']['ID_ad']), null);
        return $r;
    }

    static function premium1($ad, $duration, $method = "créditos")
    {
        $time = time() + $duration;
        $d = array(
            "premium1" => 1,
            "date_premium1" => $time,
            "active" => adStatus::Active
        );
        $r1 = updateSQL("sc_ad", $d, $w = array('ID_ad' => $ad));
        $r2 = self::insertService($ad, "premium1", $method, $time);


        return ($r1 && $r2);
    }

    static function premium2($ad, $frec, $night)
    {
        $time = time();
        $d = array(
            "premium2" => 1,
            "date_premium2" => $time,
            "date_ad" => $time,
            "premium2_frecuency" => $frec,
            "premium2_night" => $night,
            "renovate" => 1
        );
        $r1 = updateSQL("sc_ad", $d, $w = array('ID_ad' => $ad));
        $r2 = self::insertService($ad, "premium2", "créditos", 0);

        return ($r1 && $r2);
    }

    static function premium3($ad, $duration, $method = "créditos")
    {
        $time = time() + $duration;
        $d = array(
            "premium3" => 1,
            "date_premium3" => $time,
            "active" => adStatus::Active
        );
        $r1 = updateSQL("sc_ad", $d, $w = array('ID_ad' => $ad));
        $r2 = self::insertService($ad, "premium3", $method , $time);

        return ($r1 && $r2);
    }

    static function renovation($ad)
    {
        Statistic::addAnuncioRenovadoPremium();
        return renoveAd($ad);
    }

    static function TYPE($type)
    {
        $tipes = array(
            "premium1" => "Top anuncio",
            "premium2" => "Listado",
            "premium3" => "Destacado",
            "banner" => "Banner",
            "diario" => "Diario",
            "autodiario" => "Autodiario",
            "autorenueva" => "Autorenueva",
            "top" => "Top",
            "destacado" => "Destacado",
        );

        if(isset($tipes[$type]))
            return $tipes[$type];

        return "Tipo desconocido";
    }

    static function getSolicitudesFactura()
    {
        $facturas = selectSQL("sc_facturas");
        return $facturas;
    }

    static function mapFacturas($facturas)
    {
        $map = array(
            "data" => array(),
        );
        foreach ($facturas as $factura) {
            $fac = array(
                $factura['ID_factura'],
                $factura['name'],
                $factura['dni'],
                $factura['address'],
                $factura['zip'],
                $factura['zone'],
                $factura['province'],
                $factura['email'],
                parseDate($factura['date'], 'd/m/Y'),
                $factura['status']
            );

            $tickets = countSQL("sc_tickets", $w = array('ID_factura' => $factura['ID_factura']));
            $fac[] = $tickets;
            $map['data'][] = $fac;
        }
        return $map;
    }

    static function solicitudFactura()
    {
        if(isset($_POST['email']))
        {
            $return = getCaptcha($_POST['g-recaptcha-response']);
            if($return->success == true && $return->score > 0.5){
                insertSQL("sc_facturas", $d = array(
                    "ID_user" => $_SESSION['data']['ID_user'],
                    "email" => $_POST['email'],
                    "name" => $_POST['name'],
                    "dni" => $_POST['dni'],
                    "address" => $_POST['address'],
                    "zip" => $_POST['postal'],
                    "zone" => $_POST['zone'],
                    "province" => $_POST['province'],
                    "date" => "NOW",
                    "status" => $_POST['status']
                ));
                $template = loadTemplate('solicitu_factura', $_POST);
                SendMailToAdmin('Solicitud de Factura', $template, $_POST['email'], $_POST['name']);
                notifyEmail('Solicitud de Factura', $template);
                return true;
            }
        }
        return false;
    }

    static function setPlan($id, $plan, $method)
    {
        Statistic::addPedidoNuevo();
        $duration = self::DAYS($plan['days']);
        switch ($plan['plan'])
        {
            case 'diario':
                return self::diario($id, $duration, $method);
            case 'autodiario':
                return self::autodiario($id, $duration, $method);
            case 'autorenueva':
                return self::autorenueva($id, $duration, $method);
            case 'top':
                return self::premium1($id, $duration, $method);
            case 'destacado':
                return self::premium3($id, $duration, $method);
            case 'renovacion':
                return self::renovation($id);
        }

    }

    static function diario($ad, $duration, $method = "créditos")
    {
        $time = time() + $duration;
        $d = array(
            "renovable" => renovationType::Diario,
            "renovable_limit" => $time,
            "active" => adStatus::Active
        );
        $r1 = updateSQL("sc_ad", $d, $w = array('ID_ad' => $ad));
        self::insertService($ad, "diario", $method, $time);
        return $r1;
    }

    static function autodiario($ad, $duration, $method = "créditos")
    {
        $time = time() + $duration;
        $d = array(
            "renovable" => renovationType::Autodiario,
            "renovable_limit" => $time,
            "active" => adStatus::Active
        );
        $r1 = updateSQL("sc_ad", $d, $w = array('ID_ad' => $ad));
        self::insertService($ad, "autodiario", $method, $time);
        return $r1;
    }

    static function autorenueva($ad, $duration, $method = "créditos")
    {
        $time = time() + $duration;
        $d = array(
            "renovable" => renovationType::Autorenueva,
            "renovable_limit" => $time,
            "active" => adStatus::Active
        );
        $r1 = updateSQL("sc_ad", $d, $w = array('ID_ad' => $ad));
        self::insertService($ad, "autorenueva", $method, $time);
        return $r1;
    }

    static function DAYS($days)
    {
        return ($days * 24 * 3600);
    }

    
}