<?
$premium_ads = selectSQL("sc_ad",
$where=array(
    'premium1'=>1,
    'active'=>1,
    'review'=>0,
    'parent_cat'=>$category_parent,
    "ID_order" => "0"
)," rand() LIMIT 10");
$BOLSA_ID = getBolsaID();

//$selectTito = selectSQL("sc_ad",$where=array('premium1'=>1,'active'=>1,'review'=>0)," LIMIT 4");

console_log($select);
?>

<? 
    // $premium_ads = array_filter($select, function ($ad) {
    //     if($ad['premium1'] != 0) return true;
    //     return false;
    // });
    
    loadBlock('banner', array('position'=> BannerPosition::Top , 'size'=>'740120', 'parent_cat'=>$select[0]['parent_cat']));
?>

<?
$count_premium = count($premium_ads);
if($count_premium>0){

    if(isMobileDevice() && $count_premium < 3)
        $LOADING = "eager";
    else
        $LOADING = "lazy";
?>

<h3 class="title_main" ><b>TOP</b> ANUNCIOS</h3>
<!-- <span class="separator"></span> -->
<?php if( $count_premium >= 3 ): ?>
    
    <div class="premiun_container">
        <div class="splide carousel-list <?= $count_premium < 3 && $IS_MOBILE ? 'visible' : ''; ?> <?= count($premium_ads) < 5 && !$IS_MOBILE ? 'visible' : ''; ?>" >
            <div class="splide__track" >
                <ul class="splide__list">
                <!--<li class="glide__slide">
                    
                </li>-->
                <? 
                        
                        shuffle($premium_ads);
                    //   console_log($premium_ads);
                    //   console_log(count($premium_ads));
                        
                    
                        foreach ($premium_ads as $pma){
                            $ad=getDataAd($pma['ID_ad']);
                            include("item_list_gallery.php");
                        }
                        
                    ?>
                </ul>
            </div>
        
        </div>
    </div>
    <script>
        var premium_count = <?=count($premium_ads);?>;
    </script>
    <script type="text/javascript" src="<?=getConfParam('SITE_URL');?><?=JS_PATH;?>carousel.js"></script>

<?php else: ?>
    <div class="last_ads premium_ads">
        <ul class="last_ads_list">
            <? 
                
                shuffle($premium_ads);
            //   console_log($premium_ads);
            //   console_log(count($premium_ads));
                
                $j = 0;
                foreach ($premium_ads as $pma){
                    $ad=getDataAd($pma['ID_ad']);
                    include("item_list_gallery.php");
                    $j++;
                    if($j == 4)
                        break;
                }
                
            ?>
        </ul>
    </div>

<?php endif ?>


<? } ?>

<?
//if(!$user_query) include("filter_list.php");
//else include("user_info_list.php");



if($user_query) include("user_info_list.php");

$title_list = "";
if(isset($_GET['se']) || isset($_GET['s']))
{ 
    $title_list = $cat_data[0]['name'];
    $desc_list = $cat_data[0]['description'];
}


if($cat_data[0]['h1'] != "")
{
    $title_list = $cat_data[0]['h1'];
}

if(isset($name_location))
{
    $title_list .= " en ". $name_location;
    $desc_list = rtrim($desc_list, "."); 
    $desc_list .= " en ". $name_location . ".";
}else
{
    if(isset($region_ID) && $region_ID != 0)
    {
        $title_list .= " en ". $region_data[0]['name']; 
        $desc_list = rtrim($desc_list, "."); 
        $desc_list .= " en ". $region_data[0]['name'] . ".";
    }
}

if($keyword_data)
{
    $title_list = $keyword_data['h1'];
    $title_list = str_replace("%ciudad%", $region_data[0]['name'], $title_list);
}
?>
    
<?php
if(count($select)>0 && $select[0]['parent_cat'] != $BOLSA_ID)
    loadBlock('filter_list');
?>

