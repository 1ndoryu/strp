



<?
$exito_div=false;
if(isset($_POST['update_data']))
{
    $exito_div=$language_admin['manage_premium.config_saved'];

    setConfParam('CREDIT_PRICE', $_POST['credit_price']);
    unset($_POST['credit_price']);
    unset($_POST['update_data']);
    foreach($_POST['package'] as $days => $price)
    {
        updateSQL('sc_package', array('price' => $price), $w = array('ID_package' => $days));
    }
    unset($_POST['package']);
    foreach($_POST as $plan => $data)
    {
        foreach($data as $days => $price)
        {
            updateSQL('sc_plans', array('price' => $price), $w = array('plan' => $plan, 'days' => $days));
        }
    }
}

?>
<h2 style="background: #03A9F4; color: white;"><?=$language_admin['manage_premium.title_h1']?></h2>
<? if($exito_div!==FALSE) {?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$exito_div;?></div>
<? } ?>
<form action="<? $_SERVER['PHP_SELF'];?>" method="post" class="param_form premium_panel_form" onSubmit="before_submit()">
	<legend>Configurar pagos</legend>
	<div>
        <label>Precio de los creditos</label>
        <input class="input_money" type="text" name="credit_price" value="<?=getConfParam('CREDIT_PRICE');?>">
    </div>      
    <?php 
    
        $planes = Payment::getDataPlans();
        $packages = Payment::getPackages();
    ?>
    <!------------------------------------------------------------------------------------------------------>
    <legend>Planes: </legend>
    <?php foreach($planes as $plan => $data): ?>
        <div>
            <label><?=$data['name']?></label>
            <div class="input_box">
                <? foreach ($data['days'] as $days => $price): ?>
                    <?= $days ?> <?= $data['counter'] == '' ? 'días' : $data['counter'] ?>
                    <input
                        class="price_input"
                        type="text"
                        name="<?= $plan ?>[<?= $days ?>]"
                        value="<?= $price ?>"
                    >
                <?php endforeach ?>
                
            </div>
        </div>
    <?php endforeach ?>
    <div>
        <label>Descuento publicistas</label>
        <div class="input_box">
            <?php foreach($packages as $package): ?>
            
                <?=$package['value']?> anuncios
                <input
                    class="price_input"
                    type="text"
                    name="package[<?=$package['ID_package']?>]"
                    value="<?=$package['price']?>"
                >
            <?php endforeach ?>
        
            
        </div>
    </div>
    <input name="update_data" type="submit" value="<?=$language_admin['manage_premium.button_save']?>" style="margin-left: 26%;">

</form>
