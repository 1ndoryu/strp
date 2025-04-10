<?php
///*     ScriptClasificados v8.0                     *///
///*     www.scriptclasificados.com                  *///
///*     Created by cyrweb.com. All rights reserved. *///
///*     Copyright 2009-2017                         *///
include("../../../settings.inc.php");

	if(isset($_FILES['userImage']) && $_FILES['userImage']['error'] == 0 && isset($_SESSION['data'])) {
		$resultado=uploadImage($_FILES['userImage'],IMG_USER,-1,false);
		if($resultado!==false)
		{
			updateSQL("sc_user",$dsa=array('banner_img'=>$resultado),$w=array('ID_user'=>$_SESSION['data']['ID_user']));
	?>
    <span class="removeImg"><i class="fa fa-trash-o" aria-hidden="true"></i></span>
	<div class="user_image_avatar" style="background:url(<?=getConfParam('SITE_URL')?><?=IMG_USER?><?=$resultado?>"></span>
    <input type="hidden" name="banner_user" value="<?=$resultado;?>">
	<?php
    	}
}


?>