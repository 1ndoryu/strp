<?
$change_pass=false;
$error_pass=false;
$data_change=false;
if(isset($_POST['name_account'])){
	updateSQL("sc_user",$ma=array('name'=>formatName($_POST['name_account']),'phone'=>$_POST['phone_account'], 'mail'=> $_POST['mail_account']),$wm=array('ID_user'=>$_SESSION['data']['ID_user']));
	$data_change=$language['account_data.save_data'];
}

if(isset($_POST['pass1_change'])){

	if($_POST['pass1_change']==$_POST['pass2_change']){
		//md5($_POST['pass1_change']))
		updateSQL("sc_user",$ma=array('pass'=>($_POST['pass1_change'])),$wm=array('ID_user'=>$_SESSION['data']['ID_user']));
		$change_pass=$language['account_data.save_pass'];
	}else $error_pass=$language['account_data.error_pass1'];
	
}
updateLogin();
?>
<? if($data_change!==FALSE){?>
<div class="info_valid"><?=$data_change?></div>
<? } ?>
<form id="data_account" class="fm" method="post" action="<?=$urlfriendly['url.my_account']?>">
<fieldset class="pb-0 mb-0">
<div class="row">
	<? if($_SESSION['data']['banner_img']==""){?>
	<div id="photo_banner_user" class="photo_banner free">
        <input name="userBanner" id="userBanner" type="file" class="photoFile" />
    </div>
    <? }else{ ?>
	<div id="photo_banner_user" class="photo_banner">
        <span class="removeImg"><i class="fa fa-trash-o" aria-hidden="true"></i></span>
        <div class="user_image_avatar" style="background:url(<?=getConfParam('SITE_URL')?><?=IMG_USER?><?=$_SESSION['data']['banner_img']?>"></span>
        <input type="hidden" name="banner_user" value="<?=$_SESSION['data']['banner_img'];?>">
    </div>
    <? }?>
</div>
<div class="row">
<div class="col_lft"><label><?=$language['account_data.label_name']?></label></div>
<div class="col_rgt"><input name="name_account" class="account-input sm" required type="text" id="name_account" readonly size="150" maxlength="150" value="<?=$_SESSION['data']['name']?>"/>
<div class="error_msg" id="error_name"><?=$language['account_data.error_name']?></div>
</div>
</div>
<div class="row">
<div class="col_lft"><label><?=$language['account_data.label_mail']?></label></div>
<div class="col_rgt"><input name="mail_account" class="account-input sm" required readonly type="email" id="mail_account" size="150" maxlength="150" value="<?=$_SESSION['data']['mail']?>"/>
<div class="error_msg" id="error_mail">Escriba un correo electronico valido</div>
</div>
</div>
<div class="row">
<div class="col_lft"><label>Teléfono</label></div>
<div class="col_rgt"><input name="phone_account"  type="tel" readonly id="phone_account" size="150" maxlength="30" value="<?=$_SESSION['data']['phone']?>"/><div class="error_msg" id="error_phone">Indica un teléfono válido</div>
</div>
</div>
</fieldset>
<!--
<input type="button" class="button" id="butDataAccount" value="<?=$language['account_data.button_update']?>"/>
	-->
</form>
<? if($error_pass!==FALSE){?>
<div class="info_invalid"><?=$error_pass?></div>
<? } ?>
<? if($change_pass!==FALSE){?>
<div class="info_valid"><?=$change_pass?></div>
<? } ?>
<form id="pass_account" class="fm pt-0" method="post" action="<?=$urlfriendly['url.my_account']?>">
<fieldset class="pt-0 mb-0">
<div class="row">
	<div class="row">
		<div class="col_lft"><label><?=$language['account_data.label_new_pass']?></label></div>
		<div class="col_rgt"><input name="pass1_change" class="account-input" type="password" id="pass1_change" size="20" maxlength="20" /><div class="error_msg" id="error_pass1_change"><?=$language['account_data.error_new_pass']?></div>
		</div>
	</div>
	<div class="row">
		<div class="col_lft"><label><?=$language['account_data.label_new_pass2']?></label></div>
		<div class="col_rgt"><input name="pass2_change" class="account-input" type="password" id="pass2_change" size="20" maxlength="20" /><div class="error_msg" id="error_pass2_change"><?=$language['account_data.error_new_pass2']?></div>
		</div>
	</div>
</div>

</fieldset>
<input type="button" class="button" id="butChangePass" class="account-input" value="<?=$language['account_data.button_change']?>"/>
</form>
<div class="delete_account">
<?=$language['account_data.txt_delete']?>
</div>
