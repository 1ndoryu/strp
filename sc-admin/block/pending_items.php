<?php
    $success_div = Orders::catch();
    $status = "1!=";
    if($_GET['type']=="fin")
        $status = 1;
    Orders::cleanPendingOrders();
    $orders = Orders::getPendingOrders($status);
?>
<form action="index.php" method="get" class="addCat">
    <label>Buscar</label>
    <input name="q" type="text" value="<?=$_GET['q'];?>">
    <select name="field">
        <option value="mail" <? if($_GET['field']=="mail") echo "selected";?>><?=$language_admin['manage_user.mail']?></option>
        <option value="n" <? if($_GET['field']=="n") echo "selected";?>>Nº</option>
    </select>
    <input type="submit" value="Buscar" class="button_form">
    <input type="hidden" name="id" value="manage_tickets">
    <input type="hidden" name="type" value="<?=$type?>">
</form>
<table class="table table-tickets table-responsive-md">
    <thead>
        <tr>
            <th>Numero</th>
            <th>Tipo</th>
            <th>Importe</th>
            <th>Usuario</th>
            <th>Servicios</th>
            <th>fecha</th>
            <th>Ref</th>
            <th>Categoria</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $order): ?>
            <tr>
                <td><?= $order['num']; ?></td>
                <td><?= $order['method']; ?></td>
                <td><?= $order['amount']; ?></td>
                <td><?= $order['order']['user']['mail']; ?></td>
                <td>
                    <?php if($order['order']['data'] == ""): ?>
                        Créditos
                      <?php else: ?>
                        <?php foreach($order['order']['plans'] as $plan): ?>
                            <?= $plan['name']; ?>
                         <?php endforeach; ?>
                    <?php endif ?>
                </td>
                <td><?= $order['date']; ?></td>
                <td><?= $order['order']['ad']['ad']['ref']; ?></td>
                <td><?= $order['order']['ad']['category']['name']; ?></td>
                <td>
                    <?php 
                        if($order['status'] == 0)
                            echo "Pendiente";
                        else if($order['status'] == 1)
                            echo "Finalizado";
                        else if($order['status'] == 2)    
                            echo "Validado";
                    ?>
                </td>
                <td>
                    <div class="ticket-actions">
                        <?php if($order['status'] == 0): ?>
                            <a href="javascript:void(0)" onclick="setModalValidate('<?=$order['ID_pending']; ?>')">Validar</a>
                        <?php endif ?>
                        <a href="/sc-admin/index.php?id=manage_tickets&type=pen&p=<?=$order['ID_pending']; ?>&ac=eliminar">Eliminar</a>
                    </div>
                </td>
                
            </tr>
        <?php endforeach; ?>
    </tbody>

</table>


<div class="modal" tabindex="-1" role="dialog" id="modal_validate">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Validar pago</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_validate" method="post">
                    <p>Si dejas el campo vacío, se validará la orden inmediatamente.</p>
                    <div class="form-group">
                        <label >Fecha</label>
                        <input type="text" name="date" id="form-pending-date" class="form-control">
                        
                    </div>

                    <input type="hidden" name="penid" id="form-pending-id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" onclick="$('#form_validate').submit()" id="modal-refund-btn" class="btn btn-primary">Activar</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>    
            </div>
        </div>
    </div>              
</div>