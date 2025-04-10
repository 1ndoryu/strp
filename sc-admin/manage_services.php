<?php
    $exito_div=false;
    $MAX_PAG = 20; 
    $pag = $_GET["pag"]; 
    if (!$pag){ 
        $start = 0; 
        $pag=1; 
    }else $start = ($pag - 1) * $MAX_PAG;

    $total_reg=countSQL("sc_service");

    $tot_pag = ceil($total_reg / $MAX_PAG);
    if($total_reg < $MAX_PAG){ 
        if($total_reg == 0) $inn = 0; else $inn=1;
        $por_pagina = $total_reg;
    }else{ 
        $inn = 1+($MAX_PAG*($pag-1));
        if($pag == $tot_pag)
            $por_pagina=$total_reg;
        else
            $por_pagina = $MAX_PAG*($pag);
    }

    $services = Service::getServices(null,$start, $MAX_PAG);

?>
<h2>Gestionar servicios</h2>
<form action="index.php" method="get" class="addCat">
<label>Buscar</label>
<input name="q" type="text" value="<?=$_GET['q'];?>">
<select name="field">
	<option value="mail" <? if($_GET['field']=="mail") echo "selected";?>><?=$language_admin['manage_user.mail']?></option>
	<option value="ref" <? if($_GET['field']=="ref") echo "selected";?>>Ref</option>
</select>
<input type="submit" value="Buscar" class="button_form">
<input type="hidden" name="id" value="manage_services">
</form>
<table class="table service-table table-responsive-md">
    <thead>
        <tr>
            <th scope="col">Anuncio</th>
            <th scope="col">Tipo</th>
            <th scope="col">Categoria</th>
            <th scope="col">Email</th>
            <th scope="col">Fecha Inicio</th>
            <th scope="col">Fecha expiración</th>
            <th scope="col">Estado</th>
            <th scope="col">Método de pago</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($services as $service){
            $ad = getDataAd($service['ad']['ID_ad'])
            
            ?>    
            <tr>
                <td>
                    <div class="service-card">
                        <img src="<?= Images::getImage($ad['images'][0]['name_image'], IMG_ADS, true); ?>" alt="">
                        <div>
                            <span>Ref: <?= $service['ad']['ref']; ?></span>
                            <span><?= $service['ad']['title']; ?></span>
                        </div>
                    </div>
                </td>
                <td><?= Service::TYPE($service['service']['type']); ?></td>
                <td><?= $service['category']['name'];?></td>
                <td><?=$service['user']['mail'];?></td>
                <td><?= parseDate($service['service']['date']); ?></td>
                <td>
                    <?php if($service['service']['expire'] != 0){ ?>
                        <?= date('d-m-Y h:i', $service['service']['expire']); ?>
                    <?php  }?>
                </td>
                <td>

                    <?php if($service['service']['active'] == 0){ ?>
                        <span class="status status-expired">Finalizado</span>
                    <?php }else{ ?>
                        <span class="status status-active">Activo</span>
                    <?php } ?>

                </td>
                <td>
                    <?= $service['service']['method']; ?>
                </td>
            </tr>
        <?php } ?>
        

    </tbody>
</table>

<? 
	createPagButtons($tot_pag, $pag, "/sc-admin/index.php?id=manage_services");
?>