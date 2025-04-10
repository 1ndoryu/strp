<?
$image=false;
$ad=getDataAd($select[$i]['ID_ad']);
if(count($ad['images'])!=0) $image=true;
?>

<?php if($ad['ad']['active']==1){ ?>

<div class="item_list gallery<? if($ad['ad']['premium_3']==1) echo ' sub';?>" itemscope="" itemtype="http://schema.org/Product">
<? if($ad['ad']['premium2']==1){?>
<div class="item-top-label">Recomendado</div>
<? } ?>


<div class="imageAdMin">
	<p class="camera-number"><i class="fa fa-camera" aria-hidden="true"></i> <?=count($ad['images'])?></p>
	<div class="bg-img-item" style="background: url('<?=IMG_ADS;?><? if($image){ ?><?=$ad['images'][0]['name_image']; ?><? }else{ echo IMG_AD_DEFAULT; } ?>');"></div>
	<a href="<?=urlAd($ad['ad']['ID_ad']);?>" title="<?=stripslashes($ad['ad']['title']); ?>">
	<img itemprop="image" src='<?=IMG_ADS;?><? if($image){ ?><?=$ad['images'][0]['name_image']; ?><? }else{ echo IMG_AD_DEFAULT_MIN; } ?>' alt='<?=stripslashes($ad['ad']['title']); ?>'/>
	</a>
</div>
<span class="titleAd">
	<a href="<?=urlAd($ad['ad']['ID_ad']);?>" title="<?=stripslashes($ad['ad']['title']); ?>" itemprop="name"><?=stripslashes($ad['ad']['title']); ?></a>
</span>
<div itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
<span class="priceAd" itemprop="price" content="<?=number_format($ad['ad']['price'],0,',','.')?>">
<? if($ad['ad']['price']>0) echo formatPrice($ad['ad']['price']); else echo $language['item_list.no_price']?>
<meta itemprop="priceCurrency" content="EUR">
</span>
</div>
<i id="fav-<?=$ad['ad']['ID_ad'];?>" class="fav<? if(isset($_COOKIE['fav'][''.$ad['ad']['ID_ad'].''])) echo " on";?> fa fa-star" aria-hidden="true"></i>
<div class="user_item_info">
        <a href="usuario/<?=$ad['ad']['ID_user']?>"><span class="user_item_photo" style="background-image:url(<?=getPhotoUser($ad['ad']['ID_user'])?>"></span> <?=formatName($ad['user']['name'])?></a>
</div>
</div>

<?php } ?>
