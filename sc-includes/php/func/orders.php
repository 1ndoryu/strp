<?php 
class Orders
{
    static function completeOrderAjax()
    {
        if(isset($_POST['pedido']) && isset($_POST['metodo']) && isset($_POST['idad']) && checkSession())
        {
            $id_order = $_POST['pedido'];
            $method = $_POST['metodo'];
            $idad = $_POST['idad'];
            $order = Orders::getOrderByID($id_order);
            if(count($order) == 0)
                return array("status" => "error", "msg" => "No existe el pedido");
            if($order['status'] != 0)
                return array("status" => "error", "msg" => "El pedido ya ha sido completado");
            if($idad != $order['ID_ad'])
                return array("status" => "error", "msg" => "El pedido no pertenece a tu anuncio");

            switch ($method) {
                case 'paypal':
                        if(Payment::completePayment($idad, $order['plans'], "paypal"))
                        {
                            self::completeOrder($id_order);
                            return array("status" => "success", "msg" => "Pago exitoso");
                        }
                        else
                            return array("status" => "error", "msg" => "Error al completar el pago");
                    break;
                case 'creditos':
                    $amount = Payment::getTotal($order['plans']);
                    $amount = Payment::aplyDiscount($amount);
                    $id_user = $_SESSION['data']['ID_user'];
                    if(!Payment::removeCredits($id_user, $amount))
                        return array("status" => "error", "msg" => "No tienes suficientes créditos");

                    if(Payment::completePayment($idad, $order['plans']))
                    {
                        self::completeOrder($id_order);
                        return array("status" => "success", "msg" => "Pago exitoso");
                    }
                    else
                        return array("status" => "error", "msg" => "Error al completar el pago");
                    break;
                case 'transferencia':
                case 'bizum':
                    $number = substr(time(), -6);
                    $amount = Payment::getTotal($order['plans']);
                    $pending = array(
                        "ID_order" => $id_order,
                        "method" => $method,
                        "amount" => $amount,
                        "status" => 0,
                        "num" => $number,
                    );
                    if(insertSQL("sc_pending", $pending))
                    {
                        $pending = lastIdSQL();
                        return array("status" => "success", "msg" => "", "number" => $number, "pending" => $pending);
                    }
                    break;
            }
        } 
        return array("status" => "error", "msg" => "Error al completar el pedido");
    }
    static function catch()
    {
        if(isset($_GET['p']) && isset($_GET['ac']))
        {
            switch ($_GET['ac']) {
            
                case 'validar':
                    $id = $_GET['p'];
                    self::validatePendingOrder($id);
                    return "Pago validado";
                    break;

                case 'eliminar':
                    $id = $_GET['p'];
                    deleteSQL("sc_pending", array('ID_pending' => $id));
                    return "Pago pendiente eliminado";
                    break;

                default:
                    return "";
                    break;
            }
        }

        if(isset($_POST['penid']) && isset($_POST['date']))
        {
            $date = $_POST['date'];
            $id = $_POST['penid'];
        
            if($date == "")
            {
                if(self::validatePendingOrder($id))
                    updateSQL("sc_pending", array('status' => 1), array('ID_pending' => $id));
                else
                    return "Error al validar el pago";
            }
            else
                updateSQL("sc_pending", array('status' => 2), array('ID_pending' => $id));
            
                return "Pago validado";
            
        }

        return "";
    }

