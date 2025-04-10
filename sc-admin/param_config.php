<?
$config_change=false;
if(isset($_POST['save_config'])){
	$config_save=array(
		'SITE_NAME'=>$_POST['SITE_NAME'],
		'SITE_URL'=>$_POST['SITE_URL'],
		'CONTACT_MAIL'=>$_POST['CONTACT_MAIL'],
		'NOTIFY_EMAIL'=>$_POST['NOTIFY_EMAIL'],
		'SSL_OPTION'=>$_POST['SSL_OPTION'],
		'DOMAIN_PREFER'=>$_POST['DOMAIN_PREFER'],
		// Items config
		'ITEM_TIME_ON'=>$_POST['ITEM_TIME_ON'],
		'ITEM_TIME_NOTICE'=>$_POST['ITEM_TIME_NOTICE'],
		'REVIEW_ITEM'=>$_POST['REVIEW_ITEM'],
		'POST_ITEM_REG'=>$_POST['POST_ITEM_REG'],
		'MAX_PHOTOS_AD'=>$_POST['MAX_PHOTOS_AD'],
		'ITEM_PER_PAGE'=>$_POST['ITEM_PER_PAGE'],
		'ITEM_FEED_TOT'=>$_POST['ITEM_FEED_TOT'],
		'ITEM_LIMIT'=>$_POST['ITEM_LIMIT'],
		'ITEM_LIMIT_1'=>$_POST['ITEM_LIMIT_1'],
		'ITEM_LIMIT_2'=>$_POST['ITEM_LIMIT_2'],
		'SEO_TITLE'=>$_POST['SEO_TITLE'],
		'SEO_DESC'=>$_POST['SEO_DESC'],
		'SEO_KEYWORD'=>$_POST['SEO_KEYWORD'],
		'EVENTS_VALIDATE_LIMIT' => $_POST['EVENTS_VALIDATE_LIMIT'],
		'RE_LIMIT'=>$_POST['RE_LIMIT'],
		'RE_COST'=>$_POST['RE_COST'],
		'EDIT_LIMIT'=>$_POST['EDIT_LIMIT'],
		'ITEM_FORMAT_PRICE'=>$_POST['ITEM_FORMAT_PRICE'],
		'ITEM_CURRENCY_CODE'=>$_POST['ITEM_CURRENCY_CODE'],
		'BANNER_MIDDLE_POS'=>$_POST['BANNER_MIDDLE_POS'],
		// Google Analytics
		'ANALYTICS_ID'=>$_POST['ANALYTICS_ID'],
		// Social links
		'FB_PAGE_LINK'=>$_POST['FB_PAGE_LINK'],
		'TW_PAGE_LINK'=>$_POST['TW_PAGE_LINK'],
		'GP_PAGE_LINK'=>$_POST['GP_PAGE_LINK'],		
	);
	if($config_save['SSL_OPTION']==1) httpsOn(); else httpsOff();
	if($config_save['DOMAIN_PREFER']==1) domainWWWOn(); else domainWWWOff();
	foreach($config_save as $param => $value){
		updateSQL("sc_config",$s=array('value_param'=>$value),$w=array('name_param'=>$param));
	}
	$config_change=$language_admin['param_config.save_changes'];
}
?>
<h2><?=$language_admin['param_config.site_config']?></h2>
<? if($config_change!==FALSE){?>
<div class="info_valid"><i class="fa fa-check-circle" aria-hidden="true"></i><?=$config_change?></div>
<? } ?>
<form action="<? $_SERVER['PHP_SELF'];?>" method="post" class="param_form">
<legend><?=$language_admin['param_config.site_details']?></legend>
<?
$param_config_form=array('SITE_NAME','SITE_URL', 'CONTACT_MAIL', 'NOTIFY_EMAIL', 'SEO_TITLE', 'SEO_DESC', 'SEO_KEYWORD');
for($i=0;$i<count($param_config_form);$i++)
	echo '<div><label>'.getConfText($param_config_form[$i]).'</label>
		  <input type="text" name="'.$param_config_form[$i].'" value="'.getConfParam($param_config_form[$i]).'"></div>
		  ';
