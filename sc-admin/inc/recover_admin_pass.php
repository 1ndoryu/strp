<?php

///*     ScriptClasificados v8.0                     *///

///*     www.scriptclasificados.com                  *///

///*     Created by cyrweb.com. All rights reserved. *///

///*     Copyright 2009-2017                         *///



include("../../settings.inc.php");

if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){



if(isset($_POST['ma'])){

	$user=selectSQL("sc_config",$w=array('name_param'=>'ADMIN_MAIL'));
    
	if(count($user)!=0){

		if($user[0]['value_param'] == $_POST['ma']){
			$codec = randomString(6);
			$codec = md5($codec);
			updateSQL("sc_config",$datos=array('value_param'=>$codec),$ww=array('name_param'=>'ADMIN_TOKEN'));

			if(adminRecPass($codec, $_POST['ma'])) echo "1"; else echo $language['recover.txt_error_1'];
		}else
			print "Correo Incorrecto";
		//$pass = randomString(6);

		//$codec= md5($pass);

		//updateSQL("sc_user",$datos=array('pass'=>$codec),$ww=array('ID_user'=>$user[0]['ID_user']));

		//if(mailRecPass(formatName($user[0]['name']),$user[0]['mail'],$user[0]['pass'])) echo "1"; else echo $language['recover.txt_error_1'];

	}else echo $language['recover.txt_error_2'];

}else echo $language['recover.txt_error_3'];



}

