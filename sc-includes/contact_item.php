<?
require("../settings.inc.php");
if(isset($_SESSION['data'])){
?>
<div class="contact_div">
<span id="close"><i class="fa fa-times-circle-o" aria-hidden="true"></i></span>
<? if(isset($_POST['id_product'])){
	$ad=getDataAd($_POST['id_product']);
	if($ad['ad']['ID_user']!=$_SESSION['data']['ID_user']){
?>
<h2>Contacta con <?=formatName($ad['user']['name'])?></h2>
<div id="contactEmail"></div>
<form class="contact-product-form">
    <textarea name="msg_c" id="msg_c">¡Hola! Me gustaría recibir más información sobre tu anuncio "<?=$ad['ad']['title'];?>"...</textarea>
    <div class="error_msg" id="error_msg_c"><?=$language['item.error_message']?></div>
    <input name="contact_item_btn" class="btn_large" type="button" id="contact_item_btn" value="<?=$language['item.button_send']?>">
    <input type="hidden" value="<?=$ad['ad']['ID_ad'];?>" id="id_ad_contact" name="id_ad_contact">
    <?=$language['item.info_terms']?>
</form>
<? }else{ ?>
<h2>Contacta con <?=formatName($ad['user']['name'])?></h2>
<div class="info_invalid">¡No puedes contactarte a ti mismo!</div>
<? } ?>
<? } ?>
</div>
<? }else include("access.php");?>