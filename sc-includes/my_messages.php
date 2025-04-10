<?
$exito_div = false;

if(!isset($_GET['i'])){
	if(isset($_GET['d']) && isset($_GET['u'])){
		mysqli_query($Connection, "UPDATE sc_messages SET recibe_del=1 WHERE recibe='".$_SESSION['data']['ID_user']."' AND  envia='".$_GET['u']."' AND ID_ad='".$_GET['d']."'");
		mysqli_query($Connection, "UPDATE sc_messages SET envia_del=1 WHERE envia='".$_SESSION['data']['ID_user']."' AND  recibe='".$_GET['u']."' AND ID_ad='".$_GET['d']."'");
	}
?>
<?
$hilos=array();

$all_messages=mysqli_query($Connection, "SELECT m.*
FROM sc_messages AS m
INNER JOIN sc_ad AS a ON m.ID_ad = a.ID_ad
WHERE ((m.recibe = '".$_SESSION['data']['ID_user']."' AND m.recibe_del = 0)
       OR (m.envia = '".$_SESSION['data']['ID_user']."' AND m.envia_del = 0))
ORDER BY m.date_send DESC;");
while($each=mysqli_fetch_array($all_messages)){
		
		// SI ENVIO
		if($each['envia']==$_SESSION['data']['ID_user']){
			
			$hilo=array($each['ID_ad'],$each['recibe']);
		
		// SI RECIBO
		}elseif($each['recibe']==$_SESSION['data']['ID_user']){
			$hilo=array($each['ID_ad'],$each['envia']);
		}
		
		if(!in_array($hilo,$hilos)){
			$hilos[]=$hilo;
		}		
}
$blacklist_data = selectSQL('sc_blacklist', $w = array('ID_user' => $_SESSION['data']['ID_user']));
$blacklist = array();
foreach ($blacklist_data as $key => $value) {
	$blacklist[] = $value['user_banned'];
}

if(isset($_GET['b'])){
	$user = $_GET['b'];
	if(in_array($user, $blacklist)){
		$data = array('ID_user' => $_SESSION['data']['ID_user'], 'user_banned' => $user);
		if(deleteSQL('sc_blacklist', $data))
			$exito_div = 'Usuario Desbloqueado';
	}else{
		$data = array('ID_user' => $_SESSION['data']['ID_user'], 'user_banned' => $user);
		if(insertSQL('sc_blacklist', $data))
			$exito_div = 'Usuario Bloqueado';
	}
}
if($exito_div !== false){
	$blacklist_data = selectSQL('sc_blacklist', $w = array('ID_user' => $_SESSION['data']['ID_user']));
	$blacklist = array();
	foreach ($blacklist_data as $key => $value) {
		$blacklist[] = $value['user_banned'];
	}
}

$messages=mysqli_query($Connection, "SELECT * FROM sc_messages WHERE (recibe='".$_SESSION['data']['ID_user']."' AND recibe_del=0) OR (envia='".$_SESSION['data']['ID_user']."' AND envia_del=0) GROUP BY envia, ID_ad ORDER BY date_send DESC");
?>
<?php if( $exito_div !== false ): ?>
<div class="info_valid"><?=$exito_div?></div>
<?php endif ?>

<? if(count($hilos)>0){?>
<ul class="messages_list">
    <?
	for($i=0;$i<count($hilos);$i++){
			if($hilos[$i][1] == 0)
				continue;
			$id_otro=$hilos[$i][1];
			$ad=getDataAd($hilos[$i][0]);
			$last_message=mysqli_query($Connection, "SELECT * FROM sc_messages WHERE 
			((recibe='".$_SESSION['data']['ID_user']."' AND recibe_del=0 AND envia='".$id_otro."') OR 
			(envia='".$_SESSION['data']['ID_user']."' AND envia_del=0 AND recibe='".$id_otro."'))
			AND ID_ad='".$ad['ad']['ID_ad']."' ORDER BY date_send DESC LIMIT 1");
			$message_last=mysqli_fetch_array($last_message);
			
			if($message_last['envia']==$_SESSION['data']['ID_user']){
				$title_msh=formatName($_SESSION['data']['name']);
				$respuesta=false;
			}elseif($message_last['recibe']==$_SESSION['data']['ID_user']){
				$name_envia=selectSQL("sc_user",$wm=array('ID_user'=>$id_otro));
				$title_msh= $name_envia[0]['name'];
				$respuesta=true;
			}
			if(count($ad['images'])>0) $image =true; else $image =false;
			
			$same_messages=mysqli_query($Connection, "SELECT ID_message FROM sc_messages WHERE 
			( (recibe='".$_SESSION['data']['ID_user']."' AND recibe_del=0 AND envia='".$id_otro."') OR 
			(envia='".$_SESSION['data']['ID_user']."' AND envia_del=0 AND recibe='".$id_otro."') )
			AND ID_ad='".$ad['ad']['ID_ad']."'") or die(mysqli_error($Connection));
			$tot_messages=mysqli_num_rows($same_messages);
			
			$messages_no_read=mysqli_query($Connection, "SELECT ID_message FROM sc_messages WHERE leido=1 AND
			(recibe='".$_SESSION['data']['ID_user']."' AND recibe_del=0 AND envia='".$id_otro."')
			AND ID_ad='".$ad['ad']['ID_ad']."'") or die(mysqli_error($Connection));;
			$tot_messages_no_read=mysqli_num_rows($messages_no_read);
		?>
		<li <? if($respuesta){?> class="entry"<? } ?>>
        	<? if($tot_messages_no_read>0){?>
                <span class="no-read">
				<?=$tot_messages_no_read;?>
                </span>
			<? } ?>
        	<div class="img_msj">
				<?php 
					$orientation = getImgOrientation($ad['images'][0]['name_image']);
				?>
				<div class="imageAdMin <?=$orientation?>">
					<div class="bg-img-item" style="background: url('<?=IMG_ADS;?><? if($image){ ?><?=$ad['images'][0]['name_image']; ?><? }else{ echo IMG_AD_DEFAULT; } ?>');"></div>
					<a href="<?=urlAd($ad['ad']['ID_ad']);?>" title="<?=stripslashes($ad['ad']['title']); ?>">
					<img itemprop="image" src='<?=IMG_ADS;?><? if($image){ ?><?=$ad['images'][0]['name_image']; ?><? }else{ echo IMG_AD_DEFAULT_MIN; } ?>' alt='<?=stripslashes($ad['ad']['title']); ?>'/>
					</a>
				</div>
            </div>
            <div class="info_msj">
                <span class="ad_msj">  <?=$ad['ad']['title'];?> - Ref. <?=$ad['ad']['ref'];?></span>
                <span class="options_msj">                
                <a href="mis-mensajes/?i=<?=$ad['ad']['ID_ad'];?>&u=<?=$id_otro?>">
                Ver conversación (<?=$tot_messages?>)
                </a>
				<?php if( $id_otro != 0 ): ?>
					<a href="mis-mensajes/?b=<?=$id_otro?>" class="delete" >
					<?php if(in_array($id_otro, $blacklist)) print "Desbloquear Usuario"; else print "Bloquear Usuario"; ?>
				</a>
				<?php endif ?>
				<a href="mis-mensajes/?d=<?=$ad['ad']['ID_ad'];?>&u=<?=$id_otro?>" class="delete">Eliminar</a>
				</span>
            </div>
        </li>
		<?	}
    ?>
</ul>
<? }
else{ ?>
<div class="no_item_founds">
<p>No tienes ningún mensaje en este momento.</p>
</div>
<? } ?>
<? }else include("view_message.php");?>