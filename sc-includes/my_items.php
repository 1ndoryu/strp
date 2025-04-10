<?php
$deleted_ad=false;
$renoved_ad=false;
$edited_ad=false;
$del_list=false;
$BOLSA_ID = getBolsaID();


$DATAJSON = array();
$DATAJSON['renovation_price'] = getConfParam('RE_COST');

// if(isset($_GET['re'])){ 
// 	renoveAd($_GET['re']); $renoved_ad=true; 
// }

if(isset($_GET['d']))
{ 
	trashAd($_GET['d'], Motivo::Usuario);
	$deleted_ad=true;
}
if(isset($_GET['edit'])){ $edited_ad=true; }
if(isset($_GET['delist'])){
	if(deleteListing($_GET['delist']))
		$sc_msg = "tu anuncio fue desactivado";
}

if(isset($_GET['delprem'])){
	if(deletePremium($_GET['delprem']))
		$sc_msg = "tu anuncio fue desactivado";
}

if(isset($_GET['delbanner'])){
	if(deleteBanner($_GET['delbanner']))
		$sc_msg = "banner desactivado";

}

if(isset($_GET['paysuccess']))
{
	$sc_msg = $_GET['paysuccess'] != "" ? $_GET['paysuccess'] : "Servicios activados correctamente";
}

if(isset($_GET['payerror']))
{
	$sc_msg_invalid = $_GET['payerror'] != "" ? $_GET['payerror'] : "Error al activar servicios";
}

if(isset($_GET['editlimit'])){
	$sc_msg_invalid = "no puedes editar mas de ".getConfParam('EDIT_LIMIT')." anuncio cada 30 dias.";
}

?>
<? if($edited_ad){?><div class="info_valid"><?=$language['my_items.info_updated']?></div><? }?>
<? if($deleted_ad){?><div class="info_valid"><?=$language['my_items.info_deleted']?></div><? }?>
<? if($renoved_ad){?><div class="info_valid"><?=$language['my_items.info_renoved']?></div><? }?>


<?php if(isset($sc_msg)): ?>
	<div class="info_valid" style="max-width:450px;"><?= $sc_msg?></div>
<?php endif ?>
<?php if(isset($sc_msg_invalid)): ?>
	<div class="info_invalid"><?= $sc_msg_invalid?></div>
<?php endif ?>

