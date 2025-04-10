<?
	$sitios = selectSQL("sc_ad",$where=array('premium1'=>1,'active'=>1,'review'=>0)," rand() LIMIT 5");
	if(count($sitios) !=0){
	?>
<div class="item_premium">
<ul class="item_premium_list">
	<? for($i=0;$i<count($sitios);$i++){ 
	$ad=getDataAd($sitios[$i]['ID_ad']);
	$image=false;
	if(count($ad['images'])!=0) $image=true;
?>
        <li>
        <div class="price_item"><? if($ad['ad']['price']>0) echo formatPrice($ad['ad']['price']); else echo $language['ads_premium.no_price'];?></div>
        <div class="image_box">
        <table><tr><td>
        <a href="<?=urlAd($ad['ad']['ID_ad']);?>">
        <img src="<?=IMG_ADS;?><? if($image){ echo min_image($ad['images'][0]['name_image']); }else{ echo IMG_AD_DEFAULT_MIN; } ?>" alt="<?=stripslashes($ad['ad']['title'])?>"/>
        </a></td>
        </tr>
        </table>
        </div>
        <div class="dm_name">
		<a href="<?=urlAd($ad['ad']['ID_ad']);?>"><?=stripslashes($ad['ad']['title']);?></a>
        </div>
        </li>
	<? } ?>
	</ul>
</div>
<? } ?>
