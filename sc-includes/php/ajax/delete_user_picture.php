<?php
///*     ScriptClasificados v8.0                     *///
///*     www.scriptclasificados.com                  *///
///*     Created by cyrweb.com. All rights reserved. *///
///*     Copyright 2009-2017                         *///

include("../../../settings.inc.php");

if(isset($_POST['name_image']) && isset($_SESSION['data'])) {
	$user=selectSQL("sc_user",$wm=array('ID_user'=>$_SESSION['data']['ID_user']));
	if(count($user)!=0){
			@unlink(ABSPATH . IMG_USER . $user[0]['banner_img']);
			@unlink(ABSPATH . IMG_USER . min_image($user[0]['banner_img']));
			updateSQL("sc_user",$wm=array('banner_img'=>""),$w=array('ID_user'=>$_SESSION['data']['ID_user']));
			echo 1;
	}else echo 2;
}else echo 3;


?>