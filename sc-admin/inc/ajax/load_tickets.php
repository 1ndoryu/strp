<?php

include("../../../settings.inc.php");

$tickets = selectSQL("sc_tickets", $w = array('ID_factura' => $_GET['id']));

header('Content-Type: text/html');
?>
<ul>
    <?php foreach($tickets as $ticket): ?>
        <li>
            <div class="d-flex justify-content-between gap-2">
                <a class="f-1200" target="_blank" href="<?= getConfParam('SITE_URL') ?>print_ticket.php?id=<?= $ticket['ID_ticket'] ?>">
                    <?=parseDate($ticket['date'], "d/m/Y H:i") ?> - Nº: <?= $ticket['ID_ticket'] ?>
                </a>
                <button onclick="enviarFactura(<?= $ticket['ID_ticket'] ?>, <?=$_GET['id']?>)" class="btn btn-panel">Enviar Factura</button>
            </div>
        </li>
    <?php endforeach ?>
</ul>

