<?php
use Dompdf\Dompdf;

class Tickets
{
    public static function catch()
    {   
        if(isset($_GET['action']))
        {
            switch ($_GET['action']) {
            
                case 'send':
                    $id = $_GET['t'];
                    self::sendTicket($id);
                    return "ticket enviado";
                    break;

                case 'delete':
                    $id = $_GET['t'];
                    deleteSQL("sc_tickets", array('ID_ticket' => $id));
                    return "ticket eliminado";
                    break;

                default:
                    return "";
                    break;
            }
        }

        if(isset($_POST['refund_id']))
        {
            $w = array('ID_ticket' => $_POST['refund_id']);
            $d = array('refund' => $_POST['refund_type'], 'refund_date' => $_POST['refund_date']);

            updateSQL("sc_tickets", $d, $w);
            return "ticket devuelto";
        }

        return "";
    }

    public static function getTickets()
    {
        $w = array();
        $query = "";

        if(isset($_GET['q']) && isset($_GET['field']) && $_GET['q']!='')
        {
            switch ($_GET['field']) {
                case 'mail':
                    $query = "SELECT sc_tickets.*
                            FROM sc_tickets
                            JOIN sc_user ON sc_tickets.ID_user = sc_user.ID_user
                            WHERE sc_user.mail LIKE '%".trim($_GET['q'])."%';";
                    break;
                case 'n':
                    $w['ID_ticket']=$_GET['q'];
                    break;
                case 'order':
                    $w[$_GET['field']]=$_GET['q']."%";
                    break;
                default:
                    # code...
                    break;
            }
        }
        if($query == "")
            $tickets = selectSQL("sc_tickets", $w);
        else
            $tickets = rawQuerySQL($query);

        foreach ($tickets as $key => $value) {
            $user = selectSQL("sc_user", $w = array(
                'ID_user' => $value['ID_user']
            ));
            $ad = selectSQL("sc_ad", $w = array(
                'ID_ad' => $value['ID_ad']
            ));
            $tickets[$key]['ad'] = $ad[0];
            $tickets[$key]['user'] = $user[0];
        }

        return $tickets;
    }

    public static function getTicket($id)
    {
        $ticket = selectSQL("sc_tickets", $w = array(
            'ID_ticket' => $id
        ));
        if (count($ticket) > 0) {
            $ticket = $ticket[0];
            $user = selectSQL("sc_user", $w = array(
                'ID_user' => $ticket['ID_user']
            ));
            $ad = selectSQL("sc_ad", $w = array(
                'ID_ad' => $ticket['ID_ad']
            ));

            $ticket['ad'] = $ad[0];
            $ticket['user'] = $user[0];
            return $ticket;
        }

        return null;
    }

    public static function getTicketTemplate($id)
    {
        $ticket = selectSQL("sc_tickets", $w = array(
            'ID_ticket' => $id
        ));
        if (count($ticket) > 0) {
            $ticket = $ticket[0];
            $user = selectSQL("sc_user", $w = array(
                'ID_user' => $ticket['ID_user']
            ));
            $ad = selectSQL("sc_ad", $w = array(
                'ID_ad' => $ticket['ID_ad']
            ));

            $data = array();
            $data['email'] = $user[0]['mail'];
            $data['ID_ad'] = $ad[0]['ID_ad'];
            $data['ticket'] = $ticket['ID_ticket'];
            $date = new DateTime($ticket['date']);
            $data['date'] =  $date->format('d/m/Y');

            $imp = self::calcIVA($ticket['amount']);
            $base_imponible = $imp['base']; 
            $iva_mont = $imp['iva'];
    

            $data['importe'] = number_format($ticket['amount'], 2, '.', '');

            $data['base_imponible'] = number_format($base_imponible, 2, '.', '');

            $data['iva_mont'] = number_format($iva_mont, 2, '.', '');

            $compras = array();
            if($ticket['data'] != null)
            {
                $plans = Payment::parsePlans($ticket['data']);
                //$total = Payment::getTotal($plans);
                $cantidad = $ticket['quantity'];
                foreach($plans as $key => $plan)
                {
                    $compras[] = array(
                        "concepto" => $plan['name'],
                        "cantidad" => $cantidad,
                        "precio" => number_format($plan['price'], 2, '.', ''),
                        "importe" => number_format($plan['price'] * $cantidad, 2, '.', '')
                    );
                    
                }
            }else
            {
                $compras = array(array(
                    "concepto" => $ticket['comment'],
                    "cantidad" => $ticket['quantity'],
                    "precio" => number_format(round($ticket['amount'] / $ticket['quantity']), 2, '.', ''),
                    "importe" => number_format($ticket['amount'], 2, '.', ''),

                ));
            }

            $data['compras'] = $compras;

            ob_start();
            loadBlock("ticket", $data);
            $ticket = ob_get_contents();
            ob_end_clean();
    
            return $ticket;
        }
        return "";
    }

    static function createPDF($id)
    {
        $ticket = self::getTicketTemplate($id);
        $dompdf = new Dompdf(array('enable_remote' => true));
        $dompdf->loadHtml($ticket);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $output = $dompdf->output();
    
        return $output;
    }

    static function calcIVA($total)
    {
        $r = array();
        $r['base'] = $total/ 1.21;
        $r['iva'] = $r['base'] * 0.21; 
        return $r;
    }

    static function sendTicket($id)
    {
        $ticket = self::getTicket($id);
        $mail = new PHPMailer();
        if (activeSMTP()) {
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'ssl';
            $mail->Host = getConfParam('SMTP_HOST');
            $mail->Port = getConfParam('SMTP_PORT');
            $mail->Username = getConfParam('SMTP_USER');
            $mail->Password = getConfParam('SMTP_PASSWORD');
        }
        $mail->setFrom(getConfParam('DEFAULT_MAIL'), getConfParam('DEFAULT_NAME'));
        $mail->addAddress($ticket['user']['mail']);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Ticket de compra';
        $mail->Body = "Le enviamos un correo electrónico con el detalle de tu compra.";
        $buff = self::createPDF($id);
        $attachment = ABSPATH . "tmp/ticket" . self::uniqD() . ".pdf";
        file_put_contents($attachment, $buff);
        $mail->addAttachment($attachment, 'ticket.pdf');
        try {
            //code...
            $mail->send();
            return true;

        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }

    }

    static function insertTicket($id_user, $id_ad, $comment, $quantity, $amount, $order, $data = null)
    {
        if($id_user == 0 && $id_ad != 0)
        {
            $id_user = user::getUserByIdad($id_ad);
        }

        if($amount == 0 && $data != null)
        {
            $amount = Payment::getTotal($data);
        }

        $ticket = array(
            "ID_user" => $id_user,
            "ID_ad" => $id_ad,
            "comment" => $comment,
            "quantity" => $quantity,
            "amount" => $amount,
            "order" => $order
        );

        $factura = getMyFacturas($id_user);
        if(count($factura) > 0)
            $ticket['ID_factura'] = $factura[0]['ID_factura'];

        if(is_array($data))
            $ticket['data'] = json_encode($data);
        else if(gettype($data) == "string")
            $ticket['data'] = $data;

        insertSQL("sc_tickets", $ticket);

    }

    static function uniqD()
    {
        $timestamp = time();
        $timestampStr = strval($timestamp);
        $lastDigit = substr($timestampStr, -1);
        return $lastDigit;
    }
}