<? if(!$user_query){?>
<h3 id="title_listing" class="title_listing">
<!-- <i><? echo $total_reg; ?>    
</i> <?=$language['list.ads']?> -->
<? 
   echo $title_list;
?>

</h3>
<h4 class="desc_listing">
    <?=$desc_list?>
</h4>



<? } ?>
<div class="big list <?=$select[0]['parent_cat'] == $BOLSA_ID ? 'list-col' : ''?>" id="list_items">
<?
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
    
    console_log('premium2');
    console_log($premium2_ads);

    console_log('premium3');
    console_log($premium3_ads);

    $key = 0;
    
    foreach($premium3_ads as $pm3){
        $ad=getDataAd($pm3['ID_ad']);
        $key++;
        include("item_list_gallery2_destacados.php");
    }
    foreach($premium2_ads as $pm2){
        $ad=getDataAd($pm2['ID_ad']);
        $key++;
        include("item_list_gallery2_destacados.php");
    }
    
    // NORMAL ADS
    
    $no_premium2_ads = array_filter($select, function ($ad) {
        $time_limit = time() - $ad['date_premium2'];
        if(($ad['premium2'] == 0 || $time_limit > (20*60)) && $ad['premium3'] == 0){
            return true;  
        } 
        return false;
    });
    
    console_log("no premium");
    console_log($no_premium2_ads);

    $banner_position = getConfParam('BANNER_MIDDLE_POS');

    foreach($no_premium2_ads as $npm2)
    {
        $ad=getDataAd($npm2['ID_ad']);
        if($ad['ad']['parent_cat'] == $BOLSA_ID)
            include("item_list_bolsa.php");
        else
            include("item_list_gallery3.php");

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
}

?>

<div style='clear:both;'></div>

</div>

<?php 
if($select[0]['parent_cat'] != $BOLSA_ID)
    loadBlock('banner', array('position'=> BannerPosition::Bottom , 'size'=>'740120', 'parent_cat'=>$select[0]['parent_cat'])); 
?>

<?php
	if($tot_pag != 0){ ?>
		<div class="pag_buttons">
       
            <button id="first_page_button" onclick="getFirstPage()"  style="display: none;"><i class="fa fa-angle-double-left"></i></button>
            <button id="prev_button" onclick="getPrevPage()"  style="display: none;"><i class="fa fa-angle-left"></i></button>
         
            <button id="page_current" class="current"><?=$pag?></button>
           
            <button id="next_button" style="<?=$tot_pag == 1 ? "display: none;": ''?>" onclick="getNextPage()"><i class="fa fa-angle-right"></i></button>
            <button id="last_page_button" style="<?=$tot_pag == 1 ? "display: none;": ''?>" onclick="getLastPage()"><i class="fa fa-angle-double-right"></i></button>

          

        </div>

	<?php }
?>
<?
// if($search_query && $total_reg>0){
// 	saveSearch($q,$_GET['q']);
// }
?>
<?php if(isMobileDevice()): ?>
  <script>
        $(document).ready(function(){
            $(document).on("click", ".item_list.gallery", (e) => {
                const element = $(e.currentTarget);
                if($(element).hasClass("opened"))
                    return;
                //e.preventDefault();
                $('.item_list.gallery').removeClass("opened");
                $(element).addClass("opened");
                $("a.open-link").css("pointer-events", "");

                const link = $(element).find("a.open-link")[0];
                const link2 = $(element).find("a.hidden-link")[0];
                setTimeout(() => {
                    link.style.pointerEvents = "auto";
                    link2.style.pointerEvents = "auto";
                }, 200); // Igual al tiempo de la animación (0.5s)

                setTimeout(() => {
                    link.style.pointerEvents = "none";
                    link2.style.pointerEvents = "none";
                    $(element).removeClass("opened");
                }, 2000); // Igual al tiempo de la animación (3s)
            });
        });
  </script>
<?php endif ?>



<?php
    if(is_adult())
        loadBlock('adv-msg');
?>

<?php 
    if(count($select)>0 && $select[0]['parent_cat'] != $BOLSA_ID)
        loadBlock('links');
 ?>

<?php loadBlock('text'); ?>
<script>
    search_params = <?=json_encode($_GET);?>;
    search_params.pag = 1;
    search_params.tot_pag = <?=$tot_pag?>;
</script>