<?
$motivo = Motivo::Cancelado;
$motivo1 = Motivo::Desactivado;
$motivo2 = Motivo::Repetido;
$motivo3 = Motivo::INCUMPLIMIENTO;
$lapso = time() - 2*24*3600;
$user_ads= rawQuerySQL("SELECT * FROM sc_ad WHERE (active = 1 OR active = 2) AND (trash = 0 OR motivo = $motivo OR motivo = $motivo1 OR (motivo = $motivo2 AND date_trash > $lapso) OR (motivo = $motivo3 AND date_trash > $lapso) ) AND ID_user = '".$_SESSION['data']['ID_user']."'  ORDER BY date_ad DESC");
$user_ads_count = count($user_ads);
if($user_ads_count!=0){?>
<div class="my_items_search">
	<div>
		<input type="text" class="form-control" onkeyup="searchItems(this.value)" placeholder="Buscar anuncio" >
	</div>
	<div class="user_info">
		<span class="r-show"><?=$_SESSION['data']['mail']?></span>
	
	</div>
	<div class="item_info">
			<a href="/terminos-y-condiciones-de-uso" class="info_link r-show">
				<b>Normas</b> de publicación
			</a>
			<?php if($_SESSION['data']['extras'] != 0): ?>
				<span class="discount_counter" onclick="$('.act-service').toggle()" title="Anuncios Restantes">
					Activar anuncios
					<span id="count_extras">
						<?=$_SESSION['data']['extras']?>
					</span>
				</span>
				
			<?php endif; ?>
			<?php if($_SESSION['data']['rol'] == UserRole::Publicista || $_SESSION['data']['rol'] == UserRole::Centro): ?>
			
			<?php else: ?>
			  <img onclick="$('#discount_info').attr('open', true)" src="<?=Images::getImage("super-descuento.png")?>" class="discount_ads" alt="Descuento de publicista">
		  	<?php endif ?>
	</div>
</div>
<div class="my_items_info no-mb">
	
	<span class="info"><?=$_SESSION['data']['mail']?> Total de anuncios: <?=count($user_ads)?></span>
	<!-- <span class="options"><?=$_SESSION['data']['mail']?></span> -->
	<a href="/terminos-y-condiciones-de-uso" class="info_link">
		<b>Normas</b> de publicación
	</a>
</div>
<form action="" id="form_my_item">
<ul class="my_items_list">
<?
for($i=0;$i<count($user_ads);$i++){
	$review_on = false;
	$image = false;
	$ad = getDataAd($user_ads[$i]['ID_ad']);
	if( count($ad['images']) != 0 ) $image = true;
	if($ad['ad']['review'] != 0 ) $review_on=true;
	if($ad['ad']['motivo'] == Motivo::Desactivado) $review_on = true;
?>
<li class="item">
	<?php if($ad['ad']['review'] == 0 && $ad['ad']['ID_order'] != 0): ?>
		<div class="cover">
			<h6>Tu anuncio ha sido validado.</h6>
			<p>Completa el pago para activar tu anuncio.</p>
		</div>
	<?php endif ?>
	<?php 
	$edited_time = time() - $ad['ad']['date_edit'];
	$AD_DISCARD = false;
	$AD_DISCARD = ($ad['ad']['discard'] == 1 && $edited_time < 3600*24*2);
	if($AD_DISCARD): ?>
	  	<div class="cover">
			<h6>Tu anuncio no ha sido editado.</h6>
			<p><?=$ad['ad']['trash_comment']?></p>
		</div>
	<?php endif ?>
	<?php if($ad['ad']['trash'] == 1): ?>
		<div class="cover">
				<?php if($ad['ad']['motivo'] == Motivo::Desactivado): ?>
					<h6 >Tu anuncio ha sido desactivado.</h6>
					<p><?=$ad['ad']['trash_comment']?></p>
				<?php endif ?>
				<?php if($ad['ad']['motivo'] == Motivo::Cancelado): ?>
					<h6>Tu anuncio no ha sido publicado.</h6>
					<p><?=$ad['ad']['trash_comment']?></p>
				<?php endif ?>
				<?php if($ad['ad']['motivo'] == Motivo::INCUMPLIMIENTO): ?>
					<h6>Tu anuncio no ha sido aprobado.</h6>
					<p>Tu anuncio no cumple con las condiciones de uso.</p>
				<?php endif ?>
				<?php if($ad['ad']['motivo'] == Motivo::Repetido): ?>
					<h6>Tu anuncio no ha sido aprobado.</h6>
					<p>Tu anuncio ya había sido publicado.</p>
				<?php endif ?>
				<?php if($ad['ad']['motivo'] != Motivo::Desactivado): ?>
					<a target="_blank" href="<?=getConfParam('SITE_URL')?>terminos-y-condiciones-de-uso">Condiciones de uso</a>
				<?php endif ?>
		</div>
	<?php endif ?>
	<div class="item-head">
		<div>
			<span>
				<?php if(!$review_on): ?>
					<input type="checkbox" name="idad" value="<?=$ad['ad']['ID_ad']?>" class="idad">
				<?php endif ?>
				Ref. del anuncio <span class="searchable"><?=$ad['ad']['ref']?></span>
			</span>
			<div class="item-active-services">
				
				<?php if($ad['ad']['premium1'] == 1):
					
					?>
					<div class="active-service" title="Exp: <?= date('d.m h:i', $ad['ad']['date_premium1'])?>" >
					TOP
					
				</div>
				<?php endif ?>
				<?php if($ad['ad']['premium2'] == 1): ?>
					
				<?php endif ?>
				<?php if($ad['ad']['premium3'] == 1): ?>
					<div class="active-service" title="Exp: <?= date('d.m h:i', $ad['ad']['date_premium3'])?>">
						Destacado
						
					</div>
				<?php endif ?>
				<?php if($ad['ad']['renovable'] == renovationType::Autorenueva): ?>
					<div class="active-service" title="Exp: <?= date('d.m h:i', $ad['ad']['renovable_limit'])?>" >
					Autosubidas
				
					</div>
				<?php endif ?>
				<?php if($ad['ad']['renovable'] == renovationType::Autodiario): ?>
					<div class="active-service" title="Exp: <?= date('d.m h:i', $ad['ad']['renovable_limit'])?>" >
						Diario Automatico
					
					</div>
				<?php endif ?>
				<?php if($ad['ad']['renovable'] == renovationType::Diario): ?>
					<div class="active-service" title="Exp: <?= date('d.m h:i', $ad['ad']['renovable_limit'])?>" >
						Extra Diario 
					</div>
				<?php endif ?>
			</div>
		</div>
		<?php if($ad['ad']['renovable'] != renovationType::Diario): ?>
			<button onclick="ActivateExtra(<?=$ad['ad']['ID_ad']?>)" type="button" style="display: none;" class="act-service">
				Activar Diario Extra
			</button>	  
		<?php endif ?>
		<?php if($ad['ad']['motivo'] != 7): ?>
			<?if($ad['ad']['active'] == 1): ?>
					<p>Caduca en <?= cal_restant($ad['ad']['date_ad']) ?> días</p>
				<?php elseif($ad['ad']['active'] == 2): ?>
					<p class="text-waring">Anuncio desactivado</p>
			<?endif ?>
		<?php endif ?>
		<a href="contactar?ad=<?=$ad['ad']['ID_ad']?>" class="send-mail">
			<i class="fa fa-envelope"></i>
		</a>
	</div>
	<div class="image_my_item">
		
			<? if($image){ ?>
			<?php if(count($ad['images']) > 1): ?>
			<div class="splide splide_my_item" role="group">
				<div class="splide__track">
					<ul class="splide__list">
						<?php foreach ($ad['images'] as $foto) { ?>
							<li class="splide__slide">
								<div class="imageAditem <?=getImgOrientation($foto['name_image'])?>">
									<a href="<?=urlAd($ad['ad']['ID_ad'])?>" target="_blank" title="<?=stripslashes($ad['ad']['title'])?>">
										<img height="220" width="172"  src='<?=Images::getImage($foto['name_image'], IMG_ADS, true, $foto['edit'])?>'>
									</a>
								</div>
							</li>
						<? } ?>
					</ul>
				</div>
			</div>
			<?php else: $foto = $ad['images'][0]; ?>
				<div class="imageAditem <?=getImgOrientation($foto['name_image'])?>">
					<a href="<?=urlAd($ad['ad']['ID_ad'])?>" target="_blank" title="<?=stripslashes($ad['ad']['title'])?>">
						<img height="220" width="172"  src='<?=Images::getImage($foto['name_image'], IMG_ADS, true, $foto['edit'])?>'>
					</a>
				</div>
			<?php endif ?>
			<?php } else{ ?>
			<?php if($BOLSA_ID == $ad['ad']['parent_cat']){ ?>
				<div class="imageAditem vertical">
					<a href="<?=urlAd($ad['ad']['ID_ad'])?>" target="_blank" title="<?=stripslashes($ad['ad']['title'])?>">
						<img src='<?=Images::getBolsaImg()?>'/>
					</a>
				</div>
			<?php } else { ?>
				<div class="imageAditem">
					<a href="<?=urlAd($ad['ad']['ID_ad'])?>" target="_blank" title="<?=stripslashes($ad['ad']['title'])?>">
						<img src='src/photos/<?=IMG_AD_DEFAULT_MIN;?>'/>
					</a>
				</div>
			<?php } ?>
			<? } ?>
		

	</div>

<script>
	current_pay = {i: 0, target: 0};
</script>

<div class="info_my_item">
	<span class="titleAd"><a href="<?=urlAd($ad['ad']['ID_ad'])?>" class="searchable" title="<?=stripslashes($ad['ad']['title'])?>"><?=stripslashes($ad['ad']['title'])?></a></span>
	<span class="zoneAd"></span>


	<span class="zoneAd">
		<?=$ad['parent_cat']['name'];?> 
		<!-- (<?=$ad['category']['name'];?>)  -->
		 en
		<? echo $ad['city']['name'];?> <?=$ad['region']['name'];?>
	</span>
	<? if(!$review_on){?>
	
	<? } ?>
	<span class="text2Ad">
		<?php if(isMobileDevice()): ?>
		  
			<span class="short">
					<?
						echo mb_strimwidth($ad['ad']['texto'], 0, 120, "...");
						//echo $ad['ad']['texto'];
					?>
			</span>
			<span class="open hidden">
					<?
						//echo mb_strimwidth($ad['ad']['texto'], 0, 220, "...");
						echo $ad['ad']['texto'];
					?>
			</span>
			<?php if(strlen($ad['ad']['texto']) > 120): ?>
			  
				<a href="javascript:void(0);" class="moreAd" onclick="showText(this);">
					ver más
				</a>

			<?php endif ?>
			<?php else: ?>
				<span class="short">
					<?
						echo mb_strimwidth($ad['ad']['texto'], 0, 280, "...");
						//echo $ad['ad']['texto'];
					?>
				</span>
				<span class="open hidden">
						<?
							//echo mb_strimwidth($ad['ad']['texto'], 0, 220, "...");
							echo $ad['ad']['texto'];
						?>
				</span>
				<?php if(strlen($ad['ad']['texto']) > 280): ?>
					<a href="javascript:void(0);" class="moreAd" onclick="showText(this);">
						ver más
					</a>
			<?php endif ?>
		<?php endif ?>
			
	 </span>
	<span class="dateAd"><?=$language['my_items.posted_since']?><?=timeSince($ad['ad']['date_ad'], false);?></span>
	<span class="dateAd">
		Visitas <?=$ad['ad']['visit']?>
	</span>
	<span class="statsAd">
		<!-- <b class="visits"><i><?=$ad['ad']['visit']?></i> <?=$language['my_items.visits']?></b> -->
		
	</span>
	<!-- <span class="priceAd"><? if($ad['ad']['price']>0) echo formatPrice($ad['ad']['price']); else echo $language['my_items.no_price'];?></span> -->
</div>
<div class="options_my_item <?=$ad['ad']['trash'] == 1 ? 'pb-5 pb-md-0': '' ?>">
		<?php if($ad['ad']['review'] != 0 && $ad['ad']['trash'] == 0): ?>
			<b class="text-on-top"><?=$language['my_items.on_review']?></b>
		<?php endif ?>
		<?php if($ad['ad']['motivo'] == 7): ?>
			<p class="text-md-right text-caducado" style="color: #e26d6d">Caducado</p>
		<?php endif ?>
	<ul <?= $ad['ad']['motivo'] == 7 ? 'style="display: none;"' : ''?>>

	<? if(!$review_on){?>

	<?php if($ad['ad']['review'] == 2): ?>
		<li class="d-none d-md-block">
			<b><?=$language['my_items.on_review']?></b>
		</li>
		
	<?php endif ?>

	<?if($ad['ad']['active'] == 1): ?>
		<li class="item_responsive">
			<a  href='javascript:void(0);' onclick="renovate(<?=$ad['ad']['ID_ad']?>)"> <?= $ad['ad']['motivo'] == 7 ? 'Reactivar' : 'Renovar'?></a>
		</li>
	<?php elseif($ad['ad']['active'] == 2): ?>
			<li class="item_responsive">
				<a onclick="activarAnuncio(<?=$ad['ad']['ID_ad']?>)"  href='javascript:void(0);' > Activar</a>
			</li>
	<?endif ?>

	<?php if($ad['ad']['review'] == 0): ?>
		
		<li class="item_responsive"><a <?if ($ad['ad']['premium1'] == 1 || ($ad['ad']['date_premium2'] + 60*20)  >= time()) {
			echo " class=disable_renove onclick=\"alert('No se puede modificar el anuncio mientras el servicio Premium esté activo.');return false;\" href=#>";
		} else {?> href='<?=$urlfriendly['url.my_items'].$urlfriendly['url.my_items.edit']?><?=$ad['ad']['ID_ad']?>'> <?}?> <?=$language['my_items.opt_edit']?></a></li>
	<?php endif ?>

	<? }else{ ?>

		<?php if($ad['ad']['motivo'] == Motivo::Cancelado): ?>
			<!-- <li><b>Cancelado</b></li> -->
			<li class="item_responsive"><a href="<?=$urlfriendly['url.my_items'].$urlfriendly['url.my_items.edit']?><?=$ad['ad']['ID_ad']?>" ><?=$language['my_items.opt_edit']?></a></li>
		<?php elseif($ad['ad']['motivo'] == Motivo::Desactivado): ?>
			<!-- <li><b>Desactivado</b></li> -->
			<li class="item_responsive"><a href="<?=$urlfriendly['url.my_items'].$urlfriendly['url.my_items.edit']?><?=$ad['ad']['ID_ad']?>" ><?=$language['my_items.opt_edit']?></a></li>
		<?php elseif($ad['ad']['trash'] == 0): ?>
			<li class="d-none d-md-block"><b><?=$language['my_items.on_review']?></b></li>
		<?php endif ?>
	

	<? }?>
	<li class="item_responsive ">
	<a href="javascript:void(0);" class="delete_ad_link btn-pink" onclick="setDeleteAd(<?=$ad['ad']['ID_ad']?>)" ><?=$language['my_items.opt_delete']?></a></li>
	<? if(!$review_on){
			$pending = Orders::checkActiveOrders($ad['ad']['ID_ad']);
		?>
		<?php if($pending == 0): ?>
			<li class="item_image r-hide">
				<a class="" href="javascript:void(0);" onclick="openPayment(<?=$ad['ad']['ID_ad']?>)" >
					<img src="<?=Images::getImage('boton-servicios.webp')?>?t" alt="servicios" height="55px">
				</a>
			</li>

		<?php else: ?>
		  
			<li class="item_pending r-hide">
				<a class="" onclick="openPending(<?=$pending?>)" href="javascript:void(0);" >
					Revisar pago pendiente
				</a>
			</li>
		<?php endif ?>

	<?php } ?>
</ul>
</div>

<div class="pay_my_item <?= !$review_on && $ad['ad']['ID_order'] != 0 ? "item-up" : ""?> <?=$ad['ad']['trash'] == 1 ? 'position-absolute': 'pt-2' ?>" id="pay_my_item_<?=$ad['ad']['ID_ad']?>">
	<div class="row-d text-center ">
		<ul class="row-items ">
			<?php if(!$AD_DISCARD): ?>
			  
				<? if(!$review_on){?>
					<?php if($ad['ad']['ID_order'] == 0){ ?>
	
						<?php
							if($ad['ad']['renovable'] == renovationType::Diario)
							{
								$hours = time() - $ad['ad']['date_ad'];
								$hours = $hours / 3600;
								$hours = 12 - intval($hours);
							}else
								$hours = 24;
						?>
						<?if($ad['ad']['active'] == 1): ?>
							<li class="item_responsive"><a  href='javascript:void(0);' onclick="renovate(<?=$ad['ad']['ID_ad']?>, false, <?=$hours?>)"> <?= $ad['ad']['motivo'] == 7 ? 'Reactivar' : 'Renovar anuncio'?></a></li>
							<?php elseif($ad['ad']['active'] == 2): ?>
								<li class="item_responsive">
									<a  onclick="activarAnuncio(<?=$ad['ad']['ID_ad']?>)"  href='javascript:void(0);' > Activar</a>
								</li>
						<?endif ?>
						<?php if($ad['ad']['review'] == 0): ?>
							
							<li class="item_responsive"><a <?if ($ad['ad']['premium1'] == 1 || ($ad['ad']['date_premium2'] + 60*20)  >= time()) {
								echo " class=disable_renove onclick=\"alert('No se puede modificar el anuncio mientras el servicio Premium esté activo.');return false;\" href=#>";
							} else {?> href='<?=$urlfriendly['url.my_items'].$urlfriendly['url.my_items.edit']?><?=$ad['ad']['ID_ad']?>'> <?}?> Editar Anuncio</a></li>
						<?php endif ?>
					<?php }else{ ?>
						<li class="item_responsive "><a class="btn-green" href="javascript:void(0)" onclick="orderPayment(<?=$ad['ad']['ID_order']?>)">Activar</a></li>
					<?php } ?>
					
				<? }else{ ?>
	
					<?php if($ad['ad']['motivo'] == Motivo::Cancelado): ?>
						<li class="item_responsive"><a href="<?=$urlfriendly['url.my_items'].$urlfriendly['url.my_items.edit']?><?=$ad['ad']['ID_ad']?>" >Editar anuncio</a></li>
					<?php endif ?>
	
	
	
				<? }?>
				<li class="item_responsive"><a href="javascript:void(0);" class="delete_ad_link btn-pink" onclick="setDeleteAd(<?=$ad['ad']['ID_ad']?>)">Eliminar anuncio</a></li>
				<?php if($pending == 0): ?>
						<li class="item_image r-show">
							<a class="" href="javascript:void(0);" onclick="openPayment(<?=$ad['ad']['ID_ad']?>)" >
								<img src="<?=Images::getImage('boton-servicios.webp')?>?t" alt="servicios" height="60px">
							</a>
						</li>

					<?php else: ?>
					
						<li class="item_pending r-show">
							<a class="" onclick="openPending(<?=$pending?>)" href="javascript:void(0);" >
								Revisar <br> pago pendiente
							</a>
						</li>
					<?php endif ?>
			<?php else: ?>
				<li class="item_responsive"><a href="<?=$urlfriendly['url.my_items'].$urlfriendly['url.my_items.edit']?><?=$ad['ad']['ID_ad']?>" ><?=$language['my_items.opt_edit']?></a></li>
				<li class="item_responsive"><a href="javascript:void(0);" class="delete_ad_link" onclick="deleteDiscardMsg(<?=$ad['ad']['ID_ad']?>)">Eliminar cambios</a></li>
			<?php endif ?>
			
		</ul>
	</div>
</div>

</li>
<?
} // FOR
?>
</ul>
</form>
<?
}else{ // IF
?>
<div class="no_item_founds">
<p>No tienes ningún anuncio en este momento.</p>
<a href="publicar-anuncio-gratis/">¡Publica tu anuncio ahora!</a></div>
<?
} 

