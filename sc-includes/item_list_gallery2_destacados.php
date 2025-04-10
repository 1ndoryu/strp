<?

// $ad=getDataAd($select[$i]['ID_ad']);

$image=false;
$imgvertical = false;
if(count($ad['images'])!=0) $image=true;

?>

<? //verifica si hay destacados con la cinta esa y los imprime

if ($ad['ad']['premium2']!=0 || $ad['ad']['premium3']==1){

?>

<div class="item_list gallery   <? if($ad['ad']['premium2']==1) echo ' item_top';?>" itemscope="" itemtype="http://schema.org/Product">

<? 
// $time_limit = time() - $ad['ad']['date_premium2']; 

// if($ad['ad']['premium2']==1 && $time_limit <= (15*60)){

?>

<div class="item-top-label listing">
	DESTACADO
	<svg
   width="22"
   height="22"
   viewBox="0 0 6.3500001 6.3500001"
   version="1.1"
   id="svg1"
   xmlns="http://www.w3.org/2000/svg"
   xmlns:svg="http://www.w3.org/2000/svg">
  <defs
     id="defs1" />
  <g
     id="layer1">
    <path
       style="fill:#ffffff;stroke-width:0.264583"
       id="path495"
       d="M 3.6282233,0.0978832 0.89238569,1.721353 -1.8434519,3.3448231 -1.8814992,0.16378334 -1.9195467,-3.0172565 0.85433816,-1.4596866 Z"
       transform="rotate(-179.19248,2.2480036,1.6438653)" />
  </g>
</svg>
</div>

<?php 

?>
<div class="imageAdMin <?= getImgOrientation($ad['images'][0]['name_image']); ?>">

	<!-- <p class="camera-number"><i class="fa fa-camera" aria-hidden="true"></i> <?=count($ad['images'])?></p> -->

	<!-- <div class="bg-img-item" style="background: url('<?=Images::getImage($ad['images'][0]['name_image'], IMG_ADS, true);?>');"></div> -->

	<a href="<?=URL($ad);?>" onclick="event.preventDefault();" >
		<img loading="lazy" itemprop="image" src='<?=Images::getImage($ad['images'][0]['name_image'], IMG_ADS, true,  $ad['images'][0]['edit']);?>' alt='<?=stripslashes($ad['ad']['title']); ?>'/>
	</a>

</div>

<div class="infoAd">
	<a href="<?=URL($ad);?>" target="_blank" class="hidden-link">
		<h2><?= stripslashes($ad['ad']['title']); ?></h2>
	</a>
	<p class="infotext"><?= stripslashes($ad['ad']['texto']); ?></p>
	<?php if(in_favs($ad['ad']['ID_ad'])): ?>
		<span class="fav on" data-id="fav-<?=$ad['ad']['ID_ad']?>" >
			<i class="fa fa-heart"></i>
		</span>
		<?php else: ?>
		<span class="fav" data-id="fav-<?=$ad['ad']['ID_ad']?>" >
			<i class="fa fa-heart"></i>
		</span>
	<?php endif ?>
	<p class="details">
		<!-- <span class="region">
			<?=stripslashes($ad['region']['name']); ?>
		</span> -->
		
		<!-- <span class="priceAd" itemprop="price" content="<?=number_format($ad['ad']['price'],0,',','.')?>">
			<? if($ad['ad']['price']>0) echo formatPrice($ad['ad']['price']); else echo $language['item_list.no_price']?>
			<meta itemprop="priceCurrency" content="EUR">
		</span> -->

	</p>
	<a href="<?=URL($ad);?>" target="_blank" class="open-link">
	 Ver anuncio
	</a>
</div>

	<div class="legenAd">
		<div class="titlediv">
			<span class="titleAd">
				<?=stripslashes($ad['region']['name']); ?>
			</span>
		</div>

	</div>
	<div class="iconsdiv">
		
		<div class="iconAd">
			<img src="<?=Images::getImage('disponibilidad.svg');?>?v=0" alt="disponibilidad">
			<span class="iconAdPopup">
				<?php if($ad['ad']['dis'] == 0): ?>
					Disponibilidad
				<?php endif ?>
				<?php if($ad['ad']['dis'] == 1): ?>
					Todos los días
				<?php endif ?>
				<?php if($ad['ad']['dis'] == 2): ?>
					Lunes a Viernes
				<?php endif ?>
				<?php if($ad['ad']['dis'] == 3): ?>
					Lunes a Sábado
				<?php endif ?>
				<?php if($ad['ad']['dis'] == 4): ?>
					Sábados y Domingos
				<?php endif ?>
			</span>
		</div>
		<div class="iconAd">
			<img src="<?=Images::getImage('horario.webp');?>?v=0" alt="horario">
			<span class="iconAdPopup">
				<?php if($ad['ad']['hor_start'] != "" && $ad['ad']['hor_end'] != "" && $ad['ad']['hor_start'] != $ad['ad']['hor_end']): ?>
					<?= $ad['ad']['hor_start'] ?> a <?= $ad['ad']['hor_end'] ?>h
				<?php else: ?>
					A consultar
				<?php endif ?>
			</span>
			<?php if($ad['ad']['out'] == 1): ?>
				<div class="iconAd">
					<img src="<?=Images::getImage('salidas.svg');?>?v=0" alt="Salidas">
					<span class="iconAdPopup">Salidas</span>
				</div>
			<?php endif ?>
	</div>
</div>

<? }
//}
?>