    static function validatePendingOrder($id)
    {
        $pending = self::getPendingOrderByID($id);
        
        if($pending['order']['data'] == "")
        {
            $id_user = $pending['order']['ID_user'];
            $cantidad = getConfParam('CREDIT_PRICE');
            $cantidad = $pending['amount'] / $cantidad;
            user::addCredits($id_user, $cantidad);
            updateSQL("sc_pending", array('status' => 1), array('ID_pending' => $id));
            self::completeOrder($pending['order']['ID_order']);  
            Tickets::insertTicket($id_user, 0, "Compra de créditos", $cantidad, $pending['amount'], $pending['num']);
            return true;        
        }
        $id_ad = $pending['order']['ID_ad'];
        $plans = $pending['order']['plans'];
        $method = $pending['method'];
        if(Payment::completePayment($id_ad, $plans, $method))
        {
            updateSQL("sc_pending", array('status' => 1), array('ID_pending' => $id));
            self::completeOrder($pending['order']['ID_order']);
            Tickets::insertTicket(0, $id_ad, "", 1, $pending['amount'], $pending['num'], $pending['order']['data']);
            return true;
        }
        return false;
    }

    static function getOrderByID($id)
    {
        $order = selectSQL("sc_orders", array("ID_order" => $id));
        if(count($order) == 0)
            return array();
        $order = $order[0];
        if($order['ID_user'] != 0)
            $order['user'] = User::getUserByID($order['ID_user']);
        if($order['ID_ad'] != 0)
            $order['ad'] = getDataAd($order['ID_ad']);
        if($order['data'] != "")
            $order['plans'] = Payment::parsePlans($order['data']);
        else
            $order['plans'] = array();
        
        return $order;
    }

    static function getOrderDetails($id)
    {
        $order = selectSQL("sc_orders", array("ID_order" => $id));
        if(count($order) == 0)
            return array();
        $order = $order[0];
        $plans = Payment::parsePlans($order['data']);
        $monto = Payment::getTotal($plans);
        $details = "";
        foreach($plans as $key => $plan)
        {
            if($key != 0)
                $details .= " | ";
            $details .= "<strong>". $plan['name'] . "</strong> (" . $plan['days'] . " días)";
        }
        return array($monto, $details);
    }

    static function getOrdersByUser($id_user)
    {
        $orders = selectSQL("sc_orders", array("ID_user" => $id_user, "status" => 0));
        return $orders;
    }

    static function getOrdersByAd($id_ad)
    {
        $orders = selectSQL("sc_orders", array("ID_ad" => $id_ad, "status" => 0));
        return $orders;
    }

    static function completeOrder($id_order)
    {
        updateSQL("sc_orders", array("status" => 1), array("ID_order" => $id_order));
    }

    static function newOrder($plans, $id_user = 0, $id_ad = 0)
    {
        if($plans != "")
            $plans = json_encode($plans);

        $order = array(
            "ID_user" => $id_user,
            "ID_ad" => $id_ad,
            "status" => 0,
            "data" => $plans,
        );

        if(insertSQL("sc_orders", $order))
        {
            $id_order = lastIdSQL();
            return $id_order;
        }else
            return 0;
    }

    static function saveOrder()
    {
        if(isset($_POST['planes']))
        {
            $plans = $_POST['planes'];
            if(isset($_POST['orderID']))
                $number = $_POST['orderID'];
            else
                $number = substr(time(), -6);

            if(checkSession())
                $id_user = $_SESSION['data']['ID_user'];
            else 
            {
                if(isset($_POST['email']))
                {
                    $user = selectSQL("sc_user", array("mail" => $_POST['email']));
                    if(count($user) > 0)
                        $id_user = $user[0]['ID_user'];
                }
            }

            if(isset($_POST['idad']))
                $id_ad = $_POST['idad'];
            else
                $id_ad = 0;

            $id_order = Orders::newOrder($plans, $id_user, $id_ad);
            if($id_order != 0)
            {
                if(isset($_POST['metodo']) && isset($_POST['monto']) && ($_POST['metodo'] == "bizum" || $_POST['metodo'] == "transferencia"))
                {
                    $pending = array(
                        "ID_order" => $id_order,
                        "method" => $_POST['metodo'],
                        "amount" => $_POST['monto'],
                        "status" => 0,
                        "num" => $number,
                    );
                    if(insertSQL("sc_pending", $pending))
                    {
                        $pending = lastIdSQL();
                        if(count($plans) > 1)
                            orderMail("Pendiente", $number, "varios servicios", $_POST['monto'], $_POST['metodo'], $id_user);
                        else
                            orderMail("Pendiente", $number, $plans[0], $_POST['monto'], $_POST['metodo'], $id_user);
                        
                        return array("status" => "success", "msg" => "Orden creada correctamente", "number" => $number, "pending" => $pending);
                    }
                }
                return array("status" => "success", "order" => $id_order, "msg" => "Orden creada correctamente");
            }
        }
        return array("status" => "error", "msg" => "Error al crear la orden");
    }

