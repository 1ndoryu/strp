<?php

///*     ScriptClasificados v8.0                     *///

///*     www.scriptclasificados.com                  *///

///*     Created by cyrweb.com. All rights reserved. *///

///*     Copyright 2009-2017                         *///



include("../../../settings.inc.php");



if(isset($_POST['m']) && isset($_POST['p']) && isset($_POST['r'])){


	if(!empty($_POST['m']) && !empty($_POST['p'])){

	if($_POST['r'] == 'true')
		$r = true;
	else
		$r = false;

	$result = login($_POST['m'], $_POST['p'],'', $r);

	if($result==2) echo $language['login.txt_1'];

	if($result==0) echo $language['login.txt_2'];

	if($result==1) echo $language['login.txt_3'];

	if($result==4) echo "Debes confirmar tu correo electrónico";

	if($result==3){
		echo "1";
		updateSQL('sc_user', $d = array('IP_user' => get_client_ip()), $w = array('mail' => $_POST['m']));
	}

	}else echo $language['login.txt_5'];

}




