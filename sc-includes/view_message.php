<?
$enviado=false;
$error_div = false;
$blacklist_data = selectSQL('sc_blacklist', $w = array('ID_user' => $_SESSION['data']['ID_user']));
$blacklist = array();
foreach ($blacklist_data as $key => $value) {
	$blacklist[] = $value['user_banned'];
}

$ad=getDataAd($_GET['i']);
if(isset($_POST['id_ad'])){
	if(verifyFormToken('answ_msg',$_POST['token'])){
		if($_POST['respuesta']!=""){
			if(!in_array($_POST['recibe'],$blacklist)){
				if(insertSQL("sc_messages",$dats=array('ID_ad'=>$_POST['id_ad'],'message'=>addslashes($_POST['respuesta']),'recibe'=>$_POST['recibe'],'envia'=>$_SESSION['data']['ID_user'],'date_send'=>time()))){ 
					$enviado=true;
				}
			}else{
				$error_div = "usuario bloqueado";
			}
		}
	}
}
$id_otro=$_GET['u'];
$messages=mysqli_query($Connection, "SELECT * FROM sc_messages WHERE 
			( (recibe='".$_SESSION['data']['ID_user']."' AND recibe_del=0 AND envia='".$id_otro."') OR 
			(envia='".$_SESSION['data']['ID_user']."' AND envia_del=0 AND recibe='".$id_otro."') )
			AND ID_ad='".$ad['ad']['ID_ad']."' ORDER BY date_send DESC");
?>
<? if($enviado){ ?>
	<div class="info_valid">Mensaje enviado!</div>
<?php } ?>
<?php if( $error_div !== false ): ?>
   <div class="info_invalid"><?= $error_div ?></div>
<?php endif ?>
<h1 class="mt-3">
	<span class="messsage_title">
	<?=$ad['ad']['title'];?> - Ref:<?=$ad['ad']['ID_ad'];?>
	</span>
	<a class="back_link" href="mis-mensajes/">Volver a mis mensajes</a>
</h1>
<div class="respuesta_msj">
<form method="post" action="mis-mensajes/?i=<?=$ad['ad']['ID_ad'];?>&u=<?=$id_otro;?>">
<textarea placeholder="Escribe tu mensaje..." name="respuesta" <?php if(in_array($id_otro, $blacklist)) print "readonly" ?>><?php if(in_array($id_otro, $blacklist)) print "Este usuario esta bloqueado y no podrá ni enviar ni recibir mensajes"; else print ''; ?></textarea>
<input type="hidden" name="recibe" value="<?=$id_otro;?>">
<input type="hidden" name="id_ad" value="<?=$ad['ad']['ID_ad'];?>">
<input type="submit" value="Responder">
<? $token_q = generateFormToken('answ_msg'); ?>
<input type="hidden" name="token" id="token" value="<?=$token_q;?>">
</form>
</div>
<? if(mysqli_num_rows($messages)!=0){?>
<ul class="messages_list view">
<? while($message=mysqli_fetch_array($messages)){
			if($message['envia']==$_SESSION['data']['ID_user']){
				$title_msh=formatName($_SESSION['data']['name']);
				$respuesta=false;
				$id_u=$message['envia'];
			}else{
				$name_envia=selectSQL("sc_user",$wm=array('ID_user'=>$message['envia']));
				$title_msh= formatName($name_envia[0]['name']);
				$respuesta=true;
			}
		?>
		<li <? if($respuesta){?> class="entry"<? } ?>>
        	<div class="user_item_info"><?if ($message['envia']==0){ ?>
            <span class="user_item_photo" style="background-image:url(<?=getPhotoUser($message['envia'])?>"></span><? }else{ ?>            <a href="usuario/<?=$message['envia']?>"><span class="user_item_photo" style="background-image:url(<?=getPhotoUser($message['envia'])?>"></span></a><? } ?>
            </div>
            <div class="msj_content">
            	<a href="usuario/<?=$message['envia']?>" class="name_user"><?=$title_msh?></a>
                <span class="text_msj"><?=stripslashes($message['message']);?></span>
                <span class="date_msj"><?=date("d/m/Y H:i",$message['date_send']);?></span>
            </div>
        </li>
		<?	}
    ?>
</ul>
<? } ?>
<?
mysqli_query($Connection, "UPDATE sc_messages SET leido=0 WHERE leido=1 AND (recibe='".$_SESSION['data']['ID_user']."' AND recibe_del=0) AND ID_ad='".$ad['ad']['ID_ad']."'");
?>