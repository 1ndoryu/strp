<?php

class Statistic
{
    const Visita = 1;
    const ANUNCIO_NUEVO = 2;
    const ANUCNIO_ELIMINADO = 3;
    const USUARIO_NUEVO = 4;
    const USUARIO_ELIMINADO = 5;
    const EMAIL_CONTACTO = 6;
    const PEDIDO_NUEVO = 7;
    const ANUNCIO_RENOVADO = 8;
    const PEDIDO_ELIMINADO = 9;
    const ANUNCIO_RENOVADO_PREMIUM = 10;
    const DESTACADO = 11;
    const AUTORENUEVA = 12;
    const DIARIO = 13;
    const AUTODIARIO = 14;
    const TOP = 15;
    const ANUNCIO_NUEVO_PREMIUM = 16;


    static function initDays()
    {
        $selectSQL = "SELECT COUNT(*) as count FROM sc_statistic WHERE type IN (".self::Visita.", ".self::ANUNCIO_NUEVO.", ".self::ANUCNIO_ELIMINADO.") AND DATE(date) = DATE(NOW())";
        $count = rawQuerySQL($selectSQL)[0]["count"];
        if ($count == 0) 
        {
            insertSQL("sc_statistic", array(
                "type" => self::Visita,
                "valor" => 0
            ));
            insertSQL("sc_statistic", array(
                "type" => self::ANUNCIO_NUEVO,
                "valor" => 0
            ));
            insertSQL("sc_statistic", array(
                "type" => self::ANUCNIO_ELIMINADO,
                "valor" => 0
            ));
            insertSQL("sc_statistic", array(
                "type" => self::USUARIO_NUEVO,
                "valor" => 0
            ));
            insertSQL("sc_statistic", array(
                "type" => self::USUARIO_ELIMINADO,
                "valor" => 0
            ));            
            insertSQL("sc_statistic", array(
                "type" => self::EMAIL_CONTACTO,
                "valor" => 0
            ));
            insertSQL("sc_statistic", array(
                "type" => self::PEDIDO_NUEVO,
                "valor" => 0
            ));
            insertSQL("sc_statistic", array(
                "type" => self::ANUNCIO_RENOVADO,
                "valor" => 0
            ));
            insertSQL("sc_statistic", array(
                "type" => self::PEDIDO_ELIMINADO,
                "valor" => 0
            ));
            insertSQL("sc_statistic", array(
                "type" => self::ANUNCIO_RENOVADO_PREMIUM,
                "valor" => 0
            ));

            insertSQL("sc_statistic", array(
                "type" => self::ANUNCIO_NUEVO_PREMIUM,
                "valor" => 0
            ));


            $count = countSQL("sc_ad", array("premium3" => 1));

            insertSQL("sc_statistic", array(
                "type" => self::DESTACADO,
                "valor" => $count
            ));

            $count = countSQL("sc_ad", array("renovable" => 3));

            insertSQL("sc_statistic", array(
                "type" => self::AUTORENUEVA,
                "valor" => $count
            ));

            $count = countSQL("sc_ad", array("renovable" => 2));

            insertSQL("sc_statistic", array(
                "type" => self::AUTODIARIO,
                "valor" => $count
            ));

            $count = countSQL("sc_ad", array("renovable" => 1));

            insertSQL("sc_statistic", array(
                "type" => self::DIARIO,
                "valor" => $count
            ));

            $count = countSQL("sc_ad", array("premium1" => 1));

            insertSQL("sc_statistic", array(
                "type" => self::TOP,
                "valor" => $count
            ));

            
        }

    }

    static function getStats()
    {
        $stats = array();
        $stats['usuarios'] = countSQL("sc_user");
        
        $last24h = self::getStatsLast24h();

        $stats['publicados'] = array(
            "normal" => $last24h[self::ANUNCIO_NUEVO],
            "premium" => $last24h[self::ANUNCIO_NUEVO_PREMIUM]
        );
        $stats['renovados'] = array(
            "normal" => $last24h[self::ANUNCIO_RENOVADO],
            "premium" => $last24h[self::ANUNCIO_RENOVADO_PREMIUM]
        );

        $stats['destacados'] = array(
            "destacados" => self::getDestacados(),
            "top" => self::getTop(),
            "autorenueva" => self::getAutorenueva(),
            "autodiario" => self::getAutodiario()
        );

        $stats['servicios'] = self::getServiceLast24h();

        $months = self::getLastSixMonths();

        $stats['meses'] = array();
        foreach ($months as $month)
        {
            $data = self::getStatsByMonth($month['yearMonth']);
            if(count($data) == 0)
             {
                $stats['meses'][$month['fullMonthYear']] = array(
                    "nuevos_usuarios" => 0,
                    "pedidos" => 0,
                );
                continue;
             }
            $stats['meses'][$month['fullMonthYear']] = array(
                "nuevos_usuarios" => $data[self::USUARIO_NUEVO],
                "pedidos" => $data[self::PEDIDO_NUEVO],
            );
        }

        $stats['banners'] = self::getStatsBanners();
        $stats['contactos'] = self::getContactMail();
        $stats['facturas'] = self::getFacturas();

        return $stats;
    }

    static function getStatsBanners()
    {
        return countSQL("sc_banners", array("status" => 1));
    }

    static function getContactMail()
    {
        $query = "SELECT SUM(valor) AS total FROM sc_statistic WHERE DATE(date) >= DATE(NOW() - INTERVAL 1 DAY) AND type = ". self::EMAIL_CONTACTO;
        $res = rawQuerySQL($query);
        return $res[0]['total'];
    }

