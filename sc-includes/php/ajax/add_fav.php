<?
///*     ScriptClasificados v8.0                     *///
///*     www.scriptclasificados.com                  *///
///*     Created by cyrweb.com. All rights reserved. *///
///*     Copyright 2009-2017                         *///

include("../../../settings.inc.php");
if(isset($_COOKIE['CONFIG']))
	$config = json_decode($_COOKIE['CONFIG']);

if($config->preferencias == true){

	if(isset($_GET['i'])){
		if(count(selectSQL("sc_ad",$d=array('ID_ad'=>$_GET['i']),''))!=0){
			if(isset($_COOKIE['fav'][''.$_GET['i'].''])){
				if(setcookie('fav['.$_GET['i'].']',0,time()-24*3600*15, '/')){
					if(isset($_SESSION['data']))
						deleteSQL('sc_favorites', array('ID_ad' => $_GET['i'], 'ID_user' => $_SESSION['data']['ID_user']));

					echo "2";
				} 
			}else{
				if(setcookie('fav['.$_GET['i'].']',$_GET['i'], time()+24*3600*15, '/')){
					if(isset($_SESSION['data']))
						insertSQL('sc_favorites', array('ID_ad' => $_GET['i'], 'ID_user' => $_SESSION['data']['ID_user']));
					echo "1";
				}else{
					echo "0";
				}
			}
		}else{
			echo "0";
		}
	}else{
		echo "0";
	}
	
}else
	print "0";



