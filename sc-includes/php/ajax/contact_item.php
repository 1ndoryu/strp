<?
///*     ScriptClasificados v8.0                     *///
///*     www.scriptclasificados.com                  *///
///*     Created by cyrweb.com. All rights reserved. *///
///*     Copyright 2009-2017                         *///

include("../../../settings.inc.php");

	if(isset($_POST['msg']) && isset($_POST['i']) && isset($_POST['n']) && isset($_POST['p']) && isset($_POST['m'])){

		$ad=getDataAd($_POST['i']);
		if(isset($_SESSION['data']['ID_user']) && $_SESSION['data']['ID_user']!==$ad['ad']['ID_user']){
			if(!addMessage($_SESSION['data']['ID_user'],$_POST['msg'],$_POST['i'])){
				echo '<div class="info_invalid">El usuario te bloqueo</div>';
				exit;
			}
		}
		$user = $_SESSION['data']['ID_user'];
		mysqli_query($Connection, "UPDATE sc_ad SET contact_times=contact_times+1 WHERE ID_ad=".$_POST['i']);
		echo '<div class="info_valid">'.$language['contact_item.txt_successfull'].'</div>';		        
		if(isset($user) && is_numeric($user))
			Notice::addNotice($ad['ad']['ID_user'],"Nueva notificación" ,  "Tienes un nuevo mensaje", "/mis-mensajes/?i=".$ad['ad']['ID_ad']."&u=".$user, array('ID_ad' => $_POST['i'], 'ID_user' => $user));

		mailAdContact(formatName($_POST['n']),$_POST['m'],$_POST['p'],$_POST['msg'],$_POST['i']);
	}else echo '<div class="info_invalid">'.$language['contact_item.error_1'].'</div>';
	

?>