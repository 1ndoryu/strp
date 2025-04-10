<?php
include("../../../settings.inc.php");
include(ABSPATH . "sc-includes/php/func/search_param.php");

if($total_reg!=0){
    
    // LISTING ADS
    
    $premium2_ads = array_filter($select, function ($ad) {
        $time_limit = time() - $ad['date_premium2'];
        $limit = Service::getOption('LISTING', 'TIME_TAG') * 60;
        if($ad['premium2'] != 0 && $time_limit <= $limit){
            return true;  
        } 
        return false;
    });
    $premium3_ads = array_filter($select, function ($ad) {

        if($ad['premium3'] == 1){
            return true;  
        } 
        return false;
    });
    


    $key = 0;
    
    foreach($premium3_ads as $pm3){
        $ad=getDataAd($pm3['ID_ad']);
        $key++;
        include("../../item_list_gallery2_destacados.php");
    }
    foreach($premium2_ads as $pm2){
        $ad=getDataAd($pm2['ID_ad']);
        $key++;
        include("../../item_list_gallery2_destacados.php");
    }
    
    // NORMAL ADS
    
    $no_premium2_ads = array_filter($select, function ($ad) {
        $time_limit = time() - $ad['date_premium2'];
        if(($ad['premium2'] == 0 || $time_limit > (20*60)) && $ad['premium3'] == 0){
            return true;  
        } 
        return false;
    });


    $banner_position = getConfParam('BANNER_MIDDLE_POS');

    foreach($no_premium2_ads as $npm2)
    {
        $ad=getDataAd($npm2['ID_ad']);
        include("../../item_list_gallery3.php");

        $key++;
        if($key == $banner_position)
        {
            $banner = getBanner("740120", BannerPosition::Middle,$select[0]['parent_cat']);

            echo $banner;
        }
        
    }
  	

}else{
	?>
    <div class="no_item_founds">
	<?=$language['list.no_items_found']?>
    </div>
    <?
}?>

<div style='clear:both;'></div>