loadBlock("panel-publicista");
?>
<div class="modal" id="delete-ad" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body delad-content">
			<!-- $user_ads_count == 1 -->
				<?php if(false): ?>
					<p class="text-center mb-1">
						<strong class="f-1150" >¿Estás seguro que quieres borrar este anuncio?</strong>
					</p>
					<p class="text-center">Si eliminas este anuncio <strong>GRATIS</strong>. deberás esperar unos días para volver a publicar otro anuncio.</p>
				  <?php else: ?>
					<p class="text-center mb-1">
					<strong class="f-1150" >¿Estás seguro que quieres borrar este anuncio?</strong>
					</p>
					<p class="text-center">Si eliminas este anuncio deberás comprar otro anuncio <br> para publicar.</p>
				<?php endif ?>

				<div class="text-center">
					<button type="button" class="btn btn-pink" data-dismiss="modal" id="delete-ad-btn">
						Borrar
					</button>
				</div>
			</div>
		</div>
	</div>

</div>

<dialog class="dialog"  id="discount_info" >
	<div class="dialog-modal">
		<a href="javascript:void(0);" onclick="$('#discount_info').attr('open', false)" style="color: black;">
			<i class="fa-times-circle fa"></i>
		</a>
		<p class="hightlight black">
              SUPER  <b>DESCUENTO</b>
        </p>
		<p>si eres centro o publicista entra en contacto con nosotros</p>
		
		<a href="/contactar" class="payment-btn">
			Contactar
		</a>
	</div>
