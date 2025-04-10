<?php 
include("settings.inc.php");
use Dompdf\Dompdf;

if(isset($_GET['id'])){
    
    $id = $_GET['id'];
    $ticket = Tickets::getTicketTemplate($id);
    $dompdf = new Dompdf(array('enable_remote' => true));
    $dompdf->loadHtml($ticket);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="ticket.pdf"');
    $dompdf->stream("ticket.pdf", array("Attachment" => 0));
}
