<?php
class Payment
{
    static function getPlans()
    {
        $plans = selectSQL("sc_plans");
        return $plans;
    }

    static function getPlan($id)
    {
        $plan = selectSQL("sc_plans", array("ID_plan" => $id));
        return $plan[0];
    }

    static function parsePlans($plans)
    {
        $data = array();
        $plans = json_decode($plans, true);
        foreach($plans as $key => $value)
        {
            $data[] = self::getPlan($value);
        }
        return $data;
    }

    static function getTotal($plans)
    {
        $total = 0;
        foreach($plans as $key => $value)
        {
            if(gettype($value) == "string")
                $plan = Payment::getPlan($value);
            else
                $plan = $value;
            $total += $plan['price'];
        }
        return $total;
    }

    static function getPackages()
    {
        $select = selectSQL('sc_package');
        return $select;
    }

    static function getDataPlans()
    { 
        $plans = self::getPlans();
        $data = array();
        foreach($plans as $key => $plan)
        {
           if(!isset($data[$plan['plan']]))
           {
                $data[$plan['plan']]  = array(
                    'name' => $plan['name'],
                    'comment' => $plan['comment'],
                    'posted' => $plan['posted'],
                    'counter' => $plan['counter'],
                );
                $data[$plan['plan']]['days'] = array(
                    $plan['days'] => $plan['price']
                );
           }else
           {
                $data[$plan['plan']]['days'][$plan['days']] = $plan['price'];
           }
        }
        return $data;
    }

    static function selectPlans()
    {
        $planes = array();
        $monto = 0;
        if(count($_GET) > 0)
        {
            foreach($_GET as $key => $value)
            {
                $sql = "SELECT * FROM sc_plans WHERE plan = '$key' AND days = '$value'";
                $res = rawQuerySQL($sql);
                if($res !== null)
                {
                    $planes[] = $res[0];
                    $monto += $res[0]['price'];
                }
            }
        }

        return array($planes, $monto);
    }

    static function aplyDiscount($amount)
    {
        if($amount > 100)
        {
            $amount -= $amount * 0.2;
            return $amount;
        }
        if($amount > 300)
        {
            $amount -= $amount * 0.3;
            return $amount;
        }

        return $amount;
    }

    static function getDiscount($amount)
    {
        if($amount > 100)
        {
            $amount -= $amount * 0.2;
            return array($amount, 0.2);
        }
        if($amount > 300)
        {
            $amount -= $amount * 0.3;
            return array($amount, 0.3);
        }

        return array($amount, 0);
    }

    static function buyExtraAds()
    {
        if(isset($_POST['metodo']) && isset($_POST['p']) && checkSession())
        {
            $metodo = $_POST['metodo'];
            $p = $_POST['p'];
            $id_user = $_SESSION['data']['ID_user'];
            $limit = time() + 3600 * 24 * 32;
            switch ($metodo) {
                case 'creditos':
                    $amount = self::getPackagePrice($p);
                    if($amount == 0)
                        return Payment::return(false, "No existe ese paquete");
                    $amount = Payment::aplyDiscount($amount);
                    if(!Payment::removeCredits($id_user, $amount))
                        return Payment::return(false, "No tienes suficientes créditos");
                    else
                    {
                        $d = array("extras" => $p);
                        $d['extras_limit'] = $limit;
                        updateSQL("sc_user", $d, array("ID_user" => $id_user));
                        return Payment::return(true, "Paquete activado correctamente");
                    }
                break;
                case 'paypal':
                    $d = array("extras" => $p, "extras_limit" => $limit);
                    updateSQL("sc_user", $d, array("ID_user" => $id_user));
                    return Payment::return(true, "Paquete activado correctamente");
                break;

            }
        }
    }

    static function getPackagePrice($p)
    {
        $select = selectSQL("sc_package", array("value" => $p));
        if(count($select) > 0)
            return $select[0]['price'];
        return 0;
    }
    static function processPayment()
    {
        global $con;
        if(isset($_POST['metodo']) && isset($_POST['monto']) && isset($_POST['idad']) && isset($_POST['planes']) && checkSession())
        {
            $metodo = $_POST['metodo'];
            $monto = $_POST['monto'];
            $idad = $_POST['idad'];
            $planes = $_POST['planes'];

            switch ($metodo) {
                case 'paypal':
                        if(Payment::completePayment($idad, $planes, "paypal"))
                        {
                            if(is_array($idad))
                            {
                                Tickets::insertTicket(0, $idad[0], "", count($idad), $monto, $_POST['paypalID'] , $planes);
                                $idad = $idad[0];
                            }
                            else
                                Tickets::insertTicket(0, $idad, "", 1, $monto, $_POST['paypalID'] , $planes);

                            
                            if(count($planes) > 1)
                                orderMail("Pagado", $_POST['paypalID'], "varios servicios", $monto, $metodo, 0, $idad);
                            else
                                orderMail("Pagado", $_POST['paypalID'], $planes[0], $monto, $metodo, 0, $idad);

                            return Payment::return(true, "Servicios activados correctamente");
                        
                        }
                        else
                            return Payment::return(false);
                    break;
                case 'creditos':
                    $amount = Payment::getTotal($planes);
                    $id_user = $_SESSION['data']['ID_user'];
                    if(is_array($idad))
                        $amount = $amount * count($idad);

                    $amount = Payment::aplyDiscount($amount);
                    if(!Payment::removeCredits($id_user, $amount))
                        return Payment::return(false, "No tienes suficientes créditos");

                    if(Payment::completePayment($idad, $planes))
                    {
                        if(is_array($idad))
                            Tickets::insertTicket(0, $idad[0], "", count($idad), $monto, "acreditada" , $planes);
                        else
                            Tickets::insertTicket(0, $idad, "", 1, $monto, "acreditada" , $planes);
                        
                        
                        if(count($planes) > 1)
                            orderMail("Pagado", "no aplica", "varios servicios", $monto, $metodo, $id_user);
                        else
                            orderMail("Pagado", "no aplica", $planes[0], $monto, $metodo, $id_user);
                        
                        return Payment::return(true, "Servicios activados correctamente");
                    }
                    else
                        return Payment::return(false);
                    break;
              
            }
        }
        
    }