    static function getFacturas()
    {
        $query = "SELECT * FROM sc_facturas WHERE date >= NOW() - INTERVAL 1 DAY";
        $res = rawQuerySQL($query);
        return count($res);
    }

    static function getStatsByMonth($month)
    {
        $query = "SELECT type, SUM(valor) AS total
        FROM sc_statistic
        WHERE DATE_FORMAT(date, '%Y-%m') = '$month'
        GROUP BY type;";

        $res = rawQuerySQL($query);

        $stats = array();
        foreach ($res as $row)
        {
            $stats[$row['type']] = $row['total'];
        }

        return $stats;
    }

    static function getLastSixMonths() {
        // Array de meses en español
        $monthsNames = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
    
        // Crear un array para almacenar los resultados
        $months = [];
        
        // Iterar sobre los últimos 6 meses
        for ($i = 0; $i < 6; $i++) {
            // Crear un objeto DateTime ajustado a cada mes
            $date = new DateTime();
            $date->modify("-$i month");
    
            // Formato YYYY-MM
            $yearMonth = $date->format('Y-m');
            
            // Formato MMMM YYYY usando el array de meses en español
            $fullMonthYear = $monthsNames[(int)$date->format('n') - 1] . ' ' . $date->format('Y');
            
            // Añadir el mes formateado al array
            $months[] = [
                'yearMonth' => $yearMonth,
                'fullMonthYear' => $fullMonthYear
            ];
        }
    
        return $months;
    }
    

    static function getServiceLast24h()
    {
        $arr = array();

        $query = "SELECT ID_service FROM sc_service WHERE date >= NOW() - INTERVAL 1 DAY AND active = 1";
        $res = rawQuerySQL($query);
        $arr['pagados'] = count($res);

        $query = "SELECT ID_pending FROM sc_pending WHERE date >= NOW() - INTERVAL 1 DAY AND status = 1";
        $res = rawQuerySQL($query);
        $arr['pendientes'] = count($res);

        $query = "SELECT SUM(valor) AS total FROM sc_statistic WHERE DATE(date) >= DATE(NOW() - INTERVAL 1 DAY) AND type = ". self::PEDIDO_ELIMINADO;
        $res = rawQuerySQL($query);
        $arr['eliminados'] = $res[0]['total'];
        $arr['fallidos'] = 0;

        return $arr;
    }

    static function getDestacados()
    {
        return countSQL("sc_service", array("active" => 1, "type" => "premium3"));
    }

    static function getTop()
    {
        return countSQL("sc_service", array("active" => 1, "type" => "premium1"));
    }

    static function getAutorenueva()
    {
        return countSQL("sc_service", array("active" => 1, "type" => "autorenueva"));
    }

    static function getAutodiario()
    {
        return countSQL("sc_service", array("active" => 1, "type" => "autodiario"));
    }

    static function getStatsLast24h()
    {
        $query = "SELECT type, valor FROM sc_statistic WHERE DATE(date) = DATE(NOW())";
        $stats = rawQuerySQL($query);
        $arr = array();
        foreach ($stats as $stat)
        {
            $arr[$stat["type"]] = $stat["valor"];
        }
        return $arr;
    }

    static function add($type)
    {
        $query = "UPDATE sc_statistic SET valor = valor + 1 WHERE type = $type AND DATE(date) = DATE(NOW())";
        rawQuerySQL($query);
    }

    static function addVisita()
    {
        self::add(self::Visita);
    }

    static function addAnuncioNuevo()
    {
        self::add(self::ANUNCIO_NUEVO);
    }
    static function addAnuncioNuevoPremium()
    {
        self::add(self::ANUNCIO_NUEVO_PREMIUM);
    }

    static function addAnuncioEliminado()
    {
        self::add(self::ANUCNIO_ELIMINADO);
    }

    static function addUsuarioNuevo()
    {
        self::add(self::USUARIO_NUEVO);
    }

    static function addUsuarioEliminado()
    {
        self::add(self::USUARIO_ELIMINADO);
    }

    static function addEmailContacto()
    {
        self::add(self::EMAIL_CONTACTO);
    }

    static function addPedidoNuevo()
    {
        self::add(self::PEDIDO_NUEVO);
    }

    static function addAnuncioRenovado()
    {
        self::add(self::ANUNCIO_RENOVADO);
    }
    static function addAnuncioRenovadoPremium()
    {
        self::add(self::ANUNCIO_RENOVADO_PREMIUM);
    }

    static function addPedidoEliminado()
    {
        self::add(self::PEDIDO_ELIMINADO);
    }   

    static function getCompleteStats()
    {
        if(isset($_GET['mes']) && isset($_GET['key']))
        {
            switch($_GET['key'])
            {
                default:
                case 0:
                    $stats = array();
                    $stats['renovados'] = self::getHistogram($_GET['mes'], self::ANUNCIO_RENOVADO);
                    $stats['renovados_premium'] = self::getHistogram($_GET['mes'], self::ANUNCIO_RENOVADO_PREMIUM);
                    return $stats;
                    break;
            }
        }

    }

    static function getHistogram($month, $type)
    {
        $date_param = ($month == 0 ? "date > DATE(NOW() - INTERVAL 30 DAY)" : "DATE_FORMAT(date, '%Y-%m') = '$month'");
        $query = "SELECT valor, date FROM sc_statistic WHERE $date_param AND type = $type ORDER BY date DESC";
        $res = rawQuerySQL($query);
        $arr = array();
        foreach ($res as $row)
        {
            $arr[$row['date']] = $row['valor'];
        }
        return $arr;
    }
    

}
