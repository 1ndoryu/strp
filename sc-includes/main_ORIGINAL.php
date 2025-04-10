<div class="col_single box_white pub_bottom">
    <? if(getBanner('97090')!=''){?>
    <?=getBanner('97090')?>
    <? } ?>
</div>
<?
$select = selectSQL("sc_ad",$where=array('premium1'=>1,'active'=>1,'review'=>0)," rand() LIMIT 4");
if(count($select)>0){
?>
<h3 class="title_main">TOP ANUNCIOS</h3>
<span class="separator"><i></i></span>
<div class="last_ads">
    <ul class="last_ads_list">
        <? for($i=0;$i<count($select);$i++){
			include("item_list_gallery.php");
		} ?>
    </ul>
</div>
<? } ?>
<h3 class="title_main">Categorías</h3>
<span class="separator"><i></i></span>
<div class="col_single categories_list">
		<? // Categorías fercode
		$listadoCat=selectSQL("sc_category",$a=array('parent_cat'=>"0<"),"ord ASC");
		for($i=0;$i<count($listadoCat);$i++){
			$listadoSubCat=selectSQL("sc_category",$a=array('parent_cat'=>$listadoCat[$i]['ID_cat']),"name ASC");
		?>
		<div class="categories">
        <a href="<?=$listadoCat[$i]['name_seo'];?>/" title="<?=$language['main.ads_of']?><?=$listadoCat[$i]['name'];?>">
        	<h2><i class="fa <?=$listadoCat[$i]['icon'];?>"></i><?=$listadoCat[$i]['name'];?></h2>
        </a>
        	<ul>
            	<? for($j=0;$j<count($listadoSubCat);$j++){?>
                	<li><a href="<?=$listadoCat[$i]['name_seo'];?>/<?=$listadoSubCat[$j]['name_seo'];?>/"><?=$listadoSubCat[$j]['name']?></a></li>
                <? } ?>
            </ul>
        </div>
		<?
		}
		?>
</div>
<h3 class="title_main">Anuncios por provincia</h3>
<span class="separator"><i></i></span>
<div class="col_single box_white regions_list">
    <ul class="ul-regions">
        <? // Provincias
		$more_post=getMoreRegion();
        $listadoProv=selectSQL("sc_region",$a=array(),"name ASC");
        for($i=0;$i<count($listadoProv);$i++){?>
        <li class="region-<?=$listadoProv[$i]['name_seo']?>"><a href="<?=$urlfriendly['url.classifieds.region'].$listadoProv[$i]['name_seo'];?>/" title="<?=$language['main.ads_in']?><?=$listadoProv[$i]['name'];?>" class="blue">
		<? if(in_array($listadoProv[$i]['ID_region'],$more_post)){?>
			<b><?=$listadoProv[$i]['name'];?></b>
        <? }else{ ?>
			<?=$listadoProv[$i]['name'];?>
        <? } ?>
        </a></li>
        <? } ?>
	</ul>
</div>
<div class="col_single box_white pub_bottom">
    <? if(getBanner('97090')!=''){?>
    <?=getBanner('97090')?>
    <? } ?>
</div>
