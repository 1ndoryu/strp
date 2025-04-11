<?
if (isset($_GET['i'])) {
    $ad = getDataAd($_GET['i']);
    if (count($ad['ad']) != 0) {
        if (count($ad['images']) != 0) $image = true;

        $paypal_configuration = selectSQL("sc_paypal", $w = array('ID_paypal' => 1));
        $paypal_currency_codes = selectSQL("sc_currency_code", $w = array('ID_currency' => $paypal_configuration[0]['ID_currency_code']));

?>
        <div class="col_single">
            <h1><?= $language['premium.title_h1'] ?></h1>
            <div class="info_item_up">
                <div class="info_item_img" style="background: url('<?= IMG_ADS; ?><? if ($image) { ?><?= $ad['images'][0]['name_image']; ?><? } else {
                                                                                                                                            echo IMG_AD_DEFAULT;
                                                                                                                                        } ?>');">
                </div>
                <h2><?= stripslashes(ucfirst($ad['ad']['title'])) ?></h2>
                <a href="<?= urlAd($ad['ad']['ID_ad']) ?>" class="show-ad">Ver anuncio</a>
                <?= $language['premium.info_subtitle'] ?>
            </div>
            <div class="box_premium">
                <h3><?= $language['premium.premium_1_title'] ?></h3>
                <div class="box_premium_img"><img src="src/images/premium1_example.png" alt="<?= $language['premium.premium_1_title'] ?>" /></div>
                <div class="info"><?= $language['premium.premium_1_info'] ?><?= $paypal_configuration[0]['time_1']; ?><?= $language['premium.premium_1_info2'] ?><?= $paypal_configuration[0]['time_1']; ?><?= $language['premium.premium_1_info3'] ?>
                    <span class="pay" id="paypal_1"><?= $language['premium.premium_1_button'] ?><?= $paypal_configuration[0]['price_1']; ?> <?= $paypal_currency_codes[0]['code_symbol']; ?></span>
                    <span class="paypal_logo"></span>
                </div>
            </div>
            <div class="box_premium">
                <h3><?= $language['premium.premium_2_title'] ?></h3>
                <div class="box_premium_img"><img src="src/images/premium2_example.png" alt="Destacar anuncio" /></div>
                <div class="info"><?= $language['premium.premium_2_info'] ?><?= $paypal_configuration[0]['time_2']; ?><?= $language['premium.premium_2_info2'] ?><?= $paypal_configuration[0]['time_2']; ?><?= $language['premium.premium_2_info3'] ?>
                    <span class="pay" id="paypal_2"><?= $language['premium.premium_2_button'] ?><?= $paypal_configuration[0]['price_2']; ?> <?= $paypal_currency_codes[0]['code_symbol']; ?></span>
                    <span class="paypal_logo"></span>
                </div>
            </div>
        </div>
<? }
} ?>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" id="paypal_DES">
    <input type='hidden' name='cmd' value='_xclick'>
    <input type='hidden' name='business' value='<?= $paypal_configuration[0]['paypal_email']; ?>'>
    <input type='hidden' name='item_name' value='<?= $language['premium.form_premium1'] ?> - <?= substr($ad['ad']['title'], 0, 20) ?>.. - <?= $ad['ad']['ID_ad'] ?>'>
    <input name="return" type="hidden" value="<?= getConfParam('SITE_URL'); ?>">
    <input name="notify_url" type="hidden" value="<?= getConfParam('SITE_URL'); ?>sc-includes/php/paypal/paypal_1.php?id=<?= $ad['ad']['ID_ad'] ?>">
    <input name="item_numer" type="hidden" value="<?= $ad['ad']['ID_ad'] ?>">
    <input type='hidden' name='amount' value='<?= $paypal_configuration[0]['price_1']; ?>'>
    <input type='hidden' name='page_style' value='primary'>
    <input type="hidden" name="landing_page" value="Billing" />
    <input type='hidden' name='no_shipping' value='1'>
    <input type='hidden' name='no_note' value='1'>
    <input type='hidden' name='currency_code' value='<?= $paypal_currency_codes[0]['currency_code']; ?>'>
    <input type='hidden' name='cn' value='PP-BuyNowBF'>
    <input type='hidden' name='lc' value='<?= COUNTRY_NAME; ?>'>
    <input type='hidden' name='country' value='<?= COUNTRY_NAME; ?>'>
</form>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" id="paypal_SUB">
    <input type='hidden' name='cmd' value='_xclick'>
    <input type='hidden' name='business' value='<?= $paypal_configuration[0]['paypal_email']; ?>'>
    <input type='hidden' name='item_name' value='<?= $language['premium.form_premium2'] ?> - <?= substr($ad['ad']['title'], 0, 20) ?>.. - <?= $ad['ad']['ID_ad'] ?>'>
    <input name="return" type="hidden" value="<?= getConfParam('SITE_URL'); ?>">
    <input name="notify_url" type="hidden" value="<?= getConfParam('SITE_URL'); ?>sc-includes/php/paypal/paypal_2.php?id=<?= $ad['ad']['ID_ad'] ?>">
    <input name="item_numer" type="hidden" value="<?= $ad['ad']['ID_ad'] ?>">
    <input type="hidden" name="landing_page" value="Billing" />
    <input type='hidden' name='amount' value='<?= $paypal_configuration[0]['price_2']; ?>'>
    <input type='hidden' name='page_style' value='primary'>
    <input type='hidden' name='no_shipping' value='1'>
    <input type='hidden' name='no_note' value='1'>
    <input type='hidden' name='currency_code' value='<?= $paypal_currency_codes[0]['currency_code']; ?>'>
    <input type='hidden' name='cn' value='PP-BuyNowBF'>
    <input type='hidden' name='lc' value='<?= COUNTRY_NAME; ?>'>
    <input type='hidden' name='country' value='<?= COUNTRY_NAME; ?>'>
</form>