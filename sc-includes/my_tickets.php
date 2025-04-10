<?php
    if(Service::solicitudFactura())
    {
       $msg_success = "Se ha enviado tu solicitud de factura";
    }

    $tickets = getMyTickets($_SESSION['data']['ID_user']);
    $services = Service::getServices($_SESSION['data']['ID_user']);
    $facturas = getMyFacturas($_SESSION['data']['ID_user']);
    $files = array();
    
    if(count($facturas) > 0)
        $files = selectSQL("sc_files", $w = array('ID_factura' => $facturas[0]['ID_factura']));
    

    $type = "";
    if(isset($_GET['type']))
        $type = $_GET['type'];

?>
<?php if(isset($msg_success)): ?>
    <div class="alert alert-success" role="alert">
        <?= $msg_success; ?>
    </div>
<?php endif; ?>
<div class="my-tickets-container">

    <div class="row mx-0">
        <div class="col-md-6 px-0">
            <a href="<?=getConfParam("SITE_URL")?>/mis-tickets/?type=ticket" class="btn btn-main mb-2">
                 Mis tickets
            </a>
            <a href="<?=getConfParam("SITE_URL")?>/mis-tickets/?type=factura" class="btn btn-pink mb-2">
                 Mis facturas
            </a>
        </div>
        <div class="col-md-6 text-right px-0">
            <?php if(count($facturas) == 0): ?>
                <a href="javascript:void(0)" onclick="$('#modal-ticket').modal('show')" class="btn btn-secondary mb-2">
                     Enviar datos de facturación
                </a>
            <?php endif ?>
    
        </div>
        
    </div>

    <?php if($type == "ticket"): ?>

        <table class="table table-responsive-md mb-5">
            <thead>
                <tr>
                    <!-- <th>Ref.</th> -->
                    <!-- <th>Fecha</th> -->
                    <th>Descripción</th> 
                    <th>Cantidad</th>
                    <th>Importe</th>
                    <th>Ver Ticket</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($tickets as $ticket): ?>
                    <tr>
                        <!-- <td><?php echo $ticket['ID_ticket']; ?></td> -->
                        <!-- <td class="big-cell"><?php echo  parseDate($ticket['date']); ?></td> -->
                        <td class="text-center text-md-left"><?php echo $ticket['comment']; ?></td>
                        <td class="text-center text-md-left"><?php echo $ticket['quantity']; ?></td>
                        <td class="text-center text-md-left"><?php echo $ticket['amount']; ?></td>
                        <td><a target="_blank" href="/print_ticket.php?id=<?php echo $ticket['ID_ticket']; ?>">Ver ticket</a></td>
                    </tr>
                <?php endforeach; ?>    
            </tbody>   
        </table>

    <?php endif ?>
    <?php if($type == "factura"): ?>

        <table class="table table-responsive-md mb-5">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Nº Ticket</th>
                    <th>Factura</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($files as $file): ?>
                    <tr>
                       
                        <td class="big-cell"><?php echo  parseDate($file['date'], 'd-m-Y'); ?></td>
                        <td><?php echo $file['ID_ticket']; ?></td>
                        <td><a target="_blank" href="/print_factura.php?file=<?php echo $file['src']; ?>">Ver factura</a></td>
                        
                    </tr>
                <?php endforeach; ?>    
            </tbody>   
        </table>

    <?php endif ?>
    
    
    
    <h2 class="mt-5">Mis servicios</h2>
    <div class="arrow-container">

        <div class="arrow">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <table class="table table-responsive-md">
        <thead>
            <tr>
                <th>Anuncio</th>
                <th>Servicio</th>
                <th>Fecha de inicio</th>
                <th>Categoría</th>
                <th>Fecha de expiración</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($services as $service): ?>
                <tr>
                    <td>
                        <div class="service-card">
                            <img src="<?= Images::getImageAd($service['ad']['ID_ad']); ?>" alt="">
                            <div>
                                <span class="d-inline-block">Ref. <?= $service['ad']['ref']; ?></span>
                                <!-- <span>Titulo: <?= $service['ad']['title']; ?></span> -->
                            </div>
                        </div>
                    </td>
                    <td>
                        <p class="mt-4 text-center text-md-left"><?= Service::TYPE($service['service']['type']); ?></p>
                    </td>
                    <td class="big-cell">
                        <p class="mt-4"><?= parseDate($service['service']['date']); ?></p>
                        
                    </td>
                    <td>
                        <p class="mt-4 text-center text-md-left"><?= $service['category']['name']; ?></p>
                    </td>
                    <td class="big-cell">
                        <p class="mt-4"><?= date('d-m-Y H:i', $service['service']['expire']); ?></p>
                    </td>
                    <td>
                        <?php if($service['service']['active'] == 0): ?>
                            <span class="status status-expired mt-4">Finalizado</span>
                        <?php else: ?>
                            <span class="status status-active mt-4">Activo</span>
                        <?php endif; ?>
                    </td>
                </tr>
            
            <?php endforeach; ?>    
        </tbody>   
    </table>
</div>


<div class="modal" id="modal-ticket">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Solicitar factura</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" method="post" id="form_factura">
                    <div class="form-group">
                        <label for="province">Soy</label>
                         <select name="status" class="form-control">
                            <option value="0">Autónomo</option>
                            <option value="1">Empresa</option>
                         </select>
                    </div>
                    <div class="form-group">
                        <!-- nombre -->
                        <label for="name">Nombre</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Nombre">
                    </div>
                    <!-- DNI -->
                    <div class="form-group">
                        <label for="dni">DNI o CIF </label>
                        <input type="text" class="form-control" id="dni" name="dni" placeholder="DNI">
                    </div>
                    <!-- direccion -->
                    <div class="form-group">
                        <label for="address">Dirección</label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="Dirección">
                    </div>
                   <!-- codigo postal -->
                    <div class="form-group">
                        <label for="postal">Código postal</label>
                        <input type="text" class="form-control" id="postal" name="postal" placeholder="Código postal"> 
                    </div>
                    <!-- zona -->
                    <div class="form-group">
                        <label for="zone">Zona</label>
                        <input type="text" class="form-control" id="zone" name="zone" placeholder="Zona">
                    </div>  
                    <!-- provincia -->
                    <div class="form-group">
                        <label for="province">Provincia</label>
                        <input type="text" class="form-control" id="province" name="province" placeholder="Provincia">
                    </div>

                    <input type="hidden" name="email" value="<?= $_SESSION['data']['mail']; ?>">
                    <button type="button" class="btn btn-primary" onclick="submitForm()">Enviar</button>
                    <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" />
                </form>
            </div>
        </div>
</div>
<script>
	function submitForm() {
    	grecaptcha.ready(function() {
			grecaptcha.execute('<?=SITE_KEY ?>', {action: 'submit'}).then(function(token) 
			{
		        document.getElementById('g-recaptcha-response').value=token;
				//console.log(token);
		        $("#form_factura").submit();
		    });
	    });
	}
</script>
<script src='https://www.google.com/recaptcha/api.js?render=<?php echo SITE_KEY; ?>'></script>