    static function cleanPendingOrders()
    {
        $query = "DELETE FROM sc_pending WHERE DATE_ADD(date, INTERVAL 7 DAY) < NOW()";
        rawQuerySQL($query);
    }

    static function getPendingOrders($status)
    {
        $w = array("status" => $status);
        $query = "";
        if(isset($_GET['q']) && isset($_GET['field']))
        {
            switch ($_GET['field']) {
                case 'mail':
                    $query = "SELECT sp.* 
                    FROM sc_pending sp
                    JOIN sc_orders so ON sp.ID_order = so.ID_order
                    JOIN sc_user su ON so.ID_user = su.ID_user
                    WHERE su.mail LIKE '%".trim($_GET['q'])."%';";
                    break;
                case 'n':
                        $w['num'] = $_GET['q'];
                    break;
                default:
                    # code...
                    break;
            }
        }
        if($query == "")
            $select = selectSQL("sc_pending", $w, "date DESC");
        else
            $select = rawQuerySQL($query);
        
        foreach($select as $key => $value)
        {
            if($value['ID_order'] != 0)
                $select[$key]['order'] = Orders::getOrderByID($value['ID_order']);
        }

        return $select;
    }

    static function getPendingOrderByID($id)
    {
        $order = selectSQL("sc_pending", array("ID_pending" => $id));
        if(count($order) == 0)
            return array();
        $order = $order[0];
        if($order['ID_order'] != 0)
            $order['order'] = Orders::getOrderByID($order['ID_order']);
        return $order;
    }

    static function getOrderAjax()
    {
        if(isset($_GET['id']))
        {
            $order = Orders::getOrderByID($_GET['id']);
            if(count($order) > 0)
            {
                $total = Payment::getTotal($order['plans']);
                $res = array(
                    "status" => "success",
                    "precio" => $total,
                    "idad" => $order['ID_ad'],
                    "planes" => $order['plans'],
                );
                return $res;
            }
        }

        return array("status" => "error", "msg" => "Error al obtener el pedido");
    }

    static function getPendingAjax()
    {
        if(isset($_GET['id']))
        {
            $order = Orders::getPendingOrderByID($_GET['id']);
            if(count($order) > 0)
            {
                $details = "";
                $plans =  $order['order']['plans'];
                foreach($plans as $key => $plan)
                {
                    if($key != 0)
                        $details .= " | ";
                    $details .= $plan['name'] . " (" . $plan['days'] . " días)";
                }
                $precio = Payment::getTotal($plans);
                list($total, $discount) = Payment::getDiscount($precio);
                $res = array(
                    "status" => "success",
                    "precio" => $precio,
                    "total" => $total,
                    "method" => $order['method'],
                    "number" => $order['num'],
                    "details" => $details,
                    "discount" => $discount
                );
                return $res;
            }
        }
    }

    static function checkActiveOrders($id_ad)
    {
        $sql = "SELECT sp.* 
            FROM sc_pending sp
            JOIN sc_orders so ON sp.ID_order = so.ID_order
            JOIN sc_ad sa ON so.ID_ad = sa.ID_ad
            WHERE sp.status = 0 AND sa.ID_ad = $id_ad;";
        $select = rawQuerySQL($sql);

        if(count($select) > 0)
            return $select[0]['ID_pending'];    
        else
            return 0;
    }

    static function deletePending($id)
    {
        deleteSQL("sc_pending", array("ID_pending" => $id));
        return true;
    }
}