</dialog>

<div class="premium_contianer" style="display: none;" id="pay-listado">
	<div class="box_premium listing">
		<span class="close" onclick="$('#pay-listado').hide()">
			<fa class="fa fa-times"></fa>
		</span>
		<h3><?= $language['premium.premium_2_title'] ?></h3>
		<div class="box_premium_p">
			<div class="box_premium_img"><img src="src/images/premium2_example.png" alt="Destacar anuncio" /></div>
			<div class="info listing"><?= $language['premium.premium_2_info'] ?></div>
			<form method="post" action="">

				<div class="box_credits">
					<input type="hidden" name="credits" id="listing-credits" data-normal="<?=$_SESSION['data']['credits']?>" data-adult="<?=$_SESSION['data']['credits_adult']?>" value="">
					<div class="credit_text">
						<?= $language['credit.label'] ?>
					</div><!--
				--><div class="credit_number" id="show-credits">
						
					</div><img src="src/images/credits_coins.png" alt="credits" class="coin_img">
				</div>
				
				<div class="box_select">
					<label for="frecuency">Subir</label>
					<select class="select" name="frecuency" id="frecuency" onchange="change_button()">

						<?
								$plan_times = selectSQL("sc_time_options", $a = array('owner' => 'LISTING'));
								for ($i = 0; $i < count($plan_times); $i++) { ?>

							<option 
								<? if ($ad['ad']['premium2_frecuency'] === $plan_times[$i]['value']) echo 'selected'; ?> 
								value="<?= $plan_times[$i]['value'] ?>">Cada <?= $plan_times[$i]['quantity'] == 1 ? '' : $plan_times[$i]['quantity']; ?> <?= $plan_times[$i]['time_unit'] ?></option>
						<? } ?>

						<option <? if ($ad['ad']['premium2_frecuency'] == 0) echo 'selected'; ?> value="0">Desactivado</option>
							
					</select>
				</div>

				<div class="box_select">
					<label for="night">Noche</label>
					<select class="select" name="night" id="night">
						<option value="0" >No</option>
						<option value="1" <? if ($ad['ad']['premium2_night'] == 1) echo 'selected'; ?>>Si</option>
					</select>
				</div>

				<!--<label><?= print_r($ad) ?></label>-->

				<div class="box_pay">
					
					<input type="submit" name="submit" class="pay" id="premium2_button" value="SUBIR" >
					<!--<div class="pay" id="paypal_2"><?= $language['premium.premium_2_button'] ?></div>-->
					<div class="buy_credit_text">
						<a href="<?=$urlfriendly['url.credits']?>">
							<?= $language['premium.buy_credits'] ?><img src="src/images/coins_bottom.png" alt="credits" width="40px">
						</a>
					</div>
				</div>

			</form>
		</div>
	</div>