?>
<? formRadioConfigParam('SSL_OPTION'); ?>
<div><label><?=getConfText('DOMAIN_PREFER');?></label>
<label class="radio"><input type="radio" name="DOMAIN_PREFER" value="1" <? if(getConfParam('DOMAIN_PREFER')==1) echo 'checked';?>> Con www</label>
<label class="radio"><input type="radio" name="DOMAIN_PREFER" value="2" <? if(getConfParam('DOMAIN_PREFER')==2) echo 'checked';?>> Sin www</label>
</div>

<legend><?=$language_admin['param_config.ads_config']?></legend>
<? formRadioConfigParam('REVIEW_ITEM'); ?>
<? formRadioConfigParam('POST_ITEM_REG'); ?>
<?
$param_config_form=array('ITEM_TIME_ON', 'ITEM_TIME_NOTICE','MAX_PHOTOS_AD','ITEM_PER_PAGE','ITEM_FEED_TOT','BANNER_MIDDLE_POS','ITEM_LIMIT','ITEM_LIMIT_1','ITEM_LIMIT_2','RE_LIMIT','RE_COST','EDIT_LIMIT', 'EVENTS_VALIDATE_LIMIT');
for($i=0;$i<count($param_config_form);$i++)
	echo '<div><label>'.getConfText($param_config_form[$i]).'</label>
		  <input type="text" name="'.$param_config_form[$i].'" value="'.getConfParam($param_config_form[$i]).'"></div>
		  ';
?>
<div><label><?=getConfText('ITEM_FORMAT_PRICE')?></label>
		  <select name="ITEM_FORMAT_PRICE">
          	<option value="1" <? if(getConfParam('ITEM_FORMAT_PRICE')==1) echo 'selected'; ?>>1.234,56 <?=getConfParam('ITEM_CURRENCY_CODE')?></option>
          	<option value="2" <? if(getConfParam('ITEM_FORMAT_PRICE')==2) echo 'selected'; ?>>1,234.56 <?=getConfParam('ITEM_CURRENCY_CODE')?></option>
          	<option value="3" <? if(getConfParam('ITEM_FORMAT_PRICE')==3) echo 'selected'; ?>><?=getConfParam('ITEM_CURRENCY_CODE')?> 1,234.56</option>
          	<option value="4" <? if(getConfParam('ITEM_FORMAT_PRICE')==4) echo 'selected'; ?>><?=getConfParam('ITEM_CURRENCY_CODE')?> 1.234,56</option>
          </select>
          </div>
<div>
<label><?=getConfText('ITEM_CURRENCY_CODE')?></label>
		  <input type="text" name="ITEM_CURRENCY_CODE" value="<?=getConfParam('ITEM_CURRENCY_CODE')?>"></div>
          
<legend>Google Analytics</legend>
<?
$param_config_form=array('ANALYTICS_ID');
for($i=0;$i<count($param_config_form);$i++)
	echo '<div><label>'.getConfText($param_config_form[$i]).'</label>
		  <input type="text" name="'.$param_config_form[$i].'" value="'.getConfParam($param_config_form[$i]).'"></div>
		  ';
?>
<legend><?=$language_admin['param_config.social_pages']?></legend>
<?
$param_config_form=array('FB_PAGE_LINK','TW_PAGE_LINK','GP_PAGE_LINK');
for($i=0;$i<count($param_config_form);$i++)
	echo '<div><label>'.getConfText($param_config_form[$i]).'</label>
		  <input type="text" name="'.$param_config_form[$i].'" value="'.getConfParam($param_config_form[$i]).'"></div>
		  ';
?>
<input name="save_config" type="submit" id="save_config" value="<?=$language_admin['param_config.button_save']?>">
</form>