    static function return($r = true, $msg = "")
    {
        if($r)
            return array("status" => "success", "msg" => $msg);
        else
            return array("status" => "error",  "msg" => $msg);
    }

    static function completePayment($idad, $planes, $method = "créditos")
    {
        global $con;
        $return = true;
        $masivo = is_array($idad);
        $data = array();

        foreach($planes as $key => $value)
        {
            if(gettype($value) == "string")
            {
                $plan = Payment::getPlan($value);
                $data[] = $value;
            }
            else
            {
                $plan = $value;
                $data[] = $value['ID_plan'];
            }

            if($masivo)
            {
                foreach($idad as $id)
                {
                    $return = $return && Service::setPlan($id, $plan, $method);
                }
            }else
                $return = $return && Service::setPlan($idad, $plan, $method);
        }
        if($return)
        {
            if($masivo)
            {
                foreach($idad as $id)
                {
                    updateSQL("sc_ad", array("ID_order" => "0"), array("ID_ad" => $id));
                }
            }else
            {
                updateSQL("sc_ad", array("ID_order" => "0"), array("ID_ad" => $idad));
            }
        }
        
        return $return;
    }

    static function removeCredits($id_user, $amount)
    {
        $user = selectSQL('sc_user', array('ID_user' => $id_user));
        if(count($user) > 0)
        {
            $credits = $user[0]['credits'];
            if($credits >= $amount)
            {
                $credits -= $amount;
                updateSQL('sc_user', array('credits' => $credits), array('ID_user' => $id_user));
                return true;
            }
        }

        return false;
    }

    function savePedido()
    {
        if(isset($_POST['monto']) && isset($_POST['planes']))
        {
            $monto = $_POST['monto'];
            $planes = $_POST['planes'];
            $_m = 0;
            foreach($planes as $key => $value)
            {
                $plan = Payment::getPlan($value);
                $_m += $plan['precio'];
            }
            if($monto != $_m)
                return array("status" => "error", "msg" => "Monto no coincide con los planes de pago");
            
            if(checkSession())
                $id_user = $_SESSION['data']['ID_user'];
            else if(isset($_POST['email']))
            {
                $user = selectSQL("sc_user", array("mail" => $_POST['email']));
                if(count($user) > 0)
                {
                    $id_user = $user[0]['ID_user'];
                }else
                    return array("status" => "error", "msg" => "No existe el usuario con esa dirección de correo");
            }else
                return array("status" => "error", "msg" => "No tienes sesión");
            $orderID = time();
            $datos = json_encode($planes);
            if(insertSQL("sc_pedidos", array("pedido" => $orderID, "ID_user" => $id_user, "price" => $monto, "data" => $datos, "status" => 0)))
            {
                $pedido = lastIdSQL();
                return array("status" => "success", "msg" => "Pedido creado correctamente", "id" => $pedido);
            }else
                return array("status" => "error", "msg" => "Error al crear el pedido");
            
            
        }
    }

    static function buyCredits()
    {
        if(isset($_POST['metodo']) && isset($_POST['cantidad']) && checkSession())
        {
            $metodo = $_POST['metodo'];
            $cantidad = $_POST['cantidad'];
            $id_user = $_SESSION['data']['ID_user'];
            switch ($metodo) {
                case 'paypal':
                    if(User::addCredits($id_user, $cantidad))
                    {
                        $paypalID = $_POST['paypalID'];
                        Tickets::insertTicket($id_user, 0, "Compra de créditos", $cantidad, $cantidad * getConfParam('CREDIT_PRICE'), $paypalID);
                        return array("status" => "success", "msg" => "Se ha añadido correctamente");
                    }
                    else
                        return array("status" => "error", "msg" => "Error al añadir créditos");
                    break;
                    
                case 'bizum':
                case 'transferencia':
                    $id_order = Orders::newOrder("", $id_user);
                    $number = substr(time(), -6);
                    $pending = array(
                        "ID_order" => $id_order,
                        "method" => $metodo,
                        "amount" => $cantidad * getConfParam('CREDIT_PRICE'),
                        "status" => 0,
                        "num" => $number,
                    );
                    if(insertSQL("sc_pending", $pending))
                    {
                        $pending = lastIdSQL();
                        return array("status" => "pending", "msg" => "Se ha añadido correctamente", "number" => $number, "pending" => $pending);
                    }
                    return array("status" => "error", "msg" => "Error al añadir créditos");
                    break;
            }
        }else
        {
            return array("status" => "error", "msg" => "No tienes sesión");
        }
    }
}