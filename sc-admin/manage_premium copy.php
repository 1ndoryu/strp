<script type="text/javascript">
    var tosubmit = []
    function add(name) {
        if(tosubmit.indexOf(name) == -1)
            tosubmit.push(name);
    }
    
    function is_changed(name) {
        for(var k = 0; k < tosubmit.length; k++)
            if(name == tosubmit[k])
                return name && true;
        return false;
    }
    
    function before_submit() {
        var allInputs = document.getElementsByTagName("input");
        for(var k = 0; k < allInputs.length; k++) {
            var name = allInputs[k].name;
            if(!is_changed(name))
                allInputs[k].disabled = true;
        }
    }
    
</script>



<?
$exito_div=false;
if(isset($_POST['currency_code'])){
	$update_data=array(
	'ID_currency_code'=>$_POST['currency_code'],
	'paypal_email'=>$_POST['email_paypal'],
// 	'price_1'=>$_POST['price1'],
// 	'price_2'=>$_POST['price2'],
// 	'time_1'=>$_POST['time1'],
// 	'time_2'=>$_POST['time2']	
	);
	updateSQL("sc_paypal",$update_data,$w=array('ID_paypal'=>1));
	$exito_div=$language_admin['manage_premium.config_saved'];
	
	console_log($_POST);
	
	update_prices($_POST);
	
	
}
$paypal_configuration=selectSQL("sc_paypal",$w=array('ID_paypal'=>1));
$paypal_currency_codes=selectSQL("sc_currency_code");
?>
<h2 style="background: #03A9F4; color: white;"><?=$language_admin['manage_premium.title_h1']?></h2>
<? if($exito_div!==FALSE) {?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$exito_div;?></div>
<? } ?>
<form action="<? $_SERVER['PHP_SELF'];?>" method="post" class="param_form premium_panel_form" onSubmit="before_submit()">
	<legend>Configurar pagos por PayPal</legend>
	<div><label><?=$language_admin['manage_premium.label_mail']?></label>
    <input type="text" name="email_paypal" value="<?=$paypal_configuration[0]['paypal_email'];?>">
    </div>      
	<div><label><?=$language_admin['manage_premium.label_currency']?></label>
    <select name="currency_code">
    <option value="0"><?=$language_admin['manage_premium.select_currency']?></option>
    <? for($i=0;$i<count($paypal_currency_codes);$i++){?>
    <option value="<?=$paypal_currency_codes[$i]['ID_currency']?>" <? if($paypal_currency_codes[$i]['ID_currency']==$paypal_configuration[0]['ID_currency_code']) echo 'selected';?>><?=$paypal_currency_codes[$i]['currency_code']?></option>
    <? } ?>
    </select>
    
    <?

    $premium1 = selectSQL('sc_time_options', $w = array('owner' => 'PREMIUM'), 'value ASC');

    $premium2 = selectSQL('sc_time_options', $w = array('owner' => 'LISTING'), 'value DESC LIMIT 1');
    
    $premium3 = selectSQL('sc_time_options', $w = array('owner' => 'PREMIUM3'), 'value ASC');

    $banners = selectSQL('sc_time_options', $w = array('owner' => 'BANNER'));

    console_log($premium2);
    ?>
    
    <!------------------------------------------------------------------------------------------------------>
    
    <div><label><?= $language_admin['manage_premium.time_price_premium'] ?></label>
        <div class="input_box">
        <? foreach ($premium1 as $premium) { ?>
            <?= $premium['quantity'] ?> <?= $premium['time_unit'] ?>
            <input
                onchange="add('premium_<?= $premium['quantity'] ?>_price')"
                class="price_input"
                type="text"
                name="premium_<?= $premium['quantity'] ?>_price" 
                value="<?= $premium['price']; ?>"
            >
        <? } ?>
            
        </div>
    </div>
    <div><label>Anuncios Destacado</label>
        <div class="input_box">
        <? foreach ($premium3 as $premium) { ?>
            <?= $premium['quantity'] ?> <?= $premium['time_unit'] ?>
            <input
                onchange="add('premium3_<?= $premium['quantity'] ?>_price')"
                class="price_input"
                type="text"
                name="premium3_<?= $premium['quantity'] ?>_price" 
                value="<?= $premium['price']; ?>"
            >
        <? } ?>
            
        </div>
    </div>
    
    <div><label><?= $language_admin['manage_premium.time_price_banner'] ?></label>
        <div class="input_box">
            <? foreach ($banners as $banner) { ?>
                
                    <?= $banner['quantity'] ?> <?= $banner['time_unit'] ?> <?= $banner['position'] ?>
                    <input
                        onchange="add('banner_<?= $banner['quantity'] ?>_<?= $banner['position'] ?>_price')"
                        class="price_input banner"
                        type="text" 
                        name="banner_<?= $banner['quantity'] ?>_<?= $banner['position'] ?>_price" 
                        value="<?= $banner['price']; ?>"
                    >
                
                
            <? } ?>
        </div>
    </div>
    
    <div><label><?= $language_admin['manage_premium.price_credit'] ?></label>
        <div class="input_box listing">
        <input
            onchange="add('credit_price')"
            class="price_input"
            type="text"
            name="credit_price" 
            value="<?= $premium2[0]['price']; ?>"
        >
        </div>
    </div>
    
    <!------------------------------------------------------------------------------------------------------>
    
    <!-- <h3 style="margin: 2% 30%"><?= $language_admin['manage_premium.adult_title'] ?></h3> -->
    
    <div class="d-none"><label><?= $language_admin['manage_premium.time_price_premium'] ?></label>
        <div class="input_box">
        <? foreach ($premium1 as $premium) { ?>
            <?= $premium['quantity'] ?> <?= $premium['time_unit'] ?>
            <input
                onchange="add('premium_<?= $premium['quantity'] ?>_adult')"
                class="price_input"
                type="text" 
                name="premium_<?= $premium['quantity'] ?>_adult" 
                value="<?= $premium['price_adult']; ?>"
            >
        <? } ?>
            
        </div>
    </div>
    
    <div class="d-none"><label>Anuncios Destacado</label>
        <div class="input_box">
        <? foreach ($premium3 as $premium) { ?>
            <?= $premium['quantity'] ?> <?= $premium['time_unit'] ?>
            <input
                onchange="add('premium3_<?= $premium['quantity'] ?>_adult')"
                class="price_input"
                type="text" 
                name="premium3_<?= $premium['quantity'] ?>_adult" 
                value="<?= $premium['price_adult']; ?>"
            >
        <? } ?>
            
        </div>
    </div>
    
    <div class="d-none"><label><?= $language_admin['manage_premium.time_price_banner'] ?></label>
        <div class="input_box">
            <? foreach ($banners as $banner) { ?>
                <?= $banner['quantity'] ?> <?= $banner['time_unit'] ?> <?= $banner['position'] ?>
                <input
                    onchange="add('banner_<?= $banner['quantity'] ?>_<?= $banner['position'] ?>_adult')"
                    class="price_input banner"
                    type="text" 
                    name="banner_<?= $banner['quantity'] ?>_<?= $banner['position'] ?>_adult" 
                    value="<?= $banner['price_adult']; ?>"
                >
            <? } ?>
        </div>
    </div>
    
    <div class="d-none"><label><?= $language_admin['manage_premium.price_credit'] ?></label>
        <div class="input_box listing">
        <input
            onchange="add('credit_adult')"
            class="price_input"
            type="text"
            name="credit_adult" 
            value="<?= $premium2[0]['price_adult']; ?>"
        >
        </div>
    </div>

    <h3 style="margin: 2% 30%">Subir al listado</h3>

    <div><label>Timepo de la etiqueta</label>
        <div class="input_box listing">
        <input
            onchange="add('tag_premium2_time')"
            class="price_input"
            type="text"
            name="tag_premium2_time" 
            value="<?= Service::getOption('LISTING', 'TIME_TAG') ?>"
        >
        Minutos
        </div>
    </div>
    
    <!--</div>      -->
    <!--<div><label><?=$language_admin['manage_premium.price1']?></label>-->
    <!--<input type="text" name="price1" value="<?=$paypal_configuration[0]['price_1'];?>">-->
    <!--</div>-->
    <!--<div><label><?=$language_admin['manage_premium.time1']?></label>-->
    <!--<input type="text" name="time1" value="<?=$paypal_configuration[0]['time_1'];?>">-->
    <!--</div>-->
    <!--<div><label><?=$language_admin['manage_premium.price2']?></label>-->
    <!--<input type="text" name="price2" value="<?=$paypal_configuration[0]['price_2'];?>">-->
    <!--</div>-->
    <!--<div><label><?=$language_admin['manage_premium.time2']?></label>-->
    <!--<input type="text" name="time2" value="<?=$paypal_configuration[0]['time_2'];?>">-->
    <!--</div>-->
    <input name="update_data" type="submit" id="update_data" value="<?=$language_admin['manage_premium.button_save']?>" style="margin-left: 26%;">
</form>
