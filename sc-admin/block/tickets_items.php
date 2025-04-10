<?php

$success_div = Tickets::catch();
$tickets = Tickets::getTickets();
?>
<form action="index.php" method="get" class="addCat">
    <label>Buscar</label>
    <input name="q" type="text" value="<?=$_GET['q'];?>">
    <select name="field">
        <option value="mail" <? if($_GET['field']=="mail") echo "selected";?>><?=$language_admin['manage_user.mail']?></option>
        <option value="order" <? if($_GET['field']=="order") echo "selected";?>>Orden</option>
        <option value="n" <? if($_GET['field']=="n") echo "selected";?>>Nº</option>
    </select>
    <input type="submit" value="Buscar" class="button_form">
    <input type="hidden" name="id" value="manage_tickets">
    <input type="hidden" name="type" value="<?=$type?>">
</form>
<table class="table table-tickets table-responsive-md">
    <thead>
        <tr>
            <th>Nº</th>
            <th>orden</th>
            <th>importe</th>
            <th>usuario</th>
            <th>fecha</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tickets as $ticket): ?>
            <tr>
                <td><?= $ticket['ID_ticket']; ?></td>
                <td><?= $ticket['order']; ?></td>
                <td><?= $ticket['amount']; ?></td>
                <td><?= $ticket['user']['mail']; ?></td>
                <td><?= parseDate($ticket['date'], 'd/m/Y H:i'); ?></td>
                <td>
                    <div class="ticket-actions">
                        <a target="_blank" href="/print_ticket.php?&id=<?= $ticket['ID_ticket']; ?>">Imprimir</a>
                        <?php if($ticket['refund'] == 0): ?>
                            <a href="javascript:void(0)" onclick="setModalRefund('<?= $ticket['ID_ticket']; ?>', <?= $ticket['refund']; ?>, '<?= $ticket['refund_date']; ?>')">Devolver</a>
                            <?php else: ?>
                            <a href="javascript:void(0)" onclick="setModalRefund('<?= $ticket['ID_ticket']; ?>', <?= $ticket['refund']; ?>, '<?= $ticket['refund_date']; ?>')" class="danger">Ticket devuelto</a>
                        <?php endif ?>
                        <a href="index.php?id=manage_tickets&action=send&t=<?= $ticket['ID_ticket']; ?>">Enviar</a>
                        <a href="index.php?id=manage_tickets&action=delete&t=<?= $ticket['ID_ticket']; ?>">Eliminar</a>
                    </div>
                </td>
                
            </tr>
        <?php endforeach; ?>
    </tbody>

</table>


<div class="modal" tabindex="-1" role="dialog" id="modal-refund">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Devolver Ticket</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-refund" method="post">
                    <div class="form-group">
                        <label for="form-refund-amount">Tipo</label>
                        <select name="refund_type" required id="form-refund-type" class="form-control">
                            <option value="1">Paypal</option>
                            <option value="3">Por la empresa</option>
                        </select>
                    </div>

                    <div class="form-group" id="form-refund-date-group">
                        <label >Fecha</label>
                        <input type="date" name="refund_date" id="form-refund-date" class="form-control">
                    </div>
                    <input type="hidden" name="refund_id" id="form-refund-id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" onclick="$('#form-refund').submit()" id="modal-refund-btn" class="btn btn-primary">Devolver</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>    
            </div>
        </div>
    </div>              
</div>