</div>

<div class="premium_contianer" style="display: none;" id="pay-premium">

</div>
<div class="premium_contianer" style="display: none;" id="banner_preview">
	<div>
		<span class="close" onclick="$('#banner_preview').hide();$('#pay-premium').show();">
			<fa class="fa fa-times"></fa>
		</span>
		<img src="" class="banner-preview">
		<img src="" class="banner-preview-r">
	</div>	
</div>
<?php if(isset($_GET['buyExtras'])): ?>
	<dialog class="dialog" open id="extras_msg" >
        <div class="dialog-modal">
            <a href="javascript:void(0)" onclick="$('#extras_msg')[0].close();cleanUrlParams();" style="color: black;">
                <i class="fa-times-circle fa"></i>
            </a>
            <p><strong>¡Tu pago ha sido realizado con éxito!</strong></p>
			<p>Ya puedes publicar o activar tus anuncios.</p>

        </div>
    </dialog>
<?php endif ?>




<script type="text/javascript">
    function change_button() {
        d = document.getElementById("frecuency").value;
        if (d > 0){
            document.getElementById("premium2_button").value = 'SUBIR';
            document.getElementById("night").disabled = false;
        }
        else{
            document.getElementById("premium2_button").value = 'DESACTIVAR';
            document.getElementById("night").disabled = true;
        }
    }
	$(document).ready(function() {
		checkEvents(<?=$_SESSION['data']['ID_user']?>);
	});
</script>

<script defer src="<?=getConfParam('SITE_URL')?>src/js/my-items.js"></script>