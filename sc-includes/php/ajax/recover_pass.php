<?

///*     ScriptClasificados v8.0                     *///

///*     www.scriptclasificados.com                  *///

///*     Created by cyrweb.com. All rights reserved. *///

///*     Copyright 2009-2017                         *///



include("../../../settings.inc.php");


if(isset($_POST['ma'])){

	$user=selectSQL("sc_user",$w=array('mail'=>$_POST['ma']));

	if(count($user)!=0){

		//$pass = randomString(6);
	

		//$codec= md5($pass);

		//updateSQL("sc_user",$datos=array('pass'=>$codec),$ww=array('ID_user'=>$user[0]['ID_user']));

		if(mailRecPass(formatName($user[0]['name']),$user[0]['mail'],$user[0]['pass'])) print "1"; else echo $language['recover.txt_error_1'];

	}else echo $language['recover.txt_error_2'];

}else echo $language['recover.txt_error_3'];





